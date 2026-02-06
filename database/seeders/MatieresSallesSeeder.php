<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;
use App\Models\Salle;

class MatieresSallesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques matières
        \App\Models\Matiere::create([
            'nom' => 'Mathématiques',
            'libelle' => 'Mathématiques',
            'code' => 'MATH101'
        ]);

        \App\Models\Matiere::create([
            'nom' => 'Physique',
            'libelle' => 'Physique',
            'code' => 'PHYS102'
        ]);

        \App\Models\Matiere::create([
            'nom' => 'Informatique',
            'libelle' => 'Informatique',
            'code' => 'INFO103'
        ]);

        // Créer quelques salles
        \App\Models\Salle::create([
            'nom' => 'Salle A1',
            'capacite' => 30
        ]);

        \App\Models\Salle::create([
            'nom' => 'Salle B2',
            'capacite' => 25
        ]);

        \App\Models\Salle::create([
            'nom' => 'Amphi C',
            'capacite' => 100
        ]);
    }
}
