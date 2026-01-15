<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SituationFinanciere extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'situations_financieres';
    protected $primaryKey = 'id_situation_financiere';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prestataire_id',
        'exercice_fiscal_situation_financiere',
        'chiffre_affaire_situation_financiere',
        'fonds_propres_situation_financiere',
        'capacite_emprunt_situation_financiere',
        'ratio_solvabilite_situation_financiere',
        'ratio_liquidite_situation_financiere',
        'resultat_net_situation_financiere',
        'total_actif_situation_financiere',
        'total_passif_situation_financiere',
        'observations_situation_financiere',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'chiffre_affaire_situation_financiere' => 'decimal:2',
        'fonds_propres_situation_financiere' => 'decimal:2',
        'capacite_emprunt_situation_financiere' => 'decimal:2',
        'ratio_solvabilite_situation_financiere' => 'decimal:2',
        'ratio_liquidite_situation_financiere' => 'decimal:2',
        'resultat_net_situation_financiere' => 'decimal:2',
        'total_actif_situation_financiere' => 'decimal:2',
        'total_passif_situation_financiere' => 'decimal:2',
    ];

    /**
     * ================================================================
     * RELATIONS
     * ================================================================
     */

    /**
     * Prestataire associé
     */
    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class, 'prestataire_id', 'id_prestataire');
    }

    /**
     * Créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Modificateur
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Suppresseur
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * ================================================================
     * ACCESSEURS
     * ================================================================
     */

    /**
     * Marge nette (Résultat net / CA)
     */
    public function getMargeNetteAttribute()
    {
        if (empty($this->chiffre_affaire_situation_financiere) || $this->chiffre_affaire_situation_financiere == 0) {
            return null;
        }
        return round(($this->resultat_net_situation_financiere / $this->chiffre_affaire_situation_financiere) * 100, 2);
    }

    /**
     * Rentabilité des capitaux propres (ROE)
     */
    public function getRoeAttribute()
    {
        if (empty($this->fonds_propres_situation_financiere) || $this->fonds_propres_situation_financiere == 0) {
            return null;
        }
        return round(($this->resultat_net_situation_financiere / $this->fonds_propres_situation_financiere) * 100, 2);
    }

    /**
     * Rentabilité des actifs (ROA)
     */
    public function getRoaAttribute()
    {
        if (empty($this->total_actif_situation_financiere) || $this->total_actif_situation_financiere == 0) {
            return null;
        }
        return round(($this->resultat_net_situation_financiere / $this->total_actif_situation_financiere) * 100, 2);
    }

    /**
     * Ratio d'endettement
     */
    public function getRatioEndettementAttribute()
    {
        if (empty($this->fonds_propres_situation_financiere) || $this->fonds_propres_situation_financiere == 0) {
            return null;
        }
        $dettes = ($this->total_passif_situation_financiere ?? 0) - ($this->fonds_propres_situation_financiere ?? 0);
        return round(($dettes / $this->fonds_propres_situation_financiere) * 100, 2);
    }

    /**
     * Résultat positif ou négatif
     */
    public function getIsResultatPositifAttribute()
    {
        return ($this->resultat_net_situation_financiere ?? 0) >= 0;
    }

    /**
     * Exercice fiscal formaté
     */
    public function getExerciceFiscalFormatAttribute()
    {
        return $this->exercice_fiscal_situation_financiere ?? 'Non défini';
    }

    /**
     * ================================================================
     * SCOPES
     * ================================================================
     */

    /**
     * Scope par prestataire
     */
    public function scopeByPrestataire($query, $prestataireId)
    {
        return $query->where('prestataire_id', $prestataireId);
    }

    /**
     * Scope par exercice fiscal
     */
    public function scopeByExercice($query, $exercice)
    {
        return $query->where('exercice_fiscal_situation_financiere', $exercice);
    }

    /**
     * Scope résultat positif
     */
    public function scopeResultatPositif($query)
    {
        return $query->where('resultat_net_situation_financiere', '>=', 0);
    }

    /**
     * Scope résultat négatif
     */
    public function scopeResultatNegatif($query)
    {
        return $query->where('resultat_net_situation_financiere', '<', 0);
    }

    /**
     * Scope par CA minimum
     */
    public function scopeChiffreAffaireMinimum($query, $minimum)
    {
        return $query->where('chiffre_affaire_situation_financiere', '>=', $minimum);
    }

    /**
     * Scope le plus récent
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('exercice_fiscal_situation_financiere', 'desc');
    }

    /**
     * ================================================================
     * MÉTHODES
     * ================================================================
     */

    /**
     * Calculer le score de santé financière (sur 100)
     */
    public function calculerScore()
    {
        $score = 0;

        // Chiffre d'affaires (max 25 points)
        $ca = $this->chiffre_affaire_situation_financiere ?? 0;
        if ($ca >= 1000000000) { // 1 milliard
            $score += 25;
        } elseif ($ca >= 500000000) { // 500 millions
            $score += 20;
        } elseif ($ca >= 100000000) { // 100 millions
            $score += 15;
        } elseif ($ca >= 50000000) { // 50 millions
            $score += 10;
        } elseif ($ca > 0) {
            $score += 5;
        }

        // Résultat net (max 25 points)
        $resultat = $this->resultat_net_situation_financiere ?? 0;
        if ($resultat > 0) {
            $margeNette = $this->marge_nette ?? 0;
            if ($margeNette >= 15) {
                $score += 25;
            } elseif ($margeNette >= 10) {
                $score += 20;
            } elseif ($margeNette >= 5) {
                $score += 15;
            } elseif ($margeNette > 0) {
                $score += 10;
            }
        }

        // Ratio de solvabilité (max 20 points)
        $solvabilite = $this->ratio_solvabilite_situation_financiere ?? 0;
        if ($solvabilite >= 50) {
            $score += 20;
        } elseif ($solvabilite >= 30) {
            $score += 15;
        } elseif ($solvabilite >= 20) {
            $score += 10;
        } elseif ($solvabilite > 0) {
            $score += 5;
        }

        // Ratio de liquidité (max 20 points)
        $liquidite = $this->ratio_liquidite_situation_financiere ?? 0;
        if ($liquidite >= 2) {
            $score += 20;
        } elseif ($liquidite >= 1.5) {
            $score += 15;
        } elseif ($liquidite >= 1) {
            $score += 10;
        } elseif ($liquidite > 0) {
            $score += 5;
        }

        // Fonds propres positifs (max 10 points)
        if (($this->fonds_propres_situation_financiere ?? 0) > 0) {
            $score += 10;
        }

        return min($score, 100);
    }

    /**
     * Obtenir le niveau de santé financière
     */
    public function getNiveau()
    {
        $score = $this->calculerScore();

        if ($score >= 80) {
            return ['niveau' => 'Excellente', 'classe' => 'green', 'icon' => 'chart-line'];
        } elseif ($score >= 60) {
            return ['niveau' => 'Bonne', 'classe' => 'blue', 'icon' => 'thumbs-up'];
        } elseif ($score >= 40) {
            return ['niveau' => 'Moyenne', 'classe' => 'yellow', 'icon' => 'minus-circle'];
        } elseif ($score >= 20) {
            return ['niveau' => 'Fragile', 'classe' => 'orange', 'icon' => 'exclamation-circle'];
        } else {
            return ['niveau' => 'Critique', 'classe' => 'red', 'icon' => 'exclamation-triangle'];
        }
    }

    /**
     * Obtenir les indicateurs clés
     */
    public function getIndicateursCles()
    {
        return [
            'chiffre_affaires' => [
                'valeur' => $this->chiffre_affaire_situation_financiere,
                'format' => number_format($this->chiffre_affaire_situation_financiere ?? 0, 0, ',', ' ') . ' FCFA',
                'label' => 'Chiffre d\'affaires',
            ],
            'resultat_net' => [
                'valeur' => $this->resultat_net_situation_financiere,
                'format' => number_format($this->resultat_net_situation_financiere ?? 0, 0, ',', ' ') . ' FCFA',
                'label' => 'Résultat net',
                'positif' => $this->is_resultat_positif,
            ],
            'fonds_propres' => [
                'valeur' => $this->fonds_propres_situation_financiere,
                'format' => number_format($this->fonds_propres_situation_financiere ?? 0, 0, ',', ' ') . ' FCFA',
                'label' => 'Fonds propres',
            ],
            'marge_nette' => [
                'valeur' => $this->marge_nette,
                'format' => ($this->marge_nette ?? 0) . ' %',
                'label' => 'Marge nette',
            ],
            'ratio_solvabilite' => [
                'valeur' => $this->ratio_solvabilite_situation_financiere,
                'format' => ($this->ratio_solvabilite_situation_financiere ?? 0) . ' %',
                'label' => 'Ratio de solvabilité',
            ],
            'ratio_liquidite' => [
                'valeur' => $this->ratio_liquidite_situation_financiere,
                'format' => $this->ratio_liquidite_situation_financiere ?? 0,
                'label' => 'Ratio de liquidité',
            ],
        ];
    }

    /**
     * Comparer avec une autre situation financière
     */
    public function comparerAvec(SituationFinanciere $autre)
    {
        return [
            'chiffre_affaires' => [
                'actuel' => $this->chiffre_affaire_situation_financiere,
                'precedent' => $autre->chiffre_affaire_situation_financiere,
                'variation' => $this->calculerVariation(
                    $this->chiffre_affaire_situation_financiere,
                    $autre->chiffre_affaire_situation_financiere
                ),
            ],
            'resultat_net' => [
                'actuel' => $this->resultat_net_situation_financiere,
                'precedent' => $autre->resultat_net_situation_financiere,
                'variation' => $this->calculerVariation(
                    $this->resultat_net_situation_financiere,
                    $autre->resultat_net_situation_financiere
                ),
            ],
            'fonds_propres' => [
                'actuel' => $this->fonds_propres_situation_financiere,
                'precedent' => $autre->fonds_propres_situation_financiere,
                'variation' => $this->calculerVariation(
                    $this->fonds_propres_situation_financiere,
                    $autre->fonds_propres_situation_financiere
                ),
            ],
        ];
    }

    /**
     * Calculer la variation en pourcentage
     */
    protected function calculerVariation($actuel, $precedent)
    {
        if (empty($precedent) || $precedent == 0) {
            return null;
        }
        return round((($actuel - $precedent) / abs($precedent)) * 100, 2);
    }
}
