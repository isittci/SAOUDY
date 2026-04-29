<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('banques', function (Blueprint $table) {
            // Supprimer les index uniques
            $table->dropUnique(['numero_compte_banque']);
        });
    }

    public function down()
    {
        Schema::table('banques', function (Blueprint $table) {
            // Restaurer les index uniques
            $table->unique('numero_compte_banque');
        });
    }
};
