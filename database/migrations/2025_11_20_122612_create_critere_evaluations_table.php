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
        Schema::create('criteres_evaluations', function (Blueprint $table) {
            // Identifiant primaire UUID
            $table->uuid('id_critere_evaluation')->primary();

            // Relation vers LOT (UUID)
            $table->foreignUuid('lot_id')->comment('Identifiant du lot associé.')->references('id_lot')->on('lots')->onDelete('cascade');

            // Autres champs
            $table->string('numero_critere_evaluation', 20);
            $table->string('libelle_critere_evaluation', 160);
            $table->text('description_critere_evaluation')->nullable();

            // Si tu veux une précision exacte, dis-moi.
            $table->decimal('note_reference_critere_evaluation', 8, 2)->comment("La note maximale qu'on peut obtenir")->default(100);

            $table->enum('statut_critere_evaluation', [0, 1])->default(1);

            // Entier (integer)
            $table->integer('ordre_execution_critere_evaluation')->nullable();

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
        Schema::dropIfExists('criteres_evaluations');
    }
};
