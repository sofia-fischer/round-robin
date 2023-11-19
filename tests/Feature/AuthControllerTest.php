<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_requires_name()
    {
        $response = $this->post(route('auth.register'));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function register_requires_new_name()
    {
        $existingUser = User::factory()->create();
        $this->post(route('auth.register'), ['name' => $existingUser->name])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function register_requires_password()
    {
        $this->post(route('auth.register'))
            ->assertSessionHasErrors('password');
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
        $this->post(route('auth.register'), [
            'name' => 'Norbert',
            'password' => 'password',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('game.index'));

        $this->assertAuthenticated('web');
        $this->assertDatabaseHas('users', [
            'name' => 'Norbert',
        ]);
    }

    /** @test */
    public function register_with_token()
    {
        /** @var Game $game */
        $game = Game::factory()->create();

        $this->post(route('auth.register'), [
            'name' => 'Norbert',
            'password' => 'password',
            'token' => $game->token,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route("{$game->logic_identifier}.show", ['game' => $game->id]));

        $this->assertAuthenticated('web');
        $this->assertDatabaseHas('users', [
            'name' => 'Norbert',
        ]);
    }

    /** @test */
    public function login_requires_name()
    {
        $this->post(route('login'))
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function login_requires_password()
    {
        $this->post(route('login'))
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function login_requires_token()
    {
        $this->post(route('login'), ['token' => 'not-a-valid-token'])
            ->assertSessionHasErrors('token');
    }

    /** @test */
    public function login()
    {
        /** @var User $existingUser */
        $existingUser = User::factory()->create();

        $this->post(route('login', [
            'name' => $existingUser->name,
            'password' => 'password',
        ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('game.index'));

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function login_with_token()
    {
        /** @var User $existingUser */
        $existingUser = User::factory()->create();
        /** @var Game $game */
        $game = Game::factory()->create([
            'host_user_id' => $existingUser->id,
        ]);

        $this->post(route('login', [
            'name' => $existingUser->name,
            'password' => 'password',
            'token' => $game->token,
        ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route("{$game->logic_identifier}.show", ['game' => $game->id]));

        $this->assertAuthenticated('web');
    }
}
