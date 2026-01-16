<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport Factures et Paiements</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #1f2937;
            background: #fff;
        }
        .container {
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f97316;
        }
        .header h1 {
            font-size: 20px;
            color: #f97316;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 11px;
            color: #6b7280;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #374151;
            padding: 4px 8px;
            width: 20%;
            background: #fef3c7;
        }
        .info-value {
            display: table-cell;
            padding: 4px 8px;
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #fdba74;
            color: #78350f;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            border: 1px solid #e5e7eb;
        }
        td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
        }
        tr:nth-child(even) {
            background: #fff7ed;
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
        }
        .montant-paye {
            color: #16a34a;
        }
        .montant-reste {
            color: #dc2626;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
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
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            background: #fff7ed;
            border: 1px solid #fed7aa;
        }
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #f97316;
        }
        .stat-label {
            font-size: 8px;
            color: #6b7280;
            margin-top: 3px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        .page-break {
            page-break-after: always;
        }
        .small-text {
            font-size: 7px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>RAPPORT DES FACTURES ET PAIEMENTS</h1>
            <div class="subtitle">Généré le {{ $dateGeneration }}</div>
        </div>

        <!-- Informations du prestataire -->
        <div class="section">
            <div class="section-title">INFORMATIONS DU PRESTATAIRE</div>
            <table>
                <tr>
                    <td style="width: 15%; background: #fef3c7; font-weight: bold;">Raison Sociale</td>
                    <td style="width: 35%;">{{ $prestataire['raison_sociale'] }}</td>
                    <td style="width: 15%; background: #fef3c7; font-weight: bold;">N° Identification</td>
                    <td style="width: 35%;">{{ $prestataire['numero_identification'] }}</td>
                </tr>
                <tr>
                    <td style="background: #fef3c7; font-weight: bold;">Email</td>
                    <td>{{ $prestataire['email'] }}</td>
                    <td style="background: #fef3c7; font-weight: bold;">Téléphone</td>
                    <td>{{ $prestataire['telephone'] }}</td>
                </tr>
                <tr>
                    <td style="background: #fef3c7; font-weight: bold;">Adresse</td>
                    <td colspan="3">{{ $prestataire['adresse'] }}, {{ $prestataire['ville'] }}, {{ $prestataire['pays'] }}</td>
                </tr>
                <tr>
                    <td style="background: #fef3c7; font-weight: bold;">N° RCCM</td>
                    <td>{{ $prestataire['numero_rccm'] }}</td>
                    <td style="background: #fef3c7; font-weight: bold;">Statut</td>
                    <td>
                        <span class="badge {{ $prestataire['statut'] === 'Actif' ? 'badge-success' : 'badge-danger' }}">
                            {{ $prestataire['statut'] }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Statistiques -->
        <div class="section">
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value">{{ $nombreFactures }}</div>
                    <div class="stat-label">Factures</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $totalFactures }}</div>
                    <div class="stat-label">Total Facturé</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: #16a34a;">{{ $totalPaye }}</div>
                    <div class="stat-label">Total Payé</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: #dc2626;">{{ $totalReste }}</div>
                    <div class="stat-label">Reste à Payer</div>
                </div>
            </div>
        </div>

        <!-- Liste des factures (avec Appel d'Offre et Lot) -->
        <div class="section">
            <div class="section-title">LISTE DES FACTURES</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Facture</th>
                        <th>N° Proforma</th>
                        <th>Appel d'Offre</th>
                        <th>Lot</th>
                        <th>Date</th>
                        <th class="text-right">Montant</th>
                        <th class="text-right">Payé</th>
                        <th class="text-right">Reste</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMontant = 0;
                        $totalPayeSum = 0;
                        $totalResteSum = 0;
                    @endphp
                    @forelse($factures as $facture)
                        @php
                            $paye = collect($facture['paiements'])->sum('montant');
                            $reste = $facture['montant_facture'] - $paye;
                            $totalMontant += $facture['montant_facture'];
                            $totalPayeSum += $paye;
                            $totalResteSum += $reste;
                        @endphp
                        <tr>
                            <td>{{ $facture['numero_facture'] }}</td>
                            <td>{{ $facture['numero_proforma'] }}</td>
                            <td class="small-text">{{ Str::limit($facture['appel_offre_complet'], 35) }}</td>
                            <td class="small-text">{{ $facture['lot_numero'] }} - {{ Str::limit($facture['lot_libelle'], 20) }}</td>
                            <td>{{ $facture['date_facture'] ? \Carbon\Carbon::parse($facture['date_facture'])->format('d/m/Y') : '' }}</td>
                            <td class="montant">{{ number_format($facture['montant_facture'], 0, ',', ' ') }}</td>
                            <td class="montant montant-paye">{{ number_format($paye, 0, ',', ' ') }}</td>
                            <td class="montant {{ $reste > 0 ? 'montant-reste' : '' }}">{{ number_format($reste, 0, ',', ' ') }}</td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($facture['statut_facture']) {
                                        'Payée' => 'badge-success',
                                        'Validée' => 'badge-info',
                                        'Partiellement payée' => 'badge-warning',
                                        default => 'badge-warning'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $facture['statut_facture'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Aucune facture enregistrée</td>
                        </tr>
                    @endforelse
                    <tr class="total-row">
                        <td colspan="5" class="text-right">TOTAUX</td>
                        <td class="montant">{{ number_format($totalMontant, 0, ',', ' ') }} FCFA</td>
                        <td class="montant montant-paye">{{ number_format($totalPayeSum, 0, ',', ' ') }} FCFA</td>
                        <td class="montant montant-reste">{{ number_format($totalResteSum, 0, ',', ' ') }} FCFA</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Détail des paiements (avec Appel d'Offre, Lot, Référence) -->
        <div class="section">
            <div class="section-title">DÉTAIL DES PAIEMENTS</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Facture</th>
                        <th>Appel d'Offre</th>
                        <th>Lot</th>
                        <th>Date Paiement</th>
                        <th class="text-right">Montant</th>
                        <th class="text-center">Statut</th>
                        <th>Banque</th>
                        <th>Référence Paiement</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalPaiements = 0; @endphp
                    @foreach($factures as $facture)
                        @foreach($facture['paiements'] as $paiement)
                            <tr>
                                <td>{{ $facture['numero_facture'] }}</td>
                                <td class="small-text">{{ $paiement['appel_offre_numero'] }}</td>
                                <td class="small-text">{{ $paiement['lot_numero'] }} - {{ Str::limit($paiement['lot_libelle'], 15) }}</td>
                                <td>{{ $paiement['date_paiement'] ? \Carbon\Carbon::parse($paiement['date_paiement'])->format('d/m/Y') : '' }}</td>
                                <td class="montant montant-paye">{{ number_format($paiement['montant'], 0, ',', ' ') }}</td>
                                <td class="text-center">
                                    @php
                                        $pBadge = match(true) {
                                            str_contains($paiement['statut'], 'Payé') => 'badge-success',
                                            str_contains($paiement['statut'], 'Validé') => 'badge-info',
                                            str_contains($paiement['statut'], 'Rejeté') => 'badge-danger',
                                            default => 'badge-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $pBadge }}">{{ Str::limit($paiement['statut'], 15) }}</span>
                                </td>
                                <td class="small-text">{{ $paiement['nom_banque'] }}</td>
                                <td class="small-text">{{ $paiement['reference_paiement'] }}</td>
                            </tr>
                            @php
                                if(in_array($paiement['statut_code'] ?? '', [\App\Models\Paiement::STATUT_VALIDE, \App\Models\Paiement::STATUT_PAYE])) {
                                    $totalPaiements += $paiement['montant'];
                                }
                            @endphp
                        @endforeach
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="text-right">TOTAL PAIEMENTS EFFECTUÉS</td>
                        <td class="montant montant-paye">{{ number_format($totalPaiements, 0, ',', ' ') }} FCFA</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>Document généré automatiquement - {{ $dateGeneration }}</p>
            <p>Taux de règlement: {{ $tauxReglement }} | Factures soldées: {{ $facturesSoldees }}/{{ $nombreFactures }}</p>
        </div>
    </div>
</body>
</html>
