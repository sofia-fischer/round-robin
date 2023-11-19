<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function impressum()
    {
        return view('ImpressumPage');
    }

    public function show(string $view = 'login')
    {
        if (Auth::check()) {
            return redirect(route('game.index'));
        }

        return view('LoginPage', ['view' => $view, 'token' => request()->query('token')]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->data());
        Auth::login($user, true);

        if (! $request->input('token')) {
            return redirect(route('game.index'));
        }

        return $this->afterAuthentication($request->input('token'));
    }

    public function login(LoginRequest $request)
    {
        Auth::attempt([
            'name' => $request->input('name'),
            'password' => $request->input('password')
        ], true);

        return $this->afterAuthentication($request->input('token'));
    }

    private function afterAuthentication(?string $token)
    {
        if (! $token) {
            return redirect(route('game.index'));
        }

        /** @var Game $game */
        $game = Game::query()
            ->where('token', $token)
            ->firstOrFail();

        return redirect(route("{$game->logic_identifier}.show", ['game' => $game->id]));
    }
}
