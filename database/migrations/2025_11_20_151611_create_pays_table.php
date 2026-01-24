<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration pour la table des pays
     * Stocke tous les pays du monde avec leurs codes ISO et indicatifs téléphoniques
     */
    public function up(): void
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom', 100)->comment('Nom du pays en français');
            $table->string('code_iso_2', 2)->unique()->comment('Code ISO 3166-1 alpha-2 (ex: FR, US, CI)');
            $table->string('code_iso_3', 3)->unique()->comment('Code ISO 3166-1 alpha-3 (ex: FRA, USA, CIV)');
            $table->string('indicatif', 10)->comment('Indicatif téléphonique international (ex: +33, +1, +225)');
            $table->boolean('actif')->default(true)->comment('Indique si le pays est actif dans l\'application');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index('nom');
            $table->index('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pays');
    }
};
