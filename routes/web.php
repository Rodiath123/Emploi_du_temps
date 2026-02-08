<?php
use App\Http\Controllers\admin\DashboardController;
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
use App\Http\Controllers\Academic\ReferentialController;

Route::prefix('admin/referential')->group(function () {
    Route::get('/', [ReferentialController::class, 'index'])->name('referential.index');
    Route::post('/rooms', [ReferentialController::class, 'storeRoom'])->name('referential.rooms.store');
    Route::post('/subjects', [ReferentialController::class, 'storeSubject'])->name('referential.subjects.store');
    Route::post('/classes', [ReferentialController::class, 'storeClass'])->name('referential.classes.store');
    
    Route::delete('/rooms/{id}', [ReferentialController::class, 'destroyRoom'])->name('referential.rooms.destroy');
    Route::delete('/subjects/{id}', [ReferentialController::class, 'destroySubject'])->name('referential.subjects.destroy');
    Route::delete('/classes/{id}', [ReferentialController::class, 'destroyClass'])->name('referential.classes.destroy');// Dans routes/web.php


});