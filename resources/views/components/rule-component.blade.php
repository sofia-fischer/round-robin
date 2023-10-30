<?php /* @var \App\ValueObjects\PlanetXRules\PlanetXRule $rule */ ?>

<li class="max-w-sm">
    <div class="bg-slate-400 rounded-full flex px-2 py-1 my-1 justify-evenly text-sm max-w-full">
        @switch(true)
            @case($rule instanceOf \App\ValueObjects\PlanetXRules\NextToRule)
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->icon"></x-planet-x-icon>
                </div>
                <div class="px-1">
                    is next to
                </div>
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->mustBeNextToIcon"></x-planet-x-icon>
                </div>
                @break

            @case($rule instanceOf \App\ValueObjects\PlanetXRules\NotNextToRule)
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->icon"></x-planet-x-icon>
                </div>
                <div class="px-1">
                    is not next to
                </div>
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->mustNotBeNextToIcon"></x-planet-x-icon>
                </div>
                @break

            @case($rule instanceOf \App\ValueObjects\PlanetXRules\InABandOfNSectorsRule)
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->icon"></x-planet-x-icon>
                </div>
                <div class="px-1">
                    is in a band of {{ $rule->mustBeInBandOfIcon }} sectors
                </div>
                @break

            @case($rule instanceOf \App\ValueObjects\PlanetXRules\InSectorRule)
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->icon"></x-planet-x-icon>
                </div>
                <div class="px-1">
                    is in sector {{ $rule->sector + 1 }}
                </div>
                @break

            @case($rule instanceOf \App\ValueObjects\PlanetXRules\NotInSectorRule)
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->icon"></x-planet-x-icon>
                </div>
                <div class="px-1">
                    is not in sector {{ $rule->sector + 1}}
                </div>
                @break

            @case($rule instanceOf \App\ValueObjects\PlanetXRules\WithinNSectorsRule)
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->icon"></x-planet-x-icon>
                </div>
                <div class="px-1">
                    is within {{ $rule->within }} sectors of
                </div>
                <div class="h-5 w-5">
                    <x-planet-x-icon class="fill-slate-800 stroke-slate-800" :icon="$rule->otherIcon"></x-planet-x-icon>
                </div>
                @break

        @endswitch
    </div>

    @if(! $rule->isValid($board))
        <div class="text-red-800 text-xs w-40">
            {{ $rule->getErrorMessage() }}
        </div>
    @endif
</li>
