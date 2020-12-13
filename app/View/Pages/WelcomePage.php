<?php

namespace App\View\Pages;

class WelcomePage
{
    public function __invoke()
    {
        return view('WelcomePage', [
            'tabs'      => 'lol',
        ]);
    }
}
