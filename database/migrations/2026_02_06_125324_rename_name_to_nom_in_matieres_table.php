<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('matieres', function (Blueprint $table) {

        $table->string('nom')->after('id'); // On ajoute 'nom'
    });

    // Optionnel : Si tu veux supprimer l'ancien nom
    Schema::table('matieres', function (Blueprint $table) {
        $table->dropColumn('name');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
        });
    }
};
