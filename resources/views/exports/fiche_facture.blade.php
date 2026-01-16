<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche Facture</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f97316;
        }
        .header h1 {
            font-size: 22px;
            color: #f97316;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 10px;
            color: #6b7280;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            padding: 10px 15px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 12px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #fdba74;
            color: #78350f;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #e5e7eb;
        }
        td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background: #fff7ed;
        }
        .info-table td {
            background: transparent;
        }
        .info-table .label {
            background: #fef3c7;
            font-weight: bold;
            width: 20%;
        }
        .info-table .value {
            width: 30%;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background: #fed7aa !important;
            font-weight: bold;
        }
        .montant {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
        }
        .montant-paye {
            color: #16a34a;
        }
        .montant-reste {
            color: #dc2626;
        }
        .montant-big {
            font-size: 14px;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background: #dcfce7;
            color: #166534;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .summary-box {
            background: #fff7ed;
            border: 2px solid #fed7aa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #fed7aa;
        }
        .summary-item:last-child {
            border-right: none;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #f97316;
        }
        .summary-label {
            font-size: 8px;
            color: #6b7280;
            margin-top: 5px;
        }
        .progress-container {
            margin-top: 15px;
            text-align: center;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #f97316, #22c55e);
            border-radius: 10px;
            transition: width 0.3s;
        }
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>FICHE FACTURE</h1>
            <h2>{{ $facture['numero'] }}</h2>
            <div class="subtitle">Généré le {{ $dateGeneration }}</div>
        </div>

        <!-- Résumé financier -->
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">{{ $formatMontant($facture['montant']) }}</div>
                    <div class="summary-label">Montant Facture</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #16a34a;">{{ $formatMontant($facture['montant_paye']) }}</div>
                    <div class="summary-label">Total Payé</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #dc2626;">{{ $formatMontant($facture['reste_a_payer']) }}</div>
                    <div class="summary-label">Reste à Payer</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ number_format($facture['taux_paiement'], 1) }}%</div>
                    <div class="summary-label">Taux de Paiement</div>
                </div>
            </div>
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min($facture['taux_paiement'], 100) }}%;"></div>
                </div>
                <small>Progression du paiement</small>
            </div>
        </div>

        <!-- Informations de la facture -->
        <div class="section">
            <div class="section-title">INFORMATIONS DE LA FACTURE</div>
            <table class="info-table">
                <tr>
                    <td class="label">N° Facture</td>
                    <td class="value"><strong>{{ $facture['numero'] }}</strong></td>
                    <td class="label">Statut</td>
                    <td class="value">
                        @php
                            $statutBadge = match($facture['statut']) {
                                'Payée' => 'badge-success',
                                'Validée' => 'badge-info',
                                'Partiellement payée' => 'badge-warning',
                                'Rejetée' => 'badge-danger',
                                default => 'badge-warning'
                            };
                        @endphp
                        <span class="badge {{ $statutBadge }}">{{ $facture['statut'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Date Facture</td>
                    <td class="value">{{ $facture['date_facture'] ? \Carbon\Carbon::parse($facture['date_facture'])->format('d/m/Y') : '-' }}</td>
                    <td class="label">Date Réception</td>
                    <td class="value">{{ $facture['date_reception'] ? \Carbon\Carbon::parse($facture['date_reception'])->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Montant</td>
                    <td class="value montant montant-big">{{ $formatMontant($facture['montant']) }}</td>
                    <td class="label">Créateur</td>
                    <td class="value">{{ $facture['createur'] ?: '-' }}</td>
                </tr>
                @if($facture['commentaire'])
                <tr>
                    <td class="label">Commentaire</td>
                    <td colspan="3">{{ $facture['commentaire'] }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Prestataire -->
        @if($prestataire)
        <div class="section">
            <div class="section-title">PRESTATAIRE</div>
            <table class="info-table">
                <tr>
                    <td class="label">Raison Sociale</td>
                    <td class="value"><strong>{{ $prestataire['raison_sociale'] }}</strong></td>
                    <td class="label">N° Identification</td>
                    <td class="value">{{ $prestataire['numero_identification'] }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $prestataire['email'] }}</td>
                    <td class="label">Téléphone</td>
                    <td class="value">{{ $prestataire['telephone'] }}</td>
                </tr>
                <tr>
                    <td class="label">Adresse</td>
                    <td colspan="3">{{ $prestataire['adresse'] }}</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Appel d'offre et Lot -->
        @if($appelOffre || $lot)
        <div class="section">
            <div class="section-title">APPEL D'OFFRE ET LOT</div>
            <table class="info-table">
                @if($appelOffre)
                <tr>
                    <td class="label">Appel d'Offre</td>
                    <td colspan="3"><strong>{{ $appelOffre['numero'] }}</strong> - {{ $appelOffre['libelle'] }}</td>
                </tr>
                @endif
                @if($lot)
                <tr>
                    <td class="label">Lot</td>
                    <td colspan="3"><strong>{{ $lot['numero'] }}</strong> - {{ $lot['libelle'] }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- Proforma associée -->
        @if($proforma)
        <div class="section">
            <div class="section-title">PROFORMA ASSOCIÉE</div>
            <table class="info-table">
                <tr>
                    <td class="label">N° Proforma</td>
                    <td class="value"><strong>{{ $proforma['numero'] }}</strong></td>
                    <td class="label">Date</td>
                    <td class="value">{{ $proforma['date'] ? \Carbon\Carbon::parse($proforma['date'])->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Montant HT</td>
                    <td class="value montant">{{ $formatMontant($proforma['montant_ht']) }}</td>
                    <td class="label">TVA</td>
                    <td class="value montant">{{ $formatMontant($proforma['taxe']) }}</td>
                </tr>
                <tr>
                    <td class="label">Remise</td>
                    <td class="value montant">{{ $formatMontant($proforma['remise']) }}</td>
                    <td class="label">Montant TTC</td>
                    <td class="value montant montant-big">{{ $formatMontant($proforma['montant_ttc']) }}</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Historique des paiements -->
        <div class="section">
            <div class="section-title">HISTORIQUE DES PAIEMENTS</div>
            @if(count($paiements) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date Paiement</th>
                        <th class="text-right">Montant</th>
                        <th class="text-center">Statut</th>
                        <th>Banque</th>
                        <th>N° Compte</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalPaiements = 0; @endphp
                    @foreach($paiements as $paiement)
                        @php
                            $pBadge = match($paiement['statut_code']) {
                                \App\Models\Paiement::STATUT_PAYE => 'badge-success',
                                \App\Models\Paiement::STATUT_VALIDE => 'badge-info',
                                \App\Models\Paiement::STATUT_EN_TRAITEMENT => 'badge-info',
                                \App\Models\Paiement::STATUT_REJETE => 'badge-danger',
                                default => 'badge-warning'
                            };

                            if(in_array($paiement['statut_code'], [\App\Models\Paiement::STATUT_VALIDE, \App\Models\Paiement::STATUT_PAYE])) {
                                $totalPaiements += $paiement['montant'];
                            }
                        @endphp
                        <tr>
                            <td>{{ $paiement['date_paiement'] ? \Carbon\Carbon::parse($paiement['date_paiement'])->format('d/m/Y') : '-' }}</td>
                            <td class="montant montant-paye">{{ $formatMontant($paiement['montant']) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $pBadge }}">{{ Str::limit($paiement['statut'], 20) }}</span>
                            </td>
                            <td>{{ $paiement['banque_nom'] }}</td>
                            <td>{{ $paiement['banque_compte'] }}</td>
                            <td>{{ $paiement['reference'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td class="text-right">TOTAL</td>
                        <td class="montant montant-paye">{{ $formatMontant($totalPaiements) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tbody>
            </table>
            @else
            <div class="no-data">
                <p>Aucun paiement enregistré pour cette facture</p>
            </div>
            @endif
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>Document généré automatiquement - {{ $dateGeneration }}</p>
            <p>Facture {{ $facture['numero'] }} | {{ $facture['statut'] }} | Taux de paiement: {{ number_format($facture['taux_paiement'], 2) }}%</p>
        </div>
    </div>
</body>
</html>
