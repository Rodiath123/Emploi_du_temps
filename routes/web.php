<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController; // On s'assure qu'il est bien là
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// CORRECTION : On demande au CourseController d'ouvrir le dashboard
// C'est la fonction index() qui va charger les cours
Route::get('/dashboard', [CourseController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route pour enregistrer les cours
Route::post('/courses', [CourseController::class, 'store'])
    ->middleware('auth')
    ->name('courses.store');

require __DIR__.'/auth.php';
Route::resource('courses', CourseController::class);