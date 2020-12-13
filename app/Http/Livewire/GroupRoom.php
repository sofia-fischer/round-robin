<?php

namespace App\Http\Livewire;

use App\Models\Group;
use Livewire\Component;

class GroupRoom extends Component
{
    public $groupId = null;

    public function render()
    {
        return view('livewire.group-room', [
            'group' => Group::findOrFail($this->groupId),
        ]);
    }
}
