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
        Schema::create('proformas', function (Blueprint $table) {
            $table->uuid('id_proforma')->primary();
            // version
            $table->integer('version_proforma')->default(1)->comment('Version du critère pour le suivi des modifications.');
            $table->string('numero_proforma',20)->comment(" Référence dans tous les documents. Généré automatiquement dans le controller");


            $table->date('date_proforma')->comment('Date de création de la proforma.');
            $table->date('date_debut_validee')->comment('Date du début valitéé.');
            $table->date('date_redemarrage')->comment('Date de redemarrage validée.');
            $table->date('date_fin_validee')->comment('Date  de fin validée.');


            $table->decimal('montant_retenu_proforma',15,2)->default(0)->comment('');
            $table->decimal('taxe_montant',15,2)->default(0)->comment("TVA par defaut 18% du montent retenu");
            $table->decimal('remise_montant_proforma',15,2)->default( 0)->comment('La rémise (reduction)');
            $table->string('modalite_proforma')->comment('Modalités de paiement spécifiées dans la proforma.');
            $table->decimal('penalites_proforma',15,2)->default(0)->comment('Pénalités associées à la proforma.');

            // motif_modification
            $table->text('motif_modification_proforma')->nullable()->comment("Pourquoi cette modification (ex: Demande du maître d'ouvrage, Erreur initiale, Force majeure).");

            $table->boolean('actif_proforma')->default(true)->comment('Permet de désactiver temporairement une proforma sans la supprimer.');


            $table->foreignUuid('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // Soft delete
            $table->softDeletes();
        });





        // Auto relation parent_id
        Schema::table('proformas', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->comment('Identifiant du critère parent, si applicable.')->references('id_proforma')->on('proformas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proformas');
    }
};
