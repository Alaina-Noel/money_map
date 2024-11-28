<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LineItemController;
use App\Http\Controllers\PaycheckController;

// Auth routes (unprotected)
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Budget routes
    Route::get('budgets', [BudgetController::class, 'index']);
    Route::get('budgets/{month}', [BudgetController::class, 'show'])
        ->where('month', '\d{4}-\d{2}');
    Route::post('budgets', [BudgetController::class, 'store']);
    Route::get('budgets/{month}/dashboard', [BudgetController::class, 'getDashboardSummary'])
        ->where('month', '\d{4}-\d{2}');

    // Category routes
    Route::get('budgets/{month}/categories', [CategoryController::class, 'index'])
        ->where('month', '\d{4}-\d{2}');
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('categories/{category}/metrics', [CategoryController::class, 'getCategoryMetrics']);

    // Line Item routes
    Route::get('categories/{category}/line-items', [LineItemController::class, 'index']);
    Route::post('line-items', [LineItemController::class, 'store']);
    Route::put('line-items/{lineItem}', [LineItemController::class, 'update']);
    Route::delete('line-items/{lineItem}', [LineItemController::class, 'destroy']);

    // Paycheck routes
    Route::get('budgets/{month}/paychecks', [PaycheckController::class, 'index'])
        ->where('month', '\d{4}-\d{2}');
    Route::post('paychecks', [PaycheckController::class, 'store']);
    Route::put('paychecks/{paycheck}', [PaycheckController::class, 'update']);
    Route::delete('paychecks/{paycheck}', [PaycheckController::class, 'destroy']);
});
