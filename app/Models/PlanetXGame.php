<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObjects\PlanetXBoard;
use App\ValueObjects\PlanetXConferences;
use App\ValueObjects\PlanetXRules\PlanetXRule;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static PlanetXGame create(array $attributes = [])
 * @method static \Database\Factories\PlanetXGameFactory factory(...$parameters)
 */
class PlanetXGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = 'planet_x';

    static $title = 'Planet X';

    static $description = 'The sky is separated in 12 Sections.
        In each Section is exactly one object out of Planet X, Galaxy, Dwarf Planet, Moon, Asteroid or Empty Space.
        The Players take turns and gather Information or publishing Theories on the different Sections.
        The Game ends when one Person locates Planet X and the surrounding Sections correctly.
        In the end Players are rewarded by correctly published Sections. ';

    public static function query(): Builder
    {
        return parent::query()->where('logic_identifier', self::$logic_identifier);
    }

    public function getCurrentBoard(): PlanetXBoard
    {
        return PlanetXBoard::fromArray($this->currentPayloadAttribute('board'));
    }

    public function getCurrentConference(): PlanetXConferences
    {
        return PlanetXConferences::fromArray($this->currentPayloadAttribute('conference'));
    }

    public function getCurrentNightSkyIndex(): int
    {
        return $this->players->map(fn (Player $player) => $this->getPlayerTimeFor($player))->min() % 12;
    }

    public function isInCurrentNightSky(int $index): bool
    {
        $current = $this->getCurrentNightSkyIndex();

        $allCurrent = array_map(fn ($sector) => $sector % 12, range($current, $current + 5));

        return in_array($index, $allCurrent);
    }

    public function gradientDegree(): int
    {
        return match ($this->getCurrentNightSkyIndex()) {
            0 => 90,
            1 => 40,
            2 => 20,
            3 => 0,
            4 => 340,
            5 => 320,
            6 => 270,
            7 => 220,
            8 => 200,
            9 => 180,
            10 => 160,
            11 => 150,
        };
    }

    public function getAuthenticatedPlayerBoard(): PlanetXBoard
    {
        $move = $this->authenticatedCurrentMove;

        if ($move === null) {
            $move = $this->authenticatedPlayer->moves()->create([
                'round_id' => $this->currentRound->id,
                'user_id' => $this->authenticatedPlayer->user_id,
            ]);
            $this->refresh();
        }

        $rawBoard = $move->getPayloadWithKey('board');

        if ($rawBoard === null) {
            return $this->setAuthenticatedPlayerBoard(PlanetXBoard::playerBoard());
        }

        return PlanetXBoard::fromArray($rawBoard);
    }

    public function setAuthenticatedPlayerBoard(PlanetXBoard $board): PlanetXBoard
    {
        $this->authenticatedCurrentMove->setPayloadWithKey('board', $board->toArray());

        return $board;
    }

    public function getAuthenticatedPlayerConference(): PlanetXConferences
    {
        $move = $this->authenticatedCurrentMove;

        if ($move === null) {
            $move = $this->authenticatedPlayer->moves()->create([
                'round_id' => $this->currentRound->id,
                'user_id' => $this->authenticatedPlayer->user_id,
            ]);
            $this->refresh();
        }

        $rawConference = $move->getPayloadWithKey('conference');

        if ($rawConference === null) {
            return $this->setAuthenticatedPlayerConference(PlanetXConferences::fromArray([]));
        }

        return PlanetXConferences::fromArray($rawConference);
    }

    public function setAuthenticatedPlayerConference(PlanetXConferences $conference): PlanetXConferences
    {
        $this->authenticatedCurrentMove->setPayloadWithKey('conference', $conference->toArray());

        return $conference;
    }

    /**
     * @param  array<PlanetXRule>  $rules
     * @return array<PlanetXRule>
     */
    public function setAuthenticatedPlayerRules(array $rules): array
    {
        $raw = array_map(fn (PlanetXRule $rule) => $rule->toArray(), $rules);
        $this->authenticatedCurrentMove->setPayloadWithKey('rules', $raw);

        return $rules;
    }

    public function getAuthenticatedPlayerRules(): array
    {
        $move = $this->authenticatedCurrentMove;

        if ($move === null) {
            $move = $this->authenticatedPlayer->moves()->create([
                'round_id' => $this->currentRound->id,
                'user_id' => $this->authenticatedPlayer->user_id,
            ]);
            $this->refresh();
        }

        $rules = [];
        foreach ($move->getPayloadWithKey('rules', []) as $key => $rule) {
            $rules[$key] = PlanetXRule::fromArray($rule);
        }

        return $rules;
    }

    public function setAuthenticatedPlayerTime(int $time): int
    {
        $this->authenticatedCurrentMove->setPayloadWithKey('time', $time);

        return $time;
    }

    public function getAuthenticatedPlayerTime(): int
    {
        return $this->getPlayerTimeFor($this->authenticatedPlayer);
    }

    public function getPlayerTimeFor(Player $player): int
    {
        $move = $player->currentMove;

        if ($move === null) {
            $move = $this->authenticatedPlayer->moves()->create([
                'round_id' => $this->currentRound->id,
                'user_id' => $this->authenticatedPlayer->user_id,
            ]);
        }

        return $move->getPayloadWithKey('time', 0);
    }
}
