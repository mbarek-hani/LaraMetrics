<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analytics_reports', function (Blueprint $table) {
            $table->date('date_debut')->nullable()->after('modele');
            $table->date('date_fin')->nullable()->after('date_debut');
        });
    }

    public function down(): void
    {
        Schema::table('ai_analytics_reports', function (Blueprint $table) {
            $table->dropColumn(['date_debut', 'date_fin']);
        });
    }
};
