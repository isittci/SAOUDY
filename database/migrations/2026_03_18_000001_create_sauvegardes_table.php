<?php
// 2026_03_18_000001_create_sauvegardes_table.php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sauvegardes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nom_fichier', 255);
            $table->string('chemin_stockage', 500)->comment('Chemin relatif dans storage/app');
            $table->enum('type', ['manuelle', 'automatique'])->default('manuelle');
            $table->enum('statut', ['en_cours', 'terminee', 'echec'])->default('en_cours');

            // Taille et méta
            $table->unsignedBigInteger('taille_octets')->nullable()->comment('Taille du fichier en octets');
            $table->string('checksum_md5', 32)->nullable()->comment('MD5 pour vérification intégrité');
            $table->text('tables_incluses')->nullable()->comment('JSON : liste des tables sauvegardées');
            $table->text('message_erreur')->nullable();

            // Rétention
            $table->timestamp('expire_a')->nullable()->comment('NULL = conservation permanente');

            // Traçabilité
            $table->foreignUuid('creee_par_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_declencheur', 45)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('statut');
            $table->index('created_at');
            $table->index('expire_a');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sauvegardes');
    }
};
