<?php

use App\View\Pages\GamePage;
use App\View\Pages\WelcomePage;
use App\View\Pages\GroupRoomPage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoveStoreController;

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

Route::get('/game/{game}', GamePage::class)->middleware(['auth:sanctum'])->name('game.show');

Route::get('/group/{group}/game/{game}', GamePage::class)->middleware(['auth:sanctum'])->name('GamePage');

Route::get('/group/{group}', GroupRoomPage::class)->middleware(['auth:sanctum']);
