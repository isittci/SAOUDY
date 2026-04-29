<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prestataires', function (Blueprint $table) {
            $table->dropColumn('numero_identification_prestataire');
        });
    }

    public function down(): void
    {
        Schema::table('prestataires', function (Blueprint $table) {
            $table->string('numero_identification_prestataire')->nullable();
        });
    }
};
