<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('proformas', function (Blueprint $table) {
            $table->dropColumn('date_redemarrage_proforma');
        });

        Schema::table('appels_offres', function (Blueprint $table) {
            $table->dropColumn('date_limite_depot_critere_appel_offre');
            $table->dropColumn('date_ouverture_plis_critere_appel_offre');
        });

    }

    public function down(): void
    {
        Schema::table('proformas', function (Blueprint $table) {
            $table->date('date_redemarrage_proforma')->nullable();
        });
        Schema::table('appels_offres', function (Blueprint $table) {
            $table->date('date_limite_depot_critere_appel_offre')->nullable();
            $table->date('date_ouverture_plis_critere_appel_offre')->nullable();
        });
    }
};

