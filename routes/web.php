<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Academic\ReferentialController;
use App\Http\Controllers\ConsultationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Accueil
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
    // Important : Garde ces routes pour ton middleware de sécurité !
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

// --- NOUVELLES ROUTES : CONSULTATION & PROGRAMMATION ---
Route::middleware(['auth'])->group(function () {
    // Emploi du temps
    Route::get('/mon-emploi-du-temps', [ConsultationController::class, 'index'])->name('consultation.index');

    // Salles
    Route::get('/salles/create', [ConsultationController::class, 'createSalle'])->name('salles.create');
    Route::post('/salles/store', [ConsultationController::class, 'storeSalle'])->name('salles.store');

    // Matières
    Route::get('/matieres/create', [ConsultationController::class, 'createMatiere'])->name('matieres.create');
    Route::post('/matieres/store', [ConsultationController::class, 'storeMatiere'])->name('matieres.store');
    Route::delete('/matieres/{id}', [ConsultationController::class, 'destroyMatiere'])->name('matieres.destroy');

    // Programmation
    Route::post('/programmer-cours', [ConsultationController::class, 'programmerCours'])->name('cours.programmer');
});

require __DIR__.'/auth.php';