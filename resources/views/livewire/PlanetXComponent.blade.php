@php use App\ValueObjects\Enums\PlanetXIconEnum; @endphp
<div class="w-full">
    <?php
    /* @see \App\Http\Livewire\PlanetXComponent */

    /* @var App\Models\PlanetXGame $game */
    ?>

    <div class="w-full flex justify-center">
        <form
            method="POST"
            style="background: linear-gradient({{ $game->gradientDegree() }}deg, #3b82f6 0%, rgb(51, 65, 85, 1) 40%);"
            class="m-1 px-3 rounded-full p-2 max-w-2xl">
            @csrf
            <div class="flex justify-evenly w-full py-5 ">
                <div class="px-1">

                    <?php /* @var \App\ValueObjects\PlanetXSector $sector */ ?>
                    @foreach (array_slice([... $game->getAuthenticatedPlayerBoard()], 0, 6) as $index => $sector)
                        <ul class="first:ml-16 last:ml-16 [&:nth-child(2)]:ml-6 [&:nth-child(5)]:ml-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2 border-blue-400
                    {{ $game->isInCurrentNightSky($index)  ? 'border-2' : ''}}">
                            <div class="bg-slate-500 rounded-full px-1 text-xs"> {{ $index + 1 }} </div>

                            @foreach (PlanetXIconEnum::cases() as $icon)
                                @if($icon === PlanetXIconEnum::MOON && in_array($index,[0,3,5,7,8,9,11]))
                                    <li class="m-4"></li>
                                @else
                                    <li class="mx-1 h-8">
                                            <?php /* @see \App\Http\Livewire\PlanetXComponent::updated() */ ?>
                                        <input type="checkbox"
                                            wire:model.live="{{ 'form.sector_' . $index  . '_' . $icon->value }}"
                                            id="{{ 'sector_' . $index  . '_' . $icon->value }}"
                                            {{ $sector->hasIcon($icon)  ? ' checked' : '' }}
                                            class="absolute peer invisible h-0 w-0"/>

                                        <x-planet-x-icon :icon="$icon"
                                            class="peer-checked:fill-yellow-300 fill-slate-500 peer-checked:stroke-yellow-300 stroke-slate-500"
                                            for="{{ 'sector_' . $index  . '_' . $icon->value }}">
                                        </x-planet-x-icon>
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    @endforeach
                </div>
                <div class="px-1">
                    @foreach (array_reverse(array_slice([... $game->getAuthenticatedPlayerBoard()], 6, 11, true), true) as $index => $sector)
                        <ul class="first:mr-16 last:mr-16 [&:nth-child(2)]:mr-6 [&:nth-child(5)]:mr-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2 border-blue-400
                            {{ $game->isInCurrentNightSky($index)  ? 'border-2' : ''}}">

                            @foreach (array_reverse(PlanetXIconEnum::cases()) as $icon)
                                @if($icon === PlanetXIconEnum::MOON && in_array($index,[0,3,5,7,8,9,11]))
                                    <li class="m-4"></li>
                                @else
                                    <li class="h-8 mx-1">
                                            <?php /* @see \App\Http\Livewire\PlanetXComponent::updated() */ ?>
                                        <input type="checkbox"
                                            wire:model.live="{{ 'form.sector_' . $index  . '_' . $icon->value }}"
                                            id="{{ 'sector_' . $index  . '_' . $icon->value }}"
                                            {{ $sector->hasIcon($icon)  ? ' checked' : '' }}
                                            class="absolute peer invisible h-0 w-0"/>

                                        <x-planet-x-icon :icon="$icon"
                                            class="peer-checked:fill-yellow-300 fill-slate-500 peer-checked:stroke-yellow-300 stroke-slate-500"
                                            for="{{ 'sector_' . $index  . '_' . $icon->value }}">
                                        </x-planet-x-icon>
                                    </li>
                                @endif
                            @endforeach

                            <div class="bg-slate-500 rounded-full px-1 text-xs"> {{ $index + 1 }} </div>
                        </ul>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <div class="flex flex-wrap text-sm">
        <div class="bg-slate-300 rounded-3xl p-2 m-2 text-center">
            Base Rules (known to everyone):
            <ul>
                @foreach(\App\ValueObjects\PlanetXBoard::getVisibleStartingRules() as $rule)
                    <x-rule-component :rule="$rule" :board="$game->getAuthenticatedPlayerBoard()"></x-rule-component>
                @endforeach
            </ul>
        </div>
        <div class="bg-slate-300 rounded-3xl p-2 m-2 text-center">
            Additional Rules (different for everyone):
            <ul>
                @foreach($game->getAuthenticatedPlayerRules() as $rule)
                    <x-rule-component :rule="$rule" :board="$game->getAuthenticatedPlayerBoard()"></x-rule-component>
                @endforeach
            </ul>
        </div>
        <div class="bg-slate-300 rounded-3xl p-2 m-2 text-center">
            Conference (same for everyone):
            <ul>
                @foreach($game->getAuthenticatedPlayerConference() as $index => $rule)
                    <div class="pl-2 text-xs my-1 flex align-middle">
                        <p class="py-3 mr-2">{{ $index }}</p>
                        @if($rule)
                            <x-rule-component :rule="$rule" :board="$game->getAuthenticatedPlayerBoard()"></x-rule-component>
                        @else
                            <form action="{{ route('planet_x.conference', ['game' => $game->uuid, 'conference' => $index]) }}"
                                method="POST" class="my-1 w-full">
                                @csrf
                                <button type="submit"
                                    {{ $index === 'X' ? 'disabled' : '' }}
                                    class="px-2 py-2 rounded-full w-full text-yellow-300 bg-slate-700 hover:bg-yellow-200 hover:text-slate-800">
                                    ???
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </ul>
        </div>
        <div class="bg-slate-300 rounded-3xl p-2 m-2 text-center">
            <form action="{{ route('planet_x.target', ['game' => $game->uuid]) }}"
                method="POST" class="my-1 w-full flex">
                @csrf

                <select id="target" class="bg-slate-300" name="target">
                    <option {{ $game->isInCurrentNightSky(0) ? '' : 'disabled' }} value="0">1</option>
                    <option {{ $game->isInCurrentNightSky(1) ? '' : 'disabled' }} value="1">2</option>
                    <option {{ $game->isInCurrentNightSky(2) ? '' : 'disabled' }} value="2">3</option>
                    <option {{ $game->isInCurrentNightSky(3) ? '' : 'disabled' }} value="3">4</option>
                    <option {{ $game->isInCurrentNightSky(4) ? '' : 'disabled' }} value="4">5</option>
                    <option {{ $game->isInCurrentNightSky(5) ? '' : 'disabled' }} value="5">6</option>
                    <option {{ $game->isInCurrentNightSky(6) ? '' : 'disabled' }} value="6">7</option>
                    <option {{ $game->isInCurrentNightSky(7) ? '' : 'disabled' }} value="7">8</option>
                    <option {{ $game->isInCurrentNightSky(8) ? '' : 'disabled' }} value="8">9</option>
                    <option {{ $game->isInCurrentNightSky(9) ? '' : 'disabled' }} value="9">10</option>
                    <option {{ $game->isInCurrentNightSky(10) ? '' : 'disabled' }} value="10">11</option>
                    <option {{ $game->isInCurrentNightSky(11) ? '' : 'disabled' }} value="11">12</option>
                </select>

                <button type="submit"
                    class="px-2 py-2 rounded-full w-full text-yellow-300 bg-slate-700 hover:bg-yellow-200 hover:text-slate-800">
                    Target Sector for Info
                </button>
            </form>
        </div>
    </div>
</div>
