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
        Schema::table('prestataires_lots', function (Blueprint $table) {
            $table->date('date_effective_fin')
                ->nullable()
                ->comment('Date effective de fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestataires_lots', function (Blueprint $table) {
            $table->dropColumn('date_effective_fin');
        });
    }
};
