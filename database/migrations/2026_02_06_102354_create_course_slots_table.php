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
        Schema::create('course_slots', function (Blueprint $table) {
            $table->id();

            // Correction ici : On utilise 'academic_classes' au lieu de 'classes'
            $table->foreignId('class_id')->constrained('academic_classes');
            
            // On garde les autres tels quels (vérifie bien qu'ils existent aussi dans phpMyAdmin)
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('teacher_id')->constrained('users'); 
            $table->foreignId('room_id')->constrained('rooms');

            // Informations de temps
            $table->string('day'); // Lundi, Mardi, etc.
            $table->time('start_time'); // Heure de début
            $table->time('end_time');   // Heure de fin

            // Statut pour la gestion des modifications/annulations
            $table->string('status')->default('active'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_slots');
    }
};