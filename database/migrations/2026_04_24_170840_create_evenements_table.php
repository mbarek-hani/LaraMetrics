<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("evenements", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("site_id")
                ->constrained("sites")
                ->cascadeOnDelete();

            $table
                ->foreignId("visite_id")
                ->nullable()
                ->constrained("visites")
                ->nullOnDelete();

            $table->string("session_id", 64)->index();
            $table->string("type", 100)->index(); // "clic", "formulaire"
            $table->string("nom")->nullable(); // "Clic bouton inscription"
            $table->json("donnees")->nullable(); // {"valeur": 29.99}
            $table->string("chemin", 1024)->nullable();

            $table->timestamp("cree_le")->useCurrent();

            $table->index(["site_id", "type", "cree_le"]);
            $table->index(["site_id", "cree_le"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("evenements");
    }
};
