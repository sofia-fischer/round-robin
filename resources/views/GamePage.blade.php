<?php
/* @var App\Models\Game $game */
/* @var App\Models\Player $player */
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $game->logic->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <livewire:player-overview-component :game="$game"></livewire:player-overview-component>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                @if($game->game_logic_id == 1)
                    <livewire:wave-length-component :game="$game"></livewire:wave-length-component>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>