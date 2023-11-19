<div class="w-full">
    <?php

    use App\ValueObjects\Enums\WerewolfRoleEnum;

    /* @var App\Models\WerewolfGame $game */

    /* @var \App\ValueObjects\WerewolfBoard $board */
    /* @var App\Models\Player $player */
    /* @var App\Models\Move $move */
    ?>

    {{--    Roles    --}}
    <div class="flex flex-wrap mb-8 m-4 text-center justify-evenly text-gray-500">
        @foreach(WerewolfRoleEnum::cases() as $role)
            <div>
                <div class="rounded-full border mx-2 mt-2 px-2 p-1 border-gray-600
                    {{ $board->see($game->authenticatedPlayer) === $role ? 'text-white ' . $game->authenticatedPlayer->color()->background() : ''}}">
                    {{ $board->countRole($role) }} {{ Str::title($role->value) }}
                    <div class="text-xs">{{ __('werewolf.info.' . $role->value) }}</div>
                </div>
                @if($board->see($game->authenticatedPlayer) === $role)
                    <div class="text-xs {{ 'text-' . $game->authenticatedPlayer->color()->baseColor() }}">
                        You are a {{ $role->value }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if($game->started_at)
        {{--    Timer    --}}
        <div class="flex mt-4 sm:rounded-t-full overflow-hidden">
            <x-icons.moon class="bg-indigo-900 h-4 text-white pl-2"/>
            <div class="flex-grow h-4 bg-indigo-900 {{ $board->isNight() ? 'timer' : ''}}" id="night-timer"
                style="--duration: {{ WerewolfGame::NIGHT_DURATION - $game->currentRound->created_at->diffInSeconds(now()->addSeconds(3)) }} ">
                <div class=" w-full h-4 {{ $board->isNight() ? 'bg-white' : 'bg-indigo-900'}}"></div>
            </div>
            <div class="w-4 h-4 pt-2 overflow-hidden {{ !$board->isNight() ? 'bg-indigo-900 text-white' : 'bg-white text-indigo-900'}}">
                <x-icons.sun class="h-4"/>
            </div>
            <div class="flex-grow h-4 {{ $board->isDay() ? 'timer bg-indigo-900' : 'bg-white'}}"
                id="day-timer"
                style="--duration: {{ WerewolfGame::DAY_DURATION - ($game->currentRound->created_at->diffInSeconds(now()->addSeconds(3)) - WerewolfGame::NIGHT_DURATION) }} ">
                <div class=" w-full h-4 {{ $board->isEnd() ? 'bg-indigo-900' : 'bg-white'}}"></div>
            </div>
            <x-icons.sun class="w-6 h-4 pr-2 {{ $board->isEnd() ? 'bg-indigo-900 text-white' : 'bg-white text-indigo-900'}}"/>

            <style>
                .timer div {
                    animation: roundtime calc(var(--duration) * 1s) steps(var(--duration)) forwards;
                    transform-origin: right center;
                }

                .timer div {
                    animation: roundtime calc(var(--duration) * 1s) linear forwards;
                }

                @keyframes roundtime {
                    to {
                        /* More performant than `width` */
                        transform: scaleX(0);
                    }
                }
            </style>
        </div>

        {{--    Game Window    --}}
        <div class="flex flex-col content-center w-full text-center mb-4 rounded-b-lg px-4 py-10 bg-gradient-to-b {{ $board->getState()->gradient() }}">

            {{--    Players    --}}
            @foreach($game->players as $player)
                <div class="px-2 m-2 rounded-xl h-7 w-36 {{ $player->color()->background() }}">
                    <div>
                        {{ $game->authenticatedPlayer->id === $player->id ? 'You' : $player->name }}
                    </div>

                    @if($board->canSee($game->authenticatedPlayer->id, $player->id) || $board->isEnd())
                        <div class="text-xs">
                            {{ $board->see($player->id) ?? 'Watcher' }}
                        </div>
                    @endif

                    @if($board->isNight() && $board->canMakeSeeMove($game->authenticatedPlayer, $player->id))
                        <form action="{{ route('werewolf.move', ['game' => $game->id]) }}" method="POST">
                            <input type="radio"
                                name="see"
                                id="see-{{ $player->id }}"
                                value="{{ $player->id }}"
                                class="hidden"
                                onchange="this.form.submit()">
                            <label class="cursor-pointer" for="see-{{ $player->id }}">See</label>
                        </form>
                    @endif

                    @if($board->isDay())
                        <form action="{{ route('werewolf.vote', ['game' => $game->id]) }}" method="POST">
                            <input type="radio"
                                name="vote"
                                id="vote-{{ $player->id }}"
                                value="{{ $player->id }}"
                                class="hidden"
                                onchange="this.form.submit()">
                            <label class="cursor-pointer" for="vote-{{ $player->id }}">Kill</label>
                        </form>
                    @endif

                    @if($board->isEnd())
                        <div class="text-xs">
                            Voted {{ $game->voted($player)?->name ?? 'Nobody' }}
                        </div>
                    @endif
                </div>
            @endforeach

            {{--    Game End Results    --}}
            @if($board->isEnd())
                <div class="bg-white rounded-xl px-2 text-indigo-900 bg-opacity-50 my-4 p-2">
                    The group killed
                    <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $game->currentPayloadAttribute('killedPlayerColor') }}">
                         {{ $game->currentPayloadAttribute('killedPlayerName') }}
                    </span>
                    <br class="mb-4">

                    <span class="font-extrabold text-xl"> {{ match($game->currentPayloadAttribute('win')) {
                    WerewolfGame::WEREWOLF => 'The night prevailed',
                     WerewolfGame::TANNER => 'You have underestimated the power of one person',
                     default => 'The good is winning',
                } }}</span> -
                    <span class="font-extrabold text-xl">
                    {{ $game->authenticatedPlayerMove?->score ? 'You won' : 'You lost' }}
                </span>
                </div>
            @endif
            @endif

            {{--    Game Controls    --}}
            @if(!$game->started_at)
                <form method="POST" action="{{ route('werewolf.round', ['game' => $game]) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-orange-500 px-4"> Start Game</button>
                </form>
            @endif
            @if(($game->host_user_id === $game->authenticatedPlayer->id) && $game->started_at)
                <div class="text-white text-center mx-auto">
                    <form method="POST" action="{{ route('werewolf.sunrise', ['game' => $game]) }}"> @csrf
                        <button type="submit" class="rounded-lg bg-orange-500 px-4">Make Sunrise</button>
                    </form>
                    <form method="POST" action="{{ route('werewolf.end', ['game' => $game]) }}"> @csrf
                        <button type="submit" class="rounded-lg bg-orange-500 px-4">Vote</button>
                    </form>
                    <form method="POST" action="{{ route('werewolf.round', ['game' => $game]) }}"> @csrf
                        <button type="submit" class="rounded-lg bg-orange-500 px-4">Start next round</button>
                    </form>
                </div>
            @endif
        </div>
</div>
