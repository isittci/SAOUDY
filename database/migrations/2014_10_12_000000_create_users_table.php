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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom_complet', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telephone_principal')->nullable();
            $table->string('telepone_secondaire')->nullable();
            $table->foreignUuid('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->timestamp('email_verified_at')->nullable();


            $table->enum('statut', [1, 0])->default(0);

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // Soft delete
            $table->softDeletes();
        });

        //Auto relation (created_by; updated_by; deleted_by)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
