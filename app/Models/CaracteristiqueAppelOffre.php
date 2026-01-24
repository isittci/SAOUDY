<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaracteristiqueAppelOffre extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'caracteristiques_appels_offres';
    protected $primaryKey = 'id_caracteristique_appel_offre';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'appel_offre_id',
        'parent_id',
        'version_caracteristique_appel_offre',
        'date_demarrage_prevue_caracteristique_appel_offre',
        // duree_estimee_jours_caracteristique_appel_offre est CALCULÉE automatiquement - ne pas inclure ici
        'date_livraison_previsionnelle_caracteristique_appel_offre',
        'lieu_execution_caracteristique_appel_offre',
        'montant_garantie_caracteristique_appel_offre',
        'delai_garantie_jours_caracteristique_appel_offre',
        'conditions_paiement_caracteristique_appel_offre',
        'modalites_execution_caracteristique_appel_offre',
        'documents_requis_caracteristique_appel_offre',
        'autres_informations_caracteristique_appel_offre',
        'motif_modification_caracteristique_appel_offre',
        'is_active_caracteristique_appel_offre',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'version_caracteristique_appel_offre' => 'integer',
        'date_demarrage_prevue_caracteristique_appel_offre' => 'date',
        'duree_estimee_jours_caracteristique_appel_offre' => 'integer', // CORRIGÉ : integer au lieu de date
        'date_livraison_previsionnelle_caracteristique_appel_offre' => 'date',
        'montant_garantie_caracteristique_appel_offre' => 'decimal:2',
        'delai_garantie_jours_caracteristique_appel_offre' => 'decimal:2',
        'is_active_caracteristique_appel_offre' => 'boolean',
    ];

    /**
     * Boot method pour calculer automatiquement la durée
     */
    protected static function boot()
    {
        parent::boot();

        // Avant la création
        static::creating(function ($caracteristique) {
            $caracteristique->calculerDureeEstimee();
        });

        // Avant la mise à jour
        static::updating(function ($caracteristique) {
            $caracteristique->calculerDureeEstimee();
        });
    }

    // Relations
    public function appelOffre()
    {
        return $this->belongsTo(AppelOffre::class, 'appel_offre_id', 'id_appel_offre');
    }

    public function parent()
    {
        return $this->belongsTo(CaracteristiqueAppelOffre::class, 'parent_id', 'id_caracteristique_appel_offre');
    }

    public function versions()
    {
        return $this->hasMany(CaracteristiqueAppelOffre::class, 'parent_id', 'id_caracteristique_appel_offre')
            ->orderBy('version_caracteristique_appel_offre', 'desc');
    }

    public function derniereVersion()
    {
        return $this->hasOne(CaracteristiqueAppelOffre::class, 'parent_id', 'id_caracteristique_appel_offre')
            ->latest('version_caracteristique_appel_offre');
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

    // Scopes
    public function scopeVersionActuelle($query)
    {
        return $query->whereNull('parent_id')
            ->orWhereDoesntHave('versions');
    }

    public function scopeVersions($query)
    {
        return $query->whereNotNull('parent_id')
            ->orderBy('version_caracteristique_appel_offre', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active_caracteristique_appel_offre', true);
    }

    // Méthodes de calcul automatique

    /**
     * Calcule automatiquement la durée en jours entre date de démarrage et date de livraison
     * CETTE MÉTHODE EST APPELÉE AUTOMATIQUEMENT PAR LES ÉVÉNEMENTS boot()
     */
    public function calculerDureeEstimee()
    {
        if (
            $this->date_demarrage_prevue_caracteristique_appel_offre &&
            $this->date_livraison_previsionnelle_caracteristique_appel_offre
        ) {

            $debut = Carbon::parse($this->date_demarrage_prevue_caracteristique_appel_offre);
            $fin = Carbon::parse($this->date_livraison_previsionnelle_caracteristique_appel_offre);

            // Calcul de la différence en jours
            $this->duree_estimee_jours_caracteristique_appel_offre = $debut->diffInDays($fin);
        } else {
            // Si l'une des dates est manquante, la durée est nulle
            $this->duree_estimee_jours_caracteristique_appel_offre = null;
        }
    }

    /**
     * Accesseur pour obtenir la durée formatée
     */
    public function getDureeEstimeeFormatteeAttribute()
    {
        if ($this->duree_estimee_jours_caracteristique_appel_offre !== null) {
            return number_format($this->duree_estimee_jours_caracteristique_appel_offre, 2, ',', ' ') . ' jours';
        }
        return 'N/A';
    }

    /**
     * Calcule la date de livraison prévisionnelle (pour vérification ou affichage)
     * Basée sur date de démarrage + durée
     */
    public function getDateLivraisonCalculeeAttribute()
    {
        if (
            $this->date_demarrage_prevue_caracteristique_appel_offre &&
            $this->duree_estimee_jours_caracteristique_appel_offre
        ) {

            return Carbon::parse($this->date_demarrage_prevue_caracteristique_appel_offre)
                ->addDays($this->duree_estimee_jours_caracteristique_appel_offre);
        }

        return null;
    }

    /**
     * Vérifie la cohérence entre durée calculée et dates saisies
     * La durée DOIT correspondre à la différence entre les dates
     */
    public function verifierCoherenceDates()
    {
        if (
            $this->date_demarrage_prevue_caracteristique_appel_offre &&
            $this->date_livraison_previsionnelle_caracteristique_appel_offre &&
            $this->duree_estimee_jours_caracteristique_appel_offre !== null
        ) {

            $dateLivraisonCalculee = $this->date_livraison_calculee;

            if ($dateLivraisonCalculee) {
                return $dateLivraisonCalculee->isSameDay(
                    $this->date_livraison_previsionnelle_caracteristique_appel_offre
                );
            }
        }

        return true; // Pas assez de données pour vérifier
    }

    // Méthodes de versioning

    /**
     * Crée une nouvelle version de la caractéristique
     */
    public function creerNouvelleVersion(array $donnees, $motif = null)
    {

        // Désactiver la version actuelle
        $this->is_active_caracteristique_appel_offre = false;
        $this->save();

        // Créer la nouvelle version
        $nouvelleVersion = $this->replicate();
        $nouvelleVersion->parent_id = $this->id_caracteristique_appel_offre;
        $nouvelleVersion->version_caracteristique_appel_offre = $this->getProchainNumeroVersion();
        $nouvelleVersion->motif_modification_caracteristique_appel_offre = $motif;
        $nouvelleVersion->is_active_caracteristique_appel_offre = true;

        // Appliquer les nouvelles données
        foreach ($donnees as $cle => $valeur) {
            if (in_array($cle, $this->fillable)) {
                $nouvelleVersion->$cle = $valeur;
            }
        }

        // La durée sera calculée automatiquement via l'événement creating
        $nouvelleVersion->save();
        return $nouvelleVersion;
    }

    /**
     * Obtient le prochain numéro de version
     */
    public function getProchainNumeroVersion()
    {
        if ($this->parent_id) {
            $parent = $this->parent;
            $derniereVersion = $parent->versions()->max('version_caracteristique_appel_offre');
        } else {
            $derniereVersion = $this->versions()->max('version_caracteristique_appel_offre');
        }

        return ($derniereVersion ?? $this->version_caracteristique_appel_offre) + 1;
    }

    /**
     * Vérifie si c'est la version actuelle
     */
    public function isVersionActuelle()
    {
        if ($this->parent_id) {
            return false;
        }
        return !$this->versions()->exists();
    }

    /**
     * Récupère l'historique complet des versions
     */
    public function getHistorique()
    {
        if ($this->parent_id) {
            return $this->parent->getHistorique();
        }

        return $this->versions()
            ->with(['creator', 'updater'])
            ->orderBy('version_caracteristique_appel_offre', 'desc')
            ->get()
            ->prepend($this);
    }

    /**
     * Obtient la version active pour cet identifiant de caractéristique
     */
    public static function getVersionActive($idCaracteristique)
    {
        return static::where('id_caracteristique_appel_offre', $idCaracteristique)
            ->where('is_active_caracteristique_appel_offre', true)
            ->first();
    }

    /**
     * Calcule le délai restant avant la date de livraison
     */
    public function getDelaiRestantAttribute()
    {
        if ($this->date_livraison_previsionnelle_caracteristique_appel_offre) {
            $maintenant = Carbon::now();
            $dateLivraison = Carbon::parse($this->date_livraison_previsionnelle_caracteristique_appel_offre);

            if ($dateLivraison->isFuture()) {
                return $maintenant->diffInDays($dateLivraison) . ' jours restants';
            } elseif ($dateLivraison->isToday()) {
                return 'Aujourd\'hui';
            } else {
                return 'Échue depuis ' . $maintenant->diffInDays($dateLivraison) . ' jours';
            }
        }

        return 'N/A';
    }

    /**
     * Calcule le pourcentage d'avancement théorique
     */
    public function getPourcentageAvancementTheoriqueAttribute()
    {
        if (
            $this->date_demarrage_prevue_caracteristique_appel_offre &&
            $this->date_livraison_previsionnelle_caracteristique_appel_offre &&
            $this->duree_estimee_jours_caracteristique_appel_offre > 0
        ) {

            $debut = Carbon::parse($this->date_demarrage_prevue_caracteristique_appel_offre);
            $fin = Carbon::parse($this->date_livraison_previsionnelle_caracteristique_appel_offre);
            $maintenant = Carbon::now();

            if ($maintenant->lessThan($debut)) {
                return 0; // Pas encore commencé
            } elseif ($maintenant->greaterThan($fin)) {
                return 100; // Terminé (théoriquement)
            } else {
                $joursEcoules = $debut->diffInDays($maintenant);
                $pourcentage = ($joursEcoules / $this->duree_estimee_jours_caracteristique_appel_offre) * 100;
                return round($pourcentage, 2);
            }
        }

        return null;
    }
}
