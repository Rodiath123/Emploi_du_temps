<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Change Tracking API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('changes')->name('changes.')->group(function () {
        // Model history and summaries
        Route::get('/models/{modelType}/{modelId}/history', [\App\Http\Controllers\ChangeHistoryController::class, 'showModelHistory'])->name('api.model.history');
        Route::get('/models/{modelType}/{modelId}/summary', [\App\Http\Controllers\ChangeHistoryController::class, 'showModelSummary'])->name('api.model.summary');
        Route::get('/models/{modelType}/{modelId}/timeline', [\App\Http\Controllers\ChangeHistoryController::class, 'showChangeTimeline'])->name('api.model.timeline');
        
        // User changes
        Route::get('/users/{user}/changes', [\App\Http\Controllers\ChangeHistoryController::class, 'showUserChanges'])->name('api.user.changes');
        
        // Field changes (admin only)
        Route::get('/fields/{fieldName}', [\App\Http\Controllers\ChangeHistoryController::class, 'showFieldChanges'])->name('api.field')
            ->middleware('admin');
        
        // Date range
        Route::get('/by-date-range', [\App\Http\Controllers\ChangeHistoryController::class, 'showChangesByDateRange'])->name('api.date-range');
        
        // Comparison
        Route::get('/comparisons/{audit}', [\App\Http\Controllers\ChangeHistoryController::class, 'showChangeComparison'])->name('api.comparison');
        
        // Admin only routes
        Route::middleware('admin')->group(function () {
            Route::get('/most-active-users', [\App\Http\Controllers\ChangeHistoryController::class, 'showMostActiveUsers'])->name('api.most-active-users');
            Route::get('/most-changed-models', [\App\Http\Controllers\ChangeHistoryController::class, 'showMostChangedModels'])->name('api.most-changed-models');
            Route::post('/export', [\App\Http\Controllers\ChangeHistoryController::class, 'exportAudits'])->name('api.export');
        });
    });
});
