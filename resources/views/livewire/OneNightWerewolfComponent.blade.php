<div class="w-full">
    <?php
    /* @var App\Models\Game $game */
    /* @var App\Models\Player $player */
    ?>

    {{--    Roles    --}}
    <div class="flex flex-wrap mb-8 m-4 text-center justify-evenly text-gray-500">
        @foreach($roles as $role => $count)
            <div class="flex items-center rounded-full border border-gray-600 m-2"
                 wire:click="$toggle('show{{ $role }}')">
                <div class="w-6 h-6 m-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON[$role] ?? '' }}"/>
                    </svg>
                </div>
                <div class="text-xs p-1">{{ $count }}</div>
                <div class="p-2 text-sm {{ ! ${'show'.$role} ? 'hidden' : 'block' }}">
                    {{ Str::title($role) }}
                    <div class="text-xs">{{ WerewolfRoleEnum::INFO[$role] ?? '' }}</div>
                </div>
            </div>
        @endforeach
    </div>

    @if($step != 'start')
        {{--    Timer    --}}
        <div class="flex mt-4 sm:rounded-t-full overflow-hidden">
            <div class="w-6 h-4 {{ $step == 'start' ? 'bg-white text-indigo-900' : 'bg-indigo-900 text-white'}} pl-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </div>
            <div class="flex-grow h-4 bg-indigo-900 {{ $step == 'night' ? 'timer' : ''}}"
                 id="night-timer"
                 style="--duration: {{ WerewolfRoleEnum::NIGHT_DURATION - $game->currentRound->created_at->diffInSeconds(now()) }} ">
                <div class=" w-full h-4 {{ $step == 'night' ? 'bg-white' : 'bg-indigo-900'}}"></div>
            </div>
            <div class="w-4 h-4 pt-2 overflow-hidden {{ $step == 'day' || $step == 'end' ? 'bg-indigo-900 text-white' : 'bg-white text-indigo-900'}}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-grow h-4 {{ $step == 'day' ? 'timer bg-indigo-900' : 'bg-white'}}"
                 id="day-timer"
                 style="--duration: {{ WerewolfRoleEnum::DAY_DURATION - ($game->currentRound->created_at->diffInSeconds(now()) - WerewolfRoleEnum::NIGHT_DURATION) }} ">
                <div class=" w-full h-4 {{ $step == 'end' ? 'bg-indigo-900' : 'bg-white'}}"></div>
            </div>
            <div class="w-6 h-4 pr-2 {{ $step == 'end' ? 'bg-indigo-900 text-white' : 'bg-white text-indigo-900'}}">
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
                    {{ $step == 'night' ? 'from-blue-900 to-black text-white' : ($step == 'day'
                            ? 'from-blue-300 via-pink-200 to-yellow-100 text-indigo-900'
                            : ($step == 'start' ? 'bg-white' : 'from-yellow-400 via-red-800 to-black text-white')) }}}">

        {{--    Authenticated Role    --}}
        @if($step != 'start')
            <div class="flex items-center w-full justify-between text-center text-indigo-900 bg-white bg-opacity-50 rounded-full px-2 my-4">
                <div class="font-semibold px-2"> You are a {{ Str::title($playerRole) }}</div>
                <div>
                    <div class=" px-2">Who {{ WerewolfRoleEnum::INFO[$playerRole] ?? '' }}</div>
                    <div class="text-xs px-2">You win {{ WerewolfRoleEnum::WIN[$playerRole] ?? '' }}</div>
                </div>
                <div class="mr-2 w-8 h-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON[$playerRole] ?? '' }}"/>
                    </svg>
                </div>
            </div>
        @endif

        {{--    Authenticated Night Move    --}}
        @if($step == 'night')
            <div class="bg-white rounded-xl px-2 text-indigo-900 bg-opacity-50 my-4">
                @switch($playerRole)
                    @case(WerewolfRoleEnum::WEREWOLF)
                    <div class="flex flex-wrap justify-evenly">
                        @if(count($playerByRoles[WerewolfRoleEnum::WEREWOLF]) > 1)
                            <div class="p-2 max-w-sm">Meet the pack</div>
                            <div class="flex justify-evenly text-center text-white">
                                @foreach($game->players->find($playerByRoles[WerewolfRoleEnum::WEREWOLF]) as $player)
                                    <div class="px-2 m-2 rounded-xl h-7 w-36 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
                                        {{ $player->name }}
                                    </div>
                                @endforeach
                            </div>
                        @else()
                            <div class="p-2 max-w-sm">You are a lone wolf... you can look at one anonymous role</div>
                            <div class="p-2 flex">
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2 {{ ($game->authenticatedPlayerMove->payload['seeAnonymous'] ?? 0) == 1 ? 'shadow-white' : ''}}"
                                    wire:click="performAction('seeAnonymous', 1)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2  {{ ($game->authenticatedPlayerMove->payload['seeAnonymous'] ?? 0) == 2 ? 'shadow-white' : ''}}"
                                    wire:click="performAction('seeAnonymous', 2)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2  {{ ($game->authenticatedPlayerMove->payload['seeAnonymous'] ?? 0) == 3 ? 'shadow-white' : ''}}"
                                    wire:click="performAction('seeAnonymous', 3)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                    @break
                    @case(WerewolfRoleEnum::MINION)
                    <div class="flex flex-wrap justify-evenly">
                        <div class="p-2 max-w-sm">Meet the pack</div>
                        <div class="flex justify-evenly text-center text-white">
                            @foreach($game->players->find($playerByRoles[WerewolfRoleEnum::WEREWOLF]) as $player)
                                <div class="px-2 m-2 rounded-xl h-7 w-36 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
                                    {{ $player->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @break
                    @case(WerewolfRoleEnum::MASON)
                    <div class="flex flex-wrap justify-evenly">
                        <div class="p-2 max-w-sm">You are initiated</div>
                        <div class="flex justify-evenly text-center text-white">
                            @foreach($game->players->find($playerByRoles[WerewolfRoleEnum::MASON] ?? []) as $player)
                                <div class="px-2 m-2 rounded-xl h-7 w-36 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
                                    {{ $player->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @break
                    @case(WerewolfRoleEnum::SEER)
                    <div class="flex flex-wrap justify-evenly">
                        <div class="p-2 max-w-sm">Feel the aura of one of the other players.</div>
                        <div class="flex flex-wrap justify-evenly">
                            @foreach($game->players as $player)
                                <div class="text-center text-white rounded-xl h-7 w-36 m-2 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}
                                {{ ($game->authenticatedPlayerMove->payload['see'] ?? 0) == $player->id ? 'shadow-white' : ''}}"
                                     wire:click="performAction('see', {{ $player->id }})">
                                    {{ $player->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @break
                    @case(WerewolfRoleEnum::ROBBER)
                    <div class="flex flex-wrap justify-evenly">
                        <div class="p-2 max-w-sm">Whose role to steal?</div>
                        <div class="p-2 flex">
                            @foreach($extraRoles as $index => $role)
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2 {{ ($game->authenticatedPlayerMove->payload['anonymous'] ?? 0) == 1 ? 'shadow-white' : ''}}"
                                    wire:click="performAction('anonymous', 1)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2  {{ ($game->authenticatedPlayerMove->payload['anonymous'] ?? 0) == 2 ? 'shadow-white' : ''}}"
                                    wire:click="performAction('anonymous', 2)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2  {{ ($game->authenticatedPlayerMove->payload['anonymous'] ?? 0) == 3 ? 'shadow-white' : ''}}"
                                    wire:click="performAction('anonymous', 3)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @break
                    @case(WerewolfRoleEnum::TROUBLEMAKER)
                    <div class="flex flex-wrap justify-evenly">
                        <div class="p-2 max-w-sm">Time for chaos... who should switch their roles?</div>
                        <div class="flex justify-evenly flex-wrap text-center text-white">
                            @foreach($game->players as $player)
                                <div
                                    class="px-2 m-2 rounded-xl h-7 w-36 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}
                                    {{ ($game->authenticatedPlayerMove->payload['switch1'] ?? 0) == $player->id ? 'shadow-white' : ''}}"
                                    wire:click="performAction('switch1', {{ $player->id }})">
                                    {{ $player->name }}
                                </div>
                            @endforeach
                            @foreach($extraRoles as $index => $role)
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2 {{ ($game->authenticatedPlayerMove->payload['switch1'] ?? 0) == ('anonymous-' . ($index + 1)) ? 'shadow-white' : ''}}"
                                    wire:click="performAction('switch1', '{{ 'anonymous-' . ($index + 1) }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                        <div>and</div>
                        <div class="flex justify-evenly flex-wrap text-center text-white">
                            @foreach($game->players as $player)
                                <div
                                    class="px-2 m-2 rounded-xl h-7 w-36 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}
                                    {{ ($game->authenticatedPlayerMove->payload['switch2'] ?? 0) == $player->id ? 'shadow-white' : ''}}"
                                    wire:click="performAction('switch2', {{ $player->id }})">
                                    {{ $player->name }}
                                </div>
                            @endforeach
                            @foreach($extraRoles as $index => $role)
                                <div
                                    class="rounded-full h-8 w-8 bg-gray-500 text-white m-2 {{ ($game->authenticatedPlayerMove->payload['switch2'] ?? 0) == ('anonymous-' . ($index + 1)) ? 'shadow-white' : ''}}"
                                    wire:click="performAction('switch2', '{{ 'anonymous-' . ($index + 1) }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @break
                    @case(WerewolfRoleEnum::DRUNK)
                    <div class="flex flex-wrap justify-evenly">
                        <div class="p-2 max-w-sm">Who where you again?</div>
                        <div class="p-2 flex">
                            <div
                                class="rounded-full h-8 w-8 bg-gray-500 text-white m-2 {{ ($game->authenticatedPlayerMove->payload['anonymous'] ?? 0) == 1 ? 'shadow-white' : ''}}"
                                wire:click="performAction('anonymous', 1)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                </svg>
                            </div>
                            <div
                                class="rounded-full h-8 w-8 bg-gray-500 text-white m-2  {{ ($game->authenticatedPlayerMove->payload['anonymous'] ?? 0) == 2 ? 'shadow-white' : ''}}"
                                wire:click="performAction('anonymous', 2)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                </svg>
                            </div>
                            <div
                                class="rounded-full h-8 w-8 bg-gray-500 text-white m-2  {{ ($game->authenticatedPlayerMove->payload['anonymous'] ?? 0) == 3 ? 'shadow-white' : ''}}"
                                wire:click="performAction('anonymous', 3)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON['anonymous'] }}"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @break
                @endswitch
            </div>
        @endif

        {{--    Authenticated Day Move    --}}
        @if($step == 'day' || $step == 'end')
            <div class="bg-white rounded-full px-2 text-indigo-900 bg-opacity-50 my-4">
                <div class="flex flex-wrap justify-evenly items-center">
                    @if($game->authenticatedPlayerMove->payload['sawAnonymous'] ?? false)
                        <div class="p-2">
                            You saw the anonymous role number
                            {{ $game->authenticatedPlayerMove->payload['seeAnonymous'] ?? 0 }}
                            <div class="text-xs">
                                {{ $game->currentRound->moves()->where('created_at', '<', $game->authenticatedPlayerMove->created_at)->count() + 1 }}
                                . Action
                            </div>
                        </div>
                        <div class="w-6 h-6 m-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="{{ WerewolfRoleEnum::ICON[$game->authenticatedPlayerMove->payload['sawAnonymous']] ?? '' }}"/>
                            </svg>
                        </div>
                    @endif()

                    @if($game->authenticatedPlayerMove->payload['saw'] ?? false)
                        <div>
                            You saw
                            <div class="text-xs">
                                {{ $game->currentRound->moves()->where('created_at', '<', $game->authenticatedPlayerMove->created_at)->count() + 1 }}
                                . Action
                            </div>
                        </div>
                        <div class="flex overflow-hidden justify-center items-center text-white rounded-xl h-7 w-36 m-2
                                 {{ 'bg-' . $players[$game->authenticatedPlayerMove->payload['see']]->activeColor ?? 'pink-500' }}">
                            {{ $players[$game->authenticatedPlayerMove->payload['see']]->name ?? 'Nobody' }}
                            <div class="w-6 h-6 pl-2 pt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="{{ WerewolfRoleEnum::ICON[$game->authenticatedPlayerMove->payload['saw']] ?? '' }}"/>
                                </svg>
                            </div>
                        </div>
                    @endif()

                    @if($game->authenticatedPlayerMove->payload['anonymous'] ?? false)
                        <div class="p-2">
                            You became the anonymous role
                            number {{ $game->authenticatedPlayerMove->payload['anonymous'] ?? 0 }}.
                            Now your role is
                            <div class="text-xs">
                                {{ $game->currentRound->moves()->where('created_at', '<', $game->authenticatedPlayerMove->created_at)->count() + 1 }}
                                . Action
                            </div>
                        </div>
                        @if($playerRole == WerewolfRoleEnum::DRUNK)
                            <div>something else...</div>
                        @else
                            <div class="w-6 h-6 m-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ WerewolfRoleEnum::ICON[$newPlayerRole] ?? '' }}"/>
                                </svg>
                            </div>
                        @endif
                    @endif()

                    @if($game->authenticatedPlayerMove->payload['switch1'] ?? false && $game->authenticatedPlayerMove->payload['switch2'] ?? false)
                        <div class="p-2">
                            You switched
                            <div class="text-xs">
                                {{ $game->currentRound->moves()->where('created_at', '<', $game->authenticatedPlayerMove->created_at)->count() + 1 }}
                                . Action
                            </div>
                        </div>
                        @if(Str::startsWith($game->authenticatedPlayerMove->payload['switch1'], 'anonymous-'))
                            <div class="rounded-full h-8 w-8 bg-gray-500 text-white m-2 pt-1">
                                {{ Str::replaceFirst( 'anonymous-', '', $game->authenticatedPlayerMove->payload['switch1']) }}
                            </div>
                        @else
                            <div class="text-white rounded-xl h-7 w-36 m-2 {{ 'bg-' . $players[$game->authenticatedPlayerMove->payload['switch1']]->activeColor ?? 'pink-500' }}">
                                {{ $players[$game->authenticatedPlayerMove->payload['switch1']]->name }}
                            </div>
                        @endif
                        <div>
                            and
                        </div>
                        @if(Str::startsWith($game->authenticatedPlayerMove->payload['switch2'], 'anonymous-'))
                            <div class="rounded-full h-8 w-8 bg-gray-500 text-white m-2 pt-1">
                                {{ Str::replaceFirst( 'anonymous-', '', $game->authenticatedPlayerMove->payload['switch2']) }}
                            </div>
                        @else
                            <div class="text-white rounded-xl h-7 w-36 m-2 {{ 'bg-' . $players[$game->authenticatedPlayerMove->payload['switch2']]->activeColor ?? 'pink-500' }}">
                                {{ $players[$game->authenticatedPlayerMove->payload['switch2']]->name }}
                            </div>
                        @endif
                    @endif()
                </div>
            </div>
        @endif

        {{--    Voting Input    --}}
        @if($step == 'day')
            <div class="flex flex-wrap justify-evenly items-center bg-white rounded-xl text-indigo-900 bg-opacity-50 my-4 p-2">
                <div>Who do you vote? The player with the most votes will be killed.</div>
                @foreach($game->players as $player)
                    <div
                        class="text-center text-white rounded-xl h-7 w-36 m-2 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}
                        {{ ($game->authenticatedPlayerMove->payload['vote'] ?? 0) == $player->id ? 'shadow-white' : ''}}"
                        wire:click="performAction('vote', {{ $player->id }})">
                        {{ $player->name }}
                    </div>
                @endforeach
                <div class="text-center text-white rounded-xl h-7 w-36 bg-indigo-700
                        {{ ($game->authenticatedPlayerMove->payload['vote'] ?? 0) == 0 ? 'shadow-white' : ''}}"
                     wire:click="performAction('vote', null)">
                    Nobody
                </div>
            </div>
        @endif

        {{--    Game End Results    --}}
        @if($step == 'end')
            <div class="flex flex-wrap justify-evenly items-center bg-white rounded-full px-2 text-indigo-900 bg-opacity-50 my-4">
                <div class="p-2">You voted</div>
                <div class="flex overflow-hidden justify-center  text-white rounded-xl h-7 w-36 m-2
                            {{ $votedPlayerId ? 'bg-' . $players[$votedPlayerId]->activeColor : 'bg-gray-500' }}">
                    {{ $votedPlayerId ? $players[$votedPlayerId]->name : 'Nobody'}}
                </div>
            </div>

            <div>
                {{ ($game->currentRound->payload['win'] ?? null) == WerewolfRoleEnum::WEREWOLF
                    ? 'The night prevailed'
                    : (($game->currentRound->payload['win'] ?? null) == WerewolfRoleEnum::TANNER
                        ? 'You have underestimated the power of one person'
                        : 'The good is winning')  }}
            </div>
            <div>{{ $game->authenticatedPlayerMove->score ?? false ? 'You won' : 'You lost' }}</div>

            <div class="flex flex-wrap justify-evenly text-center text-white pt-16">
                @foreach($game->players as $player)
                    <div class="flex overflow-hidden justify-between items-center rounded-xl h-7 w-36 m-2
                        {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
                        <div class="pt-1 text-white px-2 flex-grow text-left">{{ $player->name }}</div>
                        <div class="w-6 h-6 m-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="{{ WerewolfRoleEnum::ICON[$game->currentRound->payload['newPlayerRoles'][$player->id] ?? null] ?? '' }}"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
                @foreach($extraRoles as $index => $role)
                    <div class="flex overflow-hidden justify-between items-center rounded-xl h-7 w-36 m-2 bg-gray-500 text-white">
                        <div class="pt-1 text-white px-2 flex-grow text-left">{{ $index + 1 }}</div>
                        <div class="w-6 h-6 m-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="{{ WerewolfRoleEnum::ICON[$role] ?? '' }}"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($game->group->host_user_id == \Illuminate\Support\Facades\Auth::id())
            <div class="text-white text-center mx-auto">
                @switch($step)
                    @case('start')
                    <button wire:click="startGame" class="rounded-lg bg-gradient-to-br from-purple-600 to-orange-400 text-white  px-4">
                        Start Game
                    </button>@break
                    @case('night')
                    <button wire:click="makeDawn" class="rounded-lg bg-green-500 px-4">Make Day</button>@break
                    @case('day')
                    <button wire:click="makeNight" class="rounded-lg bg-green-500 px-4">End Game</button>@break
                    @case('end')
                    <button wire:click="nextRound" class="rounded-lg bg-green-500 px-4">Restart</button>@break
                @endswitch
            </div>
        @endif
    </div>

    <style>
        .shadow-white {
            -webkit-box-shadow: 0px 0px 20px 10px rgba(255, 255, 255, 1);
            -moz-box-shadow: 0px 0px 20px 10px rgba(255, 255, 255, 1);
            box-shadow: 0px 0px 20px 10px rgba(255, 255, 255, 1);
        }
    </style>

    <div class="flex flex-wrap w-full text-center px-4 pt-16">
        @foreach($game->players as $player)
            <div class="flex overflow-hidden justify-between rounded-xl h-7 w-36 m-2 {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
                <div class="pt-1 text-white px-2 flex-grow text-left">{{ $player->name }}</div>
                <div class="text-sm bg-white opacity-50 m-1 rounded-full px-2">
                    {{ $game ? $player->scoreInGame($game->id) : '' }}
                </div>
            </div>
        @endforeach
    </div>
</div>
