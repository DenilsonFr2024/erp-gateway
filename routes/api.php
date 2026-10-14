<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Http\Request;
//

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('/health', HealthController::class);
    Route::get('/test-error', function () {
        abort(500);;
    });
    Route::get('/test-validation', function (Request $request) {

        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'min:3'],
        ]);

        return \App\Http\Responses\ApiResponse::success(
            $request->only(['email', 'name'])
        );
    });
    Route::get('/test-401', function () {
        abort(401);
    });
});