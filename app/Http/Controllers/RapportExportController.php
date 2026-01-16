<?php

namespace App\Http\Controllers;

use App\Services\RapportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Contrôleur pour les exports de rapports Excel et PDF
 *
 * Routes disponibles:
 * - GET /exports/lots-en-cours/excel
 * - GET /exports/lots-en-cours/pdf
 * - GET /exports/prestataires/{id}/factures/excel
 * - GET /exports/prestataires/{id}/factures/pdf
 * - GET /exports/prestataires/{id}/fiche/excel      (NOUVEAU)
 * - GET /exports/prestataires/{id}/fiche/pdf        (NOUVEAU)
 * - GET /exports/factures/{id}/fiche/excel          (NOUVEAU)
 * - GET /exports/factures/{id}/fiche/pdf            (NOUVEAU)
 */
class RapportExportController extends Controller
{
    protected RapportExportService $exportService;

    public function __construct(RapportExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Télécharger un fichier
     */
    private function downloadFile(string $path, string $defaultName): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = basename($path);

        return Response::download($path, $filename, [
            'Content-Type' => $this->getContentType($path),
        ])->deleteFileAfterSend(true);
    }

    /**
     * Déterminer le type de contenu
     */
    private function getContentType(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return match($extension) {
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }

    /**
     * ========================================================================
     * RAPPORT 1: LOTS EN COURS AVEC AVANCEMENT
     * ========================================================================
     */

    /**
     * Export Excel des lots en cours
     * GET /exports/lots-en-cours/excel
     */
    public function lotsEnCoursExcel()
    {
        try {
            $path = $this->exportService->genererLotsEnCoursExcel();
            return $this->downloadFile($path, 'rapport_lots_en_cours.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF des lots en cours
     * GET /exports/lots-en-cours/pdf
     */
    public function lotsEnCoursPdf()
    {
        try {
            $path = $this->exportService->genererLotsEnCoursPdf();
            return $this->downloadFile($path, 'rapport_lots_en_cours.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * ========================================================================
     * RAPPORT 2: FACTURES ET PAIEMENTS D'UN PRESTATAIRE
     * ========================================================================
     */

    /**
     * Export Excel des factures et paiements d'un prestataire
     * GET /exports/prestataires/{prestataire}/factures/excel
     */
    public function facturesPaiementsExcel(string $prestataireId)
    {
        try {
            $path = $this->exportService->genererFacturesPaiementsExcel($prestataireId);
            return $this->downloadFile($path, 'rapport_factures_paiements.xlsx');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Prestataire non trouvé.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF des factures et paiements d'un prestataire
     * GET /exports/prestataires/{prestataire}/factures/pdf
     */
    public function facturesPaiementsPdf(string $prestataireId)
    {
        try {
            $path = $this->exportService->genererFacturesPaiementsPdf($prestataireId);
            return $this->downloadFile($path, 'rapport_factures_paiements.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Prestataire non trouvé.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * ========================================================================
     * RAPPORT 3: FICHE PRESTATAIRE AVEC LOTS ATTRIBUÉS (NOUVEAU)
     * ========================================================================
     */

    /**
     * Export Excel de la fiche prestataire
     * GET /exports/prestataires/{prestataire}/fiche/excel
     */
    public function fichePrestataireExcel(string $prestataireId)
    {
        try {
            $path = $this->exportService->genererFichePrestataireExcel($prestataireId);
            return $this->downloadFile($path, 'fiche_prestataire.xlsx');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Prestataire non trouvé.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF de la fiche prestataire
     * GET /exports/prestataires/{prestataire}/fiche/pdf
     */
    public function fichePrestatairePdf(string $prestataireId)
    {
        try {
            $path = $this->exportService->genererFichePrestatairePdf($prestataireId);
            return $this->downloadFile($path, 'fiche_prestataire.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Prestataire non trouvé.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * ========================================================================
     * RAPPORT 4: FICHE FACTURE DÉTAILLÉE (NOUVEAU)
     * ========================================================================
     */

    /**
     * Export Excel de la fiche facture
     * GET /exports/factures/{facture}/fiche/excel
     */
    public function ficheFactureExcel(string $factureId)
    {
        try {
            $path = $this->exportService->genererFicheFactureExcel($factureId);
            return $this->downloadFile($path, 'fiche_facture.xlsx');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Facture non trouvée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF de la fiche facture
     * GET /exports/factures/{facture}/fiche/pdf
     */
    public function ficheFacturePdf(string $factureId)
    {
        try {
            $path = $this->exportService->genererFicheFacturePdf($factureId);
            return $this->downloadFile($path, 'fiche_facture.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Facture non trouvée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * ========================================================================
     * MÉTHODES AJAX POUR PRÉVISUALISATION (OPTIONNEL)
     * ========================================================================
     */

    /**
     * Données JSON des lots en cours (pour prévisualisation)
     */
    public function lotsEnCoursData()
    {
        try {
            $data = $this->exportService->getLotsEnCoursData();
            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => count($data),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Données JSON des factures et paiements d'un prestataire
     */
    public function facturesPaiementsData(string $prestataireId)
    {
        try {
            $data = $this->exportService->getFacturesPaiementsData($prestataireId);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Prestataire non trouvé.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Données JSON de la fiche prestataire
     */
    public function fichePrestataireData(string $prestataireId)
    {
        try {
            $data = $this->exportService->getFichePrestataire($prestataireId);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Prestataire non trouvé.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Données JSON de la fiche facture
     */
    public function ficheFactureData(string $factureId)
    {
        try {
            $data = $this->exportService->getFicheFacture($factureId);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Facture non trouvée.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
