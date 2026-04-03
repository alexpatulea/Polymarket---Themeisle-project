<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aici rutele sunt protejate de middleware-ul 'auth:sanctum'.
| Orice cerere (request) trimisă aici trebuie să aibă un Header valid:
| Authorization: Bearer {API_KEY_AICI}
|
*/

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/markets', [ApiController::class, 'listMarkets']);
    Route::get('/markets/{id}', [ApiController::class, 'viewMarket']);
    
    Route::post('/markets', [ApiController::class, 'createMarket']);
    Route::post('/bets', [ApiController::class, 'placeBet']);
    
    // O rută utilă pentru bot să își verifice balanța
    Route::get('/me', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});