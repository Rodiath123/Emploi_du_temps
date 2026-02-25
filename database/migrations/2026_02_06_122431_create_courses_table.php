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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('subject');     // Nom de la matière
            $table->string('day');         // Jour (Lundi, Mardi...)
            $table->string('room');        // Salle (1A, OB, 3A)
            $table->time('start_time');    // Heure de début
            $table->time('end_time');      // Heure de fin
            $table->string('class_name');  // Classe (L1 IRT, L2 IRT, L3AL)
            
            // --- AJOUT POUR LE MODULE SUIVANT (Suivi des modifications) ---
            $table->string('status')->default('PLANNED'); 
            // --------------------------------------------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};