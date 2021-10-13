<?php
/* @var App\Models\Game $game */
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
                <div
                    class="text-center text-lg text-purple-600 font-semibold relative flex items-center justify-center">
                    <div>
                        {{ $game->logic->name }}
                    </div>

                    <div class="pl-2" id="btn-modal">
                        <svg class="w-6 h-6 text-gray-300 relative" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
