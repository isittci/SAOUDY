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

        Schema::create('prestataires_lots', function (Blueprint $table) {
            // Relations (clés composites)
            $table->uuid('prestataire_id');
            $table->uuid('lot_id');
            $table->uuid('proforma_id');

            // Foreign keys
            $table->foreign('prestataire_id')
                ->references('id_prestataire')
                ->on('prestataires')
                ->onDelete('cascade');

            $table->foreign('lot_id')
                ->references('id_lot')
                ->on('lots')
                ->onDelete('cascade');

            $table->foreign('proforma_id')
                ->references('id_proforma')
                ->on('proformas')
                ->onDelete('cascade');

            // Dates d'exécution réelles
            $table->date('date_debut_reelle')
                ->nullable()
                ->comment('Date réelle de début des travaux');

            $table->date('date_fin_reelle')
                ->nullable()
                ->comment('Date réelle de fin des travaux');

            // Statut de l'attribution
            $table->smallInteger('statut_attribution')
                ->default(0)
                ->comment('0=En attente, 1=Attribué, 2=Suspendu, 3=Retiré, 4=Terminé');

            // Informations de suspension
            $table->text('motif_suspension')
                ->nullable()
                ->comment('Raison de la suspension des travaux');

            $table->dateTime('date_suspension')
                ->nullable()
                ->comment('Date de suspension');

            // Informations de retrait
            $table->text('motif_retrait')
                ->nullable()
                ->comment('Raison du retrait du lot au prestataire');

            $table->dateTime('date_retrait')
                ->nullable()
                ->comment('Date de retrait');

            // Gestion des retards et pénalités
            $table->integer('jours_retard')
                ->default(0)
                ->comment('Nombre de jours de retard accumulés');

            $table->decimal('penalites_appliquees', 15, 2)
                ->default(0)
                ->comment('Montant total des pénalités appliquées');

            // Suivi de l'avancement
            $table->decimal('pourcentage_avancement', 5, 2)
                ->default(0)
                ->comment('Pourcentage d\'avancement des travaux (0-100)');

            $table->text('observations')
                ->nullable()
                ->comment('Observations et notes sur l\'exécution');

            // Audit
            $table->foreignUuid('created_by')
                ->nullable()
                ->comment('Utilisateur ayant créé l\'attribution')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreignUuid('updated_by')
                ->nullable()
                ->comment('Utilisateur ayant mis à jour')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreignUuid('deleted_by')
                ->nullable()
                ->comment('Utilisateur ayant retiré/supprimé')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date de création de l\'attribution');

            $table->timestamp('updated_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date de dernière mise à jour');

            // Soft delete pour garder l'historique
            $table->softDeletes()
                ->comment('Date de suppression logique (retrait)');

            // Définir la clé primaire composite
            $table->primary(['prestataire_id', 'lot_id', 'proforma_id'], 'pk_prestataires_lots');

            // Index pour améliorer les performances
            $table->index(['lot_id', 'statut_attribution'], 'prestataires_lots_idx_lot_statut');
            $table->index(['prestataire_id', 'statut_attribution'], 'prestataires_lots_idx_prestataire_statut');
            $table->index('date_debut_reelle', 'prestataires_lots_idx_date_debut');
            $table->index('date_fin_reelle', 'prestataires_lots_idx_date_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestataires_lots');
    }
};
