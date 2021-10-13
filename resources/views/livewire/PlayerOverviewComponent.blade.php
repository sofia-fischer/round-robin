<?php
/* @var App\Models\Game $game */

/* @var App\Models\Player $player */
?>
<div class="absolute w-full flex">
    @foreach($game->players as $index => $player)
        <div class="flex overflow-hidden justify-between rounded-xl w-full h-7 sm:w-36 m-3
               {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
            <div class="pt-1 text-white px-2 ">{{ $player->name }}</div>

            <div class="text-white w-6 h-4">
                @if($game->currentRound && $game->currentRound->moves->firstWhere('player_id', $player->id) ?? false)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </div>

            <div class="text-sm bg-white opacity-50 m-1 rounded-full px-2">{{ $player->score }}</div>
        </div>
    @endforeach
</div>
