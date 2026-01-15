<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {

            // Identifiant primaire UUID
            $table->uuid('id_lot')->primary();

            // Autres colonnes UUID
            $table->foreignUuid('appel_offre_id')->comment('Identifiant de l\'appel d\'offres associé.')->references('id_appel_offre')->on('appels_offres')->onDelete('cascade');

            // Strings
            $table->string('numero', 35);
            $table->string('libelle', 160)->nullable();
            
            // Textes
            $table->text('description_critere')->nullable();
            $table->text('specifications_techniques')->nullable();
            $table->text('motif_retrait')->nullable();
            $table->integer('version_lot')->default(1);

            // Dates et DateTime
            $table->date('date_attribution')->nullable();
            $table->dateTime('date_debut_prevue')->nullable();
            $table->dateTime('date_fin_prevue')->nullable();
            $table->date('date_retrait')->nullable();

            // Booléens
            $table->enum('attribution_lot', [0, 1])->default(0);
            $table->enum('statut_lot', [0, 1])->nullable();

            // Nombres
            $table->decimal('taux_penalites', 5, 2)->nullable();
            $table->decimal('budget_lot', 15, 2)->nullable();

            $table->smallInteger('statut_retrait')->nullable();

            // Audit
            $table->foreignUuid('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            $table->index(['id_lot', 'numero']);
            $table->index(['id_lot', 'version_lot']);

            // Soft delete
            $table->softDeletes();
        });

        // Auto relation parente entre lots: car à la mise à jour on écrase pas l'ancienne information on la desactive et on creer une nouvelle ligne
        Schema::table('lots', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->comment('Identifiant du lot principal associé.')->references('id_lot')->on('lots')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
