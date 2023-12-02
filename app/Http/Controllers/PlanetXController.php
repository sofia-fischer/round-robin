<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlanetXConferenceRequest;
use App\Http\Requests\PlanetXSurveyRequest;
use App\Http\Requests\PlanetXTargetRequest;
use App\Models\PlanetXGame;
use App\Models\Round;
use App\Queue\Events\PlayerCreated;
use App\Services\PlanetXBoardGenerationService;
use App\Services\PlanetXConferenceGenerationService;
use App\Services\PlanetXRuleGenerationService;
use App\ValueObjects\PlanetXRules\CountInFewSectorsRule;
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
        PlanetXRuleGenerationService       $ruleGenerationService,
    ) {
        if (! $game->authenticatedPlayer) {
            /** @var \App\Models\Player $player */
            $player = $game->players()->create(['user_id' => Auth::id()]);
            $game->refresh();
            event(new PlayerCreated($player));
        }

        if (! $game->currentRound) {
            return $this->round($game, $boardGenerationService, $conferenceGenerationService, $ruleGenerationService);
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
        $game->addAuthenticatedPlayerTime(1);

        return redirect()->route('planet_x.show', ['game' => $game]);
    }

    public function target(PlanetXGame $game, PlanetXTargetRequest $request, PlanetXRuleGenerationService $ruleService)
    {
        $rule = $ruleService->generateInSectorRule($game->getCurrentBoard(), (int) $request->get('target'));
        $game->addAuthenticatedPlayerRule($rule);
        $game->addAuthenticatedPlayerTime(4);

        return redirect()->route('planet_x.show', ['game' => $game]);
    }

    public function round(
        PlanetXGame                        $game,
        PlanetXBoardGenerationService      $boardGenerationService,
        PlanetXConferenceGenerationService $conferenceGenerationService,
        PlanetXRuleGenerationService       $ruleGenerationService,
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

            /** @var Round $round */
            $round = Round::create([
                'game_id' => $game->id,
                'payload' => [
                    'board' => $board->toArray(),
                    'conference' => $conferenceGenerationService->generateRulesForConferences($board)->toArray(),
                ],
            ]);

            foreach ($game->players as $player) {
                $player->moves()->create([
                    'round_id' => $round->id,
                    'user_id' => $player->user_id,
                ]);
                $rules = $ruleGenerationService->generateStartingRules($board, 6);
                $game->setAuthenticatedPlayerRules($rules);
            }

            $game->refresh();
        }

        return redirect()->route('planet_x.show', ['game' => $game]);
    }

    public function survey(PlanetXGame $game, PlanetXSurveyRequest $request, PlanetXRuleGenerationService $ruleService)
    {
        $rule = $ruleService->generateCountInSectorRule(
            $game->getCurrentBoard(),
            $request->getIcon(),
            (int) $request->get('from'),
            (int) $request->get('to'),
        );
        $game->addAuthenticatedPlayerRule($rule);
        $game->addAuthenticatedPlayerTime($rule instanceof CountInFewSectorsRule ? 3 : 4);

        return redirect()->route('planet_x.show', ['game' => $game]);
    }
}
