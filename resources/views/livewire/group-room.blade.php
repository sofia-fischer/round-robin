<?php
/* @var App\Models\Group $group */
?>

<div class="max-w-7xl mt-4 mx-auto">

    <div class="w-full">
        <livewire:player-overview-component :group="$group"></livewire:player-overview-component>
    </div>

    {{--  Games  --}}
    <div class="max-w-2xl mx-auto">
        <div class="flex flex-wrap justify-evenly p-4">
            @foreach($group->games as $game)
                <button
                    class="m-2 font-semibold rounded-lg overflow-hidden bg-gradient-to-r from-indigo-400 to-pink-500 hover:from-pink-500 hover:to-yellow-500"
                    wire:click="joinGame({{ $game->id }})">
                    <div class="p-4 text-white">
                        {{ $game->logic->name }}
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="opacity-50">
                        <path fill="#FFFFFF" fill-opacity="1" d="M0,224L80,202.7C160,181,320,139,480,144C640,149,800,203,960,240C1120,277,1280,299,1360,309.3L1440,320L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
                    </svg>
                    <div class="text-black text-sm bg-white opacity-50 p-4 text-left">
                        <div class="flex mb-4">
                            <div class="h-5 w-5 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                A social guessing game
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="h-5 w-5 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                2 or more
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="h-5 w-5 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                Round {{ $game->rounds()->count() }}
                            </div>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>

        @if( $group->host_user_id == $group->authenticatedPlayer->user_id)
            <div class="flex flex-wrap justify-evenly p-4 text-white">
                @foreach(\App\Models\GameLogic::all() as $gameLogic)
                    <button
                        class="py-2 px-4 m-2 font-semibold rounded-full bg-gradient-to-r from-indigo-400 to-pink-500 hover:from-blue-500 hover:to-green-500"
                        wire:click="startNewGame({{ $gameLogic->id }})">
                        Start a new Game {{ $gameLogic->name }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>
