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
        Schema::create('caracteristiques_appels_offres', function (Blueprint $table) {
            $table->uuid('id_caracteristique_appel_offre')->primary();

            $table->foreignUuid('appel_offre_id')->comment('Identifiant unique de l\'appel d\'offres.')->references('id_appel_offre')->on('appels_offres')->onDelete('cascade');
            // $table->foreignUuid('parent_id')->comment('Identifiant du critère parent, si applicable.')->references('id')->on('caracteristique_appel_offres')->onDelete('set null');
            $table->integer('version_caracteristique_appel_offre')->default(1)->comment('Version du critère pour le suivi des modifications.');
            $table->date('date_demarrage_prevue_caracteristique_appel_offre')->nullable()->comment('Date prévue de démarrage des travaux.');
            $table->integer('duree_estimee_jours_caracteristique_appel_offre')->nullable()->comment('Durée estimée des travaux en jours.');
            $table->date('date_livraison_previsionnelle_caracteristique_appel_offre')->nullable()->comment('Date prévue de livraison des travaux.');
            $table->string('lieu_execution_caracteristique_appel_offre', 255)->nullable()->comment('Lieu prévu pour l\'exécution des travaux.');
            $table->decimal('penalites_retard_journalier_caracteristique_appel_offre', 15,2)->nullable()->comment('Montant de pénalité par jour de retard (ex: 50 000 FCFA/jour). Dissuasif.');
            //montant_garantie
            $table->decimal('montant_garantie_caracteristique_appel_offre',15,2)->nullable()->comment("Caution de bonne exécution (souvent 5-10% du marché).");
            //delai_garantie_jours
            $table->decimal("delai_garantie_jours_caracteristique_appel_offre",15,2)->nullable()->comment("Durée de garantie après réception (ex: 365 jours).");
            //    conditions_paiement
            $table->text("conditions_paiement_caracteristique_appel_offre")->nullable()->comment("Modalités (ex: 30% avance, 40% mi-parcours, 30% livraison).");
            $table->text('modalites_execution_caracteristique_appel_offre')->nullable()->comment('Exigences particulières.');
            $table->text('documents_requis_caracteristique_appel_offre')->nullable()->comment("Liste des pièces à fournir (ex: [Attestation fiscale, Assurance, Caution]).");

            $table->boolean('is_active')->default(true)->comment('Indique si cette version est active ou obsolète.');
            $table->text('autres_informations_caracteristique_appel_offre')->nullable()->comment("Infos diverses.");

            $table->text('motif_modification_caracteristique_appel_offre')->nullable()->comment("Pourquoi cette modification (ex: Demande du maître d'ouvrage, Erreur initiale, Force majeure).");

            $table->foreignUuid('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // Soft delete
            $table->softDeletes();
        });

        // Auto relation parent_id car à la mise à jour on écrase pas l'ancienne information on la desactive et on creer une nouvelle ligne
        Schema::table('caracteristiques_appels_offres', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->comment('Identifiant du critère parent, si applicable.')->references('id_caracteristique_appel_offre')->on('caracteristiques_appels_offres')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caracteristiques_appels_offres');
    }
};
