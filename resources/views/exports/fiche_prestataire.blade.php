<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche Prestataire</title>
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
        .header h2 {
            font-size: 14px;
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
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
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
        .info-table td {
            background: transparent;
        }
        .info-table .label {
            background: #fef3c7;
            font-weight: bold;
            width: 15%;
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
        .badge-gray {
            background: #e5e7eb;
            color: #374151;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-box {
            display: table-cell;
            width: 20%;
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
            font-size: 7px;
            color: #6b7280;
            margin-top: 3px;
        }
        .progress-bar {
            width: 100%;
            height: 12px;
            background: #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 6px;
        }
        .progress-low {
            background: #ef4444;
        }
        .progress-medium {
            background: #f59e0b;
        }
        .progress-high {
            background: #22c55e;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>FICHE PRESTATAIRE</h1>
            <h2>{{ $prestataire['raison_sociale'] }}</h2>
            <div class="subtitle">Généré le {{ $dateGeneration }}</div>
        </div>

        <!-- Informations générales -->
        <div class="section">
            <div class="section-title">INFORMATIONS GÉNÉRALES</div>
            <table class="info-table">
                <tr>
                    <td class="label">N° Identification</td>
                    <td>{{ $prestataire['numero_identification'] }}</td>
                    <td class="label">Email</td>
                    <td>{{ $prestataire['email'] }}</td>
                </tr>
                <tr>
                    <td class="label">Téléphone</td>
                    <td>{{ $prestataire['telephone'] }}</td>
                    <td class="label">Adresse</td>
                    <td>{{ $prestataire['adresse'] }}, {{ $prestataire['ville'] }}</td>
                </tr>
                <tr>
                    <td class="label">N° RCCM</td>
                    <td>{{ $prestataire['numero_rccm'] }}</td>
                    <td class="label">N° CC</td>
                    <td>{{ $prestataire['numero_cc'] }}</td>
                </tr>
                <tr>
                    <td class="label">Représentant Légal</td>
                    <td>{{ $prestataire['representant_legal'] }}</td>
                    <td class="label">Statut</td>
                    <td>
                        <span class="badge {{ $prestataire['statut'] === 'Actif' ? 'badge-success' : 'badge-danger' }}">
                            {{ $prestataire['statut'] }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Statistiques globales -->
        <div class="section">
            <div class="section-title">STATISTIQUES GLOBALES</div>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value">{{ $statistiques['total_lots'] }}</div>
                    <div class="stat-label">Total Lots</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $statistiques['lots_en_cours'] }}</div>
                    <div class="stat-label">En Cours</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: #16a34a;">{{ $statistiques['lots_termines'] }}</div>
                    <div class="stat-label">Terminés</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: #dc2626;">{{ $statistiques['lots_retires'] }}</div>
                    <div class="stat-label">Retirés</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($statistiques['avancement_moyen'], 1) }}%</div>
                    <div class="stat-label">Avancement Moyen</div>
                </div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">Montant Total Engagé</td>
                    <td class="montant">{{ $formatMontant($statistiques['montant_total_engage']) }}</td>
                    <td class="label">Montant Total Payé</td>
                    <td class="montant montant-paye">{{ $formatMontant($statistiques['montant_total_paye']) }}</td>
                </tr>
                <tr>
                    <td class="label">Reste à Payer</td>
                    <td class="montant montant-reste">{{ $formatMontant($statistiques['reste_total_a_payer']) }}</td>
                    <td class="label">Taux de Paiement</td>
                    <td><strong>{{ number_format($statistiques['taux_paiement_global'], 2) }}%</strong></td>
                </tr>

            </table>
        </div>

        <!-- Liste des lots attribués -->
        <div class="section">
            <div class="section-title">LISTE DES LOTS ATTRIBUÉS</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Lot</th>
                        <th>Libellé</th>
                        <th>Appel d'Offre</th>
                        <th class="text-center">Avancement</th>
                        <th class="text-right">Montant TTC</th>
                        <th class="text-right">Payé</th>
                        <th class="text-right">Reste</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Retard</th>
                        <th>Date Fin</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMontant = 0;
                        $totalPaye = 0;
                        $totalReste = 0;
                    @endphp
                    @forelse($lots as $lot)
                        @php
                            $totalMontant += $lot['montant_proforma_ttc'];
                            $totalPaye += $lot['montant_paye'];
                            $totalReste += $lot['reste_a_payer'];

                            $progressClass = $lot['pourcentage_avancement'] >= 80 ? 'progress-high' :
                                           ($lot['pourcentage_avancement'] >= 50 ? 'progress-medium' : 'progress-low');

                            $statutBadge = match($lot['statut']) {
                                'Attribué' => 'badge-success',
                                'Terminé' => 'badge-info',
                                'Suspendu' => 'badge-warning',
                                'Retiré' => 'badge-danger',
                                default => 'badge-gray'
                            };
                        @endphp
                        <tr>
                            <td>{{ $lot['lot_numero'] }}</td>
                            <td>{{ Str::limit($lot['lot_libelle'], 25) }}</td>
                            <td style="font-size: 7px;">{{ $lot['appel_offre_numero'] }}</td>
                            <td class="text-center">
                                <div class="progress-bar">
                                    <div class="progress-fill {{ $progressClass }}" style="width: {{ $lot['pourcentage_avancement'] }}%;"></div>
                                </div>
                                <span style="font-size: 7px;">{{ $lot['pourcentage_avancement'] }}%</span>
                            </td>
                            <td class="montant">{{ number_format($lot['montant_proforma_ttc'], 0, ',', ' ') }}</td>
                            <td class="montant montant-paye">{{ number_format($lot['montant_paye'], 0, ',', ' ') }}</td>
                            <td class="montant {{ $lot['reste_a_payer'] > 0 ? 'montant-reste' : '' }}">{{ number_format($lot['reste_a_payer'], 0, ',', ' ') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $statutBadge }}">{{ $lot['statut'] }}</span>
                            </td>
                            <td class="text-center {{ $lot['jours_retard'] > 0 ? 'montant-reste' : '' }}">
                                {{ $lot['jours_retard'] > 0 ? $lot['jours_retard'] . ' j' : '-' }}
                            </td>

                            <td>{{ $lot['date_fin_prevue'] ? \Carbon\Carbon::parse($lot['date_fin_prevue'])->format('d/m/Y') : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Aucun lot attribué à ce prestataire</td>
                        </tr>
                    @endforelse
                    <tr class="total-row">
                        <td colspan="4" class="text-right">TOTAUX</td>
                        <td class="montant">{{ number_format($totalMontant, 0, ',', ' ') }} FCFA</td>
                        <td class="montant montant-paye">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</td>
                        <td class="montant montant-reste">{{ number_format($totalReste, 0, ',', ' ') }} FCFA</td>
                        <td colspan="2"></td>

                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Coordonnées bancaires -->
        @if(!empty($banques))
        <div class="section">
            <div class="section-title">COORDONNÉES BANCAIRES</div>
            <table>
                <thead>
                    <tr>
                        <th>Banque</th>
                        <th>N° Compte</th>
                        <th>IBAN</th>
                        <th>Titulaire</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banques as $banque)
                        <tr>
                            <td>{{ $banque['nom'] }}</td>
                            <td>{{ $banque['numero_compte'] }}</td>
                            <td>{{ $banque['iban'] }}</td>
                            <td>{{ $banque['titulaire'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Pied de page -->
        <div class="footer">
            <p>Document généré automatiquement - {{ $dateGeneration }}</p>
            <p>{{ $prestataire['raison_sociale'] }} | {{ $statistiques['total_lots'] }} lot(s) | Taux paiement: {{ number_format($statistiques['taux_paiement_global'], 2) }}%</p>
        </div>
    </div>
</body>
</html>
