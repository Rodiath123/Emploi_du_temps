<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // On fusionne ton RoleSeeder et son MatieresSallesSeeder
        $this->call([
            RoleSeeder::class,
            MatieresSallesSeeder::class,
        ]);

        // Les lignes commentées ci-dessous peuvent être supprimées ou gardées
        // \App\Models\User::factory(10)->create();
    }
}