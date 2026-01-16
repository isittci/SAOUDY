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
 */
class RapportExportServiceCopy
{
    /**
     * Formatage des montants en FCFA
     */
    private function formatMontant($montant): string
    {
        return number_format($montant, 0, ',', ' ') . ' FCFA';
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
     * ========================================================================
     * RAPPORT 1: LISTE DES LOTS EN COURS AVEC AVANCEMENT
     * ========================================================================
     */

    /**
     * Récupère les données des lots en cours
     */
    public function getLotsEnCoursData(): array
    {
        return PrestataireLot::with([
            'lot.appelOffre',
            'prestataire',
            'proforma'
        ])
        ->whereIn('statut_attribution', [
            PrestataireLot::STATUT_ATTRIBUE,
            PrestataireLot::STATUT_SUSPENDU
        ])
        ->where('is_active', true)
        ->get()
        ->map(function ($attribution) {
            $proforma = $attribution->proforma;
            $lot = $attribution->lot;

            // Calcul du montant TTC de la proforma
            $montantProformaTTC = $proforma ? $proforma->calculerMontantTTC() : 0;

            // Calcul du reste à payer
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
                'montant_proforma_ht' => $proforma->montant_retenu_proforma ?? 0,
                'taxe_montant' => $proforma->taxe_montant ?? 0,
                'remise' => $proforma->remise_montant_proforma ?? 0,
                'montant_proforma_ttc' => $montantProformaTTC,
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'statut' => PrestataireLot::STATUT_LABELS[$attribution->statut_attribution] ?? 'Inconnu',
                'jours_retard' => $attribution->jours_retard_actuels ?? 0,
                'penalites_appliquees' => $attribution->penalites_appliquees ?? 0,
                'observations' => $attribution->observations ?? '',
            ];
        })
        ->toArray();
    }

