<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetSummaryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LineItemController;
use App\Http\Controllers\PaycheckController;

// Auth routes (unprotected)
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes that require authentication
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Budget routes
    Route::apiResource('budgets', BudgetSummaryController::class);

// Category routes
    Route::prefix('categories')->group(function () {
        Route::get('/current', [CategoryController::class, 'getCurrentMonth']);
        Route::get('/{month}', [CategoryController::class, 'getByMonth'])
            ->where('month', '\d{4}-\d{2}');
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });
    // Other resources
    Route::apiResource('line-items', LineItemController::class);
    Route::apiResource('paychecks', PaycheckController::class);
});
