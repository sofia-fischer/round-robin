<?php

namespace App\View\Components;

use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public function render()
    {
        $defaultBladeName = str_replace('\\', '.', get_class($this));

        return view($defaultBladeName, $this->data());
    }

    public function data()
    {
        return [];
    }
}
