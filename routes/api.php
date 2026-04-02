<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SuggestionController;

Route::prefix('v1')->group(function (): void {
    Route::get('/', fn () => response()->json([
        'name' => 'Ne Yapsam API',
        'status' => 'ok',
    ]));

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}/subcategories', [CategoryController::class, 'subcategories']);
    Route::get('/subcategories/{subcategory:slug}/suggestions', [SuggestionController::class, 'index']);
    Route::get('/suggestions/{suggestion}', [SuggestionController::class, 'show']);

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/guest', [AuthController::class, 'guest']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::post('/suggestions', [SuggestionController::class, 'store'])->middleware('throttle:suggestions');
    Route::post('/suggestions/{suggestion}/vote', [SuggestionController::class, 'vote'])->middleware('throttle:votes');
    Route::post('/suggestions/{suggestion}/report', [SuggestionController::class, 'report']);
});
