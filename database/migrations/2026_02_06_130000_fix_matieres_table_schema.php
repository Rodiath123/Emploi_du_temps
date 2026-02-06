<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la colonne 'name' existe, sinon la table n'existe pas encore
        if (Schema::hasTable('matieres')) {
            // Si 'name' existe et 'nom' n'existe pas, renommer
            if (Schema::hasColumn('matieres', 'name') && !Schema::hasColumn('matieres', 'nom')) {
                Schema::table('matieres', function (Blueprint $table) {
                    $table->renameColumn('name', 'nom');
                });
            }
            
            // Ajouter 'libelle' s'il n'existe pas
            if (!Schema::hasColumn('matieres', 'libelle')) {
                Schema::table('matieres', function (Blueprint $table) {
                    $table->string('libelle')->nullable()->after('nom');
                });
            }
            
            // Ajouter 'code' s'il n'existe pas
            if (!Schema::hasColumn('matieres', 'code')) {
                Schema::table('matieres', function (Blueprint $table) {
                    $table->string('code')->unique()->after('libelle');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('matieres')) {
            Schema::table('matieres', function (Blueprint $table) {
                // Restaurer les colonnes en cas de rollback
                if (Schema::hasColumn('matieres', 'nom') && !Schema::hasColumn('matieres', 'name')) {
                    $table->renameColumn('nom', 'name');
                }
            });
        }
    }
};
