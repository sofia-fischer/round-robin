<?php

namespace App\View\Pages;

use App\Models\Group;
use App\Models\User;

class WelcomePage
{
    public function __invoke()
    {
        // clean up database
        User::whereNull('email')->where('created_at', '<', now()->subWeek())->delete();
        Group::where('updated_at', '<', now()->subWeek())->delete();

        return view('WelcomePage');
    }
}
