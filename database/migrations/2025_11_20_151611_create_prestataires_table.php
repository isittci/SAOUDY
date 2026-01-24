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
        Schema::create('prestataires', function (Blueprint $table) {
            $table->uuid('id_prestataire')->primary();

            // Informations générales
            $table->string('raison_sociale_prestataire', 255);
            $table->string('numero_identification_prestataire', 25);
            $table->string('email_prestataire', 255);
            $table->string('numero_cc_prestataire', 50)->comment('Numéro de la carte de contribuable');
            $table->string('numero_rccm_prestataire', 50)->comment('Numéro du Registre de Commerce et du Crédit Mobilier');
            $table->string('telephone_principal_prestataire', 20)->comment('Numéro de téléphone principal du prestataire');
            $table->string('telephone_secondaire_prestataire', 20)->nullable()->comment('Numéro de téléphone secondaire du prestataire');

            // Contacts
            $table->text('adresse_prestataire')->comment('Adresse physique du prestataire');
            $table->string('ville_prestataire', 50);
            // $table->string('pays_prestataire', 50);
            $table->foreignUuid('pays_prestataire')->references('id')->on('pays')->onDelete('set null');

            // Représentant légal
            $table->json('representant_legal_prestataire')->comment('Informations sur le représentant légal au format JSON (tableau de represents): id, statut, nom, prenoms, contact, email, nationalité, pays, adresse, profession, date_naissance, lieu_naissance, numero_piece_identite, type_piece_identite, date_delivrance, lieu_delivrance, date_expiration.');

            // Statut
            $table->boolean('statut_prestataire')->default(false);

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
        Schema::dropIfExists('prestataires');
    }
};
