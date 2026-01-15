<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapaciteTechnique extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'capacites_techniques';
    protected $primaryKey = 'id_capacite_technique';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prestataire_id',
        'effectif_permanent_capacite_technique',
        'effectif_temporaire_capacite_technique',
        'moyens_materiels_capacite_technique',
        'certifications_capacite_technique',
        'agrements_capacite_technique',
        'references_capacite_technique',
        'competences_cles_capacite_technique',
        'domaines_expertise_capacite_technique',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'effectif_permanent_capacite_technique' => 'integer',
        'effectif_temporaire_capacite_technique' => 'integer',
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
     * Effectif total
     */
    public function getEffectifTotalAttribute()
    {
        return ($this->effectif_permanent_capacite_technique ?? 0) +
               ($this->effectif_temporaire_capacite_technique ?? 0);
    }

    /**
     * Certifications en tableau
     */
    public function getCertificationsArrayAttribute()
    {
        if (empty($this->certifications_capacite_technique)) {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $this->certifications_capacite_technique)));
    }

    /**
     * Agréments en tableau
     */
    public function getAgrementsArrayAttribute()
    {
        if (empty($this->agrements_capacite_technique)) {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $this->agrements_capacite_technique)));
    }

    /**
     * Domaines d'expertise en tableau
     */
    public function getDomainesExpertiseArrayAttribute()
    {
        if (empty($this->domaines_expertise_capacite_technique)) {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $this->domaines_expertise_capacite_technique)));
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
     * Scope avec certifications
     */
    public function scopeAvecCertifications($query)
    {
        return $query->whereNotNull('certifications_capacite_technique')
                     ->where('certifications_capacite_technique', '!=', '');
    }

    /**
     * Scope avec agréments
     */
    public function scopeAvecAgrements($query)
    {
        return $query->whereNotNull('agrements_capacite_technique')
                     ->where('agrements_capacite_technique', '!=', '');
    }

    /**
     * Scope par effectif minimum
     */
    public function scopeEffectifMinimum($query, $minimum)
    {
        return $query->whereRaw('(COALESCE(effectif_permanent_capacite_technique, 0) + COALESCE(effectif_temporaire_capacite_technique, 0)) >= ?', [$minimum]);
    }

    /**
     * ================================================================
     * MÉTHODES
     * ================================================================
     */

    /**
     * Vérifier si le prestataire a des certifications
     */
    public function hasCertifications()
    {
        return !empty($this->certifications_capacite_technique);
    }

    /**
     * Vérifier si le prestataire a des agréments
     */
    public function hasAgrements()
    {
        return !empty($this->agrements_capacite_technique);
    }

    /**
     * Calculer le score de capacité technique (sur 100)
     */
    public function calculerScore()
    {
        $score = 0;

        // Effectif (max 30 points)
        $effectifTotal = $this->effectif_total;
        if ($effectifTotal >= 50) {
            $score += 30;
        } elseif ($effectifTotal >= 20) {
            $score += 20;
        } elseif ($effectifTotal >= 10) {
            $score += 15;
        } elseif ($effectifTotal >= 5) {
            $score += 10;
        } elseif ($effectifTotal > 0) {
            $score += 5;
        }

        // Certifications (max 25 points)
        $nbCertifications = count($this->certifications_array);
        $score += min($nbCertifications * 5, 25);

        // Agréments (max 20 points)
        $nbAgrements = count($this->agrements_array);
        $score += min($nbAgrements * 5, 20);

        // Moyens matériels (max 15 points)
        if (!empty($this->moyens_materiels_capacite_technique)) {
            $score += 15;
        }

        // Références (max 10 points)
        if (!empty($this->references_capacite_technique)) {
            $score += 10;
        }

        return min($score, 100);
    }

    /**
     * Obtenir le niveau de capacité
     */
    public function getNiveau()
    {
        $score = $this->calculerScore();

        if ($score >= 80) {
            return ['niveau' => 'Excellent', 'classe' => 'green', 'icon' => 'star'];
        } elseif ($score >= 60) {
            return ['niveau' => 'Bon', 'classe' => 'blue', 'icon' => 'thumbs-up'];
        } elseif ($score >= 40) {
            return ['niveau' => 'Moyen', 'classe' => 'yellow', 'icon' => 'minus-circle'];
        } else {
            return ['niveau' => 'Faible', 'classe' => 'red', 'icon' => 'exclamation-triangle'];
        }
    }
}
