<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

use App\Http\Controllers\Api\V2\ApiController;

Route::get('status', function(Request $request){
    return array(
		'status' => true,
		'version' => '2.0',
	);
});