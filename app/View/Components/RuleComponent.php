<?php

declare(strict_types=1);

namespace App\View\Components;

use App\ValueObjects\PlanetXBoard;
use App\ValueObjects\PlanetXRules\PlanetXRule;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RuleComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public PlanetXRule $rule, public PlanetXBoard $board)
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.rule-component');
    }
}
