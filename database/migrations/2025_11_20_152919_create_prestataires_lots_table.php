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
            // Clé primaire UUID (permet l'historique complet des attributions)
            $table->uuid('id_attribution')->primary()->comment('Identifiant unique de l\'attribution');

            // Relations principales
            $table->foreignUuid('prestataire_id')->comment('Prestataire attributaire')->references('id_prestataire')->on('prestataires')->onDelete('cascade');
            $table->foreignUuid('lot_id')->comment('Lot concerné')->references('id_lot')->on('lots')->onDelete('cascade');
            $table->foreignUuid('proforma_id')->comment('Proforma associée')->references('id_proforma')->on('proformas')->onDelete('cascade');

            // Versionnement et traçabilité
            $table->integer('version_attribution')->default(1)->comment('Version de l\'attribution (incrémente à chaque réattribution)');
            $table->boolean('is_active')->default(true)->comment('TRUE = attribution active, FALSE = historique');
            $table->string('numero_attribution', 30)->nullable()->comment('Numéro unique (ex: ATT-2025-001)');

            // Dates d'attribution
            $table->date('date_attribution')->nullable()->comment('Date officielle d\'attribution');
            $table->date('date_debut_prevue')->nullable()->comment('Date de début prévue');
            $table->date('date_fin_prevue')->nullable()->comment('Date de fin prévue');
            $table->date('date_debut_reelle')->nullable()->comment('Date réelle de début');
            $table->date('date_fin_reelle')->nullable()->comment('Date réelle de fin');

            $table->date('date_effective_fin')
                ->nullable()
                ->comment('Date effective de fin');

            // Statut: 0=En attente, 1=Attribué, 2=Suspendu, 3=Retiré, 4=Terminé, 5=Annulé
            $table->smallInteger('statut_attribution')->default(0)->comment('0=En attente, 1=Attribué, 2=Suspendu, 3=Retiré, 4=Terminé, 5=Annulé');

            // Suspension
            $table->text('motif_suspension')->nullable()->comment('Raison de la suspension');
            $table->dateTime('date_suspension')->nullable()->comment('Date de suspension');
            $table->date('date_reprise_prevue')->nullable()->comment('Date prévue de reprise');
            $table->date('date_reprise_reelle')->nullable()->comment('Date réelle de reprise');

            // Retrait
            $table->text('motif_retrait')->nullable()->comment('Raison du retrait');
            $table->dateTime('date_retrait')->nullable()->comment('Date du retrait');
            $table->enum('type_retrait', ['volontaire', 'force', 'resiliation', 'abandon'])->nullable()->comment('Type de retrait');

            // Pénalités et retards
            $table->integer('jours_retard')->default(0)->comment('Jours de retard accumulés');

            // Avancement et finances
            $table->decimal('pourcentage_avancement', 5, 2)->default(0)->comment('Avancement (0-100%)');
            $table->decimal('montant_engage', 15, 2)->default(0)->comment('Montant engagé');
            $table->decimal('montant_paye', 15, 2)->default(0)->comment('Montant payé');

            // Notes
            $table->text('observations')->nullable()->comment('Observations');
            $table->text('conditions_particulieres')->nullable()->comment('Conditions particulières');

            // Audit
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->softDeletes();

            // Index
            $table->index(['lot_id', 'is_active'], 'idx_lot_active');
            $table->index(['lot_id', 'statut_attribution'], 'idx_lot_statut');
            $table->index(['prestataire_id', 'statut_attribution'], 'idx_prestataire_statut');
            $table->index(['prestataire_id', 'is_active'], 'idx_prestataire_active');
            $table->index('numero_attribution', 'idx_numero_attribution');
        });

        // Auto-relation pour traçabilité des réattributions
        Schema::table('prestataires_lots', function (Blueprint $table) {
            $table->foreignUuid('parent_attribution_id')->nullable()->after('id_attribution')->comment('Attribution précédente (chaînage)')->references('id_attribution')->on('prestataires_lots')->onDelete('set null');
        });


        Schema::table('evaluations', function (Blueprint $table) {

            // ============================================
            // RELATION AVEC L'ATTRIBUTION
            // ============================================
            $table->foreignUuid('attribution_id')->comment('Référence vers l\'attribution (prestataires_lots)')->references('id_attribution')->on('prestataires_lots')->onDelete('cascade');

            $table->index('attribution_id', 'idx_evaluation_attribution');
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
