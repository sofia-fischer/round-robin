<?php

use App\Models\Game;
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

Route::get('/', \App\View\Pages\WelcomePage::class);

Route::get('/welcome', \App\View\Pages\WelcomePage::class)->name('WelcomePage');


Route::get('/game/{game}', function (Game $game) {
    return redirect()->route('GamePage', ['group' => $game->group->uuid, 'game' => $game->uuid]);
})->middleware(['auth:sanctum']);

Route::get('/group/{group}/game/{game}', \App\View\Pages\GamePage::class)->middleware(['auth:sanctum'])->name('GamePage');

Route::get('/group/{group}', \App\View\Pages\GroupRoomPage::class)->middleware(['auth:sanctum']);
