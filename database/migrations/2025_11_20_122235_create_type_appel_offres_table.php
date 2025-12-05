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
        Schema::create('types_appels_offres', function (Blueprint $table) {
            $table->uuid('id_type_appel_offre')->primary();
            $table->string('libelle_type_appel_offre', 160)->comment('Libellé du type d\'appel d\'offres');
            $table->string('code_type_appel_offre',10)->comment("Code court (ex: AOT, AOS, AOF). Utilisé dans les numéros d'AO.");
            $table->decimal('valeur_minimuim_type_appel_offre', 15, 2)->comment('Valeur minimale associée au type d\'appel d\'offres');
            $table->decimal('valeur_maximuim_type_appel_offre',15,2)->comment('Valeur maximale associée au type d\'appel d\'offres');

            $table->text('description_critere_type_appel_offre')->nullable()->comment('Description détaillée du type d\'appel d\'offres');
            $table->boolean('actif_type_appel_offre')->default(true)->comment('Permet de désactiver temporairement un type sans le supprimer.');

            $table->foreignUuid('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // Soft delete
            $table->softDeletes();
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
