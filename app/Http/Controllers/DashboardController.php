<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lot;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\AppelOffre;
use App\Models\Prestataire;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TypeAppelOffre;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord.
     */
    public function index(Request $request)
    {
        // ================================================================
        // GESTION DE LA PÉRIODE
        // ================================================================
        $periodeType = $request->get('periode', 'annee_courante');

        // Déterminer les dates selon le type de période
        switch ($periodeType) {
            case 'mois_courant':
                $dateDebut = Carbon::now()->startOfMonth();
                $dateFin = Carbon::now()->endOfMonth();
                $periodeLabel = 'Mois en cours';
                break;

            case 'trimestre_courant':
                $dateDebut = Carbon::now()->startOfQuarter();
                $dateFin = Carbon::now()->endOfQuarter();
                $periodeLabel = 'Trimestre en cours';
                break;

            case 'semestre_courant':
                $moisActuel = Carbon::now()->month;
                if ($moisActuel <= 6) {
                    $dateDebut = Carbon::now()->startOfYear();
                    $dateFin = Carbon::now()->startOfYear()->addMonths(6)->subDay();
                } else {
                    $dateDebut = Carbon::now()->startOfYear()->addMonths(6);
                    $dateFin = Carbon::now()->endOfYear();
                }
                $periodeLabel = 'Semestre en cours';
                break;

            case 'annee_precedente':
                $dateDebut = Carbon::now()->subYear()->startOfYear();
                $dateFin = Carbon::now()->subYear()->endOfYear();
                $periodeLabel = 'Année précédente (' . $dateDebut->year . ')';
                break;

            case 'personnalise':
                $dateDebut = $request->has('date_debut')
                    ? Carbon::parse($request->get('date_debut'))->startOfDay()
                    : Carbon::now()->startOfYear();
                $dateFin = $request->has('date_fin')
                    ? Carbon::parse($request->get('date_fin'))->endOfDay()
                    : Carbon::now()->endOfYear();
                $periodeLabel = 'Du ' . $dateDebut->format('d/m/Y') . ' au ' . $dateFin->format('d/m/Y');
                break;

            case 'tout':
                $dateDebut = null;
                $dateFin = null;
                $periodeLabel = 'Toutes les données';
                break;

            case 'annee_courante':
            default:
                $dateDebut = Carbon::now()->startOfYear();
                $dateFin = Carbon::now()->endOfYear();
                $periodeLabel = 'Année en cours (' . $dateDebut->year . ')';
                $periodeType = 'annee_courante';
                break;
        }

        // Stocker les infos de période pour la vue
        $periode = [
            'type' => $periodeType,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'label' => $periodeLabel,
            'date_debut_input' => $dateDebut ? $dateDebut->format('Y-m-d') : null,
            'date_fin_input' => $dateFin ? $dateFin->format('Y-m-d') : null,
        ];

        // ================================================================
        // STATISTIQUES GLOBALES (Cartes) - FILTRÉES PAR PÉRIODE
        // ================================================================

        // Requêtes de base avec filtre de période
        $appelsOffresQuery = AppelOffre::query();
        $lotsQuery = Lot::whereNull('parent_id');
        $facturesQuery = Facture::query();
        $paiementsQuery = Paiement::query();

        // Appliquer les filtres de période
        if ($dateDebut && $dateFin) {
            $appelsOffresQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
            $lotsQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
            $facturesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
            $paiementsQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        // Montant payé = somme des paiements avec statut PAYE
        $paiementsPayesQuery = Paiement::where('statut_paiement', Paiement::STATUT_PAYE)
            ->whereHas('facture', function ($query) {
                $query->whereNull('deleted_at');
            });



        if ($dateDebut && $dateFin) {
            $paiementsPayesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        $montantPayeFactures = $paiementsPayesQuery->sum('montant_net_paye_paiement');
        $montantTotalFactures = (clone $facturesQuery)->sum('montant_facture');


        $statsGlobales = [
            'appels_offres' => [
                'total' => (clone $appelsOffresQuery)->count(),
                'montant_total' => (clone $appelsOffresQuery)->sum('montant_global_appel_offre'),
            ],
            'lots' => [
                'total' => (clone $lotsQuery)->count(),
                'attribues' => (clone $lotsQuery)->where('attribution_lot', 1)->count(),
                'non_attribues' => (clone $lotsQuery)->where('attribution_lot', 0)->count(),
            ],
            'prestataires' => [
                'total' => Prestataire::count(), // Les prestataires ne sont pas filtrés par période
                'actifs' => Prestataire::where('statut_prestataire', true)->count(),
                'avec_lots' => Prestataire::whereHas('attributionsActives')->count(),
            ],
            'factures' => [
                'total' => (clone $facturesQuery)->count(),
                'en_attente' => (clone $facturesQuery)->where('statut_facture', 'en_attente')->count(),
                'validees' => (clone $facturesQuery)->where('statut_facture', 'validee')->count(),
                'payees' => (clone $facturesQuery)->where('statut_facture', 'payee')->count(),
                'partiellement_payees' => (clone $facturesQuery)->where('statut_facture', 'partiellement_payee')->count(),
                'montant_total' => $montantTotalFactures,
                'montant_paye' => $montantPayeFactures,
            ],
            'paiements' => [
                'total' => (clone $paiementsQuery)->count(),
                'en_attente' => (clone $paiementsQuery)->where('statut_paiement', Paiement::STATUT_EN_ATTENTE)->count(),
                'valides' => (clone $paiementsQuery)->where('statut_paiement', Paiement::STATUT_VALIDE)->count(),
                'payes' => (clone $paiementsQuery)->where('statut_paiement', Paiement::STATUT_PAYE)->count(),
                'montant_total' => (clone $paiementsQuery)->where('statut_paiement', Paiement::STATUT_PAYE)->sum('montant_net_paye_paiement'),
            ],
        ];

        // Calcul du taux de paiement global
        $statsGlobales['factures']['taux_paiement'] = $statsGlobales['factures']['montant_total'] > 0
            ? round(($statsGlobales['factures']['montant_paye'] / $statsGlobales['factures']['montant_total']) * 100, 1)
            : 0;

        // ================================================================
        // GRAPHIQUE CAMEMBERT - Appels d'offres par type
        // ================================================================
        $appelsParType = TypeAppelOffre::where('actif_type_appel_offre', true)
            ->get()
            ->map(function ($type) use ($dateDebut, $dateFin) {
                $query = $type->appelOffres();
                if ($dateDebut && $dateFin) {
                    $query->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
                $appels = $query->get();
                return [
                    'label' => $type->libelle_type_appel_offre,
                    'code' => $type->code_type_appel_offre,
                    'nombre' => $appels->count(),
                    'montant' => $appels->sum('montant_global_appel_offre'),
                ];
            })
            ->filter(fn($item) => $item['nombre'] > 0)
            ->values();

        // ================================================================
        // GRAPHIQUE HISTOGRAMME - Lots par prestataire
        // ================================================================
        $lotsParPrestataire = Prestataire::whereHas('attributionsActives', function ($query) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $query->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
            })
            ->with(['attributionsActives' => function ($query) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $query->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
                $query->with('proforma');
            }])
            ->get()
            ->map(function ($prestataire) {
                $attributions = $prestataire->attributionsActives;
                $montantTotal = $attributions->sum(function ($attribution) {
                    return $attribution->proforma?->montant_retenu_proforma ?? 0;
                });
                return [
                    'label' => Str::limit($prestataire->raison_sociale_prestataire, 20),
                    'label_complet' => $prestataire->raison_sociale_prestataire,
                    'nombre_lots' => $attributions->count(),
                    'montant' => $montantTotal,
                ];
            })
            ->filter(fn($item) => $item['nombre_lots'] > 0)
            ->sortByDesc('nombre_lots')
            ->take(10)
            ->values();

        // ================================================================
        // GRAPHIQUE LIGNE - Évolution des appels d'offres
        // ================================================================
        $evolutionQuery = AppelOffre::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as mois"),
                DB::raw('COUNT(*) as nombre'),
                DB::raw('COALESCE(SUM(montant_global_appel_offre), 0) as montant')
            );

        if ($dateDebut && $dateFin) {
            $evolutionQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        } else {
            $evolutionQuery->where('created_at', '>=', Carbon::now()->subMonths(12));
        }

        $evolutionAppelsOffres = $evolutionQuery
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('mois')
            ->get();


        // ================================================================
        // GRAPHIQUE BARRES - Factures par statut
        // ================================================================
        $facturesStatutQuery = Facture::select(
            'statut_facture',
            DB::raw('COUNT(*) as nombre'),
            DB::raw('COALESCE(SUM(montant_facture), 0) as montant')
        );

        if ($dateDebut && $dateFin) {
            $facturesStatutQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        $facturesParStatut = $facturesStatutQuery
            ->groupBy('statut_facture')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->statut_facture => [
                    'nombre' => $item->nombre,
                    'montant' => $item->montant,
                ]];
            });

        // ================================================================
        // GRAPHIQUE DOUGHNUT - Paiements par statut
        // ================================================================
        $paiementsStatutQuery = Paiement::select(
            'statut_paiement',
            DB::raw('COUNT(*) as nombre'),
            DB::raw('COALESCE(SUM(montant_net_paye_paiement), 0) as montant')
        );

        if ($dateDebut && $dateFin) {
            $paiementsStatutQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        $paiementsParStatut = $paiementsStatutQuery
            ->groupBy('statut_paiement')
            ->get()
            ->map(function ($item) {
                $statuts = Paiement::getStatuts();
                return [
                    'statut' => $item->statut_paiement,
                    'label' => $statuts[$item->statut_paiement] ?? 'Inconnu',
                    'nombre' => $item->nombre,
                    'montant' => $item->montant,
                ];
            });

        // ================================================================
        // TOP 5 PRESTATAIRES (par montant total)
        // ================================================================
        $topPrestataires = Prestataire::whereHas('attributions', function ($query) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $query->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
            })
            ->with(['attributions' => function ($query) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $query->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
                $query->with('proforma');
            }])
            ->get()
            ->map(function ($prestataire) {
                $montantTotal = $prestataire->attributions->sum(function ($attr) {
                    return $attr->proforma?->montant_retenu_proforma ?? 0;
                });
                $prestataire->montant_total = $montantTotal;
                $prestataire->nombre_attributions = $prestataire->attributions->count();
                return $prestataire;
            })
            ->filter(fn($p) => $p->nombre_attributions > 0)
            ->sortByDesc('montant_total')
            ->take(5)
            ->values();

        // ================================================================
        // DERNIÈRES FACTURES (avec montant payé calculé)
        // ================================================================
        $dernieresFacturesQuery = Facture::with(['proforma.prestatairePrincipal.prestataire', 'paiements']);

        if ($dateDebut && $dateFin) {
            $dernieresFacturesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        $dernieresFactures = $dernieresFacturesQuery
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($facture) {
                $facture->montant_paye_calcule = $facture->paiements
                    ->where('statut_paiement', Paiement::STATUT_PAYE)
                    ->sum('montant_net_paye_paiement');
                return $facture;
            });

        // ================================================================
        // DERNIERS PAIEMENTS
        // ================================================================
        $derniersPaiementsQuery = Paiement::with(['facture.proforma.prestatairePrincipal.prestataire', 'banque']);

        if ($dateDebut && $dateFin) {
            $derniersPaiementsQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        $derniersPaiements = $derniersPaiementsQuery
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ================================================================
        // ALERTES ET NOTIFICATIONS (toujours sur données actuelles)
        // ================================================================
        $alertes = [
            'factures_en_attente' => Facture::where('statut_facture', 'en_attente')->count(),
            'paiements_a_valider' => Paiement::where('statut_paiement', Paiement::STATUT_EN_ATTENTE)->count(),

            'lots_non_attribues' => Lot::whereNull('parent_id')->where('attribution_lot', 0)->where('statut_lot', 1)->count(),
            'prestataires_inactifs' => Prestataire::where('statut_prestataire', false)->count(),
        ];

        // ================================================================
        // GRAPHIQUE - Évolution des paiements
        // ================================================================
        $evolutionPaiementsQuery = Paiement::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as mois"),
                DB::raw('COUNT(*) as nombre'),
                DB::raw('COALESCE(SUM(montant_net_paye_paiement), 0) as montant')
            )
            ->where('statut_paiement', Paiement::STATUT_PAYE);

        if ($dateDebut && $dateFin) {
            $evolutionPaiementsQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        } else {
            $evolutionPaiementsQuery->where('created_at', '>=', Carbon::now()->subMonths(6));
        }

        $evolutionPaiements = $evolutionPaiementsQuery
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('mois')
            ->get();

        // ================================================================
        // APPELS D'OFFRES RÉCENTS
        // ================================================================
        $appelsOffresRecentsQuery = AppelOffre::with(['typeAppelOffre', 'lots']);

        if ($dateDebut && $dateFin) {
            $appelsOffresRecentsQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        $appelsOffresRecents = $appelsOffresRecentsQuery
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ================================================================
        // TAUX D'ATTRIBUTION DES LOTS
        // ================================================================
        $lotsAttribQuery = Lot::whereNull('parent_id');
        if ($dateDebut && $dateFin) {
            $lotsAttribQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }

        $tauxAttributionLots = [
            'attribues' => (clone $lotsAttribQuery)->where('attribution_lot', 1)->count(),
            'non_attribues' => (clone $lotsAttribQuery)->where('attribution_lot', 0)->count(),
            'total' => (clone $lotsAttribQuery)->count(),
        ];
        $tauxAttributionLots['pourcentage'] = $tauxAttributionLots['total'] > 0
            ? round(($tauxAttributionLots['attribues'] / $tauxAttributionLots['total']) * 100, 1)
            : 0;

        // ================================================================
        // COMPARAISON AVEC PÉRIODE PRÉCÉDENTE
        // ================================================================
        $comparaison = null;
        if ($dateDebut && $dateFin) {
            $duree = $dateDebut->diffInDays($dateFin);
            $dateDebutPrec = (clone $dateDebut)->subDays($duree + 1);
            $dateFinPrec = (clone $dateDebut)->subDay();

            $aoPrec = AppelOffre::whereBetween('created_at', [$dateDebutPrec, $dateFinPrec])->count();
            $facturesPrec = Facture::whereBetween('created_at', [$dateDebutPrec, $dateFinPrec])->sum('montant_facture');
            $paiementsPrec = Paiement::where('statut_paiement', Paiement::STATUT_PAYE)
                ->whereBetween('created_at', [$dateDebutPrec, $dateFinPrec])
                ->sum('montant_net_paye_paiement');

            $comparaison = [
                'periode_label' => 'vs période précédente',
                'appels_offres' => [
                    'precedent' => $aoPrec,
                    'variation' => $aoPrec > 0
                        ? round((($statsGlobales['appels_offres']['total'] - $aoPrec) / $aoPrec) * 100, 1)
                        : ($statsGlobales['appels_offres']['total'] > 0 ? 100 : 0),
                ],
                'factures' => [
                    'precedent' => $facturesPrec,
                    'variation' => $facturesPrec > 0
                        ? round((($statsGlobales['factures']['montant_total'] - $facturesPrec) / $facturesPrec) * 100, 1)
                        : ($statsGlobales['factures']['montant_total'] > 0 ? 100 : 0),
                ],
                'paiements' => [
                    'precedent' => $paiementsPrec,
                    'variation' => $paiementsPrec > 0
                        ? round((($statsGlobales['paiements']['montant_total'] - $paiementsPrec) / $paiementsPrec) * 100, 1)
                        : ($statsGlobales['paiements']['montant_total'] > 0 ? 100 : 0),
                ],
            ];
        }



        // ================================================================
        // LISTE DES LOTS NON TERMINÉS ET NON ATTRIBUÉS
        // ================================================================
        $lotsEnCours = Lot::with(['appelOffre', 'attributionActive.prestataire', 'attributionActive.proforma'])
            ->whereNull('parent_id')
            ->where('statut_lot', 1) // Statut actif
            ->where(function($query) {
                // Soit non attribué (attribution_lot = '0' car c'est un enum)
                $query->where('attribution_lot', 0)
                    // Soit attribué mais pas encore terminé
                    ->orWhereHas('attributionActive', function($q) {
                        $q->where('is_active', true)
                          ->whereNotIn('statut_attribution', [4, 5]); // 4=Terminé, 5=Annulé
                    });
            })
            ->orderByRaw("
                CASE
                    WHEN attribution_lot::text = '0' THEN 0
                    ELSE 1
                END
            ")
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()->map(function ($lot) {
                $attribution = $lot->attributionActive;

                // Calcul du montant du lot
                $montantLot = 0;
                if ($attribution && $attribution->proforma) {
                    $montantLot = $attribution->proforma->montant_retenu_proforma + $attribution->proforma->taxe_montant - $attribution->proforma->remise_montant_proforma;
                }

                // Calcul du montant déjà payé
                $montantPaye = 0;
                if ($attribution) {
                    $montantPaye = $attribution->montant_paye ?? 0;
                }

                // Reste à payer
                $resteAPayer = $montantLot - $montantPaye;

                // Calcul du délai restant
                $delaiRestant = null;
                $delaiJours = null;
                // if ($attribution && $lot->date_fin_prevue) {
                //     $dateFin = Carbon::parse($lot->date_fin_prevue);
                //     $maintenant = Carbon::now();

                //     if ($dateFin->isFuture()) {
                //         $delaiJours = $maintenant->diffInDays($dateFin);
                //         $delaiRestant = $delaiJours . ' jour(s)';
                //     } else {
                //         $delaiJours = -$maintenant->diffInDays($dateFin);
                //         $delaiRestant = 'Retard de ' . abs($delaiJours) . ' jour(s)';
                //     }
                // }
                if ($attribution) {
                    $dateFin = $attribution->date_fin_prevue ? Carbon::parse($attribution->date_fin_prevue) : null;
                    $dateEffective = $attribution->date_effective_fin ? Carbon::parse($attribution->date_effective_fin) : null;
                    $maintenant = Carbon::now();

                    if ($dateFin) {
                        if ($dateEffective) {
                            // Travaux terminés : comparer date effective vs date prévue
                            $delaiJours = $dateFin->diffInDays($dateEffective, false);

                            if ($delaiJours > 0) {
                                // Terminé en retard
                                $delaiRestant = 'Retard de ' . $delaiJours . ' jour' . ($delaiJours > 1 ? 's' : '');
                            } elseif ($delaiJours < 0) {
                                // Terminé en avance
                                $joursAvance = abs($delaiJours);
                                $delaiRestant = $joursAvance . ' jour' . ($joursAvance > 1 ? 's' : '') . ' d\'avance';
                            } else {
                                // Terminé à temps
                                $delaiRestant = 'Terminé à temps';
                            }
                        } else {
                            // Travaux en cours : comparer aujourd'hui vs date prévue
                            $delaiJours = $maintenant->diffInDays($dateFin, false);

                            if ($delaiJours > 0) {
                                // Dans les délais
                                $delaiRestant = $delaiJours . ' jour' . ($delaiJours > 1 ? 's' : '') . ' restant' . ($delaiJours > 1 ? 's' : '');
                            } elseif ($delaiJours < 0) {
                                // En retard
                                $joursRetard = abs($delaiJours);
                                $delaiRestant = 'Retard de ' . $joursRetard . ' jour' . ($joursRetard > 1 ? 's' : '');
                            } else {
                                // Aujourd'hui
                                $delaiRestant = 'Échéance aujourd\'hui';
                            }
                        }
                    } else {
                        $delaiJours = null;
                        $delaiRestant = null;
                    }
                } else {
                    $delaiJours = null;
                    $delaiRestant = null;
                }

                return [
                    'id_lot' => $lot->id_lot,
                    'numero_lot' => $lot->numero,
                    'libelle_lot' => $lot->libelle,
                    'appel_offre_id' => $lot->appel_offre_id,
                    'numero_attribution' => $attribution?->numero_attribution ?? '-',
                    'numero_prestataire' => $attribution?->prestataire?->numero_identification_prestataire ?? '-',
                    'raison_sociale_prestataire' => $attribution?->prestataire?->raison_sociale_prestataire ?? '-',
                    'attribution' => $attribution,
                    'montant_lot' => $montantLot,
                    'montant_paye' => $montantPaye,
                    'reste_a_payer' => $resteAPayer,
                    'delai_restant' => $delaiRestant,
                    'delai_jours' => $delaiJours,
                    'avancement' => $attribution?->pourcentage_avancement ?? 0,
                    'est_attribue' => $lot->attribution_lot == 1, // Comparaison avec string car enum
                    'statut_attribution' => $attribution?->statut_attribution,
                    'date_debut_prevue' => $attribution?->date_debut_prevue,
                    'date_effective_fin' => $attribution?->date_effective_fin,
                    'date_fin_prevue' => $attribution?->date_fin_prevue,

                ];
            });

        if($request->expectsJson() || $request->wantsJson()){
            return response()->json([
                'periode' => $periode,
                'statsGlobales' => $statsGlobales,
                'appelsParType' => $appelsParType,
                'lotsParPrestataire' => $lotsParPrestataire,
                'evolutionAppelsOffres' => $evolutionAppelsOffres,
                'facturesParStatut' => $facturesParStatut,
                'paiementsParStatut' => $paiementsParStatut,
                'topPrestataires' => $topPrestataires,
                'dernieresFactures' => $dernieresFactures,
                'derniersPaiements' => $derniersPaiements,
                'alertes' => $alertes,
                'evolutionPaiements' => $evolutionPaiements,
                'appelsOffresRecents' => $appelsOffresRecents,
                'tauxAttributionLots' => $tauxAttributionLots,
                'comparaison' => $comparaison,
                'lotsEnCours' => $lotsEnCours,
            ], 200);
        }

        // dd($lotsEnCours);

        return view('dashboard', compact(
            'periode',
            'statsGlobales',
            'appelsParType',
            'lotsParPrestataire',
            'evolutionAppelsOffres',
            'facturesParStatut',
            'paiementsParStatut',
            'topPrestataires',
            'dernieresFactures',
            'derniersPaiements',
            'alertes',
            'evolutionPaiements',
            'appelsOffresRecents',
            'tauxAttributionLots',
            'comparaison',
            'lotsEnCours'
        ));
    }
}
