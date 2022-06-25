<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_requires_email()
    {
        $this->post(route('auth.register'))
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function register_requires_new_email()
    {
        $existingUser = User::factory()->create();
        $this->post(route('auth.register'), ['email' => $existingUser->email])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function register_requires_password()
    {
        $this->post(route('auth.register'))
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function register_requires_name()
    {
        $this->post(route('auth.register'))
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function register_requires_token()
    {
        $this->post(route('auth.register'), ['token' => 'not-a-valid-token'])
            ->assertSessionHasErrors('token');
    }

    /** @test */
    public function register()
    {
        /** @var Game $game */
        $game = Game::factory()->create();

        $this->post(route('auth.register'), [
            'email'    => 'norbert@example.com',
            'name'     => 'Norbert',
            'password' => 'password',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('game.index'));

        $this->assertAuthenticated('web');
        $this->assertDatabaseHas('users', [
            'email' => 'norbert@example.com',
            'name'  => 'Norbert',
        ]);
    }

    /** @test */
    public function register_with_token()
    {
        /** @var Game $game */
        $game = Game::factory()->create();

        $this->post(route('auth.register'), [
            'email'    => 'norbert@example.com',
            'name'     => 'Norbert',
            'password' => 'password',
            'token'    => $game->token,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route("{$game->logic_identifier}.show", ['game' => $game->uuid]));

        $this->assertAuthenticated('web');
        $this->assertDatabaseHas('users', [
            'email' => 'norbert@example.com',
            'name'  => 'Norbert',
        ]);
    }

    /** @test */
    public function login_requires_email()
    {
        $this->post(route('auth.login'))
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_requires_password()
    {
        $this->post(route('auth.login'))
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function login_requires_token()
    {
        $this->post(route('auth.login'), ['token' => 'not-a-valid-token'])
            ->assertSessionHasErrors('token');
    }

    /** @test */
    public function login()
    {
        /** @var Game $game */
        $game = Game::factory()->create();
        /** @var User $existingUser */
        $existingUser = User::factory()->create();

        $this
            ->post(route('auth.login', [
                'email'    => $existingUser->email,
                'password' => 'password',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('game.index'));

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function login_with_token()
    {
        /** @var Game $game */
        $game = Game::factory()->create();
        /** @var User $existingUser */
        $existingUser = User::factory()->create();

        $this->post(route('auth.login', [
            'email'    => $existingUser->email,
            'password' => 'password',
            'token'    => $game->token,
        ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route("{$game->logic_identifier}.show", ['game' => $game->uuid]));

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function anonymous_requires_name()
    {
        $this->post(route('auth.anonymous'))
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function anonymous_requires_token()
    {
        $this->post(route('auth.anonymous'))
            ->assertSessionHasErrors('token');
    }

    /** @test */
    public function anonymous_valid_requires_token()
    {
        $this->post(route('auth.anonymous'), ['token' => 'not-a-valid-token'])
            ->assertSessionHasErrors('token');
    }

    /** @test */
    public function anonymous()
    {
        /** @var Game $game */
        $game = Game::factory()->create();

        $this->post(route('auth.anonymous'), [
            'name'  => 'Norbert',
            'token' => $game->token,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route("{$game->logic_identifier}.show", ['game' => $game->uuid]));

        $this->assertAuthenticated('web');
    }
}
