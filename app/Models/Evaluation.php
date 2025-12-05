<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'evaluations';
    protected $primaryKey = 'id_evaluation';
    protected $keyType = 'string';
    public $incrementing = false;

    // Statuts d'évaluation
    const STATUT_EN_ATTENTE = 0;
    const STATUT_EN_COURS = 1;
    const STATUT_TERMINEE = 2;
    const STATUT_VALIDEE = 3;
    const STATUT_REJETEE = 4;

    protected $fillable = [
        'appel_offre_id',
        'lot_id',
        'prestataire_id',
        'numero_evaluation',
        'date_evaluation',
        'statut_evaluation',
        'note_totale',
        'note_maximale',
        'pourcentage_final',
        'rang',
        'commentaire_general',
        'recommandation',
        'documents_evalues',
        'evaluateur_principal_id',
        'date_validation',
        'valide_par',
        'motif_rejet',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_evaluation' => 'datetime',
        'date_validation' => 'datetime',
        'statut_evaluation' => 'integer',
        'note_totale' => 'decimal:2',
        'note_maximale' => 'decimal:2',
        'pourcentage_final' => 'decimal:2',
        'rang' => 'integer',
        'documents_evalues' => 'array',
    ];

    // Relations
    public function appelOffre()
    {
        return $this->belongsTo(AppelOffre::class, 'appel_offre_id', 'id_appel_offre');
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id', 'id_lot');
    }

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    public function evaluateurPrincipal()
    {
        return $this->belongsTo(User::class, 'evaluateur_principal_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Relations many-to-many avec critères
    public function criteresEvaluation()
    {
        return $this->belongsToMany(
            CritereEvaluation::class,
            'evaluations_lots',
            'evaluation_id',
            'critere_evaluation_id'
        )
        ->withPivot([
            'prestatiare_id',
            'note_obtenue',
            'note_sur',
            'note_finale',
            'commentaire',
            'justification',
            'documents_fournis',
            'conforme',
            'created_by',
            'updated_by',
            'deleted_by'
        ])
        ->withTimestamps()
        ->using(EvaluationLot::class);
    }

    public function detailsEvaluation()
    {
        return $this->hasMany(EvaluationLot::class, 'evaluation_id', 'id_evaluation');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut_evaluation', self::STATUT_EN_ATTENTE);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut_evaluation', self::STATUT_EN_COURS);
    }

    public function scopeTerminee($query)
    {
        return $query->where('statut_evaluation', self::STATUT_TERMINEE);
    }

    public function scopeValidee($query)
    {
        return $query->where('statut_evaluation', self::STATUT_VALIDEE);
    }

    public function scopeByAppelOffre($query, $appelOffreId)
    {
        return $query->where('appel_offre_id', $appelOffreId);
    }

    public function scopeByLot($query, $lotId)
    {
        return $query->where('lot_id', $lotId);
    }

    public function scopeByPrestataire($query, $prestataireId)
    {
        return $query->where('prestataire_id', $prestataireId);
    }

    // Méthodes utilitaires
    public function demarrer($evaluateurId, $userId = null)
    {
        $this->statut_evaluation = self::STATUT_EN_COURS;
        $this->evaluateur_principal_id = $evaluateurId;
        $this->date_evaluation = now();
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    public function terminer($userId = null)
    {
        if ($this->statut_evaluation !== self::STATUT_EN_COURS) {
            throw new \Exception("Seule une évaluation en cours peut être terminée.");
        }

        // Calculer la note totale
        $this->calculerNoteFinale();

        $this->statut_evaluation = self::STATUT_TERMINEE;
        $this->updated_by = $userId;
        $this->save();

        // Calculer le rang parmi les autres évaluations du même lot
        $this->calculerRang();

        return $this;
    }

    public function valider($validateurId, $userId = null)
    {
        if ($this->statut_evaluation !== self::STATUT_TERMINEE) {
            throw new \Exception("Seule une évaluation terminée peut être validée.");
        }

        $this->statut_evaluation = self::STATUT_VALIDEE;
        $this->valide_par = $validateurId;
        $this->date_validation = now();
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    public function rejeter($motif, $validateurId, $userId = null)
    {
        if ($this->statut_evaluation !== self::STATUT_TERMINEE) {
            throw new \Exception("Seule une évaluation terminée peut être rejetée.");
        }

        $this->statut_evaluation = self::STATUT_REJETEE;
        $this->motif_rejet = $motif;
        $this->valide_par = $validateurId;
        $this->date_validation = now();
        $this->updated_by = $userId;
        $this->save();

        return $this;
    }

    public function calculerNoteFinale()
    {
        $details = $this->detailsEvaluation;

        $this->note_totale = $details->sum('note_finale');
        $this->note_maximale = $details->sum(function($detail) {
            return $detail->critereEvaluation->note_reference_critere_evaluation ?? 0;
        });

        if ($this->note_maximale > 0) {
            $this->pourcentage_final = ($this->note_totale / $this->note_maximale) * 100;
        } else {
            $this->pourcentage_final = 0;
        }

        $this->save();

        return $this->note_totale;
    }

    public function calculerRang()
    {
        if (!$this->lot_id) {
            return;
        }

        // Récupérer toutes les évaluations du même lot, triées par note
        $evaluations = self::where('lot_id', $this->lot_id)
                          ->whereIn('statut_evaluation', [self::STATUT_TERMINEE, self::STATUT_VALIDEE])
                          ->orderBy('note_totale', 'desc')
                          ->orderBy('pourcentage_final', 'desc')
                          ->get();

        // Attribuer les rangs
        $rang = 1;
        foreach ($evaluations as $evaluation) {
            $evaluation->rang = $rang;
            $evaluation->save();
            $rang++;
        }
    }

    public function ajouterEvaluationCritere(
        CritereEvaluation $critere,
        $noteObtenue,
        $noteSur,
        $commentaire = null,
        $userId = null
    ) {
        $evaluationLot = EvaluationLot::create([
            'critere_evaluation_id' => $critere->id_critere_evaluation,
            'evaluation_id' => $this->id_evaluation,
            'prestatiare_id' => $this->prestataire_id,
            'note_obtenue' => $noteObtenue,
            'note_sur' => $noteSur,
            'commentaire' => $commentaire,
            'created_by' => $userId,
        ]);

        $evaluationLot->calculerNoteFinale();

        return $evaluationLot;
    }

    public function genererRapportEvaluation()
    {
        $details = $this->detailsEvaluation()
                        ->with(['critereEvaluation'])
                        ->orderBy('critere_evaluation_id')
                        ->get();

        return [
            'evaluation' => [
                'numero' => $this->numero_evaluation,
                'date' => $this->date_evaluation->format('d/m/Y'),
                'statut' => $this->getStatutTexteAttribute(),
            ],
            'prestataire' => [
                'raison_sociale' => $this->prestataire->raison_sociale_prestataire,
                'email' => $this->prestataire->email_prestataire,
            ],
            'lot' => [
                'numero' => $this->lot->numero,
                'libelle' => $this->lot->libelle,
            ],
            'criteres' => $details->map(function($detail) {
                return [
                    'libelle' => $detail->critereEvaluation->libelle_critere_evaluation,
                    'note_obtenue' => $detail->note_obtenue,
                    'note_sur' => $detail->note_sur,
                    'note_finale' => $detail->note_finale,
                    'note_reference' => $detail->critereEvaluation->note_reference_critere_evaluation,
                    'commentaire' => $detail->commentaire,
                    'conforme' => $detail->conforme,
                ];
            }),
            'synthese' => [
                'note_totale' => $this->note_totale,
                'note_maximale' => $this->note_maximale,
                'pourcentage' => $this->pourcentage_final,
                'rang' => $this->rang,
                'recommandation' => $this->recommandation,
                'commentaire' => $this->commentaire_general,
            ],
        ];
    }

    public function getStatutTexteAttribute()
    {
        $statuts = [
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINEE => 'Terminée',
            self::STATUT_VALIDEE => 'Validée',
            self::STATUT_REJETEE => 'Rejetée',
        ];

        return $statuts[$this->statut_evaluation] ?? 'Inconnu';
    }

    public static function genererNumeroEvaluation($lotId, $annee = null)
    {
        $annee = $annee ?? date('Y');
        $lot = Lot::find($lotId);

        if (!$lot) {
            throw new \Exception("Lot introuvable.");
        }

        $dernier = self::where('lot_id', $lotId)
                      ->whereYear('created_at', $annee)
                      ->count();

        $sequence = str_pad($dernier + 1, 3, '0', STR_PAD_LEFT);

        return "EVAL-{$lot->numero}-{$annee}-{$sequence}";
    }

    public function isEnAttente()
    {
        return $this->statut_evaluation === self::STATUT_EN_ATTENTE;
    }

    public function isEnCours()
    {
        return $this->statut_evaluation === self::STATUT_EN_COURS;
    }

    public function isTerminee()
    {
        return $this->statut_evaluation === self::STATUT_TERMINEE;
    }

    public function isValidee()
    {
        return $this->statut_evaluation === self::STATUT_VALIDEE;
    }

    public function isRejetee()
    {
        return $this->statut_evaluation === self::STATUT_REJETEE;
    }

    public function peutEtreModifiee()
    {
        return in_array($this->statut_evaluation, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_EN_COURS,
            self::STATUT_REJETEE
        ]);
    }
}
