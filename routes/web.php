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

// --- DASHBOARDS ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Redirection automatique ou Dashboard par défaut (étudiant)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Espace Administration
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Espace Enseignant
    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->name('teacher.dashboard');
});

// --- PROFIL & GESTION UTILISATEURS ---
Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Gestion des utilisateurs (Admin uniquement)
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create'); // Route pour afficher le formulaire
        Route::post('/', [UserController::class, 'store'])->name('store');         // Route pour enregistrer
        Route::patch('/{user}/role', [UserController::class, 'updateRole'])->name('updateRole');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});

// --- MODULE RÉFÉRENTIEL ACADÉMIQUE ---
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