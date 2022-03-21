<div class="w-full">
    <?php
    /* @var App\Models\WerewolfGame $game */
    /* @var App\Models\Player $player */
    /* @var App\Models\Move $move */
    ?>

    {{--    Roles    --}}
    <div class="flex flex-wrap mb-8 m-4 text-center justify-evenly text-gray-500">
        @foreach($game->playerRoles->merge($game->extraRoles)->values()->countBy() as $role => $count)
            <div>
                <div class="flex items-center rounded-full border mx-2 mt-2 border-gray-600
                        {{ $game->authenticatedRole === $role ? 'text-white bg-' . $game->authenticatedPlayer->activeColor : ''}}">
                    <div class="w-6 h-6 m-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON[$role] ?? '' }}"/>
                        </svg>
                    </div>
                    <div class="p-2 text-sm">
                        {{ $count }} {{ Str::title($role) }}
                        <div class="text-xs">{{ WerewolfRoleEnum::INFO[$role] ?? '' }}</div>
                    </div>
                </div>
                @if($game->authenticatedRole === $role)
                    <div class="{{ 'text-' . $game->authenticatedPlayer->activeColor }}">
                        You are a {{ $role }}
                    </div>
                @endif

            </div>
        @endforeach
    </div>

    @if(! $game->isStart)
        {{--    Timer    --}}
        <div class="flex mt-4 sm:rounded-t-full overflow-hidden">
            <div class="w-6 h-4 bg-indigo-900 text-white pl-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </div>
            <div class="flex-grow h-4 bg-indigo-900 {{ $game->isNight ? 'timer' : ''}}" id="night-timer"
                style="--duration: {{ WerewolfRoleEnum::NIGHT_DURATION - $game->currentRound->created_at->diffInSeconds(now()->addSeconds(3)) }} ">
                <div class=" w-full h-4 {{ $game->isNight ? 'bg-white' : 'bg-indigo-900'}}"></div>
            </div>
            <div class="w-4 h-4 pt-2 overflow-hidden {{ $game->isDay || $game->isEnd ? 'bg-indigo-900 text-white' : 'bg-white text-indigo-900'}}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-grow h-4 {{ $game->isDay ? 'timer bg-indigo-900' : 'bg-white'}}"
                id="day-timer"
                style="--duration: {{ WerewolfRoleEnum::DAY_DURATION - ($game->currentRound->created_at->diffInSeconds(now()->addSeconds(3)) - WerewolfRoleEnum::NIGHT_DURATION) }} ">
                <div class=" w-full h-4 {{ $game->isEnd ? 'bg-indigo-900' : 'bg-white'}}"></div>
            </div>
            <div class="w-6 h-4 pr-2 {{ $game->isEnd ? 'bg-indigo-900 text-white' : 'bg-white text-indigo-900'}}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </div>

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
    @endif

    {{--    Game Window    --}}
    <div class="flex flex-col content-center w-full text-center mb-4 rounded-b-lg px-4 py-10 bg-gradient-to-b
            {{ $game->isNight ? 'from-blue-900 to-black text-white' : ($game->isDay
                    ? 'from-blue-300 via-pink-200 to-yellow-100 text-indigo-900'
                    : ($game->isStart ? 'bg-white' : 'from-yellow-400 via-red-800 to-black text-white')) }}}">

        {{--    Authenticated Night Move    --}}
        @if($game->isNight)
            <div class="rounded-xl px-2 text-white my-4 flex flex-col justify-center flex-col align-center">
                <form method="POST" action="{{ route('werewolf.move', ['game' => $game->uuid]) }}">
                    @csrf

                    {{--    Display Werewolves    --}}
                    @if($game->authenticatedRole === WerewolfRoleEnum::MASON || $game->authenticatedRole === WerewolfRoleEnum::WEREWOLF)
                        <div class="p-2">Meet the pack</div>
                        <div class="flex justify-evenly text-center">
                            @foreach($game->playerWithRole(WerewolfRoleEnum::WEREWOLF) as $player)
                                <div class="px-2 m-2 rounded-xl h-7 w-36 {{ 'bg-' . $player->activeColor }}">
                                    {{ $player->name }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{--    See Anonymous role for lone wolf    --}}
                    @if($game->authenticatedRole === WerewolfRoleEnum::WEREWOLF && $game->playerWithRole(WerewolfRoleEnum::WEREWOLF)->count() === 1)
                        <div class="p-2">You are a lone wolf... you can look at one anonymous role</div>
                        <div class="flex justify-around align-center rounded-full bg-white bg-opacity-50 p-2 my-2 flex-wrap">
                            @foreach(['one', 'two', 'three'] as $anonymous)
                                <div class="rounded-full h-8 w-8 pt-1 hover:bg-blue-800
                                        {{ $game->authenticatedMovePayloadAttribute('seeAnonymous') === $anonymous ? 'bg-blue-100' : 'bg-blue-800' }}">
                                    <input type="radio"
                                        name="seeAnonymous"
                                        id="seeAnonymous-{{ $anonymous }}"
                                        value="{{ $anonymous }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label for="seeAnonymous-{{ $anonymous }}">?</label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{--    Mason    --}}
                    @if($game->authenticatedRole === WerewolfRoleEnum::MASON)
                        <div class="p-2">You are initiated</div>
                        <div class="flex justify-evenly text-center">
                            @foreach($game->playerWithRole(WerewolfRoleEnum::MASON) as $player)
                                <div class="px-2 m-2 rounded-xl h-7 w-36 {{ 'bg-' . $player->activeColor }}">
                                    {{ $player->name }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{--    Seer    --}}
                    @if($game->authenticatedRole === WerewolfRoleEnum::SEER)
                        <div class="p-2">Feel the aura of one of the other players...</div>
                        <div class="flex justify-around align-center rounded-full bg-white bg-opacity-50 p-2 my-2 flex-wrap">
                            @foreach($game->players as $player)
                                <div class="rounded-full px-2 pt-1 hover:{{ 'bg-'. $player->activeColor }}
                                {{ $game->authenticatedMovePayloadAttribute('see') === $player->id ? 'bg-'. $player->activeColor : 'bg-'. $player->passiveColor }}">
                                    <input type="radio"
                                        name="see"
                                        id="see-{{ $player->id }}"
                                        value="{{ $player->id }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label class="cursor-pointer" for="see-{{ $player->id }}">{{ $player->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{--    Robber    --}}
                    @if($game->authenticatedRole === WerewolfRoleEnum::ROBBER)
                        <div class="p-2">Whose role do you want to steal?</div>
                        <div class="flex justify-around align-center rounded-full bg-white bg-opacity-50 p-2 my-2 flex-wrap">
                            @foreach($game->players as $player)
                                <div class="rounded-full px-2 pt-1 hover:{{ 'bg-'. $player->activeColor }}
                                {{ $game->authenticatedMovePayloadAttribute('steal') === $player->id ? 'bg-'. $player->activeColor : 'bg-'. $player->passiveColor }}">
                                    <input type="radio"
                                        name="steal"
                                        id="steal-{{$player->id }}"
                                        value="{{ $player->id }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label class="cursor-pointer" for="steal-{{ $player->id }}">{{ $player->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{--    Troublemaker    --}}
                    @if($game->authenticatedRole === WerewolfRoleEnum::TROUBLEMAKER)
                        <div class="p-2">Whose roles do you want to switch?</div>
                        <div class="flex justify-around align-center rounded-full bg-white bg-opacity-50 p-2 my-2 flex-wrap">
                            @foreach($game->players as $player)
                                <div class="rounded-full px-2 pt-1 hover:{{ 'bg-'. $player->activeColor }}
                                {{ $game->authenticatedMovePayloadAttribute('switch1') === $player->id ?'bg-' . $player->activeColor : 'bg-' .$player->passiveColor }}">
                                    <input
                                        type="radio"
                                        name="switch1"
                                        id="switch1-{{ $player->id }}"
                                        value="{{ $player->id }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label class="cursor-pointer" for="switch1-{{ $player->id }}">{{ $player->name }}</label>
                                </div>
                            @endforeach
                            @foreach(['one', 'two', 'three'] as $anonymous)
                                <div class="rounded-full h-8 w-8 pt-1  hover:bg-blue-800
                                        {{ $game->authenticatedMovePayloadAttribute('switch1') === $anonymous ? 'bg-blue-800' : 'bg-blue-100'}}">
                                    <input type="radio"
                                        name="switch1"
                                        id="switch1-{{ $anonymous }}"
                                        value="{{ $anonymous }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label class="cursor-pointer" for="switch1-{{ $anonymous }}">?</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-2">and</div>
                        <div class="flex justify-around align-center rounded-full bg-white bg-opacity-50 p-2 my-2 flex-wrap">
                            @foreach($game->players as $player)
                                <div class="rounded-full px-2 pt-1 hover:{{ 'bg-'. $player->activeColor }}
                                {{ $game->authenticatedMovePayloadAttribute('switch2') === $player->id ? 'bg-' .$player->activeColor : 'bg-' .$player->passiveColor }}">
                                    <input type="radio"
                                        name="switch2"
                                        id="switch2-{{ $player->id }}"
                                        value="{{ $player->id }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label class="cursor-pointer" for="switch2-{{ $player->id }}">{{ $player->name }}</label>
                                </div>
                            @endforeach
                            @foreach(['one', 'two', 'three'] as $anonymous)
                                <div class="rounded-full h-8 w-8 pt-1 hover:bg-blue-800
                                        {{ $game->authenticatedMovePayloadAttribute('switch2') === $anonymous ? 'bg-blue-800' : 'bg-blue-100' }}">
                                    <input type="radio"
                                        name="switch2"
                                        id="switch2-{{ $anonymous }}"
                                        value="{{ $anonymous }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label class="cursor-pointer" for="switch2-{{ $anonymous }}">?</label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{--    Drunk    --}}
                    @if($game->authenticatedRole === WerewolfRoleEnum::DRUNK)
                        <div class="p-2">Who are you again?</div>
                        <div class="flex justify-around align-center rounded-full bg-white bg-opacity-50 p-2 my-2 flex-wrap">
                            @foreach(['one', 'two', 'three'] as $anonymous)
                                <div class="rounded-full h-8 w-8 pt-1 hover:bg-blue-800
                                      {{ $game->authenticatedMovePayloadAttribute('drunk') === $anonymous ? 'bg-blue-100' : 'bg-blue-800' }}">
                                    <input type="radio"
                                        name="drunk"
                                        id="drunk-{{ $anonymous }}"
                                        value="{{ $anonymous }}"
                                        class="hidden"
                                        onchange="this.form.submit()">
                                    <label class="cursor-pointer" for="drunk-{{ $anonymous }}">?</label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </form>
            </div>
        @endif

        {{--    Authenticated Day Move    --}}
        @if($game->isDay || $game->isEnd)
            <div class="bg-white rounded-xl px-2 text-indigo-900 bg-opacity-50 my-4 p-2">
                @if($game->authenticatedMovePayloadAttribute('sawName'))
                    You saw the role of
                    <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $game->authenticatedMovePayloadAttribute('sawColor') }}">
                        {{ $game->authenticatedMovePayloadAttribute('sawName') }}
                    </span>
                    who was {{ $game->authenticatedMovePayloadAttribute('sawRole') }}
                @elseif($game->authenticatedMovePayloadAttribute('saw'))
                    You took the role of
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $game->authenticatedMovePayloadAttribute('becameColor') }}">
                        {{ $game->authenticatedMovePayloadAttribute('becameName') }}
                    </span>
                    who was {{ $game->authenticatedMovePayloadAttribute('becameRole') }}
                @elseif($game->authenticatedMovePayloadAttribute('switched1Name'))
                    You switched the roles of
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $game->authenticatedMovePayloadAttribute('switched1Color') }}">
                        {{ $game->authenticatedMovePayloadAttribute('switched1Name') }}
                    </span>
                    and
                        <span class="text-white rounded-full px-2 m-1  {{ 'bg-' . $game->authenticatedMovePayloadAttribute('switched2Color') }}">
                        {{ $game->authenticatedMovePayloadAttribute('switched2Name') }}
                    </span>
                @endif
            </div>
        @endif

        {{--    Voting Input    --}}
        @if($game->isDay)
            <form method="POST" action="{{ route('werewolf.move', ['game' => $game->uuid]) }}">
                @csrf

                <div class="p-2">Who do you vote? The player with the most votes will be killed.</div>
                <div class="flex justify-around align-center rounded-full bg-white bg-opacity-50 p-2 my-2 flex-wrap">
                    @foreach($game->players as $player)
                        <div class="rounded-full px-2 pt-1 hover:{{ 'bg-'. $player->activeColor }}
                        {{ $game->authenticatedMovePayloadAttribute('vote') === $player->id ? 'bg-'. $player->activeColor : 'bg-'. $player->passiveColor }}">
                            <input type="radio"
                                name="vote"
                                id="vote-{{ $player->id }}"
                                value="{{ $player->id }}"
                                class="hidden"
                                onchange="this.form.submit()">
                            <label class="cursor-pointer" for="vote-{{ $player->id }}">{{ $player->name }}</label>
                        </div>
                    @endforeach
                    <div class="rounded-full px-2 pt-1 hover:bg-gray-500
                    {{ $game->authenticatedMovePayloadAttribute('vote') === 'nobody' ? 'bg-gray-500' : 'bg-gray-300' }}">
                        <input type="radio"
                            name="vote"
                            id="vote-nobody"
                            value="nobody"
                            class="hidden"
                            onchange="this.form.submit()">
                        <label class="cursor-pointer" for="vote-nobody">Nobody</label>
                    </div>
                </div>
            </form>
        @endif

        {{--    Game End Results    --}}
        @if($game->isEnd)
            <div class="bg-white rounded-xl px-2 text-indigo-900 bg-opacity-50 my-4 p-2">
                You voted for
                <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $game->authenticatedPlayerVote?->activeColor }}">
                    {{ $game->authenticatedPlayerVote?->name ?? 'Nobody' }}
                </span>
            </div>

            <div class="bg-white rounded-xl px-2 text-indigo-900 bg-opacity-50 my-4 p-2">
                @foreach($game->players as $player)
                    <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $player->activeColor }}">
                        {{ $player->name }} woke up as {{ $game->playerRoles->get($player->id) ?? 'Watcher' }}
                    </span>
                @endforeach
                <br>
                <br>
                @foreach($game->currentRound->moves as $move)
                    @if($move->payloadAttribute('sawName'))
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $move->player->activeColor }}">{{ $move->player->name }}</span>
                        saw the role of
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $move->payloadAttribute('sawColor') }}">
                        {{ $move->payloadAttribute('sawName') }}
                    </span>
                        who was {{ $move->payloadAttribute('sawRole') }}
                    @elseif($move->payloadAttribute('saw'))
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $move->player->activeColor }}">{{ $move->player->name }}</span>
                        took the role of
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $move->payloadAttribute('becameColor') }}">
                        {{ $move->payloadAttribute('becameName') }}
                    </span>
                        who was {{ $move->payloadAttribute('becameRole') }}
                    @elseif($move->payloadAttribute('switched1Name'))
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $move->player->activeColor }}">{{ $move->player->name }}</span>
                        switched the roles of
                        <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $move->payloadAttribute('switched1Color') }}">
                        {{ $move->payloadAttribute('switched1Name') }}
                    </span>
                        and
                        <span class="text-white rounded-full px-2 m-1  {{ 'bg-' . $move->payloadAttribute('switched2Color') }}">
                        {{ $move->payloadAttribute('switched2Name') }}
                    </span>
                    @endif
                @endforeach
                <br>
                <br>
                        The group killed
                <span class="text-white rounded-full px-2 m-1 {{ 'bg-' . $game->currentPayloadAttribute('killedPlayerColor') }}">
                         {{ $game->currentPayloadAttribute('killedPlayerName') }}
                    </span>
                @if($game->currentPayloadAttribute('killedPlayerColor'))
                    <span>who is a {{ $game->currentPayloadAttribute('killedRole') }}</span>
                @endif
                <br>
                <br>
                <span class="font-extrabold text-xl"> {{ match($game->currentPayloadAttribute('win')) {
                    WerewolfRoleEnum::WEREWOLF => 'The night prevailed',
                     WerewolfRoleEnum::TANNER => 'You have underestimated the power of one person',
                     default => 'The good is winning',
                } }}</span> -
                <span class="font-extrabold text-xl">
                    {{ $game->authenticatedPlayerMove?->score ? 'You won' : 'You lost' }}
                </span>
            </div>
        @endif

        @if($game->host_user_id == \Illuminate\Support\Facades\Auth::id())
            <div class="text-white text-center mx-auto">
                <form method="POST" action="{{
                         match (true) {
                            $game->isStart => route('werewolf.round', ['game' => $game]),
                            $game->isNight => route('werewolf.sunrise', ['game' => $game]),
                            $game->isDay => route('werewolf.vote', ['game' => $game]),
                            $game->isEnd => route('werewolf.vote', ['game' => $game]),
                        } }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-orange-500 px-4">
                        {{ match (true) {
                            $game->isStart => 'Start Game',
                            $game->isNight => 'Make Sunrise',
                            $game->isDay => 'Vote',
                            $game->isEnd => 'Restart',
                        } }}
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
