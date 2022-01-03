<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\WavelengthController;

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

Route::get('/', [AuthController::class, 'show']);

Route::get('/welcome', [AuthController::class, 'show'])->name('WelcomePage');
Route::get('/impressum', [AuthController::class, 'impressum'])->name('impressums');

Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/anonymous', [AuthController::class, 'anonymousLogin'])->name('auth.anonymous');
Route::get('/auth/{view?}', [AuthController::class, 'show'])->name('auth.show');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/game', [GameController::class, 'index'])->name('game.index');
    Route::post('/game', [GameController::class, 'create'])->name('game.create');
    Route::post('/game/join', [GameController::class, 'join'])->name('game.join');

    Route::get('/game/{game}', [GameController::class, 'show'])->name('game.show');
    Route::get('/game/{game}/settings', [GameController::class, 'settings'])->name('game.settings');
    Route::patch('/game/{game}', [GameController::class, 'update'])->name('game.update');
    Route::delete('/game/{game}', [GameController::class, 'destroy'])->name('game.destroy');

    // Player
    Route::delete('/player/{player}', [PlayerController::class, 'destroy'])->name('player.destroy');
    Route::put('/player/{player}', [PlayerController::class, 'update'])->name('player.update');

    // Wavelength
    Route::get('/wavelength/{game}', [WavelengthController::class, 'join'])->name('wavelength.join');
    Route::post('/wavelength/{game}/move', [WavelengthController::class, 'move'])->name('wavelength.move');
    Route::post('/wavelength/{game}/round', [WavelengthController::class, 'round'])->name('wavelength.round');
});
