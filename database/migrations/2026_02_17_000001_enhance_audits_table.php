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
        Schema::table('audits', function (Blueprint $table) {
            // Add soft deletes support
            $table->softDeletes()->after('updated_at');
            
            // Add additional metadata columns
            $table->string('change_type')->nullable()->comment('Type of change: field, relationship, meta')->after('action');
            $table->json('metadata')->nullable()->comment('Additional metadata about the change')->after('change_type');
            
            // Improve indexing for better performance
            $table->index(['model_type', 'model_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('field_name');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['change_type', 'metadata']);
            
            // Drop indexes
            $table->dropIndex(['model_type', 'model_id', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['field_name']);
            $table->dropIndex(['action']);
        });
    }
};
