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
        Schema::create('situations_financieres', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id_situation_financiere')->primary();

            // UUID Foreign key-like field
            $table->foreignUuid('prestataire_id')->references('id_prestataire')->on('prestataires')->onDelete('set null')->comment('Identifiant du prestataire associé à la situation financière.');

            // Strings
            $table->string('exercice_fiscal_situation_financiere', 36)->nullable();

            // Decimal (montants / ratios)
            $table->decimal('chiffre_affaire_situation_financiere', 20, 2)->nullable();
            $table->decimal('fonds_propres_situation_financiere', 20, 2)->nullable();
            $table->decimal('capacite_emprunt_situation_financiere', 20, 2)->nullable();
            $table->decimal('ratio_solvabilite_situation_financiere', 20, 2)->nullable();
            $table->decimal('ratio_liquidite_situation_financiere', 20, 2)->nullable();
            $table->decimal('resultat_net_situation_financiere', 20, 2)->nullable();
            $table->decimal('total_actif_situation_financiere', 20, 2)->nullable();
            $table->decimal('total_passif_situation_financiere', 20, 2)->nullable();

            // Text
            $table->text('observations_situation_financiere')->nullable();

            // DateTime
            // $table->dateTime('date_evaluation_situation_financiere')->nullable();

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
        Schema::dropIfExists('situations_financieres');
    }
};
