<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Academic\ReferentialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// --- TES ROUTES (Authentification & Dashboards) ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Espace Administration
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Espace Enseignant
    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->name('teacher.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes pour la gestion des utilisateurs (réservé à l'admin)
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
});

// --- SES ROUTES (Module Référentiel Académique) ---
Route::middleware(['auth'])->prefix('admin/referential')->group(function () {
    Route::get('/', [ReferentialController::class, 'index'])->name('referential.index');
    Route::post('/rooms', [ReferentialController::class, 'storeRoom'])->name('referential.rooms.store');
    Route::post('/subjects', [ReferentialController::class, 'storeSubject'])->name('referential.subjects.store');
    Route::post('/classes', [ReferentialController::class, 'storeClass'])->name('referential.classes.store');
    
    Route::delete('/rooms/{id}', [ReferentialController::class, 'destroyRoom'])->name('referential.rooms.destroy');
    Route::delete('/subjects/{id}', [ReferentialController::class, 'destroySubject'])->name('referential.subjects.destroy');
    Route::delete('/classes/{id}', [ReferentialController::class, 'destroyClass'])->name('referential.classes.destroy');
});

require __DIR__.'/auth.php';