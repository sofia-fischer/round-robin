<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\OneNightWerewolfDayJob;
use App\Jobs\OneNightWerewolfNightJob;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\ValueObjects\ColorEnum;
use App\ValueObjects\Enums\WerewolfRoleEnum;
use App\ValueObjects\Enums\WerewolfStateEnum;
use App\ValueObjects\WerewolfBoard;
use App\ValueObjects\WerewolfMoves\SeeMove;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Game
 *
 * @property-read \App\Models\Player|null $authenticatedPlayerVote
 * @see \App\Models\WerewolfGame::getAuthenticatedPlayerVoteAttribute()
 *
 * @method static \Database\Factories\WerewolfGameFactory factory(...$parameters)
 *
 * @package App\Models
 */
class WerewolfGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = 'werewolf';

    static $title = 'One Night Werewolf';

    static $description = 'Each player takes on the role of a Villager, a Werewolf, or a special character.
          It is your job to figure out who the Werewolves are and to kill at least one of them in order to win
          ....unless you have become a Werewolf yourself.
          You wiII need to figure out what team you are on
          (because your role card might have been switched with another role card),
          and then figure out what teams the other players are on.
          At the end of each game you will vote for a player who is not on your team;
          the player that receives the most votes is "killed".';
    public const NIGHT_DURATION = 100;
    public const DAY_DURATION = 200;

    public static function query(): Builder
    {
        return parent::query()->where('logic_identifier', self::$logic_identifier);
    }

    public function getCurrentWerewolfBoard(): ?WerewolfBoard
    {
        if (! $this->currentRound) {
            return null;
        }

        return WerewolfBoard::fromArray($this->currentRound->payload);
    }

    public function setCurrentWerewolfBoard(WerewolfBoard $board): void
    {
        $this->currentRound->update(['payload' => $board->toArray()]);
    }

    public function currentMoveFromPlayer(Player $player): ?Move
    {
        return $player->moves->firstWhere('round_id', $this->currentRound?->id);
    }

    public function voted(Player $player): ?Player
    {
        $vote = $this->currentMoveFromPlayer($player)?->getPayloadWithKey('vote');

        if (! $vote) {
            return null;
        }

        return $this->players->firstWhere('id', $vote);
    }

    public function getAuthenticatedPlayerVoteAttribute(): ?Player
    {
        return $this->players
            ->firstWhere('id', $this->currentMoveFromPlayer($this->authenticatedPlayer)
                ?->getPayloadWithKey('vote'));
    }

    public function startRound()
    {
        $players = $this->players()->inRandomOrder()->get();

        while ($players->count() < 5) {
            $players->add($this->players()->create());
        }

        $round = WerewolfBoard::newRoundForPlayers($players->pluck('id')->toArray());
        Round::create(['game_id' => $this->id, 'payload' => $round->toArray()]);

        event(new GameRoundAction($this));
        OneNightWerewolfNightJob::dispatch($this->id)
            ->onConnection('redis')
            ->delay(now()->addSeconds(WerewolfGame::NIGHT_DURATION));
    }

    public function sunrise()
    {
        $board = $this->getCurrentWerewolfBoard();

        foreach ($this->players as $player) {
            $move = $this->currentMoveFromPlayer($player);

            $saw = $move?->getPayloadWithKey('move')['see'] ?? false;

            if (! $saw) {
                continue;
            }

            $move = new SeeMove(
                watcherId: $player->id,
                seeId: $saw,
                sawRole: $board->see($saw),
                sawName: $this->players->firstWhere('id', $saw)?->name ?? $saw,
                sawColor: ColorEnum::tryFromUuid($saw),
            );

            $board->makeMove($move);
        }

        $board->setState(WerewolfStateEnum::DAY);
        $this->setCurrentWerewolfBoard($board);

        event(new GameRoundAction($this));
        OneNightWerewolfDayJob::dispatch($this->id)->onConnection('redis')->delay(now()->addSeconds(WerewolfGame::DAY_DURATION));
    }

    public function end()
    {
        $killedPlayerId = $this->players
            ->mapWithKeys(fn (Player $player) => [
                $player->id => $this->currentMoveFromPlayer($player)?->getPayloadWithKey('vote'),
            ])
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();

        $board = $this->getCurrentWerewolfBoard();
        $killedPlayerRole = $board->see($killedPlayerId);

        $win = match ($killedPlayerRole) {
            WerewolfRoleEnum::TANNER => WerewolfRoleEnum::TANNER,
            WerewolfRoleEnum::WEREWOLF => WerewolfRoleEnum::VILLAGER,
            default => WerewolfRoleEnum::WEREWOLF,
        };


        $this->players->each(fn (Player $player) => Move::updateOrCreate([
            'round_id' => $this->currentRound->id,
            'player_id' => $player->id,
            'user_id' => $player->user_id,
        ], [
            'score' => match ($board->see($player->id)) {
                WerewolfRoleEnum::WEREWOLF => $win === WerewolfRoleEnum::WEREWOLF ? 1 : 0,
                WerewolfRoleEnum::MINION => $win === WerewolfRoleEnum::WEREWOLF ? 1 : 0,
                WerewolfRoleEnum::TANNER => $win === WerewolfRoleEnum::TANNER ? 1 : 0,
                null => 0,
                default => $win === WerewolfRoleEnum::VILLAGER ? 1 : 0
            },
        ]));

        $killedPlayer = $this->players->firstWhere('id', $killedPlayerId);
        $this->mergeCurrentPayloadAttribute([
            'state' => 'end',
            'win' => $win,
            'killedPlayerId' => $killedPlayerId,
            'killedPlayerName' => $killedPlayer?->name ?? 'Nobody',
            'killedPlayerColor' => $killedPlayer->activeColor ?? 'gray-500',
            'killedRole' => $killedPlayerRole,
        ]);

        $this->currentRound->completed_at = now();
        $this->currentRound->save();
        event(new GameEnded($this->id));
    }
}
