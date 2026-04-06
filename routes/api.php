<?php

use App\Http\Controllers\Api\AppStatusController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\PushTokenController;
use App\Http\Controllers\Api\SuggestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {

    // ---------------------------------------------------------------
    // Bakım modundan muaf: uygulama durum kontrolü
    // ---------------------------------------------------------------
    Route::get('/app-status', [AppStatusController::class, 'status'])
        ->middleware('throttle:60,1');

    // ---------------------------------------------------------------
    // Bakım modu aktifse aşağıdaki tüm route'lar 503 döner
    // ---------------------------------------------------------------
    Route::middleware('maintenance')->group(function (): void {

        Route::get('/', fn () => response()->json([
            'name'   => 'Ne Yapsam API',
            'status' => 'ok',
        ]));

        // Kategoriler & Öneriler (public)
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category:slug}/subcategories', [CategoryController::class, 'subcategories']);
        Route::get('/subcategories/{subcategory:slug}/suggestions', [SuggestionController::class, 'index']);
        Route::get('/suggestions/{suggestion}', [SuggestionController::class, 'show']);

        // Auth
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::post('/auth/guest', [AuthController::class, 'guest']);
        Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/auth/me', [MeController::class, 'show'])->middleware('auth:sanctum');

        // Kullanıcı
        Route::get('/me/suggestions', [MeController::class, 'suggestions'])->middleware('auth:sanctum');
        Route::get('/me/bookmarks', [MeController::class, 'bookmarks'])->middleware('auth:sanctum');

        // Push token
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/push-token', [PushTokenController::class, 'register']);
            Route::delete('/push-token', [PushTokenController::class, 'disable']);
        });

        // Öneriler (yazma)
        Route::post('/suggestions', [SuggestionController::class, 'store'])->middleware('throttle:suggestions');
        Route::post('/suggestions/{suggestion}/vote', [SuggestionController::class, 'vote'])->middleware('throttle:votes');
        Route::post('/suggestions/{suggestion}/bookmark', [SuggestionController::class, 'bookmark'])->middleware('auth:sanctum');
        Route::delete('/suggestions/{suggestion}/bookmark', [SuggestionController::class, 'removeBookmark'])->middleware('auth:sanctum');
        Route::post('/suggestions/{suggestion}/report', [SuggestionController::class, 'report']);
    });
});
