<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnimeController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Stream\StreamController;
use App\Http\Controllers\Import\Flv;
use App\Http\Controllers\Import\Tio;
use App\Http\Controllers\Import\Jk;
use App\Http\Controllers\Import\Monos;
use App\Http\Controllers\Import\Fenix;
use App\Http\Controllers\Auth\AdminAuthenticateController;

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
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::get('/redirect', [AppController::class, 'redirect'])->name('redirect');

Route::get('/admin/login', [AdminAuthenticateController::class, 'create'])->middleware('guest')->name('adminLogin');
Route::post('/admin/login', [AdminAuthenticateController::class, 'store'])->middleware('guest');
Route::post('/admin/logout', [AdminAuthenticateController::class, 'destroy'])->middleware('authadmin')->name('logout');

Route::middleware('authadmin')->prefix('admin')->name('admin.')->group(function (){
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('animes', AnimeController::class);
    Route::get('animesLatino', [AnimeController::class, 'indexLatino'])->name('animes.indexLatino');
    Route::resource('genres', GenreController::class);
    Route::resource('servers', ServerController::class);
    Route::get('animes-generate', [AnimeController::class, 'generate'])->name('animes.generate');
    Route::name('animes.')->prefix('animes')->group(function () {
        Route::resource('{anime_id}/episodes', EpisodeController::class);
        Route::get('{anime_id}/episodesLatino', [EpisodeController::class, 'indexLatino'])->name('episodes.indexLatino');
        Route::get('{anime_id}/episodes-generate', [EpisodeController::class, 'generate'])->name('episodes.generate');
        Route::get('{anime_id}/players-generate', [EpisodeController::class, 'generatePlayers'])->name('episodes.generatePlayers');
        Route::post('{anime_id}/episodes-alldelete', [EpisodeController::class, 'allDelete'])->name('episodes.allDelete');
        Route::name('episodes.')->prefix('{anime_id}/episodes')->group(function () {
            Route::resource('{episode_id}/players', PlayerController::class);
            Route::post('generate/storePlayers', [PlayerController::class, 'storePlayers'])->name('players.storePlayers');
            Route::post('delete/players-allDelete', [PlayerController::class, 'allDeletePlayers'])->name('players.allDeletePlayers');
        });
    });
});