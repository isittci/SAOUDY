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
        Schema::create('appels_offres', function (Blueprint $table) {
            $table->uuid('id_appel_offre')->primary();

            $table->foreignUuid('type_appel_offre_id')->comment('Identifiant unique du type.')->references('id_type_appel_offre')->on('types_appels_offres')->onDelete('restrict');
            $table->string('numero_appel_offre',20)->comment("Numéro officiel (ex: AOT-2025-045). Référence dans tous les documents.");
            $table->string("libelle_critere_appel_offre",   160)->comment("Nom du lot (ex: Gros œuvre, Électricité, Plomberie).");
            $table->text("objet_critere_appel_offre")->nullable()->comment("Description officielle de ce qui est demandé.");
            $table->decimal('montant_global_appel_offre', 15, 2)->comment('Montant total estimé pour cet appel d\'offres.');
         
            // statut_eva
            $table->enum('statut_evaluation_critere_appel_offre', [1, 0])->default(0)->comment('Statut actuel de l\'évaluation des offres. Pour savoir si actif ou non');

            $table->smallInteger('etat_appel_offre')
                ->default(0)
                ->comment('0=En attente, 1=En cours, 2=Terminé, 3=Clôturé');
            //conditions_participation
            $table->text("conditions_participation_critere_appel_offre")->nullable()->comment("Conditions requises pour participer à cet appel d'offres.");
            //criteres_selection
            $table->text("criteres_selection_critere_appel_offre")->nullable()->comment("Critères utilisés pour évaluer les offres reçues.");

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
        Schema::dropIfExists('appels_offres');
    }
};
