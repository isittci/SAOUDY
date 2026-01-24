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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('type_document', 255)->nullable()->change();
            $table->string('titre_document', 255)->nullable()->change();
            $table->string('fichier_nom_document', 255)->nullable()->change();
            $table->string('fichier_type_document', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('type_document', 20)->nullable()->change();
            $table->string('titre_document', 100)->nullable()->change();
            $table->string('fichier_nom_document', 100)->nullable()->change();
            $table->string('fichier_type_document', 50)->nullable()->change();
        });
    }
};
