<?php

namespace App\View\Pages;

use App\Models\Group;

class GroupRoomPage
{
    public function __invoke(Group $group)
    {
        return view('GroupRoomPage', ['group' => $group]);
    }
}
