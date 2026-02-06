<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Table des Classes
        Schema::create('academic_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('code')->unique(); 
            $table->string('level'); // ex: L1, L2, M1
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Table des Matières
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('credits')->default(0);
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();
        });

        // Table des Salles
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->integer('capacity');
            $table->string('building')->nullable();
            $table->enum('status', ['available', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('academic_classes');
    }
};