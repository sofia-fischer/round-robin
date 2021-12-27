<?php
/* @var \Illuminate\Support\Collection $games */

/* @var \Illuminate\Support\Collection $waveLengthGames */
/* @var \App\Models\Game $game */
?>

<x-app-layout>
    <div class="max-w-2xl mx-auto flex flex-wrap justify-evenly p-">

        <div class="bg-gradient-to-r from-indigo-400 to-pink-500 rounded-xl text-white overflow-hidden my-4">
            <div class="m-4">
                <h1 class="text-2xl font-semibold ">Wavelength</h1>
                <div class="flex md:flex-row sm:flex-col content-center">
                    <div class="p-2">{{ \App\Models\WaveLengthGame::$description }}</div>
                    <div>
                        @if( \Illuminate\Support\Facades\Auth::user()->email)
                            <form action="{{ route('game.create') }}" method="POST">
                                @csrf
                                <button name="logic"
                                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400"
                                    value="{{ \App\Models\WaveLengthGame::$logic_identifier }}">
                                    Start
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($waveLengthGames->isNotEmpty())
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="opacity-50">
                    <path fill="#FFFFFF" fill-opacity="1"
                        d="M0,224L80,202.7C160,181,320,139,480,144C640,149,800,203,960,240C1120,277,1280,299,1360,309.3L1440,320L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
                </svg>

                <div>
                    @foreach($waveLengthGames as $game)
                        <a href="{{ route('game.show', ['game' => 1]) }}">
                            <button class="font-semibold overflow-hidden w-full hover:bg-pink-500">
                                <div class="flex text-black text-sm bg-white opacity-50 p-4 text-left w-full justify-between">
                                    <div class="flex">
                                        <div class="h-5 w-5 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div> {{ $game->players_count }} Palyer</div>
                                    </div>
                                    <div class="flex">
                                        <div class="h-5 w-5 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div> Round {{ $game->rounds_count }}</div>
                                    </div>
                                </div>
                            </button>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-gradient-to-r from-indigo-400 to-pink-500 rounded-xl text-white overflow-hidden my-4">
            <div class="m-4">
                <h1 class="text-2xl font-semibold ">Werewolf BETA</h1>
                <div class="flex md:flex-row sm:flex-col content-center">
                    <div class="p-2">{{ \App\Support\GameLogics\OneNightWerewolfLogic::description() }}</div>
                    <div>
                        @if( \Illuminate\Support\Facades\Auth::user()->email)
                            <form action="{{ route('game.create') }}" method="POST">
                                @csrf
                                <button name="logic"
                                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400"
                                    value="{{ \App\Support\GameLogics\OneNightWerewolfLogic::class }}">
                                    Start
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($werewolfGames->isNotEmpty())
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="opacity-50">
                    <path fill="#FFFFFF" fill-opacity="1"
                        d="M0,224L80,202.7C160,181,320,139,480,144C640,149,800,203,960,240C1120,277,1280,299,1360,309.3L1440,320L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
                </svg>

                <div>
                    @foreach($werewolfGames as $game)
                        <a href="{{ route('game.show', ['game' => 1]) }}">
                            <button class="font-semibold overflow-hidden w-full hover:bg-pink-500">
                                <div class="flex text-black text-sm bg-white opacity-50 p-4 text-left w-full justify-between">
                                    <div class="flex">
                                        <div class="h-5 w-5 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div> {{ $game->players_count }} Palyer</div>
                                    </div>
                                    <div class="flex">
                                        <div class="h-5 w-5 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div> Round {{ $game->rounds_count }}</div>
                                    </div>
                                </div>
                            </button>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-gradient-to-r from-indigo-400 to-pink-500 rounded-xl text-white overflow-hidden my-4">
            <div class="m-4">
                <h1 class="text-2xl font-semibold ">Just One</h1>
                <div class="flex md:flex-row sm:flex-col content-center">
                    <div class="p-2">{{ \App\Support\GameLogics\JustOneLogic::description() }}</div>
                    <div>
                        @if( \Illuminate\Support\Facades\Auth::user()->email)
                            <form action="{{ route('game.create') }}" method="POST">
                                @csrf
                                <button name="logic"
                                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400"
                                    value="{{ \App\Support\GameLogics\JustOneLogic::class }}">
                                    Start
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($justOneGames->isNotEmpty())
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="opacity-50">
                    <path fill="#FFFFFF" fill-opacity="1"
                        d="M0,224L80,202.7C160,181,320,139,480,144C640,149,800,203,960,240C1120,277,1280,299,1360,309.3L1440,320L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
                </svg>

                <div>
                    @foreach($justOneGames as $game)
                        <a href="{{ route('game.show', ['game' => 1]) }}">
                            <button class="font-semibold overflow-hidden w-full hover:bg-pink-500">
                                <div class="flex text-black text-sm bg-white opacity-50 p-4 text-left w-full justify-between">
                                    <div class="flex">
                                        <div class="h-5 w-5 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div> {{ $game->players_count }} Palyer</div>
                                    </div>
                                    <div class="flex">
                                        <div class="h-5 w-5 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div> Round {{ $game->rounds_count }}</div>
                                    </div>
                                </div>
                            </button>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
