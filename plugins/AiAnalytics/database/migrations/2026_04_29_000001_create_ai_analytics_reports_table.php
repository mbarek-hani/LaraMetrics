<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analytics_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                  ->constrained('sites')
                  ->cascadeOnDelete();

            $table->unsignedTinyInteger('score');
            $table->text('resume');
            $table->json('points_cles');
            $table->json('recommandations');
            $table->json('tendances');
            $table->string('fournisseur', 50);
            $table->string('modele', 100);

            $table->timestamp('cree_le')->useCurrent();

            $table->index(['site_id', 'cree_le']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analytics_reports');
    }
};
