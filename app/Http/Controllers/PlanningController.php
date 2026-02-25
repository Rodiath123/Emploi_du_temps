<?php

namespace App\Http\Controllers;

use App\Models\CourseSlot;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    // Cette fonction affichera la page principale de l'emploi du temps
    public function index()
    {
        // On récupère tous les créneaux enregistrés
        $slots = CourseSlot::all();
        
        // On les envoie à une vue (qu'on va créer juste après)
        return view('planning.index', compact('slots'));
    }
}