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
        Schema::create('evaluations_prestataires', function (Blueprint $table) {
            // Primary key
            $table->uuid('id_evaluation_prestataire')->primary();

            // Foreign key-like UUID
            $table->foreignUuid('prestataire_id')->references('id_prestataire')->on('prestataires')->onDelete('set null');

            // Decimal fields
            $table->decimal('note_qualification_evaluation_prestataire', 20, 2)->nullable();
            $table->dateTime('date_derniere_evaluation_evaluation_prestataire')->nullable();
            $table->decimal('nombre_contrats_executes_evaluation_prestataire', 20, 2)->nullable();
            $table->decimal('taux_respect_delais_evaluation_prestataire', 20, 2)->nullable();
            $table->decimal('taux_qualite_evaluation_prestataire', 20, 2)->nullable();
            $table->decimal('nombre_litiges_evaluation_prestataire', 20, 2)->nullable();

            // String field
            $table->string('liste_statut_evaluation_prestataire', 25)->nullable();

            // Date & Heure
            $table->dateTime('date_mise_en_liste_evaluation_prestataire')->nullable();
            $table->dateTime('date_fin_sanction_evaluation_prestataire')->nullable();

            // Text fields
            $table->text('motif_liste_noire_evaluation_prestataire')->nullable();
            $table->text('commentaire_evaluation_prestataire')->nullable();

            // Audit
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
        Schema::dropIfExists('evaluations_prestataires');
    }
};
