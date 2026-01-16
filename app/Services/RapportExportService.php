<?php

namespace App\Services;

use App\Models\PrestataireLot;
use App\Models\Prestataire;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Lot;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Service pour la génération des rapports Excel et PDF
 *
 * Installation requise:
 * composer require phpoffice/phpspreadsheet
 * composer require barryvdh/laravel-dompdf
 *
 * Rapports disponibles:
 * 1. Lots en cours avec avancement
 * 2. Factures et paiements d'un prestataire (avec appel d'offre, lot, référence)
 * 3. Fiche détaillée d'un prestataire (avec ses lots)
 * 4. Fiche détaillée d'une facture
 */
class RapportExportService
{
    /**
     * Formatage des montants en FCFA
     */
    public function formatMontant($montant): string
    {
        return number_format($montant ?? 0, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Style commun pour les en-têtes
     */
    private function getHeaderStyle(): array
    {
        return [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F97316'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }

    /**
     * Style pour les sous-en-têtes
     */
    private function getSubHeaderStyle(): array
    {
        return [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '78350F'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FDBA74'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }

    /**
     * Créer le dossier exports si nécessaire
     */
    private function ensureExportDirectory(): void
    {
        $path = storage_path('app/exports');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Extrait le nom du représentant légal
     */
    private function getRepresentantLegal(Prestataire $prestataire): string
    {
        $representant = $prestataire->representant_legal_prestataire;
        if (is_array($representant) && !empty($representant)) {
            $rep = is_array($representant[0] ?? null) ? $representant[0] : $representant;
            return trim(($rep['nom'] ?? '') . ' ' . ($rep['prenoms'] ?? ''));
        }
        return '';
    }

    /**
     * ========================================================================
     * RAPPORT 1: LISTE DES LOTS EN COURS AVEC AVANCEMENT
     * ========================================================================
     */

    public function getLotsEnCoursData(): array
    {
        return PrestataireLot::with(['lot.appelOffre', 'prestataire', 'proforma'])
            ->whereIn('statut_attribution', [PrestataireLot::STATUT_ATTRIBUE, PrestataireLot::STATUT_SUSPENDU])
            ->where('is_active', true)
            ->get()
            ->map(function ($attribution) {
                $proforma = $attribution->proforma;
                $lot = $attribution->lot;

                $montantProformaTTC = $proforma ? $proforma->calculerMontantTTC() : 0;
                $montantPaye = $attribution->montant_paye ?? 0;
                $resteAPayer = max(0, $montantProformaTTC - $montantPaye);

                return [
                    'numero_lot' => $lot->numero ?? '',
                    'libelle_lot' => $lot->libelle ?? '',
                    'appel_offre' => $lot->appelOffre ?
                        $lot->appelOffre->numero_appel_offre . ' - ' . $lot->appelOffre->libelle_critere_appel_offre : '',
                    'prestataire' => $attribution->prestataire->raison_sociale_prestataire ?? '',
                    'numero_attribution' => $attribution->numero_attribution ?? '',
                    'date_attribution' => $attribution->date_attribution,
                    'date_debut_prevue' => $attribution->date_debut_prevue,
                    'date_fin_prevue' => $attribution->date_fin_prevue,
                    'pourcentage_avancement' => $attribution->pourcentage_avancement ?? 0,
                    'montant_proforma_ttc' => $montantProformaTTC,
                    'montant_paye' => $montantPaye,
                    'reste_a_payer' => $resteAPayer,
                    'statut' => PrestataireLot::STATUT_LABELS[$attribution->statut_attribution] ?? 'Inconnu',
                    'jours_retard' => $attribution->jours_retard_actuels ?? 0,
                    'penalites_appliquees' => $attribution->penalites_appliquees ?? 0,
                    'observations' => $attribution->observations ?? '',
                ];
            })->toArray();
    }

    public function genererLotsEnCoursExcel(?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getLotsEnCoursData();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lots en cours');

        // Titre
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'RAPPORT DES LOTS EN COURS AVEC AVANCEMENT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:O2');
        $sheet->setCellValue('A2', 'Généré le ' . Carbon::now()->format('d/m/Y à H:i'));

        // En-têtes
        $headers = ['N° Lot', 'Libellé', 'Appel d\'Offre', 'Prestataire', 'N° Attribution',
                    'Date Attribution', 'Date Début', 'Date Fin', 'Avancement',
                    'Montant TTC', 'Payé', 'Reste', 'Statut', 'Retard', 'Pénalités'];

        foreach ($headers as $col => $header) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, 4);
            $cell->setValue($header);
        }
        $sheet->getStyle('A4:O4')->applyFromArray($this->getHeaderStyle());

        // Données
        $row = 5;
        $totaux = ['montant' => 0, 'paye' => 0, 'reste' => 0, 'penalites' => 0];

        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item['numero_lot']);
            $sheet->setCellValue("B{$row}", $item['libelle_lot']);
            $sheet->setCellValue("C{$row}", $item['appel_offre']);
            $sheet->setCellValue("D{$row}", $item['prestataire']);
            $sheet->setCellValue("E{$row}", $item['numero_attribution']);
            $sheet->setCellValue("F{$row}", $item['date_attribution'] ? Carbon::parse($item['date_attribution'])->format('d/m/Y') : '');
            $sheet->setCellValue("G{$row}", $item['date_debut_prevue'] ? Carbon::parse($item['date_debut_prevue'])->format('d/m/Y') : '');
            $sheet->setCellValue("H{$row}", $item['date_fin_prevue'] ? Carbon::parse($item['date_fin_prevue'])->format('d/m/Y') : '');

            $sheet->setCellValue("I{$row}", $item['pourcentage_avancement'] / 100);
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            $avancementColor = $item['pourcentage_avancement'] >= 80 ? 'DCFCE7' : ($item['pourcentage_avancement'] >= 50 ? 'FEF3C7' : 'FEE2E2');
            $sheet->getStyle("I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($avancementColor);

            $sheet->setCellValue("J{$row}", $item['montant_proforma_ttc']);
            $sheet->setCellValue("K{$row}", $item['montant_paye']);
            $sheet->setCellValue("L{$row}", $item['reste_a_payer']);
            $sheet->getStyle("J{$row}:L{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('16A34A');
            $sheet->getStyle("L{$row}")->getFont()->getColor()->setRGB('DC2626');

            $sheet->setCellValue("M{$row}", $item['statut']);
            $sheet->setCellValue("N{$row}", $item['jours_retard']);
            $sheet->setCellValue("O{$row}", $item['penalites_appliquees']);
            $sheet->getStyle("O{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');

            $totaux['montant'] += $item['montant_proforma_ttc'];
            $totaux['paye'] += $item['montant_paye'];
            $totaux['reste'] += $item['reste_a_payer'];
            $totaux['penalites'] += $item['penalites_appliquees'];
            $row++;
        }

        // Totaux
        $row++;
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAUX');
        $sheet->setCellValue("J{$row}", $totaux['montant']);
        $sheet->setCellValue("K{$row}", $totaux['paye']);
        $sheet->setCellValue("L{$row}", $totaux['reste']);
        $sheet->setCellValue("O{$row}", $totaux['penalites']);
        $sheet->getStyle("J{$row}:L{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        $sheet->getStyle("O{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        $sheet->getStyle("A{$row}:O{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FED7AA');
        $sheet->getStyle("A{$row}:O{$row}")->getFont()->setBold(true);

        // Largeurs
        $widths = ['A'=>15,'B'=>35,'C'=>30,'D'=>25,'E'=>16,'F'=>14,'G'=>14,'H'=>14,'I'=>12,'J'=>18,'K'=>16,'L'=>16,'M'=>12,'N'=>10,'O'=>15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->freezePane('A5');

        $filename = $filename ?? 'rapport_lots_en_cours_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/exports/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    public function genererLotsEnCoursPdf(?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getLotsEnCoursData();

        $totaux = [
            'proforma' => array_sum(array_column($data, 'montant_proforma_ttc')),
            'paye' => array_sum(array_column($data, 'montant_paye')),
            'reste' => array_sum(array_column($data, 'reste_a_payer')),
            'penalites' => array_sum(array_column($data, 'penalites_appliquees')),
        ];

        $pdf = PDF::loadView('exports.rapport_lots_en_cours', [
            'data' => $data,
            'dateGeneration' => Carbon::now()->format('d/m/Y à H:i'),
            'totalProforma' => $this->formatMontant($totaux['proforma']),
            'totalPaye' => $this->formatMontant($totaux['paye']),
            'totalReste' => $this->formatMontant($totaux['reste']),
            'totalPenalites' => $this->formatMontant($totaux['penalites']),
            'tauxPaiement' => $totaux['proforma'] > 0 ? number_format($totaux['paye'] / $totaux['proforma'] * 100, 2) . '%' : '0%',
            'nombreLots' => count($data),
        ])->setPaper('a4', 'landscape');

        $filename = $filename ?? 'rapport_lots_en_cours_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        $path = storage_path('app/exports/' . $filename);
        $pdf->save($path);

        return $path;
    }

    /**
     * ========================================================================
     * RAPPORT 2: FACTURES ET PAIEMENTS D'UN PRESTATAIRE
     * Mis à jour: Appel d'offre dans factures, Lot/AO/Référence dans paiements
     * ========================================================================
     */

    public function getFacturesPaiementsData(string $prestataireId): array
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        $attributions = PrestataireLot::where('prestataire_id', $prestataireId)
            ->with(['proforma.facture.paiements.banque', 'lot.appelOffre'])
            ->get();

        $factures = [];
        foreach ($attributions as $attribution) {
            $proforma = $attribution->proforma;
            $lot = $attribution->lot;
            $appelOffre = $lot?->appelOffre;

            if ($proforma && $proforma->facture) {
                $facture = $proforma->facture;

                $paiements = $facture->paiements->map(function ($paiement) use ($lot, $appelOffre) {
                    return [
                        'id_paiement' => $paiement->id_paiement,
                        'date_paiement' => $paiement->date_effectif_paiement,
                        'montant' => $paiement->montant_net_paye_paiement ?? 0,
                        'statut' => Paiement::getStatuts()[$paiement->statut_paiement] ?? 'Inconnu',
                        'statut_code' => $paiement->statut_paiement,
                        'nom_banque' => $paiement->banque->nom_banque ?? '',
                        'numero_compte' => $paiement->banque->numero_compte_banque ?? '',
                        'reference_paiement' => $paiement->observations_paiement ?? '',
                        // AJOUTS: Lot et Appel d'offre
                        'lot_numero' => $lot->numero ?? '',
                        'lot_libelle' => $lot->libelle ?? '',
                        'appel_offre_numero' => $appelOffre->numero_appel_offre ?? '',
                        'appel_offre_libelle' => $appelOffre->libelle_critere_appel_offre ?? '',
                    ];
                })->toArray();

                $factures[] = [
                    'id_facture' => $facture->id_facture,
                    'numero_facture' => $facture->numero_facture,
                    'numero_proforma' => $proforma->numero_proforma,
                    // AJOUT: Appel d'offre complet
                    'appel_offre_numero' => $appelOffre->numero_appel_offre ?? '',
                    'appel_offre_libelle' => $appelOffre->libelle_critere_appel_offre ?? '',
                    'appel_offre_complet' => $appelOffre ? $appelOffre->numero_appel_offre . ' - ' . $appelOffre->libelle_critere_appel_offre : '',
                    // Lot
                    'lot_numero' => $lot->numero ?? '',
                    'lot_libelle' => $lot->libelle ?? '',
                    'lot_complet' => $lot ? $lot->numero . ' - ' . $lot->libelle : '',
                    // Dates et montants
                    'date_facture' => $facture->date_facture,
                    'date_reception' => $facture->date_reception_facture,
                    'montant_facture' => $facture->montant_facture,
                    'statut_facture' => Facture::getStatuts()[$facture->statut_facture] ?? 'Inconnu',
                    'commentaire' => $facture->comment_facture ?? '',
                    'paiements' => $paiements,
                ];
            }
        }

        return [
            'prestataire' => [
                'id' => $prestataire->id_prestataire,
                'raison_sociale' => $prestataire->raison_sociale_prestataire,
                'numero_identification' => $prestataire->numero_identification_prestataire,
                'email' => $prestataire->email_prestataire,
                'telephone' => $prestataire->telephone_principal_prestataire,
                'adresse' => $prestataire->adresse_prestataire,
                'ville' => $prestataire->ville_prestataire,
                'pays' => $prestataire->pays_prestataire,
                'numero_rccm' => $prestataire->numero_rccm_prestataire,
                'numero_cc' => $prestataire->numero_cc_prestataire,
                'representant_legal' => $this->getRepresentantLegal($prestataire),
                'statut' => $prestataire->statut_prestataire ? 'Actif' : 'Inactif',
            ],
            'factures' => $factures,
        ];
    }

    public function genererFacturesPaiementsExcel(string $prestataireId, ?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getFacturesPaiementsData($prestataireId);
        $prestataire = $data['prestataire'];
        $factures = $data['factures'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Factures et Paiements');

        // Titre
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'RAPPORT DES FACTURES ET PAIEMENTS');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'Généré le ' . Carbon::now()->format('d/m/Y à H:i'));

        // Section prestataire
        $row = 4;
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("A{$row}", 'INFORMATIONS DU PRESTATAIRE');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $infoRows = [
            ['Raison Sociale:', $prestataire['raison_sociale'], 'N° Identification:', $prestataire['numero_identification']],
            ['Email:', $prestataire['email'], 'Téléphone:', $prestataire['telephone']],
            ['Adresse:', $prestataire['adresse'] . ', ' . $prestataire['ville'], 'N° RCCM:', $prestataire['numero_rccm']],
        ];

        foreach ($infoRows as $info) {
            $row++;
            $sheet->setCellValue("A{$row}", $info[0]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("B{$row}:E{$row}");
            $sheet->setCellValue("B{$row}", $info[1]);
            $sheet->setCellValue("G{$row}", $info[2]);
            $sheet->getStyle("G{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("H{$row}:K{$row}");
            $sheet->setCellValue("H{$row}", $info[3]);
        }

        // Section LISTE DES FACTURES (avec Appel d'Offre)
        $row += 2;
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("A{$row}", 'LISTE DES FACTURES');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $row++;
        $factureHeaders = ['N° Facture', 'N° Proforma', 'Appel d\'Offre', 'Lot', 'Date Facture', 'Montant', 'Payé', 'Reste', 'Statut'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($factureHeaders as $idx => $header) {
            $sheet->setCellValue($cols[$idx] . $row, $header);
        }
        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($this->getSubHeaderStyle());

        $totalFactures = 0;
        $totalPaye = 0;
        $totalReste = 0;

        foreach ($factures as $facture) {
            $row++;
            $montantPaye = array_sum(array_column($facture['paiements'], 'montant'));
            $reste = $facture['montant_facture'] - $montantPaye;

            $sheet->setCellValue("A{$row}", $facture['numero_facture']);
            $sheet->setCellValue("B{$row}", $facture['numero_proforma']);
            $sheet->setCellValue("C{$row}", $facture['appel_offre_complet']); // APPEL D'OFFRE
            $sheet->setCellValue("D{$row}", $facture['lot_complet']); // LOT
            $sheet->setCellValue("E{$row}", $facture['date_facture'] ? Carbon::parse($facture['date_facture'])->format('d/m/Y') : '');
            $sheet->setCellValue("F{$row}", $facture['montant_facture']);
            $sheet->setCellValue("G{$row}", $montantPaye);
            $sheet->setCellValue("H{$row}", $reste);
            $sheet->setCellValue("I{$row}", $facture['statut_facture']);

            $sheet->getStyle("F{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB('16A34A');
            if ($reste > 0) $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('DC2626');

            $totalFactures += $facture['montant_facture'];
            $totalPaye += $montantPaye;
            $totalReste += $reste;
        }

        // Totaux factures
        $row++;
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAUX');
        $sheet->setCellValue("F{$row}", $totalFactures);
        $sheet->setCellValue("G{$row}", $totalPaye);
        $sheet->setCellValue("H{$row}", $totalReste);
        $sheet->getStyle("F{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FED7AA');
        $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);

        // Section DÉTAIL DES PAIEMENTS (avec Lot, Appel d'Offre, Référence)
        $row += 2;
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("A{$row}", 'DÉTAIL DES PAIEMENTS');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $row++;
        $paiementHeaders = ['N° Facture', 'Appel d\'Offre', 'Lot', 'Date Paiement', 'Montant', 'Statut', 'Banque', 'N° Compte', 'Référence Paiement'];
        $pCols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($paiementHeaders as $idx => $header) {
            $sheet->setCellValue($pCols[$idx] . $row, $header);
        }
        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($this->getSubHeaderStyle());

        $totalPaiements = 0;
        foreach ($factures as $facture) {
            foreach ($facture['paiements'] as $paiement) {
                $row++;
                $sheet->setCellValue("A{$row}", $facture['numero_facture']);
                $sheet->setCellValue("B{$row}", $paiement['appel_offre_numero']); // APPEL D'OFFRE
                $sheet->setCellValue("C{$row}", $paiement['lot_numero'] . ' - ' . substr($paiement['lot_libelle'], 0, 20)); // LOT
                $sheet->setCellValue("D{$row}", $paiement['date_paiement'] ? Carbon::parse($paiement['date_paiement'])->format('d/m/Y') : '');
                $sheet->setCellValue("E{$row}", $paiement['montant']);
                $sheet->setCellValue("F{$row}", $paiement['statut']);
                $sheet->setCellValue("G{$row}", $paiement['nom_banque']);
                $sheet->setCellValue("H{$row}", $paiement['numero_compte']);
                $sheet->setCellValue("I{$row}", $paiement['reference_paiement']); // RÉFÉRENCE

                $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
                $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB('16A34A');

                if (in_array($paiement['statut_code'], [Paiement::STATUT_VALIDE, Paiement::STATUT_PAYE])) {
                    $totalPaiements += $paiement['montant'];
                }
            }
        }

        // Total paiements
        $row++;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL PAIEMENTS');
        $sheet->setCellValue("E{$row}", $totalPaiements);
        $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FED7AA');
        $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);

        // Largeurs
        $widths = ['A'=>18,'B'=>20,'C'=>30,'D'=>25,'E'=>14,'F'=>18,'G'=>16,'H'=>18,'I'=>25,'J'=>15,'K'=>15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = $filename ?? 'rapport_factures_' . preg_replace('/[^a-zA-Z0-9]/', '_', $prestataire['raison_sociale']) . '_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/exports/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    public function genererFacturesPaiementsPdf(string $prestataireId, ?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getFacturesPaiementsData($prestataireId);

        $totalFactures = array_sum(array_column($data['factures'], 'montant_facture'));
        $totalPaye = 0;
        $factureSoldees = 0;

        foreach ($data['factures'] as $facture) {
            $totalPaye += array_sum(array_column($facture['paiements'], 'montant'));
            if ($facture['statut_facture'] === 'Payée') $factureSoldees++;
        }

        $pdf = PDF::loadView('exports.rapport_factures_paiements', [
            'prestataire' => $data['prestataire'],
            'factures' => $data['factures'],
            'dateGeneration' => Carbon::now()->format('d/m/Y à H:i'),
            'totalFactures' => $this->formatMontant($totalFactures),
            'totalPaye' => $this->formatMontant($totalPaye),
            'totalReste' => $this->formatMontant($totalFactures - $totalPaye),
            'tauxReglement' => $totalFactures > 0 ? number_format($totalPaye / $totalFactures * 100, 2) . '%' : '0%',
            'nombreFactures' => count($data['factures']),
            'facturesSoldees' => $factureSoldees,
        ])->setPaper('a4', 'landscape');

        $filename = $filename ?? 'rapport_factures_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        $path = storage_path('app/exports/' . $filename);
        $pdf->save($path);

        return $path;
    }

    /**
     * ========================================================================
     * RAPPORT 3: FICHE DÉTAILLÉE D'UN PRESTATAIRE (NOUVEAU)
     * Liste des lots attribués avec avancement, montants, reste à payer, etc.
     * ========================================================================
     */

    public function getFichePrestataire(string $prestataireId): array
    {
        $prestataire = Prestataire::with(['banques'])->findOrFail($prestataireId);

        $attributions = PrestataireLot::where('prestataire_id', $prestataireId)
            ->with(['lot.appelOffre', 'proforma.facture.paiements'])
            ->orderBy('created_at', 'desc')
            ->get();

        $lots = $attributions->map(function ($attribution) {
            $lot = $attribution->lot;
            $proforma = $attribution->proforma;
            $appelOffre = $lot?->appelOffre;
            $facture = $proforma?->facture;

            $montantProformaTTC = $proforma ? $proforma->calculerMontantTTC() : 0;
            $montantPaye = $attribution->montant_paye ?? 0;

            if ($facture) {
                $montantPaye = $facture->paiementsValides()->sum('montant_net_paye_paiement') ?? 0;
            }

            $resteAPayer = max(0, $montantProformaTTC - $montantPaye);
            $tauxPaiement = $montantProformaTTC > 0 ? ($montantPaye / $montantProformaTTC * 100) : 0;

            return [
                'numero_attribution' => $attribution->numero_attribution,
                'lot_numero' => $lot->numero ?? '',
                'lot_libelle' => $lot->libelle ?? '',
                'lot_description' => $lot->description_critere ?? '',
                'appel_offre_numero' => $appelOffre->numero_appel_offre ?? '',
                'appel_offre_libelle' => $appelOffre->libelle_critere_appel_offre ?? '',
                'date_attribution' => $attribution->date_attribution,
                'date_debut_prevue' => $attribution->date_debut_prevue,
                'date_fin_prevue' => $attribution->date_fin_prevue,
                'pourcentage_avancement' => $attribution->pourcentage_avancement ?? 0,
                'statut' => PrestataireLot::STATUT_LABELS[$attribution->statut_attribution] ?? 'Inconnu',
                'statut_code' => $attribution->statut_attribution,
                'is_active' => $attribution->is_active,
                'montant_proforma_ht' => $proforma->montant_retenu_proforma ?? 0,
                'montant_proforma_ttc' => $montantProformaTTC,
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'taux_paiement' => $tauxPaiement,
                'jours_retard' => $attribution->jours_retard ?? 0,
                'penalites_appliquees' => $attribution->penalites_appliquees ?? 0,
                'numero_proforma' => $proforma->numero_proforma ?? '',
                'numero_facture' => $facture->numero_facture ?? '',
                'observations' => $attribution->observations ?? '',
            ];
        })->toArray();

        // Statistiques
        $lotsActifs = collect($lots)->whereIn('statut_code', [PrestataireLot::STATUT_ATTRIBUE, PrestataireLot::STATUT_SUSPENDU]);
        $stats = [
            'total_lots' => count($lots),
            'lots_en_cours' => $lotsActifs->count(),
            'lots_termines' => collect($lots)->where('statut_code', PrestataireLot::STATUT_TERMINE)->count(),
            'lots_retires' => collect($lots)->where('statut_code', PrestataireLot::STATUT_RETIRE)->count(),
            'montant_total_engage' => collect($lots)->sum('montant_proforma_ttc'),
            'montant_total_paye' => collect($lots)->sum('montant_paye'),
            'reste_total_a_payer' => collect($lots)->sum('reste_a_payer'),
            'penalites_totales' => collect($lots)->sum('penalites_appliquees'),
            'avancement_moyen' => $lotsActifs->avg('pourcentage_avancement') ?? 0,
        ];
        $stats['taux_paiement_global'] = $stats['montant_total_engage'] > 0 ?
            ($stats['montant_total_paye'] / $stats['montant_total_engage']) * 100 : 0;

        $banques = $prestataire->banques->map(fn($b) => [
            'nom' => $b->nom_banque,
            'numero_compte' => $b->numero_compte_banque,
            'iban' => $b->iban_banque,
            'titulaire' => $b->titulaire_compte_banque,
        ])->toArray();

        return [
            'prestataire' => [
                'id' => $prestataire->id_prestataire,
                'raison_sociale' => $prestataire->raison_sociale_prestataire,
                'numero_identification' => $prestataire->numero_identification_prestataire,
                'email' => $prestataire->email_prestataire,
                'telephone' => $prestataire->telephone_principal_prestataire,
                'adresse' => $prestataire->adresse_prestataire,
                'ville' => $prestataire->ville_prestataire,
                'pays' => $prestataire->pays_prestataire,
                'numero_rccm' => $prestataire->numero_rccm_prestataire,
                'numero_cc' => $prestataire->numero_cc_prestataire,
                'representant_legal' => $this->getRepresentantLegal($prestataire),
                'statut' => $prestataire->statut_prestataire ? 'Actif' : 'Inactif',
            ],
            'lots' => $lots,
            'statistiques' => $stats,
            'banques' => $banques,
        ];
    }

    public function genererFichePrestataireExcel(string $prestataireId, ?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getFichePrestataire($prestataireId);
        $prestataire = $data['prestataire'];
        $lots = $data['lots'];
        $stats = $data['statistiques'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Fiche Prestataire');

        // Titre
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'FICHE PRESTATAIRE - LOTS ATTRIBUÉS');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', $prestataire['raison_sociale']);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', 'Généré le ' . Carbon::now()->format('d/m/Y à H:i'));

        // Section infos prestataire
        $row = 5;
        $sheet->mergeCells("A{$row}:L{$row}");
        $sheet->setCellValue("A{$row}", 'INFORMATIONS GÉNÉRALES');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $infoRows = [
            ['N° Identification:', $prestataire['numero_identification'], 'Email:', $prestataire['email']],
            ['Téléphone:', $prestataire['telephone'], 'Adresse:', $prestataire['adresse'] . ', ' . $prestataire['ville']],
            ['N° RCCM:', $prestataire['numero_rccm'], 'N° CC:', $prestataire['numero_cc']],
            ['Représentant Légal:', $prestataire['representant_legal'], 'Statut:', $prestataire['statut']],
        ];

        foreach ($infoRows as $info) {
            $row++;
            $sheet->setCellValue("A{$row}", $info[0]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("B{$row}:E{$row}");
            $sheet->setCellValue("B{$row}", $info[1]);
            $sheet->setCellValue("G{$row}", $info[2]);
            $sheet->getStyle("G{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("H{$row}:L{$row}");
            $sheet->setCellValue("H{$row}", $info[3]);
        }

        // Section statistiques
        $row += 2;
        $sheet->mergeCells("A{$row}:L{$row}");
        $sheet->setCellValue("A{$row}", 'STATISTIQUES GLOBALES');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $statRows = [
            ['Total Lots:', $stats['total_lots'], 'Lots en cours:', $stats['lots_en_cours']],
            ['Lots terminés:', $stats['lots_termines'], 'Lots retirés:', $stats['lots_retires']],
            ['Montant engagé:', $this->formatMontant($stats['montant_total_engage']), 'Montant payé:', $this->formatMontant($stats['montant_total_paye'])],
            ['Reste à payer:', $this->formatMontant($stats['reste_total_a_payer']), 'Taux paiement:', number_format($stats['taux_paiement_global'], 2) . '%'],
            ['Avancement moyen:', number_format($stats['avancement_moyen'], 2) . '%', 'Pénalités:', $this->formatMontant($stats['penalites_totales'])],
        ];

        foreach ($statRows as $stat) {
            $row++;
            $sheet->setCellValue("A{$row}", $stat[0]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("B{$row}:E{$row}");
            $sheet->setCellValue("B{$row}", $stat[1]);
            $sheet->setCellValue("G{$row}", $stat[2]);
            $sheet->getStyle("G{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("H{$row}:L{$row}");
            $sheet->setCellValue("H{$row}", $stat[3]);
            $sheet->getStyle("A{$row}:L{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF7ED');
        }

        // Section liste des lots
        $row += 2;
        $sheet->mergeCells("A{$row}:L{$row}");
        $sheet->setCellValue("A{$row}", 'LISTE DES LOTS ATTRIBUÉS');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $row++;
        $lotHeaders = ['N° Lot', 'Libellé', 'Appel d\'Offre', 'Avancement', 'Montant TTC', 'Payé', 'Reste', 'Statut', 'Retard', 'Pénalités', 'Date Fin'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        foreach ($lotHeaders as $idx => $header) {
            $sheet->setCellValue($cols[$idx] . $row, $header);
        }
        $sheet->getStyle("A{$row}:K{$row}")->applyFromArray($this->getSubHeaderStyle());

        $totaux = ['montant' => 0, 'paye' => 0, 'reste' => 0, 'penalites' => 0];

        foreach ($lots as $lot) {
            $row++;
            $sheet->setCellValue("A{$row}", $lot['lot_numero']);
            $sheet->setCellValue("B{$row}", substr($lot['lot_libelle'], 0, 30));
            $sheet->setCellValue("C{$row}", $lot['appel_offre_numero']);

            $sheet->setCellValue("D{$row}", $lot['pourcentage_avancement'] / 100);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            $avColor = $lot['pourcentage_avancement'] >= 80 ? 'DCFCE7' : ($lot['pourcentage_avancement'] >= 50 ? 'FEF3C7' : 'FEE2E2');
            $sheet->getStyle("D{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($avColor);

            $sheet->setCellValue("E{$row}", $lot['montant_proforma_ttc']);
            $sheet->setCellValue("F{$row}", $lot['montant_paye']);
            $sheet->setCellValue("G{$row}", $lot['reste_a_payer']);
            $sheet->getStyle("E{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('16A34A');
            if ($lot['reste_a_payer'] > 0) $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB('DC2626');

            $sheet->setCellValue("H{$row}", $lot['statut']);
            $statutColor = match($lot['statut']) {
                'Attribué' => 'DCFCE7', 'Terminé' => 'DBEAFE', 'Suspendu' => 'FEF3C7', 'Retiré' => 'FEE2E2', default => 'E5E7EB'
            };
            $sheet->getStyle("H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($statutColor);

            $sheet->setCellValue("I{$row}", $lot['jours_retard'] > 0 ? $lot['jours_retard'] . ' j' : '-');
            $sheet->setCellValue("J{$row}", $lot['penalites_appliquees']);
            $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            $sheet->setCellValue("K{$row}", $lot['date_fin_prevue'] ? Carbon::parse($lot['date_fin_prevue'])->format('d/m/Y') : '');

            $totaux['montant'] += $lot['montant_proforma_ttc'];
            $totaux['paye'] += $lot['montant_paye'];
            $totaux['reste'] += $lot['reste_a_payer'];
            $totaux['penalites'] += $lot['penalites_appliquees'];
        }

        // Totaux
        $row++;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAUX');
        $sheet->setCellValue("E{$row}", $totaux['montant']);
        $sheet->setCellValue("F{$row}", $totaux['paye']);
        $sheet->setCellValue("G{$row}", $totaux['reste']);
        $sheet->setCellValue("J{$row}", $totaux['penalites']);
        $sheet->getStyle("E{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        $sheet->getStyle("A{$row}:K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FED7AA');
        $sheet->getStyle("A{$row}:K{$row}")->getFont()->setBold(true);

        // Largeurs
        $widths = ['A'=>15,'B'=>30,'C'=>20,'D'=>12,'E'=>18,'F'=>16,'G'=>16,'H'=>12,'I'=>10,'J'=>14,'K'=>14,'L'=>12];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = $filename ?? 'fiche_prestataire_' . preg_replace('/[^a-zA-Z0-9]/', '_', $prestataire['raison_sociale']) . '_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/exports/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    public function genererFichePrestatairePdf(string $prestataireId, ?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getFichePrestataire($prestataireId);

        $pdf = PDF::loadView('exports.fiche_prestataire', [
            'prestataire' => $data['prestataire'],
            'lots' => $data['lots'],
            'statistiques' => $data['statistiques'],
            'banques' => $data['banques'],
            'dateGeneration' => Carbon::now()->format('d/m/Y à H:i'),
            'formatMontant' => fn($m) => $this->formatMontant($m),
        ])->setPaper('a4', 'landscape');

        $filename = $filename ?? 'fiche_prestataire_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        $path = storage_path('app/exports/' . $filename);
        $pdf->save($path);

        return $path;
    }

    /**
     * ========================================================================
     * RAPPORT 4: FICHE DÉTAILLÉE D'UNE FACTURE (NOUVEAU)
     * ========================================================================
     */

    public function getFicheFacture(string $factureId): array
    {
        $facture = Facture::with([
            'proforma.prestataireLotsAttributions.prestataire',
            'proforma.prestataireLotsAttributions.lot.appelOffre',
            'paiements.banque',
            'createur'
        ])->findOrFail($factureId);

        $proforma = $facture->proforma;
        $attribution = $proforma?->prestatairePrincipal;
        $prestataire = $attribution?->prestataire;
        $lot = $attribution?->lot;
        $appelOffre = $lot?->appelOffre;

        $montantPaye = $facture->paiementsValides()->sum('montant_net_paye_paiement') ?? 0;
        $resteAPayer = max(0, $facture->montant_facture - $montantPaye);

        $paiements = $facture->paiements->map(fn($p) => [
            'id' => $p->id_paiement,
            'date_creation' => $p->created_at,
            'date_validation' => $p->date_validation_paiement,
            'date_paiement' => $p->date_effectif_paiement,
            'montant' => $p->montant_net_paye_paiement ?? 0,
            'statut' => Paiement::getStatuts()[$p->statut_paiement] ?? 'Inconnu',
            'statut_code' => $p->statut_paiement,
            'banque_nom' => $p->banque->nom_banque ?? '',
            'banque_compte' => $p->banque->numero_compte_banque ?? '',
            'reference' => $p->observations_paiement ?? '',
        ])->toArray();

        return [
            'facture' => [
                'id' => $facture->id_facture,
                'numero' => $facture->numero_facture,
                'date_facture' => $facture->date_facture,
                'date_reception' => $facture->date_reception_facture,
                'montant' => $facture->montant_facture,
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'taux_paiement' => $facture->montant_facture > 0 ? ($montantPaye / $facture->montant_facture * 100) : 0,
                'statut' => Facture::getStatuts()[$facture->statut_facture] ?? 'Inconnu',
                'commentaire' => $facture->comment_facture ?? '',
                'createur' => $facture->createur?->name ?? '',
            ],
            'proforma' => $proforma ? [
                'numero' => $proforma->numero_proforma,
                'date' => $proforma->date_proforma,
                'montant_ht' => $proforma->montant_retenu_proforma ?? 0,
                'taxe' => $proforma->taxe_montant ?? 0,
                'remise' => $proforma->remise_montant_proforma ?? 0,
                'montant_ttc' => $proforma->calculerMontantTTC(),
            ] : null,
            'prestataire' => $prestataire ? [
                'raison_sociale' => $prestataire->raison_sociale_prestataire,
                'numero_identification' => $prestataire->numero_identification_prestataire,
                'email' => $prestataire->email_prestataire,
                'telephone' => $prestataire->telephone_principal_prestataire,
                'adresse' => $prestataire->adresse_prestataire . ', ' . $prestataire->ville_prestataire,
            ] : null,
            'lot' => $lot ? [
                'numero' => $lot->numero,
                'libelle' => $lot->libelle,
            ] : null,
            'appel_offre' => $appelOffre ? [
                'numero' => $appelOffre->numero_appel_offre,
                'libelle' => $appelOffre->libelle_critere_appel_offre,
            ] : null,
            'paiements' => $paiements,
        ];
    }

    public function genererFicheFactureExcel(string $factureId, ?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getFicheFacture($factureId);
        $facture = $data['facture'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Fiche Facture');

        // Titre
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'FICHE FACTURE DÉTAILLÉE');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', $facture['numero']);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Généré le ' . Carbon::now()->format('d/m/Y à H:i'));

        // Section facture
        $row = 5;
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'INFORMATIONS DE LA FACTURE');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $factureInfo = [
            ['N° Facture:', $facture['numero'], 'Statut:', $facture['statut']],
            ['Date Facture:', $facture['date_facture'] ? Carbon::parse($facture['date_facture'])->format('d/m/Y') : '', 'Date Réception:', $facture['date_reception'] ? Carbon::parse($facture['date_reception'])->format('d/m/Y') : ''],
            ['Montant:', $this->formatMontant($facture['montant']), 'Payé:', $this->formatMontant($facture['montant_paye'])],
            ['Reste à Payer:', $this->formatMontant($facture['reste_a_payer']), 'Taux:', number_format($facture['taux_paiement'], 2) . '%'],
        ];

        foreach ($factureInfo as $info) {
            $row++;
            $sheet->setCellValue("A{$row}", $info[0]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("B{$row}:C{$row}");
            $sheet->setCellValue("B{$row}", $info[1]);
            $sheet->setCellValue("E{$row}", $info[2]);
            $sheet->getStyle("E{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("F{$row}:G{$row}");
            $sheet->setCellValue("F{$row}", $info[3]);
        }

        // Prestataire
        if ($data['prestataire']) {
            $row += 2;
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", 'PRESTATAIRE');
            $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

            $row++;
            $sheet->setCellValue("A{$row}", 'Raison Sociale:');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->mergeCells("B{$row}:G{$row}");
            $sheet->setCellValue("B{$row}", $data['prestataire']['raison_sociale'] . ' - ' . $data['prestataire']['email']);
        }

        // Appel d'offre et Lot
        if ($data['appel_offre'] || $data['lot']) {
            $row += 2;
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", 'APPEL D\'OFFRE ET LOT');
            $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

            if ($data['appel_offre']) {
                $row++;
                $sheet->setCellValue("A{$row}", 'Appel d\'Offre:');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $sheet->mergeCells("B{$row}:G{$row}");
                $sheet->setCellValue("B{$row}", $data['appel_offre']['numero'] . ' - ' . $data['appel_offre']['libelle']);
            }
            if ($data['lot']) {
                $row++;
                $sheet->setCellValue("A{$row}", 'Lot:');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $sheet->mergeCells("B{$row}:G{$row}");
                $sheet->setCellValue("B{$row}", $data['lot']['numero'] . ' - ' . $data['lot']['libelle']);
            }
        }

        // Paiements
        $row += 2;
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'HISTORIQUE DES PAIEMENTS');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        if (!empty($data['paiements'])) {
            $row++;
            $pHeaders = ['Date Paiement', 'Montant', 'Statut', 'Banque', 'Référence'];
            $pCols = ['A', 'B', 'C', 'D', 'E'];
            foreach ($pHeaders as $idx => $header) {
                $sheet->setCellValue($pCols[$idx] . $row, $header);
            }
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray($this->getSubHeaderStyle());

            $totalP = 0;
            foreach ($data['paiements'] as $p) {
                $row++;
                $sheet->setCellValue("A{$row}", $p['date_paiement'] ? Carbon::parse($p['date_paiement'])->format('d/m/Y') : '');
                $sheet->setCellValue("B{$row}", $p['montant']);
                $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
                $sheet->setCellValue("C{$row}", $p['statut']);
                $sheet->setCellValue("D{$row}", $p['banque_nom']);
                $sheet->setCellValue("E{$row}", $p['reference']);

                if (in_array($p['statut_code'], [Paiement::STATUT_VALIDE, Paiement::STATUT_PAYE])) {
                    $totalP += $p['montant'];
                }
            }

            $row++;
            $sheet->setCellValue("A{$row}", 'TOTAL');
            $sheet->setCellValue("B{$row}", $totalP);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            $sheet->getStyle("A{$row}:E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FED7AA');
            $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        } else {
            $row++;
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", 'Aucun paiement enregistré');
        }

        // Largeurs
        $widths = ['A'=>18,'B'=>18,'C'=>18,'D'=>20,'E'=>25,'F'=>15,'G'=>15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = $filename ?? 'fiche_facture_' . preg_replace('/[^a-zA-Z0-9]/', '_', $facture['numero']) . '_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/exports/' . $filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    public function genererFicheFacturePdf(string $factureId, ?string $filename = null): string
    {
        $this->ensureExportDirectory();
        $data = $this->getFicheFacture($factureId);

        $pdf = PDF::loadView('exports.fiche_facture', [
            'facture' => $data['facture'],
            'proforma' => $data['proforma'],
            'prestataire' => $data['prestataire'],
            'lot' => $data['lot'],
            'appelOffre' => $data['appel_offre'],
            'paiements' => $data['paiements'],
            'dateGeneration' => Carbon::now()->format('d/m/Y à H:i'),
            'formatMontant' => fn($m) => $this->formatMontant($m),
        ])->setPaper('a4', 'portrait');

        $filename = $filename ?? 'fiche_facture_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        $path = storage_path('app/exports/' . $filename);
        $pdf->save($path);

        return $path;
    }
}
