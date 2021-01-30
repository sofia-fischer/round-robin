<?php
/* @var App\Models\Game $game */
/* @var App\Models\Player $player */
?>

<x-app-layout>
    <div class="max-w-7xl mx-auto mt-4 sm:px-6 lg:px-8">

        <div class="text-center text-lg text-indigo-500 pb-4">
            {{ $game->logic->name }}
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
            @if($game->game_logic_id == 1)
                <livewire:wave-length-component :game="$game"></livewire:wave-length-component>
            @endif
        </div>
        <livewire:player-overview-component :game="$game" :group="$group"></livewire:player-overview-component>
    </div>
</x-app-layout>
