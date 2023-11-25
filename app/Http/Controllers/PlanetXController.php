<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlanetXConferenceRequest;
use App\Http\Requests\PlanetXTargetRequest;
use App\Models\PlanetXGame;
use App\Models\Round;
use App\Queue\Events\PlayerCreated;
use App\Services\PlanetXBoardGenerationService;
use App\Services\PlanetXConferenceGenerationService;
use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXRules\InSectorRule;
use Illuminate\Support\Facades\Auth;

/**
 * @see \Tests\Feature\PlanetXControllerTest
 */
class PlanetXController
{
    public function show(
        PlanetXGame                        $game,
        PlanetXBoardGenerationService      $boardGenerationService,
        PlanetXConferenceGenerationService $conferenceGenerationService,
    ) {
        if (! $game->authenticatedPlayer) {
            /** @var \App\Models\Player $player */
            $player = $game->players()->create(['user_id' => Auth::id()]);
            $game->refresh();
            event(new PlayerCreated($player));
        }

        if (! $game->currentRound) {
            return $this->round($game, $boardGenerationService, $conferenceGenerationService);
        }

        return view('GamePage', ['game' => $game]);
    }

    public function conference(PlanetXGame $game, PlanetXConferenceRequest $request)
    {
        $playerConference = $game->getAuthenticatedPlayerConference();
        $gameConference = $game->getCurrentConference();

        match ($request->get('conference')) {
            'A' => $playerConference->alpha = $gameConference->alpha,
            'B' => $playerConference->beta = $gameConference->beta,
            'C' => $playerConference->gamma = $gameConference->gamma,
            'D' => $playerConference->delta = $gameConference->delta,
            'E' => $playerConference->epsilon = $gameConference->epsilon,
            'F' => $playerConference->roh = $gameConference->roh,
        };

        $game->setAuthenticatedPlayerConference($playerConference);

        return view('GamePage', ['game' => $game]);
    }

    public function target(PlanetXGame $game, PlanetXTargetRequest $request)
    {
        $index = (int) $request->get('target');
        $realIcon = $game->getCurrentBoard()->getSector($index)->getIcon();
        $visibleIcon = $realIcon === PlanetXIconEnum::PLANET_X ? PlanetXIconEnum::EMPTY_SPACE : $realIcon;
        $rule = new InSectorRule($visibleIcon, $index);

        $rules = $game->getAuthenticatedPlayerRules();
        $rules[] = $rule;
        $game->setAuthenticatedPlayerRules($rules);

        return view('GamePage', ['game' => $game]);
    }

    public function round(
        PlanetXGame                        $game,
        PlanetXBoardGenerationService      $boardGenerationService,
        PlanetXConferenceGenerationService $conferenceGenerationService,
    ) {
        if (! $game->started_at) {
            $game->started_at = now();
        }

        if ($game->currentRound && ! $game->currentRound->completed_at) {
            $game->currentRound->completed_at = now();
        }

        $game->save();
        if (! $game->currentRound) {
            $board = $boardGenerationService->generateBoard();
            $conference = $conferenceGenerationService->generateRulesForConferences($board);

            /** @var Round $round */
            $round = Round::create([
                'game_id' => $game->id,
                'payload' => [
                    'board' => $board->toArray(),
                    'conference' => $conference->toArray(),
                ],
            ]);

            foreach ($game->players as $player) {
                $player->moves()->create([
                    'round_id' => $round->id,
                    'user_id' => $player->user_id,
                ]);
                $rules = $conferenceGenerationService->generateRulesForBoard($board, 6);
                $game->setAuthenticatedPlayerRules($rules);
            }

            $game->refresh();
        }

        return view('GamePage', ['game' => $game]);
    }
}
