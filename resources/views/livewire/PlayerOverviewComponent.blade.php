<?php
/* @var App\Models\Game $game */

/* @var App\Models\Player $player */
?>
<div class="absolute w-full flex">

    <div class="pt-3 text-white px-2 ">
        <a href="{{ route('game.settings', ['game' => $game->uuid]) }}">
            <x-icons.cog class="w-6 h-6 text-white hover:text-pink-500"/>
        </a>
    </div>

    @foreach($game->players as $index => $player)
        <div class="flex overflow-hidden justify-between rounded-xl w-full h-7 sm:w-36 m-3 text-white px-2 py-1
               {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
            {{ $player->user->name }}
            <div class="text-sm bg-white opacity-50 px-2 rounded-full">{{ $player->score }}</div>
        </div>
    @endforeach

</div>
