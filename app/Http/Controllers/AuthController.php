<?php


namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Game;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AnonymousLoginRequest;

class AuthController
{
    public function impressum()
    {
        return view('ImpressumPage');
    }

    public function show(string $view = 'anonymous-login')
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
            'email'    => $request->input('email'),
            'password' => $request->input('password')
        ], true);

        return $this->afterAuthentication($request->input('token'));
    }

    public function anonymousLogin(AnonymousLoginRequest $request)
    {
        $user = User::create($request->data());
        Auth::login($user, true);

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

        return redirect(route("{$game->logic_identifier}.show", ['game' => $game->uuid]));
    }
}
