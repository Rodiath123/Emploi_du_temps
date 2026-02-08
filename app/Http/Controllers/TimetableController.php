<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index()
    {
    
        // Vérifie bien les crochets [ ] et les virgules ,
        $mesCours = [
            [
                'matiere'=> 'Algorithmique', 
                'heure' => '08:00', 
                'salle' => 'Salle A1', 
                'prof' => 'M. Durand'
            ],
            [
                'matiere' => 'Base de données', 
                'heure' => '10:30', 
                'salle' => 'Labo 2', 
                'prof' => 'Mme Traoré'
            ],
            [
                'matiere' => 'Anglais Tech', 
                'heure' => '14:00', 
                'salle' => 'Salle B3', 
                'prof' => 'M. Smith'
            ],
        ];

        return view('timetable.index', ['cours' => $mesCours]);
    }
}