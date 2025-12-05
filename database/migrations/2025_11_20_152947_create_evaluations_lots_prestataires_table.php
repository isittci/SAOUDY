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
        Schema::create('evaluations_lots_prestataires', function (Blueprint $table) {
            // UUID fields
            $table->foreignUuid('critere_evaluation_id')->references('id_critere_evaluation')->on('criteres_evaluations')->onDelete('set null');
            $table->foreignUuid('evaluation_id')->references('id_evaluation')->on('evaluations')->onDelete('set null');
            $table->foreignUuid('prestatiare_id')->references('id_prestataire')->on('prestataires')->onDelete('set null');

            // Définir la clé primaire composite
            $table->primary(['critere_evaluation_id', 'evaluation_id', 'prestatiare_id']);

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
        Schema::dropIfExists('evaluations_lots_prestataires');
    }
};
