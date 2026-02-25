<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Room;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Affiche le planning (accessible à tous les connectés)
     */
    public function index()
    {
        $courses = Course::all();
        $classes = AcademicClass::all();
$rooms = Room::all();

       
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        // On définit la plage horaire de 07h à 19h pour la grille
        $hours = range(7, 18); 


        return view('planning.index', compact(
            'courses',
            'days',
            'hours',
            'classes',
            'rooms'
        ));
        
    }
    

    /**
     * Enregistre un nouveau cours (ADMIN UNIQUEMENT)
     */
    public function store(Request $request) 
    {
        // Barrière de sécurité
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Action réservée aux administrateurs.');
        }

        // Validation pour éviter les erreurs de design
        $request->validate([
            'subject'    => 'required|string|max:255',
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
            'class_name' => 'required',
            'room'       => 'required',
        ]);

        Course::create($request->all() + ['status' => 'Confirmé']);
        
        return redirect()->back()->with('success', 'Cours ajouté avec succès !');
    }

    /**
     * Modifie un cours existant (ADMIN UNIQUEMENT)
     */
    public function update(Request $request, $id)
    {
        // Barrière de sécurité
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'subject'    => 'required',
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $course = Course::findOrFail($id);
        $course->update($request->all());

        return redirect()->back()->with('success', 'Cours mis à jour !');
    }

    /**
     * Supprime un cours (ADMIN UNIQUEMENT)
     */
    public function destroy($id)
    {
        // Barrière de sécurité
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->back()->with('success', 'Cours supprimé !');
    }
}