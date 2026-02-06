<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->dropColumn('credits');
        });
    }

    public function down()
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->integer('credits')->default(0);
        });
    }
};