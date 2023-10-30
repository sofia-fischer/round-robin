@php use App\ValueObjects\Enums\PlanetXIconEnum; @endphp
<div class="w-full">
    <?php
    /* @see \App\Http\Livewire\PlanetXComponent */

    /* @var \App\ValueObjects\PlanetXBoard $board */
    /* @var App\Models\PlanetXGame $game */
    /* @var App\Models\Move $move */
    ?>

    <form
        method="POST"
        action="{{ route('planet_x.hint', ['game' => $game->uuid]) }}"
        class="flex flex-wrap justify-center w-full m-1">
        <div class="bg-slate-700 px-3" style="border-top-left-radius: 9rem; border-bottom-left-radius: 9rem;">
          <?php /* @var \App\ValueObjects\PlanetXSector $sector */?>
            @foreach (array_slice([... $board], 0, 6) as $index => $sector)
                <ul class="first:ml-16 last:ml-16 [&:nth-child(2)]:ml-6 [&:nth-child(5)]:ml-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2">
                    <div class="bg-slate-500 rounded-full px-1 text-xs"> {{ $index + 1 }} </div>

                    @foreach (PlanetXIconEnum::cases() as $icon)
                        <li class="h-8 w-8 mx-1">
                            <input type="checkbox"
                                id="{{ 'sector_' . ($index) . $icon->value }}"
                                {{ $sector->hasIcon($icon)  ? ' checked' : '' }}
                                class="absolute peer invisible h-0 w-0"/>

                            <x-planet-x-icon :icon="$icon"
                                class="peer-checked:fill-yellow-300 fill-slate-500 peer-checked:stroke-yellow-300 stroke-slate-500"
                                for="{{ 'sector_' . $index . $icon->value }}">
                            </x-planet-x-icon>
                        </li>
                    @endforeach

                </ul>
            @endforeach
        </div>
        <div class="bg-slate-700 px-3" style="border-top-right-radius: 9rem; border-bottom-right-radius: 9rem;">
            @foreach (array_reverse(array_slice([... $board], 6, 11, true), true) as $index => $sector)
                <ul class="first:mr-16 last:mr-16 [&:nth-child(2)]:mr-6 [&:nth-child(5)]:mr-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2">

                    @foreach (array_reverse(PlanetXIconEnum::cases()) as $icon)
                        <li class="h-8 w-8 mx-1">
                            <input type="checkbox"
                                id="{{ 'sector_' . $index . $icon->value }}"
                                {{ $sector->hasIcon($icon)  ? ' checked' : '' }}
                                class="absolute peer invisible h-0 w-0"/>

                            <x-planet-x-icon :icon="$icon"
                                class="peer-checked:fill-yellow-300 fill-slate-500 peer-checked:stroke-yellow-300 stroke-slate-500"
                                for="{{ 'sector_' . $index . $icon->value }}">
                            </x-planet-x-icon>
                        </li>
                    @endforeach

                    <div class="bg-slate-500 rounded-full px-1 text-xs"> {{ $index + 1 }} </div>
                </ul>
            @endforeach
        </div>
    </form>

    <div class="flex flex-wrap">
        <div class="bg-slate-300 rounded-3xl p-2 m-2 text-center w-40">
            Base Rules (known to everyone):
            <ul>
                @foreach($board->getStartingRules() as $rule)
                    <x-rule-component :rule="$rule" :board="$board"></x-rule-component>
                @endforeach
            </ul>
        </div>
    </div>
</div>