    /**
     * Génère le rapport Excel des lots en cours
     */
    public function genererLotsEnCoursExcel(?string $filename = null): string
    {
        $data = $this->getLotsEnCoursData();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lots en cours');

        // Titre
        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', 'RAPPORT DES LOTS EN COURS AVEC AVANCEMENT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Sous-titre
        $sheet->mergeCells('A2:P2');
        $sheet->setCellValue('A2', 'Généré le ' . Carbon::now()->format('d/m/Y à H:i'));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 12, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // En-têtes (ligne 4)
        $headers = [
            'A4' => 'N° Lot',
            'B4' => 'Libellé',
            'C4' => 'Appel d\'Offre',
            'D4' => 'Prestataire',
            'E4' => 'N° Attribution',
            'F4' => 'Date Attribution',
            'G4' => 'Date Début',
            'H4' => 'Date Fin Prévue',
            'I4' => 'Avancement (%)',
            'J4' => 'Montant Proforma TTC',
            'K4' => 'Montant Payé',
            'L4' => 'Reste à Payer',
            'M4' => 'Statut',
            'N4' => 'Jours Retard',
            'O4' => 'Pénalités',
            'P4' => 'Observations',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $sheet->getStyle('A4:P4')->applyFromArray($this->getHeaderStyle());
        $sheet->getRowDimension(4)->setRowHeight(40);

        // Données
        $row = 5;
        $totalProforma = 0;
        $totalPaye = 0;
        $totalReste = 0;
        $totalPenalites = 0;

        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item['numero_lot']);
            $sheet->setCellValue("B{$row}", $item['libelle_lot']);
            $sheet->setCellValue("C{$row}", $item['appel_offre']);
            $sheet->setCellValue("D{$row}", $item['prestataire']);
            $sheet->setCellValue("E{$row}", $item['numero_attribution']);
            $sheet->setCellValue("F{$row}", $item['date_attribution'] ? Carbon::parse($item['date_attribution'])->format('d/m/Y') : '');
            $sheet->setCellValue("G{$row}", $item['date_debut_prevue'] ? Carbon::parse($item['date_debut_prevue'])->format('d/m/Y') : '');
            $sheet->setCellValue("H{$row}", $item['date_fin_prevue'] ? Carbon::parse($item['date_fin_prevue'])->format('d/m/Y') : '');

            // Avancement avec formatage conditionnel
            $sheet->setCellValue("I{$row}", $item['pourcentage_avancement'] / 100);
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

            $avancementColor = $item['pourcentage_avancement'] >= 80 ? 'DCFCE7' :
                              ($item['pourcentage_avancement'] >= 50 ? 'FEF3C7' : 'FEE2E2');
            $sheet->getStyle("I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($avancementColor);

            // Montants
            $sheet->setCellValue("J{$row}", $item['montant_proforma_ttc']);
            $sheet->setCellValue("K{$row}", $item['montant_paye']);
            $sheet->setCellValue("L{$row}", $item['reste_a_payer']);

            $sheet->getStyle("J{$row}:L{$row}")->getNumberFormat()
                ->setFormatCode('#,##0 "FCFA"');

            $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('16A34A');
            $sheet->getStyle("L{$row}")->getFont()->getColor()->setRGB('DC2626');

            // Statut
            $sheet->setCellValue("M{$row}", $item['statut']);
            $statutColor = match($item['statut']) {
                'Attribué' => 'DCFCE7',
                'Suspendu' => 'FEF3C7',
                'Retiré' => 'FEE2E2',
                default => 'E5E7EB'
            };
            $sheet->getStyle("M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statutColor);

            // Retard et pénalités
            $sheet->setCellValue("N{$row}", $item['jours_retard']);
            if ($item['jours_retard'] > 0) {
                $sheet->getStyle("N{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FEE2E2');
            }

            $sheet->setCellValue("O{$row}", $item['penalites_appliquees']);
            $sheet->getStyle("O{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            if ($item['penalites_appliquees'] > 0) {
                $sheet->getStyle("O{$row}")->getFont()->getColor()->setRGB('DC2626');
            }

            $sheet->setCellValue("P{$row}", $item['observations']);

            // Bordures
            $sheet->getStyle("A{$row}:P{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E5E7EB');

            // Totaux
            $totalProforma += $item['montant_proforma_ttc'];
            $totalPaye += $item['montant_paye'];
            $totalReste += $item['reste_a_payer'];
            $totalPenalites += $item['penalites_appliquees'];

            $row++;
        }

        // Ligne des totaux
        $row++;
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAUX');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("J{$row}", $totalProforma);
        $sheet->setCellValue("K{$row}", $totalPaye);
        $sheet->setCellValue("L{$row}", $totalReste);
        $sheet->setCellValue("O{$row}", $totalPenalites);

        $sheet->getStyle("J{$row}:L{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        $sheet->getStyle("O{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');

        $sheet->getStyle("A{$row}:P{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FED7AA');
        $sheet->getStyle("A{$row}:P{$row}")->getFont()->setBold(true);

        // Largeurs des colonnes
        $widths = ['A' => 15, 'B' => 40, 'C' => 35, 'D' => 30, 'E' => 18, 'F' => 15,
                   'G' => 15, 'H' => 15, 'I' => 14, 'J' => 22, 'K' => 20, 'L' => 20,
                   'M' => 12, 'N' => 12, 'O' => 18, 'P' => 40];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Figer les en-têtes
        $sheet->freezePane('A5');

        // Sauvegarde
        $filename = $filename ?? 'rapport_lots_en_cours_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    /**
     * Génère le rapport PDF des lots en cours
     */
    public function genererLotsEnCoursPdf(?string $filename = null): string
    {
        $data = $this->getLotsEnCoursData();

        // Calcul des totaux
        $totalProforma = array_sum(array_column($data, 'montant_proforma_ttc'));
        $totalPaye = array_sum(array_column($data, 'montant_paye'));
        $totalReste = array_sum(array_column($data, 'reste_a_payer'));
        $totalPenalites = array_sum(array_column($data, 'penalites_appliquees'));
        $tauxPaiement = $totalProforma > 0 ? ($totalPaye / $totalProforma * 100) : 0;

        $pdf = PDF::loadView('exports.rapport_lots_en_cours', [
            'data' => $data,
            'dateGeneration' => Carbon::now()->format('d/m/Y à H:i'),
            'totalProforma' => $this->formatMontant($totalProforma),
            'totalPaye' => $this->formatMontant($totalPaye),
            'totalReste' => $this->formatMontant($totalReste),
            'totalPenalites' => $this->formatMontant($totalPenalites),
            'tauxPaiement' => number_format($tauxPaiement, 2) . '%',
            'nombreLots' => count($data),
        ])->setPaper('a4', 'landscape');

        $filename = $filename ?? 'rapport_lots_en_cours_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        $path = storage_path('app/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);
        return $path;
    }

    /**
     * ========================================================================
     * RAPPORT 2: FACTURES ET PAIEMENTS D'UN PRESTATAIRE
     * ========================================================================
     */

    /**
     * Récupère les données des factures et paiements d'un prestataire
     */
    public function getFacturesPaiementsData(string $prestataireId): array
    {
        $prestataire = Prestataire::findOrFail($prestataireId);

        // Récupérer les factures via les proformas liées aux attributions du prestataire
        $attributions = PrestataireLot::where('prestataire_id', $prestataireId)
            ->with(['proforma.facture.paiements.banque', 'lot'])
            ->get();

        $factures = [];
        foreach ($attributions as $attribution) {
            $proforma = $attribution->proforma;
            if ($proforma && $proforma->facture) {
                $facture = $proforma->facture;

                $paiements = $facture->paiements->map(function ($paiement) {
                    return [
                        'date_paiement' => $paiement->date_effectif_paiement,
                        'montant' => $paiement->montant_net_paye_paiement ?? 0,
                        'statut' => Paiement::getStatuts()[$paiement->statut_paiement] ?? 'Inconnu',
                        'banque' => $paiement->banque ?
                            $paiement->banque->nom_banque . ' - ' . $paiement->banque->numero_compte_banque : '',
                        'reference' => $paiement->observations_paiement ?? '',
                    ];
                })->toArray();

                $factures[] = [
                    'numero_facture' => $facture->numero_facture,
                    'numero_proforma' => $proforma->numero_proforma,
                    'lot' => $attribution->lot->numero . ' - ' . $attribution->lot->libelle,
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
     * Génère le rapport Excel des factures et paiements d'un prestataire
     */
    public function genererFacturesPaiementsExcel(string $prestataireId, ?string $filename = null): string
    {
        $data = $this->getFacturesPaiementsData($prestataireId);
        $prestataire = $data['prestataire'];
        $factures = $data['factures'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Factures et Paiements');

        // Titre
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'RAPPORT DES FACTURES ET PAIEMENTS');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Sous-titre
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Généré le ' . Carbon::now()->format('d/m/Y à H:i'));

        // Section prestataire
        $row = 4;
        $sheet->mergeCells("A{$row}:L{$row}");
        $sheet->setCellValue("A{$row}", 'INFORMATIONS DU PRESTATAIRE');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $infoRows = [
            ['Raison Sociale:', $prestataire['raison_sociale'], 'N° Identification:', $prestataire['numero_identification']],
            ['Email:', $prestataire['email'], 'Téléphone:', $prestataire['telephone']],
            ['Adresse:', $prestataire['adresse'], 'Ville:', $prestataire['ville'] . ', ' . $prestataire['pays']],
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

        // Section factures
        $row += 2;
        $sheet->mergeCells("A{$row}:L{$row}");
        $sheet->setCellValue("A{$row}", 'LISTE DES FACTURES');
        $sheet->getStyle("A{$row}")->applyFromArray($this->getHeaderStyle());

        $row++;
        $factureHeaders = ['N° Facture', 'N° Proforma', 'Lot', 'Date Facture', 'Date Réception',
                          'Montant', 'Payé', 'Reste', 'Statut', 'Commentaire'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        foreach ($factureHeaders as $idx => $header) {
            $sheet->setCellValue($cols[$idx] . $row, $header);
        }
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '78350F']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDBA74']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $totalFactures = 0;
        $totalPaye = 0;
        $totalReste = 0;

        foreach ($factures as $facture) {
            $row++;
            $montantPaye = array_sum(array_column($facture['paiements'], 'montant'));
            $reste = $facture['montant_facture'] - $montantPaye;

            $sheet->setCellValue("A{$row}", $facture['numero_facture']);
            $sheet->setCellValue("B{$row}", $facture['numero_proforma']);
            $sheet->setCellValue("C{$row}", substr($facture['lot'], 0, 40));
            $sheet->setCellValue("D{$row}", $facture['date_facture'] ? Carbon::parse($facture['date_facture'])->format('d/m/Y') : '');
            $sheet->setCellValue("E{$row}", $facture['date_reception'] ? Carbon::parse($facture['date_reception'])->format('d/m/Y') : '');
            $sheet->setCellValue("F{$row}", $facture['montant_facture']);
            $sheet->setCellValue("G{$row}", $montantPaye);
            $sheet->setCellValue("H{$row}", $reste);
            $sheet->setCellValue("I{$row}", $facture['statut_facture']);
            $sheet->setCellValue("J{$row}", $facture['commentaire']);

            $sheet->getStyle("F{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB('16A34A');
            if ($reste > 0) {
                $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('DC2626');
            }

            // Couleur du statut
            $statutColors = [
                'Payée' => 'DCFCE7',
                'Validée' => 'DBEAFE',
                'Partiellement payée' => 'FED7AA',
                'En attente' => 'FEF3C7',
            ];
            if (isset($statutColors[$facture['statut_facture']])) {
                $sheet->getStyle("I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($statutColors[$facture['statut_facture']]);
            }

            $totalFactures += $facture['montant_facture'];
            $totalPaye += $montantPaye;
            $totalReste += $reste;
        }

        // Totaux factures
        $row++;
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAUX');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue("F{$row}", $totalFactures);
        $sheet->setCellValue("G{$row}", $totalPaye);
        $sheet->setCellValue("H{$row}", $totalReste);
        $sheet->getStyle("A{$row}:J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FED7AA');
        $sheet->getStyle("A{$row}:J{$row}")->getFont()->setBold(true);
        $sheet->getStyle("F{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');

        // Largeurs
        $widths = ['A' => 20, 'B' => 18, 'C' => 35, 'D' => 14, 'E' => 14,
                   'F' => 20, 'G' => 20, 'H' => 20, 'I' => 18, 'J' => 35];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Sauvegarde
        $filename = $filename ?? 'rapport_factures_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    /**
     * Génère le rapport PDF des factures et paiements d'un prestataire
     */
    public function genererFacturesPaiementsPdf(string $prestataireId, ?string $filename = null): string
    {
        $data = $this->getFacturesPaiementsData($prestataireId);

        // Calculs
        $totalFactures = array_sum(array_column($data['factures'], 'montant_facture'));
        $totalPaye = 0;
        $factureSoldees = 0;

        foreach ($data['factures'] as $facture) {
            $montantPaye = array_sum(array_column($facture['paiements'], 'montant'));
            $totalPaye += $montantPaye;
            if ($facture['statut_facture'] === 'Payée') {
                $factureSoldees++;
            }
        }

        $totalReste = $totalFactures - $totalPaye;
        $tauxReglement = $totalFactures > 0 ? ($totalPaye / $totalFactures * 100) : 0;

        $pdf = PDF::loadView('exports.rapport_factures_paiements', [
            'prestataire' => $data['prestataire'],
            'factures' => $data['factures'],
            'dateGeneration' => Carbon::now()->format('d/m/Y à H:i'),
            'totalFactures' => $this->formatMontant($totalFactures),
            'totalPaye' => $this->formatMontant($totalPaye),
            'totalReste' => $this->formatMontant($totalReste),
            'tauxReglement' => number_format($tauxReglement, 2) . '%',
            'nombreFactures' => count($data['factures']),
            'facturesSoldees' => $factureSoldees,
        ])->setPaper('a4', 'landscape');

        $filename = $filename ?? 'rapport_factures_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        $path = storage_path('app/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);
        return $path;
    }
}
