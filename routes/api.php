<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\WebController;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Stream\StreamController;
use App\Http\Controllers\Import\Tio;
use App\Http\Controllers\Import\Jk;
use App\Http\Controllers\Import\Monos;
use App\Http\Controllers\Import\Fenix;
use App\Http\Controllers\Import\Sitemap;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/sitemap', [ApiController::class, 'sitemap'])->name('sitemap');

Route::prefix('app')->name('app.')->group(function () {	
	Route::middleware('auth:sanctum')->group(function () {
		Route::post('animeslist', [AppController::class, 'getRecentApp']);
	});

	// Authenticate User
	Route::prefix('auth')->name('auth.')->group(function () {
		Route::post('tokenApp', [ApiController::class, 'getTokenApp']);
		Route::post('codePasswordRestore', [ApiController::class, 'codePasswordRestore']);
		Route::post('passwordRestore', [ApiController::class, 'passwordRestore']);
		Route::post('registerApp', [ApiController::class, 'getRegister']);
		Route::post('forgotPassword', [ApiController::class, 'forgotPassword']);
		Route::post('vipUsers', [ApiController::class, 'vipUsers']);
		Route::middleware('auth:sanctum')->group(function () {
			Route::get('logout', [ApiController::class, 'logoutUser']);
		});
		Route::middleware('auth:sanctum')->get('/user-data', function (Request $request) {
			return response()->json($request->user(), 200);
		});
		Route::middleware('appsanctum')->group(function () {
			Route::post('update', [UserController::class, 'updateProfile']);
			Route::get('user', [ApiController::class, 'loginUser']);
			Route::prefix('favorite')->group(function () {
				Route::post('add', [ApiController::class, 'addAnimeFavorite']);
				Route::post('delete', [ApiController::class, 'deleteAnimeFavorite']);
				Route::post('list', [ApiController::class, 'listAnimeFavorite']);
			});
			Route::prefix('view')->group(function () {
				Route::post('add', [ApiController::class, 'addAnimeView']);
				Route::post('delete', [ApiController::class, 'deleteAnimeView']);
				Route::post('list', [ApiController::class, 'listAnimeView']);
			});	
			Route::prefix('watching')->group(function () {
				Route::post('add', [ApiController::class, 'addAnimeWatching']);
				Route::post('delete', [ApiController::class, 'deleteAnimeWatching']);
				Route::post('list', [ApiController::class, 'listAnimeWatching']);
			});			
		});
	});

	Route::get('view-anime/{id}/{episode_id}', [ApiController::class, 'setViewsAnime']);
	Route::get('view-animes/{id}', [ApiController::class, 'setViewsAnimes']);

});