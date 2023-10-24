<?php

namespace App\View\Components;

use App\Models\PlanetXGame;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlanetXIcon extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $icon = PlanetXGame::PLANET_X)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.planet-x-icon');
    }
}
