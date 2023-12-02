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
        <div class="bg-slate-100 rounded-3xl p-2 m-2 text-center">
            Base Rules (known to everyone):
            <ul>
                @foreach(\App\ValueObjects\PlanetXBoard::getVisibleStartingRules() as $rule)
                    <x-rule-component :rule="$rule" :board="$game->getAuthenticatedPlayerBoard()"></x-rule-component>
                @endforeach
            </ul>
            Total Numbers:
            <ul>
                <li class="max-w-sm bg-slate-100 rounded-full flex px-2 py-1 my-1 justify-evenly text-sm h-8 ">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800 px-1" :icon="PlanetXIconEnum::MOON"></x-planet-x-icon>
                    2 Moons
                </li>
                <li class="max-w-sm bg-slate-100 rounded-full flex px-2 py-1 my-1 justify-evenly text-sm h-8 ">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800 px-1" :icon="PlanetXIconEnum::ASTEROID"></x-planet-x-icon>
                    4 Asteroids
                </li>
                <li class="max-w-sm bg-slate-100 rounded-full flex px-2 py-1 my-1 justify-evenly text-sm h-8 ">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800 px-1" :icon="PlanetXIconEnum::PLANET"></x-planet-x-icon>
                    1 Dwarf Planet
                </li>
                <li class="max-w-sm bg-slate-100 rounded-full flex px-2 py-1 my-1 justify-evenly text-sm h-8 ">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800 px-1" :icon="PlanetXIconEnum::GALAXY"></x-planet-x-icon>
                    2 Galaxies
                </li>
                <li class="max-w-sm bg-slate-100 rounded-full flex px-2 py-1 my-1 justify-evenly text-sm h-8 ">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800 px-1" :icon="PlanetXIconEnum::PLANET_X"></x-planet-x-icon>
                    1 Planet X
                </li>
                <li class="max-w-sm bg-slate-100 rounded-full flex px-2 py-1 my-1 justify-evenly text-sm h-8 ">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800 px-1" :icon="PlanetXIconEnum::EMPTY_SPACE"></x-planet-x-icon>
                    2 Truly Empty Spaces
                </li>
            </ul>
        </div>
        <div class="bg-slate-100 rounded-3xl p-2 m-2 text-center">
            Rules you discovered:
            <ul>
                @foreach($game->getAuthenticatedPlayerRules() as $rule)
                    <x-rule-component :rule="$rule" :board="$game->getAuthenticatedPlayerBoard()"></x-rule-component>
                @endforeach
            </ul>
        </div>
        <div class="bg-slate-100 rounded-3xl p-2 m-2 text-center">
            Conference (same for everyone):
            <ul>
                @foreach($game->getAuthenticatedPlayerConference() as $index => $rule)
                    <div class="pl-2 text-xs my-1 flex align-middle">
                        <p class="py-3 mr-2">{{ $index }}</p>
                        @if($rule)
                            <x-rule-component :rule="$rule" :board="$game->getAuthenticatedPlayerBoard()"></x-rule-component>
                        @else
                            <form action="{{ route('planet_x.conference', ['game' => $game->id, 'conference' => $index]) }}"
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
        <div class="bg-slate-100 rounded-3xl p-2 m-2 text-center">
            <form action="{{ route('planet_x.target', ['game' => $game->id]) }}"
                method="POST" class="my-1 w-full ">
                @csrf

                <div class="flex">
                    <select id="target" class="bg-slate-700 text-yellow-300 rounded-l-full" name="target">
                        @foreach(range(0, 11) as $targetIndex)
                            <option {{ $game->isInCurrentNightSky($targetIndex) ? '' : 'disabled' }} value="{{$targetIndex}}">
                                {{$targetIndex +1 }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-2 py-2 rounded-r-full w-full text-yellow-300 bg-slate-700 hover:bg-yellow-200 hover:text-slate-800">
                        Target (⏳ 4)
                    </button>
                </div>
                <div class="text-xs">Know object in one Sector</div>
            </form>

            <form action="{{ route('planet_x.survey', ['game' => $game->id]) }}"
                method="POST" class="my-1 w-full ">
                @csrf

                <div class="flex text-yellow-300 bg-slate-700 rounded-full px-3">
                    <select id="icon" class="hover:bg-slate-500 bg-slate-700" name="icon" style="appearance: none;">
                        <option value="{{ PlanetXIconEnum::MOON->value }}">{{ PlanetXIconEnum::MOON->humaneReadable() }}</option>
                        <option value="{{ PlanetXIconEnum::ASTEROID->value }}">{{ PlanetXIconEnum::ASTEROID->humaneReadable() }}</option>
                        <option value="{{ PlanetXIconEnum::PLANET->value }}">{{ PlanetXIconEnum::PLANET->humaneReadable() }}</option>
                        <option value="{{ PlanetXIconEnum::GALAXY->value }}">{{ PlanetXIconEnum::GALAXY->humaneReadable() }}</option>
                        <option value="{{ PlanetXIconEnum::EMPTY_SPACE->value }}">{{ PlanetXIconEnum::EMPTY_SPACE->humaneReadable() }}</option>
                    </select>
                    <select id="from" class="hover:bg-slate-500 bg-slate-700" name="from" style="appearance: none;">
                        @foreach(range(0, 11) as $fromIndex)
                            <option {{ $game->isInCurrentNightSky($fromIndex) ? '' : 'disabled' }} value="{{$fromIndex}}">
                                {{$fromIndex +1 }}
                            </option>
                        @endforeach
                    </select>
                    <label for="to" class="p-2">to</label>
                    <select id="to" class="hover:bg-slate-500 bg-slate-700" name="to" style="appearance: none;">
                        @foreach(range(0, 11) as $toIndex)
                            <option {{ $game->isInCurrentNightSky($toIndex) ? '' : 'disabled' }} value="{{$toIndex}}">
                                {{$toIndex +1 }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-2 py-2 rounded-r-full w-full hover:bg-yellow-200 hover:text-slate-800">
                        Survey (⏳ 3-4)
                    </button>
                </div>
                <div class="text-xs">Know the number of objects in a range of sectors</div>
            </form>
        </div>
    </div>
</div>
