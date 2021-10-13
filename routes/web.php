<?php

use App\View\Pages\WelcomePage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

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

Route::get('/', WelcomePage::class);

Route::get('/welcome', WelcomePage::class)->name('WelcomePage');

Route::get('/login', WelcomePage::class)->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/game/', [GameController::class, 'index'])->name('game.index');
    Route::get('/game/{game}', [GameController::class, 'show'])->name('game.show');
    Route::post('/game', [GameController::class, 'create'])->name('game.create');
    Route::patch('/game/{game}', [GameController::class, 'update'])->name('game.update');
    Route::delete('/game/{game}', [GameController::class, 'destroy'])->name('game.destroy');
});
