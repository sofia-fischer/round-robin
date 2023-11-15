<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\JustOneController;
use App\Http\Controllers\PlanetXController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WavelengthController;
use App\Http\Controllers\WerewolfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'show'])->name('home');

Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::get('/auth/{view?}', [AuthController::class, 'show'])->name('auth.show');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/game', [GameController::class, 'index'])->name('game.index');
    Route::post('/game', [GameController::class, 'create'])->name('game.create');
    Route::post('/game/show', [GameController::class, 'join'])->name('game.join');

    Route::get('/game/{game}/settings', [GameController::class, 'settings'])->name('game.settings');
    Route::patch('/game/{game}', [GameController::class, 'update'])->name('game.update');
    Route::delete('/game/{game}', [GameController::class, 'destroy'])->name('game.destroy');
    Route::put('/game/{game}', [GameController::class, 'round'])->name('game.round');

    // Player
    Route::delete('/player/{player}', [PlayerController::class, 'destroy'])->name('player.destroy');

    // User
    Route::get('/user', [UserController::class, 'view'])->name('user.show');
    Route::post('/user', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/logout', [UserController::class, 'logout'])->name('user.logout');

    // Wavelength
    Route::get('/wavelength/{game}', [WavelengthController::class, 'show'])->name('wavelength.show');
    Route::post('/wavelength/{game}/move', [WavelengthController::class, 'move'])->name('wavelength.move');
    Route::post('/wavelength/{game}/round', [WavelengthController::class, 'round'])->name('wavelength.round');

    // Just One
    Route::get('/justone/{game}', [JustOneController::class, 'show'])->name('justone.show');
    Route::post('/justone/{game}/move', [JustOneController::class, 'move'])->name('justone.move');
    Route::post('/justone/{game}/round', [JustOneController::class, 'round'])->name('justone.round');

    // Planet X
    Route::get('/planetx/{game}', [PlanetXController::class, 'show'])->name('planet_x.show');
    Route::post('/planetx/{game}/conference', [PlanetXController::class, 'conference'])->name('planet_x.conference');
    Route::post('/planetx/{game}/target', [PlanetXController::class, 'target'])->name('planet_x.target');

    // Werewolf
    Route::get('/werewolf/{game}', [WerewolfController::class, 'show'])->name('werewolf.show');
    Route::post('/werewolf/{game}/move', [WerewolfController::class, 'move'])->name('werewolf.move');
    Route::post('/werewolf/{game}/sunrise', [WerewolfController::class, 'sunrise'])->name('werewolf.sunrise');
    Route::post('/werewolf/{game}/vote', [WerewolfController::class, 'vote'])->name('werewolf.vote');
    Route::post('/werewolf/{game}/round', [WerewolfController::class, 'round'])->name('werewolf.round');
});
