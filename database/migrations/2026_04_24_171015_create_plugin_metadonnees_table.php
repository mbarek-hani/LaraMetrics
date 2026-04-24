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
        Schema::create('plugin_metadonnees', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('visite_id')
                ->constrained('visites')
                ->cascadeOnDelete();

            $table->string('plugin', 100);
            $table->string('cle', 100);
            $table->text('valeur')->nullable();

            $table->timestamp('cree_le')->useCurrent();

            $table->unique(['visite_id', 'plugin', 'cle']);
            $table->index(['plugin', 'cle']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_metadonnees');
    }
};
