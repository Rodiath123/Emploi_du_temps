<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matiere;
use App\Models\Salle;
use App\Models\CoursProgramme;

class ConsultationController extends Controller
{
    // Affiche l'emploi du temps
    public function index()
    {
        $matieres = Matiere::all();
        $salles = Salle::all();
        
        // Récupérer tous les cours programmés
        $coursProgrammes = CoursProgramme::with(['matiere', 'salle'])->get();
        
        // Organiser par jour et horaire pour l'affichage
        $emploi = [];
        foreach ($coursProgrammes as $cours) {
            $emploi[$cours->jour][$cours->horaire] = [
                'matiere' => $cours->matiere->name,
                'salle' => $cours->salle->nom,
                'code' => $cours->matiere->code
            ];
        }
        
        return view('consultation.index', compact('matieres', 'salles', 'emploi'));
    }

    // Formulaires de création
    public function createSalle() { return view('consultation.create_salle'); }
    public function createMatiere() { return view('consultation.create_matiere'); }

    // Enregistrement en base de données
    public function storeSalle(Request $request) {
        Salle::create(['nom' => $request->nom, 'capacite' => $request->capacite]);
        return redirect('/mon-emploi-du-temps');
    }

    public function storeMatiere(Request $request) {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:matieres,code',
        ]);
        
        // Création
        \App\Models\Matiere::create([
            'name' => $request->name,
            'libelle' => $request->name,
            'code' => $request->code,
        ]);
        
        return redirect('/mon-emploi-du-temps')->with('success', 'Matière créée avec succès!');
    }
    
    // Programmer un cours dans l'emploi du temps
    public function programmerCours(Request $request)
    {
        // Validation
        $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'salle_id' => 'required|exists:salles,id',
            'jour' => 'required|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'horaire' => 'required|in:08H-10H,10H-12H,14H-16H,16H-18H',
        ]);
        
        // Vérifier si le créneau est déjà pris
        $existe = \App\Models\CoursProgramme::where('jour', $request->jour)
                                            ->where('horaire', $request->horaire)
                                            ->exists();
        
        if ($existe) {
            return redirect('/mon-emploi-du-temps')
                ->with('error', 'Ce créneau horaire est déjà occupé !')
                ->withInput();
        }
        
        // Créer le cours programmé
        \App\Models\CoursProgramme::create([
            'matiere_id' => $request->matiere_id,
            'salle_id' => $request->salle_id,
            'jour' => $request->jour,
            'horaire' => $request->horaire,
        ]);
        
        return redirect('/mon-emploi-du-temps')->with('success', 'Cours programmé avec succès !');
    }
    public function destroyMatiere($id) {
    $matiere = \App\Models\Matiere::findOrFail($id);
    $matiere->delete();
    
    return redirect('/mon-emploi-du-temps')->with('success', 'Matière supprimée !');

}
}