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
        Schema::create('banques', function (Blueprint $table) {
            // Identifiant primaire UUID
            $table->uuid('id_banque')->primary()->comment('Identifiant unique de la banque.');

            // Référence au partenaire
            $table->foreignUuid('prestataire_id')->comment('Identifiant du prestataire associé.')->references('id_prestataire')->on('prestataires')->onDelete('cascade');

            // Informations bancaires
            $table->string('nom_banque', 150)->nullable()->comment('Nom de la banque');
            $table->string('code_banque', 25)->unique()->comment('Code banque');
            $table->string('numero_compte_banque', 25)->unique()->nullable()->comment('Numéro de compte bancaire');
            $table->string('code_guichet_banque', 25)->nullable()->comment('Code guichet bancaire');
            $table->string('cle_rib_banque', 25)->nullable()->comment('Clé RIB (Relevé d\'Identité Bancaire)');
            $table->string('iban_banque', 25)->nullable()->comment('International Bank Account Number');
            $table->string('swift_bic_banque', 25)->nullable()->comment('SWIFT/BIC code');
            $table->string('titulaire_compte_banque', 50)->nullable()->comment('Nom du titulaire du compte bancaire');

            // Statut
            $table->boolean('actif_banque')->default(true)->nullable()->comment('Permet de désactiver temporairement une banque sans la supprimer.');

            // Audit
            $table->foreignUuid('created_by')->comment('Identifiant de l\'utilisateur ayant créé la banque.')->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->comment('Identifiant de l\'utilisateur ayant mis à jour la banque.')->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->comment('Identifiant de l\'utilisateur ayant supprimé la banque.')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('Date de création de la banque.');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('Date de la dernière mise à jour de la banque.');

            // Soft delete
            $table->softDeletes()->comment('Date de suppression de la banque.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banques');
    }
};
