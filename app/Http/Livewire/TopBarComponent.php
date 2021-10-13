<?php

namespace App\Http\Livewire;

use Livewire\Component;

class TopBarComponent extends Component
{
    public ?string $activeGameUuid = null;

    public function mount()
    {
        $this->activeGameUuid = request()->route()->parameter('game');
    }

    public function render()
    {
        return view('livewire.TopBarComponent');
    }
}
