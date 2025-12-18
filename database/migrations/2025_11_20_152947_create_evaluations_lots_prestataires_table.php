<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cette table pivot stocke les notes attribuées pour chaque critère d'évaluation
     * par prestataire dans le cadre d'une évaluation.
     *
     * Relations:
     * - critere_evaluation_id → criteres_evaluations (contient lot_id)
     * - evaluation_id → evaluations
     * - prestataire_id → prestataires
     */
    public function up(): void
    {
        Schema::create('evaluations_lots_prestataires', function (Blueprint $table) {
            // ============================================
            // CLÉ PRIMAIRE UUID (permet historique)
            // ============================================
            $table->uuid('id_evaluation_critere')->primary();

            // ============================================
            // RELATIONS PRINCIPALES
            // ============================================
            $table->foreignUuid('critere_evaluation_id')->comment('Critère d\'évaluation')->references('id_critere_evaluation')->on('criteres_evaluations')->onDelete('cascade');

            $table->foreignUuid('evaluation_id')->comment('Évaluation parente')->references('id_evaluation')->on('evaluations')->onDelete('cascade');

            $table->foreignUuid('prestataire_id')->comment('Prestataire évalué')->references('id_prestataire')->on('prestataires')->onDelete('cascade');

            // ============================================
            // NOTES ET RÉSULTATS
            // ============================================
            $table->decimal('note_obtenue', 8, 2)->default(0)->comment('Note attribuée par l\'évaluateur');

            $table->decimal('note_reference', 8, 2)->default(0)->comment('Note de référence du critère (copie pour historique)');

            $table->decimal('note_finale', 8, 2)->default(0)->comment('Note finale calculée (note_obtenue)');

            $table->decimal('pourcentage', 5, 2)->default(0)->comment('Pourcentage (note_obtenue / note_reference * 100)');

            // ============================================
            // CONFORMITÉ ET JUSTIFICATION
            // ============================================
            $table->boolean('conforme')->default(false)->comment('Le critère est-il conforme?');

            $table->text('observation')->nullable()->comment('Observation sur ce critère');

            $table->text('justification')->nullable()->comment('Justification de la note attribuée');

            // ============================================
            // DOCUMENTS
            // ============================================
            $table->json('documents_fournis')->nullable()->comment('Documents fournis pour ce critère');

            // ============================================
            // AUDIT
            // ============================================
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')->onDelete('set null');

            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');

            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');

            // ============================================
            // TIMESTAMPS
            // ============================================
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // ============================================
            // SOFT DELETE
            // ============================================
            $table->softDeletes();

            // ============================================
            // INDEX
            // ============================================
            $table->unique(
                ['critere_evaluation_id', 'evaluation_id', 'prestataire_id'],
                'idx_unique_eval_critere_prestataire'
            );
            $table->index('evaluation_id', 'idx_elp_evaluation');
            $table->index('prestataire_id', 'idx_elp_prestataire');
            $table->index('critere_evaluation_id', 'idx_elp_critere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations_lots_prestataires');
    }
};
