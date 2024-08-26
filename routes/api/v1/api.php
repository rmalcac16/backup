<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

use App\Http\Controllers\Api\V1\ApiController;

Route::get('status', function(Request $request){
	$tokenName = 'tokenseguro';
	$token = PersonalAccessToken::where('token', $tokenName)->first();

	if (!$token) {
		$token = PersonalAccessToken::create([
			'token' => $tokenName,
		]);
	}
    return array(
		'status' => 200,
		'version' => 1.0,
		'data' => $token,
		'token' => $token->plainTextToken
	);
});


Route::post('codePasswordRestore', [ApiController::class, 'codePasswordRestore']);
Route::post('passwordRestore', [ApiController::class, 'passwordRestore']);
