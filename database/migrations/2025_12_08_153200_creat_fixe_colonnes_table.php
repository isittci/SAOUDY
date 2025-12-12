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
                // Auto relation parent_id car à la mise à jour on écrase pas l'ancienne information on la desactive et on creer une nouvelle ligne
        Schema::table('types_appels_offres', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->comment('Identifiant du critère parent, si applicable.')->references('id_type_appel_offre')->on('types_appels_offres')->onDelete('set null');
            $table->integer('version_type_appel_offre')->default(1);
            $table->string('motif_modification_type_appel_offre', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types_appels_offres');
    }
};
