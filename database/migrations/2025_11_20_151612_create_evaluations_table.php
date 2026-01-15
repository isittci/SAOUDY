<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Structure des relations:
     * - Evaluation ←→ evaluations_lots_prestataires (pivot)
     * - La pivot contient: critere_evaluation_id, evaluation_id, prestataire_id
     * - CritereEvaluation appartient à un Lot (lot_id)
     * - Donc: Lot et Prestataire sont accessibles via la table pivot
     *
     * A chaque mise à jour, on crée une nouvelle version
     * Le numero_evaluation reste identique entre les versions
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            // ============================================
            // IDENTIFIANT PRIMAIRE
            // ============================================
            $table->uuid('id_evaluation')->primary();

            $table->foreignUuid('critere_evaluation_id')->nullable()->after('id_evaluation')->references('id_critere_evaluation')->on('criteres_evaluations')->onDelete('set null');

            // ============================================
            // VERSIONING
            // ============================================
            $table->integer('version')->default(1)->comment('Numéro de version de l\'évaluation');
            $table->boolean('is_current')->default(true)->comment('Indique si c\'est la version active/courante');


            // ============================================
            // INFORMATIONS D'ÉVALUATION
            // ============================================
            $table->string('numero_evaluation', 50)->comment('Numéro de l\'évaluation (identique entre versions) - Généré automatiquement');

            $table->dateTime('date_evaluation')->nullable()->comment('Date de réalisation de l\'évaluation');

            // ============================================
            // NOTES ET RÉSULTATS
            // ============================================
            $table->decimal('resultat_evaluation', 10, 2)->default(0)->comment('Note totale obtenue (somme des notes par critère)');

            $table->decimal('note_maximale', 10, 2)->default(0)->comment('Note maximale possible (somme des notes de référence des critères)');

            $table->decimal('pourcentage_final', 5, 2)->default(0)->comment('Pourcentage final (resultat_evaluation / note_maximale * 100)');

            $table->integer('rang')->nullable()->comment('Rang parmi tous les prestataires évalués pour ce lot');

            // ============================================
            // RESPONSABLES (JSON)
            // ============================================
            $table->json('respo_technique_evaluation')->nullable()->comment('Responsable technique: {nom_complet, email, telephone}');

            $table->json('superviseur_evaluation')->nullable()->comment('Superviseur: {nom_complet, email, telephone}');

            $table->json('evalue_par')->nullable()->comment('Évaluateur: {nom_complet, email, telephone}');

            // ============================================
            // STATUT
            // ============================================
            $table->smallInteger('statut_evaluation')->default(0)->comment('0=En attente, 1=En cours, 2=Terminée, 3=Validée, 4=Rejetée');

            // ============================================
            // COMMENTAIRES ET RECOMMANDATIONS
            // ============================================
            $table->text('commentaire_general')->nullable()->comment('Commentaire général sur l\'évaluation');

            $table->text('recommandation')->nullable()->comment('Recommandation pour l\'attribution');

            // ============================================
            // DOCUMENTS
            // ============================================
            $table->json('documents_evalues')->nullable()->comment('Liste des documents consultés pour l\'évaluation');

            // ============================================
            // ÉVALUATEURS ET VALIDATION
            // ============================================
            $table->foreignUuid('evaluateur_principal_id')->nullable()->comment('Identifiant de l\'évaluateur principal')->references('id')->on('users')->onDelete('set null');

            $table->dateTime('date_validation')->nullable()->comment('Date de validation de l\'évaluation');

            $table->text('motif_validation')->nullable()->comment('Motif en cas de validation de l\'évaluation');

            $table->foreignUuid('valide_par')->nullable()->comment('Identifiant de l\'utilisateur ayant validé')->references('id')->on('users')->onDelete('set null');

            $table->dateTime('date_rejet')->nullable()->comment('Date du rejet de l\'évaluation');

            $table->text('motif_rejet')->nullable()->comment('Motif en cas de rejet de l\'évaluation');

            $table->foreignUuid('rejete_par')->nullable()->comment('Identifiant de l\'utilisateur ayant rejeté')->references('id')->on('users')->onDelete('set null');

            // ============================================
            // AUDIT
            // ============================================
            $table->foreignUuid('created_by')->nullable()->comment('Identifiant de l\'utilisateur créateur')->references('id')->on('users')->onDelete('set null');

            $table->foreignUuid('updated_by')->nullable()->comment('Identifiant de l\'utilisateur modificateur')->references('id')->on('users')->onDelete('set null');

            $table->foreignUuid('deleted_by')->nullable()->comment('Identifiant de l\'utilisateur suppresseur')->references('id')->on('users')->onDelete('set null');

            // ============================================
            // TIMESTAMPS
            // ============================================
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('Date de création');

            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('Date de mise à jour');

            // ============================================
            // SOFT DELETE
            // ============================================
            $table->softDeletes()->comment('Date de suppression logique');

            // ============================================
            // INDEX POUR PERFORMANCES
            // ============================================
            $table->index('statut_evaluation', 'idx_evaluation_statut');
            $table->index('rang', 'idx_evaluation_rang');
            $table->index(['numero_evaluation', 'is_current'], 'idx_evaluation_numero_current');
            $table->index('is_current', 'idx_evaluation_is_current');


        });

        // Auto-relation pour traçabilité des versions
        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignUuid('evaluation_parent_id')->nullable()->after('id_evaluation')->comment('Évaluation parente (chaînage des versions)')->references('id_evaluation')->on('evaluations')->onDelete('set null');

            $table->index('evaluation_parent_id', 'idx_evaluation_parent');
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
