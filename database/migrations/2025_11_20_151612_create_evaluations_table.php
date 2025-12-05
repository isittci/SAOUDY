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
        Schema::create('evaluations', function (Blueprint $table) {
            // Identifiant primaire UUID
            $table->uuid('id_evaluation')->primary();

            // Relations
            $table->foreignUuid('appel_offre_id')
                ->comment('Identifiant de l\'appel d\'offres.')
                ->references('id_appel_offre')
                ->on('appels_offres')
                ->onDelete('cascade');

            $table->foreignUuid('lot_id')
                ->comment('Identifiant du lot évalué.')
                ->references('id_lot')
                ->on('lots')
                ->onDelete('cascade');

            $table->foreignUuid('prestataire_id')
                ->comment('Identifiant du prestataire évalué.')
                ->references('id_prestataire')
                ->on('prestataires')
                ->onDelete('cascade');

            // Informations d'évaluation
            $table->string('numero_evaluation', 50)
                ->unique()
                ->comment('Numéro unique de l\'évaluation: generer par le système de façon automatique');

            $table->dateTime('date_evaluation')
                ->nullable()
                ->comment('Date de réalisation de l\'évaluation');

            $table->smallInteger('statut_evaluation')
                ->default(0)
                ->comment('0=En attente, 1=En cours, 2=Terminée, 3=Validée, 4=Rejetée');

            // Notes et résultats
            $table->decimal('note_totale', 10, 2)
                ->default(0)
                ->comment('Note totale obtenue');

            $table->decimal('note_maximale', 10, 2)
                ->default(0)
                ->comment('Note maximale possible');

            $table->decimal('pourcentage_final', 5, 2)
                ->default(0)
                ->comment('Pourcentage final (note_totale/note_maximale * 100)');

            $table->integer('rang')
                ->nullable()
                ->comment('Rang parmi tous les prestataires évalués pour ce lot');

            // Commentaires et recommandations
            $table->text('commentaire_general')
                ->nullable()
                ->comment('Commentaire général sur l\'évaluation');

            $table->text('recommandation')
                ->nullable()
                ->comment('Recommandation pour l\'attribution');

            // Documents
            $table->json('documents_evalues')
                ->nullable()
                ->comment('Liste des documents consultés pour l\'évaluation');

            // Évaluateurs et validation
            $table->foreignUuid('evaluateur_principal_id')
                ->nullable()
                ->comment('Identifiant de l\'évaluateur principal')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->dateTime('date_validation')
                ->nullable()
                ->comment('Date de validation de l\'évaluation');

            $table->foreignUuid('valide_par')
                ->nullable()
                ->comment('Identifiant de l\'utilisateur ayant validé')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->text('motif_rejet')
                ->nullable()
                ->comment('Motif en cas de rejet de l\'évaluation');

            // Audit
            $table->foreignUuid('created_by')
                ->comment('Identifiant de l\'utilisateur créateur')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreignUuid('updated_by')
                ->nullable()
                ->comment('Identifiant de l\'utilisateur modificateur')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreignUuid('deleted_by')
                ->nullable()
                ->comment('Identifiant de l\'utilisateur suppresseur')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date de création');

            $table->timestamp('updated_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date de mise à jour');

            // Soft delete
            $table->softDeletes()
                ->comment('Date de suppression logique');

            // Index pour améliorer les performances
            $table->index(['appel_offre_id', 'lot_id'], 'idx_evaluation_ao_lot');
            $table->index(['prestataire_id', 'statut_evaluation'], 'idx_evaluation_prestataire');
            $table->index('rang', 'idx_evaluation_rang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
