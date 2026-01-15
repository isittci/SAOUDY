<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table des paiements effectués sur les factures validées.
     * Un paiement représente une transaction financière réelle effectuée
     * pour régler tout ou partie d'une facture. Permet le suivi des règlements,
     * la gestion des paiements partiels et la traçabilité bancaire complète.
     */
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            // ============================================================
            // IDENTIFICATION
            // ============================================================

            $table->uuid('id_paiement')
                ->primary()
                ->comment('Identifiant unique du paiement au format UUID. Clé primaire générée automatiquement garantissant l\'unicité absolue de chaque transaction de paiement dans le système.');

            // ============================================================
            // RELATIONS / CLÉS ÉTRANGÈRES
            // ============================================================

            $table->foreignUuid('facture_id')
                ->after('id_paiement')
                ->references('id_facture')
                ->on('factures')
                ->onDelete('set null')
                ->comment('Référence vers la facture concernée par ce paiement. Établit le lien entre la transaction financière et le document comptable. Permet les paiements partiels (plusieurs paiements pour une même facture). Mis à NULL si la facture est supprimée pour conserver la trace du paiement.');

            $table->foreignUuid('banque_id')
                ->references('id_banque')
                ->on('banques')
                ->onDelete('set null')
                ->comment('Référence vers la banque du prestataire destinataire du paiement. Identifie le compte bancaire crédité lors du virement. Essentiel pour la réconciliation bancaire et l\'émission des ordres de virement.');


            // ============================================================
            // INFORMATIONS FINANCIÈRES DU PAIEMENT
            // ============================================================

            $table->decimal('montant_net_paye_paiement', 20, 2)
                ->nullable()
                ->comment('Montant effectivement versé au prestataire en FCFA. Représente la somme nette créditée sur le compte bancaire. Peut différer du montant facturé en cas de: retenues de garantie, pénalités déduites, acomptes, ou paiements partiels. Précision de 2 décimales. Maximum: 99 999 999 999 999 999,99 FCFA.');

            $table->smallInteger('statut_paiement')
                ->nullable()
                ->comment('Code numérique indiquant l\'état du paiement dans le workflow. Valeurs suggérées: 0=En attente de validation, 1=Validé/Approuvé, 2=En cours de traitement bancaire, 3=Payé/Exécuté, 4=Rejeté, 5=Annulé. Permet le suivi du cycle de vie complet du paiement.');

            // ============================================================
            // DATES ET VALIDATION
            // ============================================================

            //Date de paiement effective (date du virement ou émission du chèque)
            $table->dateTime('date_effectif_paiement')
                ->nullable()
                ->comment('Date et heure exactes où le paiement a été effectivement réalisé (virement bancaire, chèque émis, etc.). Indique le moment où les fonds ont quitté le compte de l\'organisation. Important pour la réconciliation bancaire et le suivi des délais de paiement.');

            $table->dateTime('date_validation_paiement')
                ->nullable()
                ->comment('Date et heure exactes de la validation/approbation du paiement par l\'autorité compétente. Marque le moment où le paiement est autorisé pour exécution. NULL tant que le paiement n\'est pas validé. Important pour les délais de traitement et l\'audit.');

            // ============================================================
            // MOTIFS ET OBSERVATIONS
            // ============================================================

            $table->text('motif_rejet_paiement')
                ->nullable()
                ->comment('Explication détaillée en cas de rejet du paiement. Doit préciser: la raison du rejet (pièces manquantes, erreur de montant, RIB invalide, etc.), les actions correctives requises, et les références réglementaires si applicable. Obligatoire si statut_paiement = Rejeté.');

            $table->text('observations_paiement')
                ->nullable()
                ->comment('Notes et commentaires libres concernant le paiement. Peut inclure: références du virement bancaire, numéro de bordereau, instructions particulières, historique des relances, ou toute information utile au suivi. Champ flexible pour documentation interne.');

            // ============================================================
            // RESPONSABLES DU TRAITEMENT
            // ============================================================

            $table->uuid('valide_par')
                ->nullable()
                ->comment('Identifiant de l\'utilisateur (ordonnateur ou responsable financier) ayant validé/approuvé le paiement. Représente l\'autorité ayant donné le feu vert pour l\'exécution du règlement. Essentiel pour la chaîne de responsabilité et la conformité aux procédures de contrôle interne.');

            $table->uuid('paye_par')
                ->nullable()
                ->comment('Identifiant de l\'utilisateur (comptable ou trésorier) ayant effectivement exécuté le paiement. Distingué du validateur car souvent deux personnes différentes (séparation des tâches). Trace qui a physiquement déclenché le virement ou émis le chèque.');

            // ============================================================
            // AUDIT ET TRAÇABILITÉ
            // ============================================================

            $table->foreignUuid('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->comment('Identifiant de l\'utilisateur ayant initié/créé la demande de paiement dans le système. Généralement le gestionnaire du marché ou le service facturier. Permet de tracer l\'origine de la demande de règlement.');

            $table->foreignUuid('updated_by')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->comment('Identifiant du dernier utilisateur ayant modifié les informations du paiement. Utile pour suivre les corrections, ajustements de montant, ou mises à jour de statut. Complète l\'historique des modifications.');

            $table->foreignUuid('deleted_by')
                ->nullable()
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->comment('Identifiant de l\'utilisateur ayant supprimé logiquement le paiement. Les paiements ne doivent jamais être supprimés physiquement pour des raisons d\'audit financier. Ce champ permet de savoir qui a procédé à l\'annulation/archivage.');

            // ============================================================
            // HORODATAGE
            // ============================================================

            $table->timestamp('created_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date et heure de création de l\'enregistrement du paiement. Horodatage automatique lors de l\'insertion. Représente le moment où la demande de paiement a été enregistrée, pas nécessairement la date d\'exécution effective.');

            $table->timestamp('updated_at')
                ->useCurrent()
                ->nullable()
                ->comment('Date et heure de la dernière modification de l\'enregistrement. Mis à jour automatiquement par Eloquent. Permet de connaître la fraîcheur des données et de détecter les modifications récentes.');

            // ============================================================
            // SUPPRESSION LOGIQUE
            // ============================================================

            $table->softDeletes()
                ->comment('Date de suppression logique (soft delete) du paiement. Si renseignée, le paiement est considéré comme annulé/archivé mais reste en base pour la comptabilité et l\'audit. Les paiements financiers ne doivent JAMAIS être supprimés définitivement pour conformité légale.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
