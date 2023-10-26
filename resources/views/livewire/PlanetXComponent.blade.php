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
            @foreach (array_slice([... $board], 6, 11) as $index => $sector)
                <ul class="first:ml-16 last:ml-16 [&:nth-child(2)]:ml-6 [&:nth-child(5)]:ml-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2">

                    @foreach (PlanetXIconEnum::cases() as $icon)
                        <li class="h-8 w-8 mx-1">
                            <input type="checkbox"
                                id="{{ 'sector_' . ($index) . $icon->value }}"
                                class="absolute peer invisible h-0 w-0"/>

                            <x-planet-x-icon for="{{ 'sector_' . ($index) . $icon->value }}"
                                value="{{ $sector->hasIcon($icon) }}"
                                class="peer-checked:fill-yellow-300 fill-slate-500 peer-checked:stroke-yellow-300 stroke-slate-500"
                                :icon="$icon"></x-planet-x-icon>
                        </li>
                    @endforeach

                </ul>
            @endforeach
        </div>
        <div class="bg-slate-700 px-3" style="border-top-right-radius: 9rem; border-bottom-right-radius: 9rem;">
            @foreach (array_slice([... $board], 6, 11) as $index => $sector)
                <ul class="first:mr-16 last:mr-16 [&:nth-child(2)]:mr-6 [&:nth-child(5)]:mr-6 h-12
                    flex items-center justify-between bg-slate-800 rounded-full my-2 px-2">

                    @foreach (array_reverse(PlanetXIconEnum::cases()) as $icon)
                        <li class="h-8 w-8 mx-1">
                            <input type="checkbox"
                                id="{{ 'sector_' . ($index + 6) . $icon->value }}"
                                class="absolute peer invisible h-0 w-0"/>

                            <x-planet-x-icon for="{{ 'sector_' . ($index + 6) . $icon->value }}"
                                value="{{ $sector->hasIcon($icon) }}"
                                class="peer-checked:fill-yellow-300 fill-slate-500 peer-checked:stroke-yellow-300 stroke-slate-500"
                                :icon="$icon"></x-planet-x-icon>
                        </li>
                    @endforeach

                </ul>
            @endforeach
        </div>
    </form>
</div>
