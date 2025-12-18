<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignUuid('critere_evaluation_id')->nullable()->after('id_evaluation')->references('id_critere_evaluation')->on('criteres_evaluations')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['critere_evaluation_id']);
            $table->dropColumn('critere_evaluation_id');
        });
    }
};
