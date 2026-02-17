<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Change Tracking and Audit Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('changes')->name('changes.')->group(function () {
        // Model history and summaries
        Route::get('/model/{modelType}/{modelId}/history', [\App\Http\Controllers\ChangeHistoryController::class, 'showModelHistory'])->name('model.history');
        Route::get('/model/{modelType}/{modelId}/summary', [\App\Http\Controllers\ChangeHistoryController::class, 'showModelSummary'])->name('model.summary');
        Route::get('/model/{modelType}/{modelId}/timeline', [\App\Http\Controllers\ChangeHistoryController::class, 'showChangeTimeline'])->name('model.timeline');
        
        // User changes
        Route::get('/user/{user}/changes', [\App\Http\Controllers\ChangeHistoryController::class, 'showUserChanges'])->name('user.changes');
        
        // Field changes
        Route::get('/field/{fieldName}', [\App\Http\Controllers\ChangeHistoryController::class, 'showFieldChanges'])->name('field');
        
        // Date range
        Route::get('/by-date-range', [\App\Http\Controllers\ChangeHistoryController::class, 'showChangesByDateRange'])->name('date-range');
        
        // Comparison
        Route::get('/comparison/{audit}', [\App\Http\Controllers\ChangeHistoryController::class, 'showChangeComparison'])->name('comparison');
        
        // Admin only routes
        Route::middleware('admin')->group(function () {
            Route::get('/most-active-users', [\App\Http\Controllers\ChangeHistoryController::class, 'showMostActiveUsers'])->name('most-active-users');
            Route::get('/most-changed-models', [\App\Http\Controllers\ChangeHistoryController::class, 'showMostChangedModels'])->name('most-changed-models');
            Route::post('/export', [\App\Http\Controllers\ChangeHistoryController::class, 'exportAudits'])->name('export');
        });
    });
});