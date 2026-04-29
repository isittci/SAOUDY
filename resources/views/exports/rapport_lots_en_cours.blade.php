<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Lots en Cours</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #F97316;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #F97316;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header .subtitle {
            color: #666;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #F97316;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
        }

        td {
            padding: 6px 4px;
            border: 1px solid #E5E7EB;
            font-size: 8px;
        }

        tr:nth-child(even) {
            background-color: #FFF7ED;
        }

        .total-row {
            background-color: #FED7AA !important;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-success {
            color: #16A34A;
        }

        .text-danger {
            color: #DC2626;
        }

        .text-primary {
            color: #1E40AF;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #DCFCE7;
            color: #166534;
        }

        .badge-warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .progress-cell {
            text-align: center;
            font-weight: bold;
        }

        .progress-high {
            background-color: #DCFCE7;
        }

        .progress-medium {
            background-color: #FEF3C7;
        }

        .progress-low {
            background-color: #FEE2E2;
        }

        .summary-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .summary-title {
            color: #F97316;
            font-size: 14px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #F97316;
        }

        .summary-table {
            width: 50%;
        }

        .summary-table td {
            padding: 8px 12px;
            background-color: #FFF7ED;
        }

        .summary-table td:first-child {
            font-weight: bold;
            width: 200px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #E5E7EB;
            padding-top: 10px;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RAPPORT DES LOTS EN COURS</h1>
            <p class="subtitle">Avec pourcentage d'avancement des travaux</p>
            <p class="subtitle">Généré le {{ $dateGeneration }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 8%">N° Lot</th>
                    <th style="width: 15%">Libellé</th>
                    <th style="width: 12%">Prestataire</th>
                    <th style="width: 6%">Avancement</th>
                    <th style="width: 10%">Montant TTC</th>
                    <th style="width: 10%">Payé</th>
                    <th style="width: 10%">Reste</th>
                    <th style="width: 7%">Statut</th>
                    <th style="width: 5%">Retard</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $lot)
                <tr>
                    <td>{{ $lot['numero_lot'] }}</td>
                    <td>{{ Str::limit($lot['libelle_lot'], 40) }}</td>
                    <td>{{ Str::limit($lot['prestataire'], 25) }}</td>
                    <td class="progress-cell @if($lot['pourcentage_avancement'] >= 80) progress-high @elseif($lot['pourcentage_avancement'] >= 50) progress-medium @else progress-low @endif">
                        {{ number_format($lot['pourcentage_avancement'], 2) }}%
                    </td>
                    <td class="text-right text-primary">{{ number_format(floor($lot['montant_proforma_ttc']), 0, ',', ' ') }}</td>
                    <td class="text-right text-success">{{ number_format(floor($lot['montant_paye']), 0, ',', ' ') }}</td>
                    <td class="text-right text-danger">{{ number_format(floor($lot['reste_a_payer']), 0, ',', ' ') }}</td>
                    <td class="text-center">
                        <span class="badge @if($lot['statut'] == 'Attribué') badge-success @elseif($lot['statut'] == 'Suspendu') badge-warning @else badge-danger @endif">
                            {{ $lot['statut'] }}
                        </span>
                    </td>
                    <td class="text-center @if($lot['jours_retard'] > 0) text-danger @endif">
                        {{ $lot['jours_retard'] > 0 ? $lot['jours_retard'] . ' j' : '-' }}
                    </td>

                </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAUX</td>
                    <td class="text-right text-primary">{{ number_format(floor($totalProforma), 0, ',', ' ') }}</td>
                    <td class="text-right text-success">{{ number_format(floor($totalPaye), 0, ',', ' ') }}</td>
                    <td class="text-right text-danger">{{ number_format(floor($totalReste), 0, ',', ' ') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <div class="summary-section">
            <h2 class="summary-title">RÉSUMÉ FINANCIER</h2>
            <table class="summary-table">
                <tr>
                    <td>Nombre de lots en cours</td>
                    <td><strong>{{ $nombreLots }}</strong></td>
                </tr>
                <tr>
                    <td>Montant total des proformas</td>
                    <td class="text-primary"><strong>{{ $totalProforma }}</strong></td>
                </tr>
                <tr>
                    <td>Montant total payé</td>
                    <td class="text-success"><strong>{{ $totalPaye }}</strong></td>
                </tr>
                <tr>
                    <td>Reste à payer</td>
                    <td class="text-danger"><strong>{{ $totalReste }}</strong></td>
                </tr>
                <tr>
                    <td>Taux de paiement global</td>
                    <td><strong>{{ $tauxPaiement }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Document généré automatiquement - Système de Gestion des Appels d'Offres</p>
        </div>
    </div>
</body>
</html>
