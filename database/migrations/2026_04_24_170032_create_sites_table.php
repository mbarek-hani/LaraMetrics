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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('domaine')->unique(); // ex: "monsite.com"
            $table->string('token_tracking', 64)->unique(); // Token du script JS
            $table->boolean('actif')->default(true);
            $table->boolean('ignorer_bots')->default(true);
            $table->boolean('ignorer_dnt')->default(false);
            $table->timestamps();

            $table->index('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
