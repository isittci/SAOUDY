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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id_document')->primary();

            // Relation avec LOT
            $table->foreignUuid('lot_id')->nullable()->references('id_lot')->on('lots')->onDelete('set null');

            // Informations document
            $table->string('type_document', 255)->nullable();
            $table->string('titre_document', 255)->nullable();
            $table->string('fichier_nom_document', 255)->nullable();

            // Path du fichier
            $table->text('fichier_path_document')->nullable();

            // Infos fichier
            $table->string('fichier_type_document', 255)->nullable();
            $table->decimal('fichier_taille_document', 10, 2)->nullable(); // précision non fournie → standardisée

            // Description
            $table->string('description_document', 120)->nullable();

            // Dates
            $table->dateTime('date_document')->nullable();
            $table->smallInteger('version_document')->nullable();

            // Validation
            $table->boolean('est_valide_document')->nullable();
            $table->uuid('valide_par')->nullable();
            $table->dateTime('valide_at')->nullable();

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
        Schema::dropIfExists('documents');
    }
};
