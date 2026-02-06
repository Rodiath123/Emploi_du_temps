<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultationController;

// Accueil
Route::get('/', function () { return view('welcome'); });

// Consultation (Ton tableau)
Route::get('/mon-emploi-du-temps', [ConsultationController::class, 'index']);

// Référentiel (Tes formulaires)
Route::get('/salles/create', [ConsultationController::class, 'createSalle']);
Route::post('/salles/store', [ConsultationController::class, 'storeSalle']);

Route::get('/matieres/create', [ConsultationController::class, 'createMatiere']);
Route::post('/matieres/store', [ConsultationController::class, 'storeMatiere']);
Route::delete('/matieres/{id}', [ConsultationController::class, 'destroyMatiere']);
Route::post('/programmer-cours', [ConsultationController::class, 'programmerCours']);
Route::post('/programmer-cours', [ConsultationController::class, 'programmerCours']);