<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Lot;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Types de documents disponibles
     */
    const TYPES_DOCUMENTS = [
        'cahier_charges' => 'Cahier des charges',
        'specification' => 'Spécification technique',
        'plan' => 'Plan',
        'contrat' => 'Contrat',
        'avenant' => 'Avenant',
        'pv_reception' => 'PV de réception',
        'rapport' => 'Rapport',
        'facture' => 'Facture',
        'bon_commande' => 'Bon de commande',
        'bon_livraison' => 'Bon de livraison',
        'attestation' => 'Attestation',
        'certificat' => 'Certificat',
        'offre_technique' => 'Offre technique',
        'offre_financiere' => 'Offre financière',
        'autre' => 'Autre',
    ];

    /**
     * Extensions autorisées
     */
    const EXTENSIONS_AUTORISEES = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];

    /**
     * Taille maximale en Mo
     */
    const TAILLE_MAX_MO = 10;

    /**
     * Afficher la liste des documents d'un lot
     */
    public function index(Request $request, $lotId)
    {
        $lot = Lot::with(['appelOffre'])->findOrFail($lotId);

        $documents = $lot->documents()
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type_document', $request->type);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('titre_document', 'ILIKE', '%' . $request->search . '%')
                      ->orWhere('description_document', 'ILIKE', '%' . $request->search . '%')
                      ->orWhere('fichier_nom_document', 'ILIKE', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('statut'), function ($query) use ($request) {
                if ($request->statut === 'valide') {
                    $query->where('est_valide_document', true);
                } elseif ($request->statut === 'non_valide') {
                    $query->where(function ($q) {
                        $q->where('est_valide_document', false)->orWhereNull('est_valide_document');
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $typesDocuments = self::TYPES_DOCUMENTS;

        return view('documents.index', compact('lot', 'documents', 'typesDocuments'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create($lotId)
    {
        $lot = Lot::with('appelOffre')->findOrFail($lotId);
        $typesDocuments = self::TYPES_DOCUMENTS;
        $extensionsAutorisees = self::EXTENSIONS_AUTORISEES;
        $tailleMaxMo = self::TAILLE_MAX_MO;

        return view('documents.create', compact('lot', 'typesDocuments', 'extensionsAutorisees', 'tailleMaxMo'));
    }

    /**
     * Enregistrer un nouveau document
     */
    public function store(StoreDocumentRequest $request, $lotId)
    {
        $lot = Lot::findOrFail($lotId);

        try {
            DB::beginTransaction();

            // Traitement du fichier
            $fichier = $request->file('fichier');
            $nomOriginal = $fichier->getClientOriginalName();
            $extension = $fichier->getClientOriginalExtension();
            $tailleMo = $fichier->getSize() / 1024 / 1024;
            $mimeType = $fichier->getMimeType();

            // Générer un nom unique pour le fichier
            $nomFichier = Str::slug(pathinfo($nomOriginal, PATHINFO_FILENAME))
                         . '_' . time()
                         . '_' . Str::random(8)
                         . '.' . $extension;

            // Définir le chemin de stockage
            $cheminDossier = 'documents/lots/' . $lot->id_lot;

            // Stocker le fichier
            $cheminFichier = $fichier->storeAs($cheminDossier, $nomFichier, 'public');

            // Déterminer la version du document
            $version = 1;
            if ($request->filled('titre_document')) {
                $documentExistant = Document::where('lot_id', $lotId)
                    ->where('titre_document', $request->titre_document)
                    ->where('type_document', $request->type_document)
                    ->orderBy('version_document', 'desc')
                    ->first();

                if ($documentExistant) {
                    $version = ($documentExistant->version_document ?? 0) + 1;
                }
            }

            // Créer le document
            $document = Document::create([
                'lot_id' => $lotId,
                'type_document' => $request->type_document,
                'titre_document' => $request->titre_document,
                'fichier_nom_document' => $nomOriginal,
                'fichier_path_document' => $cheminFichier,
                'fichier_type_document' => $mimeType,
                'fichier_taille_document' => round($tailleMo, 2),
                'description_document' => $request->description_document,
                'date_document' => $request->date_document ?? now(),
                'version_document' => $version,
                'est_valide_document' => false,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('lots.documents.index', $lotId)
                ->with('success', 'Document "' . $document->titre_document . '" ajouté avec succès (version ' . $version . ').');

        } catch (\Exception $e) {
            DB::rollBack();

            // Supprimer le fichier uploadé en cas d'erreur
            if (isset($cheminFichier) && Storage::disk('public')->exists($cheminFichier)) {
                Storage::disk('public')->delete($cheminFichier);
            }

            Log::error('Erreur lors de l\'ajout du document', [
                'lot_id' => $lotId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout du document: ' . $e->getMessage());
        }
    }

    /**
     * Afficher un document
     */
    public function show($lotId, $documentId)
    {
        $lot = Lot::with('appelOffre')->findOrFail($lotId);
        $document = Document::with(['lot', 'validateur'])
            ->where('lot_id', $lotId)
            ->findOrFail($documentId);

        $typesDocuments = self::TYPES_DOCUMENTS;

        return view('documents.show', compact('lot', 'document', 'typesDocuments'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($lotId, $documentId)
    {
        $lot = Lot::with('appelOffre')->findOrFail($lotId);
        $document = Document::where('lot_id', $lotId)->findOrFail($documentId);
        $typesDocuments = self::TYPES_DOCUMENTS;
        $extensionsAutorisees = self::EXTENSIONS_AUTORISEES;
        $tailleMaxMo = self::TAILLE_MAX_MO;

        return view('documents.edit', compact('lot', 'document', 'typesDocuments', 'extensionsAutorisees', 'tailleMaxMo'));
    }

    /**
     * Mettre à jour un document
     */
    public function update(UpdateDocumentRequest $request, $lotId, $documentId)
    {
        $lot = Lot::findOrFail($lotId);
        $document = Document::where('lot_id', $lotId)->findOrFail($documentId);

        try {
            DB::beginTransaction();

            $donnees = [
                'type_document' => $request->type_document,
                'titre_document' => $request->titre_document,
                'description_document' => $request->description_document,
                'date_document' => $request->date_document,
                'updated_by' => auth()->id(),
            ];

            // Si un nouveau fichier est uploadé
            if ($request->hasFile('fichier')) {
                $fichier = $request->file('fichier');
                $nomOriginal = $fichier->getClientOriginalName();
                $extension = $fichier->getClientOriginalExtension();
                $tailleMo = $fichier->getSize() / 1024 / 1024;
                $mimeType = $fichier->getMimeType();

                // Générer un nom unique
                $nomFichier = Str::slug(pathinfo($nomOriginal, PATHINFO_FILENAME))
                             . '_' . time()
                             . '_' . Str::random(8)
                             . '.' . $extension;

                $cheminDossier = 'documents/lots/' . $lot->id_lot;

                // Supprimer l'ancien fichier
                if ($document->fichier_path_document && Storage::disk('public')->exists($document->fichier_path_document)) {
                    Storage::disk('public')->delete($document->fichier_path_document);
                }

                // Stocker le nouveau fichier
                $cheminFichier = $fichier->storeAs($cheminDossier, $nomFichier, 'public');

                $donnees['fichier_nom_document'] = $nomOriginal;
                $donnees['fichier_path_document'] = $cheminFichier;
                $donnees['fichier_type_document'] = $mimeType;
                $donnees['fichier_taille_document'] = round($tailleMo, 2);
                $donnees['version_document'] = ($document->version_document ?? 0) + 1;

                // Réinitialiser la validation si le fichier change
                $donnees['est_valide_document'] = false;
                $donnees['valide_par'] = null;
                $donnees['valide_at'] = null;
            }

            $document->update($donnees);

            DB::commit();

            return redirect()
                ->route('lots.documents.index', $lotId)
                ->with('success', 'Document "' . $document->titre_document . '" mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la mise à jour du document', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un document
     */
    public function destroy(Request $request, $lotId, $documentId)
    {
        $document = Document::where('lot_id', $lotId)->findOrFail($documentId);
        $titre = $document->titre_document;

        try {
            DB::beginTransaction();

            // Supprimer le fichier physique
            if ($document->fichier_path_document && Storage::disk('public')->exists($document->fichier_path_document)) {
                Storage::disk('public')->delete($document->fichier_path_document);
            }

            // Soft delete avec traçabilité
            $document->deleted_by = auth()->id();
            $document->save();
            $document->delete();

            DB::commit();

            return redirect()
                ->route('lots.documents.index', $lotId)
                ->with('success', 'Document "' . $titre . '" supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la suppression du document', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Télécharger un document
     */
    public function download($lotId, $documentId)
    {
        $document = Document::where('lot_id', $lotId)->findOrFail($documentId);

        if (!$document->fichier_path_document || !Storage::disk('public')->exists($document->fichier_path_document)) {
            return back()->with('error', 'Le fichier n\'existe pas ou a été supprimé.');
        }

        $cheminComplet = Storage::disk('public')->path($document->fichier_path_document);
        $nomTelechargement = $document->fichier_nom_document ?? basename($document->fichier_path_document);

        return response()->download($cheminComplet, $nomTelechargement);
    }

    /**
     * Prévisualiser un document
     */
    public function preview($lotId, $documentId)
    {
        $document = Document::where('lot_id', $lotId)->findOrFail($documentId);

        if (!$document->fichier_path_document || !Storage::disk('public')->exists($document->fichier_path_document)) {
            abort(404, 'Fichier non trouvé');
        }

        $cheminComplet = Storage::disk('public')->path($document->fichier_path_document);
        $mimeType = $document->fichier_type_document ?? mime_content_type($cheminComplet);

        return response()->file($cheminComplet, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->fichier_nom_document . '"',
        ]);
    }

    /**
     * Valider un document
     */
    public function valider(Request $request, $lotId, $documentId)
    {
        $document = Document::where('lot_id', $lotId)->findOrFail($documentId);

        try {
            $document->est_valide_document = true;
            $document->valide_par = auth()->id();
            $document->valide_at = now();
            $document->updated_by = auth()->id();
            $document->save();

            return back()->with('success', 'Document "' . $document->titre_document . '" validé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation du document', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Invalider un document
     */
    public function invalider(Request $request, $lotId, $documentId)
    {
        $document = Document::where('lot_id', $lotId)->findOrFail($documentId);

        try {
            $document->est_valide_document = false;
            $document->valide_par = null;
            $document->valide_at = null;
            $document->updated_by = auth()->id();
            $document->save();

            return back()->with('success', 'Validation du document annulée.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Upload multiple documents
     */
    public function uploadMultiple(Request $request, $lotId)
    {
        $lot = Lot::findOrFail($lotId);

        $request->validate([
            'fichiers' => 'required|array|min:1|max:10',
            'fichiers.*' => 'required|file|max:' . (self::TAILLE_MAX_MO * 1024),
            'type_document' => 'required|string|max:20',
        ], [
            'fichiers.required' => 'Veuillez sélectionner au moins un fichier.',
            'fichiers.max' => 'Vous pouvez uploader maximum 10 fichiers à la fois.',
            'fichiers.*.max' => 'Chaque fichier ne doit pas dépasser ' . self::TAILLE_MAX_MO . ' Mo.',
        ]);

        $documentsCreees = 0;
        $erreurs = [];

        try {
            DB::beginTransaction();

            foreach ($request->file('fichiers') as $fichier) {
                try {
                    $nomOriginal = $fichier->getClientOriginalName();
                    $extension = strtolower($fichier->getClientOriginalExtension());

                    // Vérifier l'extension
                    if (!in_array($extension, self::EXTENSIONS_AUTORISEES)) {
                        $erreurs[] = "{$nomOriginal}: Extension non autorisée.";
                        continue;
                    }

                    $tailleMo = $fichier->getSize() / 1024 / 1024;
                    $mimeType = $fichier->getMimeType();

                    $nomFichier = Str::slug(pathinfo($nomOriginal, PATHINFO_FILENAME))
                                 . '_' . time()
                                 . '_' . Str::random(8)
                                 . '.' . $extension;

                    $cheminDossier = 'documents/lots/' . $lot->id_lot;
                    $cheminFichier = $fichier->storeAs($cheminDossier, $nomFichier, 'public');

                    Document::create([
                        'lot_id' => $lotId,
                        'type_document' => $request->type_document,
                        'titre_document' => pathinfo($nomOriginal, PATHINFO_FILENAME),
                        'fichier_nom_document' => $nomOriginal,
                        'fichier_path_document' => $cheminFichier,
                        'fichier_type_document' => $mimeType,
                        'fichier_taille_document' => round($tailleMo, 2),
                        'date_document' => now(),
                        'version_document' => 1,
                        'est_valide_document' => false,
                        'created_by' => auth()->id(),
                    ]);

                    $documentsCreees++;

                } catch (\Exception $e) {
                    $erreurs[] = "{$nomOriginal}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = $documentsCreees . ' document(s) ajouté(s) avec succès.';
            if (count($erreurs) > 0) {
                $message .= ' ' . count($erreurs) . ' erreur(s).';
            }

            return redirect()
                ->route('lots.documents.index', $lotId)
                ->with('success', $message)
                ->with('erreurs', $erreurs);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de l\'upload multiple', [
                'lot_id' => $lotId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de l\'upload: ' . $e->getMessage());
        }
    }
}
