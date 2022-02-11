<?php
/* @var App\Models\Game|\App\Models\WaveLengthGame $game */
/* @var App\Models\Game|\App\Models\JustOneGame $game */

/* @var App\Models\Player $player */
?>

<x-app-layout>
    <div class="max-w-7xl mx-auto mt-4 sm:px-6 lg:px-8 flex">

        <div class="w-full">
            <div class="relative">
                <div style="height: 30px; overflow: hidden;">
                    <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="h-full w-full absolute">
                        <path d="M0.00,150.48 C252.25,-3.45 252.25,-3.45 500.00,150.48 L500.00,150.48 L0.00,150.48 Z"
                            style="stroke: none; fill: #ffffff;"></path>
                    </svg>
                </div>
                <div class="text-center text-lg text-purple-600 font-semibold relative flex items-center justify-center">
                    <div>{{ $game->title }}</div>

                    <div class="px-2" id="btn-modal">
                        <svg class="w-6 h-6 text-gray-300 relative" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg sm:p-4">
                @if(get_class($game) === \App\Models\WaveLengthGame::class)
                    <livewire:wave-length-component :game="$game"></livewire:wave-length-component>
                @elseif($game->logic_identifier ===  App\Support\GameLogics\OneNightWerewolfLogic::class)
                    <livewire:one-night-werewolf-component :game="$game"></livewire:one-night-werewolf-component>
                @elseif(get_class($game) === \App\Models\JustOneGame::class)
                    <livewire:just-one-component :game="$game"></livewire:just-one-component>
                @endif
            </div>

            <div>
                <livewire:player-overview-component :game="$game"></livewire:player-overview-component>
            </div>
        </div>
    </div>

    <style>
        .is-visible {
            visibility: visible;
            pointer-events: auto;
        }
    </style>

    <div class="bg-black opacity-50 w-screen h-screen absolute invisible left-0 top-0" id="overlay"></div>
    <div
        class="bg-white text-small invisible rounded-lg shadow-lg max-w-xl top-32 w-full left-0 right-0 mx-auto p-4 text-center absolute"
        id="modal">
        <h2 class="text-center text-purple-600 pb-2">
            {{ $game->title }}
        </h2>

        <p>
            {{ $game::$description }}
        </p>
    </div>

    <script>
        document.getElementById('btn-modal').addEventListener('click', function () {
            document.getElementById('overlay').classList.add('is-visible');
            document.getElementById('modal').classList.add('is-visible');
        });

        document.getElementById('overlay').addEventListener('click', function () {
            document.getElementById('overlay').classList.remove('is-visible');
            document.getElementById('modal').classList.remove('is-visible');
        });

    </script>
</x-app-layout>
