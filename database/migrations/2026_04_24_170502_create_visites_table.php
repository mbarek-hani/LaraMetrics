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
        Schema::create("visites", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("site_id")
                ->constrained("sites")
                ->cascadeOnDelete();

            // Session anonymisée : hash(IP + UserAgent + date + sel)
            // Jamais l'IP brute stockée
            $table->string("session_id", 64)->index();

            // ─── Page visitée ──────────────────────────────────────
            $table->string("url", 2048);
            $table->string("chemin", 1024); // ex: /blog/article
            $table->string("titre")->nullable();

            // ─── Provenance ────────────────────────────────────────
            $table->string("referent", 2048)->nullable();
            $table->string("referent_domaine")->nullable();
            $table->string("utm_source")->nullable();
            $table->string("utm_medium")->nullable();
            $table->string("utm_campagne")->nullable();

            // ─── Navigateur & Appareil ─────────────────────────────
            $table->string("navigateur")->nullable(); // "Chrome"
            $table->string("version_navigateur")->nullable(); // "120"
            $table->string("systeme_exploitation")->nullable(); // "Windows"
            $table
                ->enum("appareil", [
                    "ordinateur",
                    "mobile",
                    "tablette",
                    "inconnu",
                ])
                ->default("inconnu");

            // ─── Localisation (pays uniquement = vie privée) ───────
            $table->char("pays_code", 2)->nullable(); // "FR"
            $table->string("pays_nom")->nullable(); // "France"

            // ─── Métriques ─────────────────────────────────────────
            $table->unsignedSmallInteger("duree_session")->nullable(); // secondes
            $table->boolean("est_rebond")->default(true);
            $table->boolean("est_nouvelle_session")->default(true);

            // Pas de updated_at : les visites sont immuables
            $table->timestamp("cree_le")->useCurrent()->index();

            // ─── Index composites ──────────────────────────────────
            $table->index(["site_id", "cree_le"]);
            $table->index(["site_id", "chemin", "cree_le"]);
            $table->index(["site_id", "pays_code"]);
            $table->index(["site_id", "appareil"]);
            $table->index(["site_id", "referent_domaine"]);
            $table->index(["session_id", "cree_le"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("visites");
    }
};
