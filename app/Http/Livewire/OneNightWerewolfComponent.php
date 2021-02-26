<?php

namespace App\Http\Livewire;

use App\Jobs\OneNightWerewolfNightJob;
use App\Models\Game;
use App\Models\Player;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerKicked;
use App\Queue\Events\PlayerUpdated;
use App\Support\Enums\WerewolfRoleEnum;
use App\Support\GamePolicies\OneNightWerewolfPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class OneNightWerewolfComponent extends Component
{
    public Game $game;

    public bool $showwerewolf = false;

    public bool $showmason = false;

    public bool $showminion = false;

    public bool $showseer = false;

    public bool $showtroublemaker = false;

    public bool $showrobber = false;

    public bool $showvillager = false;

    public bool $showdrunk = false;

    public bool $showtanner = false;

    public bool $showinsomniac = false;

    public function render()
    {
        $roundInfos = $this->game->currentRound->payload ?? [];

        $step = 'start';

        if ($roundInfos) {
            $step = $roundInfos['state'];
        }

        if ($this->game->currentRound->completed_at ?? false) {
            $step = 'end';
        }

        $playerByRoles = [];
        collect($roundInfos['playerRoles'] ?? [])->map(function ($role, $playerId) use (&$playerByRoles) {
            return $playerByRoles[$role] = [...($playerByRoles[$role] ?? []), $playerId];
        });

        return view('livewire.OneNightWerewolfComponent', [
            'step'           => $step,
            'roles'          => collect($roundInfos['playerRoles'] ?? [])->merge($roundInfos['extraRoles'] ?? [])->values()->countBy()->toArray(),
            'playerRole'     => $roundInfos['playerRoles'][$this->game->authenticatedPlayer->id] ?? 'watcher',
            'newPlayerRole'  => $roundInfos['newPlayerRoles'][$this->game->authenticatedPlayer->id] ?? 'watcher',
            'playerByRoles'  => $playerByRoles,
            'playerRoles'    => $roundInfos['playerRoles'] ?? [],
            'newPlayerRoles' => $roundInfos['newPlayerRoles'] ?? [],
            'extraRoles'     => $roundInfos['extraRoles'] ?? [],
            'players'        => $this->game->players->mapWithKeys(function (Player $player) {
                return [$player->id => $player];
            }),
            'votedPlayerId'  => $this->game ? $this->game->authenticatedPlayerMove->payload['vote'] ?? null : null,
        ]);
    }

    /**
     * @return array
     */
    public function getListeners() : array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameStarted::class           => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class       => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerUpdated::class => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerKicked::class  => 'handlePlayerKick',

            'nightTimeEnd' => 'checkTimeOuts',
            'dayTimeEnd'   => 'checkTimeOuts',
        ];
    }

    public function handlePlayerKick($playerKicked)
    {
        $this->nextRound();
    }

    public function checkTimeOuts()
    {
        if ($this->game->group->host_user_id != Auth::id()) {
            sleep(1);
        }

        if (!$this->game->currentRound) {
            return;
        }

        if ($this->game->currentRound->completed_at) {
            return;
        }

        $step = $this->game->currentRound->payload['state'];
        $gameDuration = $this->game->currentRound->created_at->diffInSeconds(now());

        if ($step == 'night' && ($gameDuration >= WerewolfRoleEnum::NIGHT_DURATION)) {
            OneNightWerewolfPolicy::calculateSunrise($this->game->currentRound);
            event(new GameRoundAction($this->game->id));

            return;
        }

        if ($step == 'day' && ($gameDuration >= (WerewolfRoleEnum::DAY_DURATION + WerewolfRoleEnum::NIGHT_DURATION))) {
            OneNightWerewolfPolicy::calculateResults($this->game->currentRound);
            event(new GameRoundAction($this->game->id));
        }
    }

    public function testEvent()
    {
        Log::info('dipatch OneNightWerewolfNightJob ');
        OneNightWerewolfNightJob::dispatch($this->game->id)->onConnection('redis')->delay(now()->addSeconds(5));
    }

    public function startGame()
    {
        $this->game->start();
    }

    public function performAction($action, $contentId)
    {
        $this->game->roundAction(array_merge($this->game->authenticatedPlayerMove->payload ?? [], [$action => $contentId]));
    }

    public function nextRound()
    {
        $this->game->endRound();
    }

    public function makeDawn()
    {
        OneNightWerewolfPolicy::calculateSunrise($this->game->currentRound);
        event(new GameRoundAction($this->game->id));
    }

    public function makeNight()
    {
        OneNightWerewolfPolicy::calculateResults($this->game->currentRound);
        event(new GameRoundAction($this->game->id));
    }
}
