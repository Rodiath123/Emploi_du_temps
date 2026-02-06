<?php

use App\Http\Controllers\ProfileController;
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
// Route par défaut (Étudiants)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes spécifiques aux rôles
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
    Route::get('/admin/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}/role', [App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('admin.users.updateRole');
});

require __DIR__.'/auth.php';
