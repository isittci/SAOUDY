<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Vider la table des tokens (les utilisateurs devront se reconnecter)
        DB::table('personal_access_tokens')->truncate();

        // Supprimer la contrainte de clé primaire existante
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropPrimary('personal_access_tokens_pkey');
        });

        // Supprimer l'ancienne colonne id
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        // Ajouter la nouvelle colonne id UUID
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
        });
    }

    public function down(): void
    {
        DB::table('personal_access_tokens')->truncate();

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropPrimary('personal_access_tokens_pkey');
            $table->dropColumn('id');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->id()->first();
        });
    }
};
