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
        Schema::create('paiements', function (Blueprint $table) {
            // Primary key
            $table->uuid('id_paiement')->primary();

            // Foreign key-like UUID
            $table->foreignUuid('banque_id')->references('id_banque')->on('banques')->onDelete('set null');

            // Paiement
            $table->decimal('montant_net_paye_paiement', 20, 2)->nullable();
            $table->smallInteger('statut_paiement')->nullable();
            $table->dateTime('date_validation_paiement')->nullable();

            // Motifs / Observations
            $table->text('motif_rejet_paiement')->nullable();
            $table->text('observations_paiement')->nullable();

            // Validation / Paiement
            $table->uuid('valide_par')->nullable();
            $table->uuid('paye_par')->nullable();

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
        Schema::dropIfExists('paiements');
    }
};
