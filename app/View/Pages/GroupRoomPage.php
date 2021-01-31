<?php

namespace App\View\Pages;

use App\Models\Group;

class GroupRoomPage
{
    public function __invoke(Group $group)
    {
        if (!$group->authenticatedPlayer) {
            return redirect()->route('WelcomePage');
        }

        return view('GroupRoomPage', ['group' => $group]);
    }
}
