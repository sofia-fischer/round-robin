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
            <div class="flex justify-evenly w-full pt-2">
                <div class="px-3">

                    <?php /* @var \App\ValueObjects\PlanetXSector $sector */ ?>
                    @foreach (array_slice([... $game->getAuthenticatedPlayerBoard()], 0, 6) as $index => $sector)
                        <ul class="first:ml-16 last:ml-16 [&:nth-child(2)]:ml-6 [&:nth-child(5)]:ml-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2 border-blue-400
                    {{ $game->isInCurrentNightSky($index)  ? 'border-2' : ''}}">
                            <div class="bg-slate-500 rounded-full px-1 text-xs"> {{ $index + 1 }} </div>

                            @foreach (PlanetXIconEnum::cases() as $icon)
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
                            @endforeach

                        </ul>
                    @endforeach
                </div>
                <div class="px-3">
                    @foreach (array_reverse(array_slice([... $game->getAuthenticatedPlayerBoard()], 6, 11, true), true) as $index => $sector)
                        <ul class="first:mr-16 last:mr-16 [&:nth-child(2)]:mr-6 [&:nth-child(5)]:mr-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2 border-blue-400
                            {{ $game->isInCurrentNightSky($index)  ? 'border-2' : ''}}">


                            @foreach (array_reverse(PlanetXIconEnum::cases()) as $icon)
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
                @foreach(\App\ValueObjects\PlanetXBoard::getStartingRules() as $rule)
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
    </div>
</div>
