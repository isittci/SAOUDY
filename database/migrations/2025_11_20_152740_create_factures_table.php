<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table des factures liées aux proformas validées.
     * Une facture représente le document comptable officiel émis par le prestataire
     * après validation d'une proforma, servant de base pour le déclenchement du paiement.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            // ============================================================
            // IDENTIFICATION
            // ============================================================

            $table->uuid('id_facture')
                ->primary()
                ->comment('Identifiant unique de la facture au format UUID. Clé primaire générée automatiquement pour garantir l\'unicité à travers tous les systèmes.');

            $table->foreignUuid('proforma_id')
                ->after('id_facture')
                ->references('id_proforma')
                ->on('proformas')
                ->onDelete('set null')
                ->comment('Référence vers la proforma validée à l\'origine de cette facture. Lien obligatoire établissant la traçabilité entre le devis accepté et la facturation. Mis à NULL si la proforma est supprimée pour conserver l\'historique.');

            $table->string('numero_facture', 30)
                ->unique()
                ->comment('Numéro unique de la facture attribué par le prestataire. Format attendu: FAC-YYYY-XXXXX ou selon la nomenclature du prestataire. Sert de référence officielle dans tous les échanges et documents comptables.');

            // ============================================================
            // INFORMATIONS FINANCIÈRES
            // ============================================================

            $table->decimal('montant_facture', 15, 2)
                ->comment('Montant total TTC de la facture en FCFA. Doit correspondre au montant de la proforma validée (montant_retenu + TVA - remise + pénalités). Précision de 2 décimales pour les calculs comptables. Maximum: 9 999 999 999 999,99 FCFA.');

            // ============================================================
            // DATES IMPORTANTES
            // ============================================================

            $table->date('date_facture')
                ->comment('Date d\'émission de la facture par le prestataire. Date figurant sur le document officiel de facturation. Sert de référence pour le calcul des délais de paiement légaux.');

            $table->date('date_reception_facture')
                ->comment('Date de réception effective de la facture par le service gestionnaire. Point de départ du délai de traitement administratif. Important pour le respect des délais de paiement réglementaires (généralement 30 jours en marchés publics).');

            // ============================================================
            // STATUT ET SUIVI
            // ============================================================

            $table->enum('statut_facture', ['en_attente', 'validee', 'rejetee', 'payee', 'partiellement_payee', 'annulee'])
                ->default('en_attente')
                ->comment('État actuel de la facture dans le workflow de traitement. Valeurs possibles: en_attente (réception, vérification en cours), validee (conforme, prête pour paiement), rejetee (non conforme, retournée au prestataire), payee (règlement total effectué), partiellement_payee (acompte versé), annulee (facture invalidée).');

            $table->text('comment_facture')
                ->nullable()
                ->comment('Observations, remarques ou notes internes concernant la facture. Peut contenir: motifs de rejet, instructions particulières, références de documents complémentaires, historique des échanges avec le prestataire.');

            // ============================================================
            // AUDIT ET TRAÇABILITÉ
            // ============================================================

            $table->foreignUuid('created_by')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->comment('Identifiant de l\'utilisateur ayant enregistré la facture dans le système. Permet de tracer la responsabilité de la saisie initiale. Conservé même si l\'utilisateur est supprimé.');

            $table->foreignUuid('updated_by')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->comment('Identifiant du dernier utilisateur ayant modifié les informations de la facture. Permet de suivre les modifications successives et d\'identifier le responsable de la dernière mise à jour.');

            $table->foreignUuid('deleted_by')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->comment('Identifiant de l\'utilisateur ayant procédé à la suppression logique de la facture. Requis pour l\'audit et la conformité réglementaire. Permet de retracer qui a archivé/supprimé la facture.');

            // ============================================================
            // HORODATAGE
            // ============================================================

            $table->timestamp('created_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date et heure de création de l\'enregistrement dans la base de données. Générée automatiquement lors de l\'insertion. Format: YYYY-MM-DD HH:MM:SS.');

            $table->timestamp('updated_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date et heure de la dernière modification de l\'enregistrement. Mise à jour automatiquement par Eloquent à chaque sauvegarde. Permet de suivre la fraîcheur des données.');

            $table->softDeletes()
                ->comment('Date de suppression logique (soft delete). Si non NULL, la facture est considérée comme supprimée mais reste en base pour archivage et audit. Permet la restauration ultérieure si nécessaire.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
