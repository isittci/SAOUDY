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
        Schema::create('capacites_techniques', function (Blueprint $table) {
            // Primary key
            $table->uuid('id_capacite_technique')->primary();

            // Foreign keys / UUID fields
            // $table->uuid('prestataire_id')->nullable();
            $table->foreignUuid('prestataire_id')->nullable()->references('id_prestataire')->on('prestataires')->onDelete('set null');

            // Integer fields
            $table->integer('effectif_permanent_capacite_technique')->nullable();
            $table->integer('effectif_temporaire_capacite_technique')->nullable();

            // Text fields
            $table->text('moyens_materiels_capacite_technique')->nullable();
            $table->text('certifications_capacite_technique')->nullable();
            $table->text('agrements_capacite_technique')->nullable();
            $table->string('references_capacite_technique', 10)->nullable();
            $table->string('competences_cles_capacite_technique', 25)->nullable();
            $table->text('domaines_expertise_capacite_technique')->nullable();

            // Date & datetime fields
            // $table->dateTime('date_evaluation_capacite_technique')->nullable();

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
        Schema::dropIfExists('capacites_techniques');
    }
};
