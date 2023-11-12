<?php


namespace App\Http\Controllers;


use App\Http\Requests\WerewolfMoveCreateRequest;
use App\Models\Move;
use App\Models\PlanetXGame;
use App\Models\Round;
use App\Services\PlanetXBoardGenerationService;
use App\Services\PlanetXConferenceGenerationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PlanetXController
{
    public function move(WerewolfMoveCreateRequest $request, PlanetXGame $game)
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id' => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
            'user_id' => Auth::id(),
        ], [
            'uuid' => Str::uuid(),
        ]);

//        $move->setPayloadAttribute($request->payloadKey(), $request->payloadValue());

        return view('GamePage', ['game' => $game]);
    }

    public function show(
        PlanetXGame                        $game,
        PlanetXBoardGenerationService      $boardGenerationService,
        PlanetXConferenceGenerationService $conferenceGenerationService,
    ) {
        if (! $game->authenticatedPlayer) {
            /** @var \App\Models\Player $player */
            $player = $game->players()->create([
                'user_id' => Auth::id(),
                'game_id' => $game->id,
            ]);
            $game->refresh();
        }

        if (! $game->currentRound) {
            return $this->round($game, $boardGenerationService, $conferenceGenerationService);
        }

        return view('GamePage', ['game' => $game]);
    }

    public function round(
        PlanetXGame                        $game,
        PlanetXBoardGenerationService      $boardGenerationService,
        PlanetXConferenceGenerationService $conferenceGenerationService,
    ) {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        if (! $game->started_at) {
            $game->started_at = now();
            $game->save();
        }

        if ($game->currentRound && ! $game->currentRound->completed_at) {
            $game->currentRound->completed_at = now();
            $game->currentRound->save();
        }

        if (! $game->currentRound) {
            $board = $boardGenerationService->generateBoard();
            $conference = $conferenceGenerationService->generateRulesForConferences($board);

            $round = Round::create([
                'game_id' => $game->id,
                'payload' => [
                    'board' => $board->toArray(),
                    'conference' => $conference->toArray(),
                ],
            ]);

            $game->refresh();
        }

        return view('GamePage', ['game' => $game]);
    }
}
