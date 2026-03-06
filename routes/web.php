<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Academic\ReferentialController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\CourseController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Accueil
    Route::get('/', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

// --- DASHBOARDS & COURS ---
// --- DASHBOARDS & ACCUEIL ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ON FORCE L'UTILISATION DU FICHIER UNIQUE
    Route::get('/dashboard', function () {
        return view('dashboard'); 
    })->name('dashboard');

    // On garde ces noms au cas où tes contrôleurs les utilisent
    Route::view('/admin/home', 'dashboard')->name('admin.dashboard');
    Route::view('/teacher/home', 'dashboard')->name('teacher.dashboard');

    Route::get('/mon-planning', [CourseController::class, 'index'])->name('dashboard.emploi');
});
// --- PROFIL & GESTION UTILISATEURS ---
Route::middleware('auth')->group(function () {
    // Profil (Sécurité mdp incluse)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Gestion des utilisateurs (Admin uniquement)
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
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

// --- CONSULTATION, PROGRAMMATION & COURS ---
Route::middleware(['auth'])->group(function () {
    // Emploi du temps
    Route::get('/mon-emploi-du-temps', [ConsultationController::class, 'index'])->name('consultation.index');

    // Salles & Matières
    Route::get('/salles/create', [ConsultationController::class, 'createSalle'])->name('salles.create');
    Route::post('/salles/store', [ConsultationController::class, 'storeSalle'])->name('salles.store');
    Route::get('/matieres/create', [ConsultationController::class, 'createMatiere'])->name('matieres.create');
    Route::post('/matieres/store', [ConsultationController::class, 'storeMatiere'])->name('matieres.store');
    Route::delete('/matieres/{id}', [ConsultationController::class, 'destroyMatiere'])->name('matieres.destroy');

    // Programmation des cours (Les deux méthodes sont préservées pour éviter les bugs)
    Route::post('/programmer-cours', [ConsultationController::class, 'programmerCours'])->name('cours.programmer');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::resource('courses', CourseController::class)->except(['index', 'store']);
});

require __DIR__.'/auth.php';