<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour garantir l'unicité de la proforma dans les attributions
 * 
 * Principe: Une proforma ne peut être associée qu'à une seule attribution
 * Le triplet (prestataire_id, lot_id, proforma_id) est de facto unique
 * car proforma_id est déjà unique
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prestataires_lots', function (Blueprint $table) {
            // Index unique sur proforma_id (une proforma = une attribution)
            // Cela garantit qu'une proforma ne peut être utilisée qu'une seule fois
            $table->unique('proforma_id', 'unique_proforma_attribution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestataires_lots', function (Blueprint $table) {
            $table->dropUnique('unique_proforma_attribution');
        });
    }
};
