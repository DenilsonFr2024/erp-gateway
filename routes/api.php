<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\IdentityController;
use App\Http\Controllers\Api\V1\ServiceProxyController;
use App\Http\Middleware\EnsureAuthenticatedWithIdentity;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('/health', HealthController::class);
    Route::get('/identity/health', [IdentityController::class, 'health']);

    if (app()->environment(['local', 'testing'])) {
        Route::get('/test-error', function () {
            abort(500);
        });

        Route::get('/test-validation', function (Request $request) {
            $request->validate([
                'email' => ['required', 'email'],
                'name' => ['required', 'string', 'min:3'],
            ]);

            return ApiResponse::success(
                $request->only(['email', 'name'])
            );
        });

        Route::get('/test-401', function () {
            abort(401);
        });
    }

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:auth-login');

    $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    Route::middleware(EnsureAuthenticatedWithIdentity::class)->group(function () use ($methods) {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::match($methods, '/core/{path?}', [ServiceProxyController::class, 'core'])
            ->where('path', '.*');
        Route::match($methods, '/master-data/{path?}', [ServiceProxyController::class, 'masterData'])
            ->where('path', '.*');
        Route::match($methods, '/sales/{path?}', [ServiceProxyController::class, 'sales'])
            ->where('path', '.*');
        Route::match($methods, '/inventory/{path?}', [ServiceProxyController::class, 'inventory'])
            ->where('path', '.*');
        Route::match($methods, '/finance/{path?}', [ServiceProxyController::class, 'finance'])
            ->where('path', '.*');
        Route::match($methods, '/hr/{path?}', [ServiceProxyController::class, 'hr'])
            ->where('path', '.*');
        Route::match($methods, '/purchasing/{path?}', [ServiceProxyController::class, 'purchasing'])
            ->where('path', '.*');
        Route::match($methods, '/analytics/{path?}', [ServiceProxyController::class, 'analytics'])
            ->where('path', '.*');
    });
});
