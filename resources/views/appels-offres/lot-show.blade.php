@extends('layouts.main')
@section('title', 'Détails Lot - ' . $lot->numero)
@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}" @endcan
        class="text-white/80 hover:text-white transition-colors"
        title="{{ $lot->appelOffre->libelle_critere_appel_offre }}">{{ \Illuminate\Support\Str::limit($lot->appelOffre->libelle_critere_appel_offre, 10) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('lots.read') href="{{ route('lots-appels-offres.index', [$lot->appelOffre->id_appel_offre]) }}" @endcan
        class="text-white/80 hover:text-white transition-colors" title="Lites de lots">Lots</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium"
        title="{{ $lot->libelle }}">{{ \Illuminate\Support\Str::limit($lot->libelle, 35) }}</span>
@endsection


@php
    $allPaiements = $lot->attributionActive?->proforma?->facture?->paiements ?? null;
    $facture = $lot->attributionActive?->proforma?->facture ?? null;
    $proforma = $lot->attributionActive?->proforma ?? null;

    // Calcul du montant payé et reste
    $montantPaye = $allPaiements ? $allPaiements->sum('montant_net_paye_paiement') : 0;
    $montantFacture = $facture?->montant_facture ?? 0;
    $resteAPayer = max(0, $montantFacture - $montantPaye);
    $tauxPaiement = $montantFacture > 0 ? ($montantPaye / $montantFacture) * 100 : 0;

    // États
    $paiementTermine = $montantFacture > 0 && $montantPaye >= $montantFacture;
    $paiementEnCours = $montantPaye > 0 && $montantPaye < $montantFacture;
    $paiementNonCommence = $montantFacture > 0 && $montantPaye == 0;
    $pasPaiementPrevu = !$facture;

    // Évaluation
    $sommesReferencesCriteresEvaluations = $lot->criteresEvaluation->sum('note_reference_critere_evaluation');
    $sommesNotesEvaluations = $lot->criteresEvaluation->flatMap->evaluations->sum('resultat_evaluation');

    $evaluationTerminee =
        $sommesReferencesCriteresEvaluations > 0 && $sommesNotesEvaluations >= $sommesReferencesCriteresEvaluations;
    $evaluationEnCours = $sommesNotesEvaluations > 0 && $sommesNotesEvaluations < $sommesReferencesCriteresEvaluations;
    $evaluationNonCommencee = $sommesReferencesCriteresEvaluations > 0 && $sommesNotesEvaluations == 0;
    $pasEvaluationPrevue = $sommesReferencesCriteresEvaluations == 0;
@endphp

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('lots-appels-offres.index', [$lot->appelOffre->id_appel_offre]) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $lot->numero }}</h1>
                            @if ($lot->statut_lot)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-check-circle mr-1"></i> Actif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inactif
                                </span>
                            @endif

                            {{-- Statut d'attribution du lot --}}
                            @if ($lot->attribution_lot || $lot->attributionActive)
                                <div class="flex flex-wrap items-center gap-2">

                                    {{-- Statut: Attribué (actif, pas de suspension ni retrait) --}}
                                    @if (!$lot->attributionActive->date_suspension && !$lot->attributionActive->date_retrait)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm">
                                            <i class="fas fa-check-circle mr-1.5 text-emerald-500"></i>
                                            Attribué
                                        </span>

                                        {{-- Statut: Retiré (pas de suspension mais date de retrait présente) --}}
                                    @elseif (!$lot->attributionActive->date_suspension && $lot->attributionActive->date_retrait)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                            <i class="fas fa-times-circle mr-1.5 text-red-500"></i>
                                            Retiré
                                        </span>

                                        {{-- Statut: Suspendu --}}
                                    @elseif ($lot->attributionActive->date_suspension)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200 shadow-sm">
                                            <i class="fas fa-pause-circle mr-1.5 text-amber-500"></i>
                                            Suspendu
                                        </span>
                                    @endif


                                </div>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    <i class="fas fa-user-slash mr-1.5 text-gray-400"></i>
                                    Non attribué
                                </span>
                            @endif

                        </div>
                        <p class="text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($lot->libelle, 60) }}</p>
                    </div>
                </div>

                @canany(['attributions_lots.view-details', 'criteres_evaluations.read', 'attributions_lots.assign',
                    'documents_lots.read', 'lots.update', 'lots.view-history', 'lots.duplicate', 'lots.delete'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap">

                        {{-- Boutons d'action pour les lots non attribués --}}
                        <div class="flex flex-wrap items-center gap-3">

                            @can('attributions_lots.view-details')
                                @if ($lot->attribution_lot || $lot->attributionActive)
                                    <a href="{{ route('attributions.show', $lot->attributionActive) }}"
                                        class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 hover:border-blue-400 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm group">
                                        <i class="fas fa-info-circle mr-1.5"></i>
                                        Détails attribution
                                    </a>
                                @endif
                            @endcan


                            @can('criteres_evaluations.read')
                                <a href="{{ route('criteres-evaluations.index', [$lot->appel_offre_id, $lot->id_lot]) }}"
                                    class="px-4 py-2.5 bg-white border border-purple-300 text-purple-600 hover:bg-purple-50 hover:border-purple-400 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm group">
                                    <i class="fas fa-clipboard-check text-sm group-hover:scale-110 transition-transform"></i>
                                    <span class="text-sm font-medium">Critères d'évaluation</span>
                                </a>
                            @endcan


                            @can('attributions_lots.assign')
                                {{-- Bouton Attribuer --}}
                                @if (
                                    !$lot->attribution_lot &&
                                        !$lot->isRetire() &&
                                        $lot->criteresEvaluation->count() > 0 &&
                                        $lot->criteresEvaluation->sum('note_reference_critere_evaluation') == 100)
                                    <button onclick="openAttributionModal()"
                                        class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-500 text-white hover:from-emerald-600 hover:to-green-600 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md group">
                                        <i class="fas fa-user-plus text-sm group-hover:scale-110 transition-transform"></i>
                                        <span class="text-sm font-medium">Attribuer</span>
                                    </button>
                                @endif
                            @endcan

                        </div>

                        @can('documents_lots.read')
                            <a href="{{ route('lots.documents.index', $lot->id_lot) }}"
                                class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-edit text-sm"></i>
                                <span class="text-sm font-medium">Documents</span>
                            </a>
                        @endcan

                        @can('lots.update')
                            @if (!$lot->attribution_lot)
                                <button
                                    onclick="window.location.href='{{ route('lots-appels-offres.edit', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}'"
                                    class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-edit text-sm"></i>
                                    <span class="text-sm font-medium">Modifier</span>
                                </button>
                            @endif
                        @endcan

                        @canany(['lots.view-history', 'lots.duplicate', 'lots.delete'])
                            <!-- Menu dropdown -->
                            <div class="relative">
                                <button onclick="toggleMenu()" id="menuBtn"
                                    class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                                <div id="actionMenu"
                                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                                    <div class="py-1">
                                        @can('lots.view-history')
                                            <a href="{{ route('lots-appels-offres.historique', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-history mr-2 text-blue-500"></i>
                                                Historique
                                            </a>
                                        @endcan

                                        @can('lots.delete')
                                            @if (!$lot->isAttribue())
                                                <button onclick="confirmDelete()"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                    <i class="fas fa-trash mr-2"></i>
                                                    Supprimer
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endcanany
                    </div>
                @endcanany
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informations principales -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                            Informations générales
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Appel d'offres -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Appel d'offres</label>
                            <div class="flex items-center space-x-3">
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                    {{ $lot->appelOffre->numero_appel_offre }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $lot->appelOffre->libelle_critere_appel_offre }}</p>
                                    <p class="text-xs text-gray-500">
                                        <i
                                            class="fas fa-tag mr-1"></i>{{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
                                    </p>
                                </div>
                                <a href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}"
                                    class="ml-auto p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                    title="Voir l'appel d'offres">
                                    <i class="fas fa-external-link-alt text-sm"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Numéro et Libellé -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Numéro</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                    {{ $lot->numero }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Libellé</label>
                                <p class="text-gray-900 font-medium">{{ $lot->libelle }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        @if ($lot->description_critere)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Description</label>
                                <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">
                                    {{ $lot->description_critere }}
                                </p>
                            </div>
                        @endif

                        <!-- Spécifications techniques -->
                        @if ($lot->specifications_techniques)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Spécifications
                                    techniques</label>
                                <p class="text-gray-700 leading-relaxed bg-blue-50 p-4 rounded-lg whitespace-pre-wrap">
                                    {{ $lot->specifications_techniques }}
                                </p>
                            </div>
                        @endif

                        <!-- Budget du lot -->
                        @if ($lot->budget_lot)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Budget du lot</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-100 text-green-700">
                                    <i class="fas fa-coins mr-2"></i>
                                    {{ rtrim(rtrim(number_format(floor($lot->budget_lot), 0, ',', ' '), '0'), ',') }} FCFA
                                </span>
                            </div>
                        @endif

                        <!-- État de l'évaluation -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">État de l'évaluation</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                @if ($pasEvaluationPrevue)
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-gray-200 text-gray-600">
                                        <i class="fas fa-minus-circle mr-2"></i>
                                        Aucune évaluation prévue
                                    </span>
                                @elseif ($evaluationTerminee)
                                    <div class="flex items-center space-x-3">
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Évaluation terminée
                                        </span>
                                        <span class="text-sm text-gray-700 font-medium">
                                            {{ number_format(floor($sommesNotesEvaluations), 0) }} /
                                            {{ number_format(floor($sommesReferencesCriteresEvaluations), 0) }}
                                        </span>
                                    </div>
                                @elseif ($evaluationEnCours)
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-spinner mr-2"></i>
                                                Évaluation en cours
                                            </span>
                                            <span class="text-sm font-bold text-gray-700">
                                                {{ number_format(($sommesNotesEvaluations / $sommesReferencesCriteresEvaluations) * 100, 2) }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-yellow-500 h-2.5 rounded-full transition-all"
                                                style="width: {{ ($sommesNotesEvaluations / $sommesReferencesCriteresEvaluations) * 100 }}%">
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            {{ number_format(floor($sommesNotesEvaluations), 2) }} /
                                            {{ number_format(floor($sommesReferencesCriteresEvaluations), 2) }} points
                                        </p>
                                    </div>
                                @elseif ($evaluationNonCommencee)
                                    <div class="flex items-center space-x-3">
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-red-100 text-red-700">
                                            <i class="fas fa-clock mr-2"></i>
                                            Évaluation non commencée
                                        </span>
                                        <span class="text-sm text-gray-700 font-medium">
                                            0 / {{ number_format($sommesReferencesCriteresEvaluations, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- État du paiement -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">État du paiement</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                @if ($pasPaiementPrevu)
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-gray-200 text-gray-600">
                                        <i class="fas fa-minus-circle mr-2"></i>
                                        Aucune facture associée
                                    </span>
                                @elseif ($paiementTermine)
                                    <div class="space-y-3">
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Paiement terminé
                                        </span>
                                        <div class="grid grid-cols-3 gap-4 pt-2 border-t border-gray-200">
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Montant facturé</p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ number_format(floor($montantFacture), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Montant payé</p>
                                                <p class="text-sm font-medium text-green-700">
                                                    {{ number_format(floor($montantPaye), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Reste à payer</p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ number_format(floor($resteAPayer), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($paiementEnCours)
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-blue-100 text-blue-700">
                                                <i class="fas fa-hourglass-half mr-2"></i>
                                                Paiement en cours
                                            </span>
                                            <span class="text-sm font-bold text-gray-700">
                                                {{ number_format($tauxPaiement, 2) }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-500 h-2.5 rounded-full transition-all"
                                                style="width: {{ $tauxPaiement }}%">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 pt-2 border-t border-gray-200">
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Montant facturé</p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ number_format(floor($montantFacture), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Montant payé</p>
                                                <p class="text-sm font-medium text-green-700">
                                                    {{ number_format(floor($montantPaye), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Reste à payer</p>
                                                <p class="text-sm font-medium text-orange-700">
                                                    {{ number_format(floor($resteAPayer), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($paiementNonCommence)
                                    <div class="space-y-3">
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            Paiement non commencé
                                        </span>
                                        <div class="grid grid-cols-3 gap-4 pt-2 border-t border-gray-200">
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Montant facturé</p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ number_format(floor($montantFacture), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Montant payé</p>
                                                <p class="text-sm font-medium text-gray-600">
                                                    0 FCFA
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Reste à payer</p>
                                                <p class="text-sm font-medium text-orange-700">
                                                    {{ number_format(floor($resteAPayer), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dates et délais -->
                @if ($lot->date_debut_prevue || $lot->date_fin_prevue || $lot->date_attribution)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Dates et Délais
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Date d'attribution -->
                                @if ($lot->date_attribution)
                                    <div
                                        class="bg-gradient-to-br from-green-50 to-white p-5 rounded-xl border border-green-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-600">Attribution</span>
                                            <i class="fas fa-user-check text-green-500"></i>
                                        </div>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ $lot->date_attribution->format('d/m/Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $lot->date_attribution->diffForHumans() }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Date début prévue -->
                                @if ($lot->date_debut_prevue)
                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-600">Début prévu</span>
                                            <i class="fas fa-play text-blue-500"></i>
                                        </div>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ $lot->date_debut_prevue->format('d/m/Y') }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Date fin prévue -->
                                @if ($lot->date_fin_prevue)
                                    <div
                                        class="bg-gradient-to-br from-orange-50 to-white p-5 rounded-xl border border-orange-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-600">Fin prévue</span>
                                            <i class="fas fa-flag-checkered text-orange-500"></i>
                                        </div>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ $lot->date_fin_prevue->format('d/m/Y') }}
                                        </p>
                                        @if ($lot->calculerDuree())
                                            <p class="text-xs text-orange-600 font-semibold mt-1">
                                                Durée: {{ $lot->calculerDuree() }} jour(s)
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Attribution active -->
                @if ($lot->attributionActive)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-user-tie text-green-500 mr-2"></i>
                                Attribution actuelle
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        {{ $lot->attributionActive->prestataire->raison_sociale_prestataire ?? 'N/A' }}
                                    </h3>

                                    @if ($lot->attributionActive->proforma)
                                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                                            <span>
                                                <i class="fas fa-file-invoice-dollar mr-1"></i>
                                                Proforma: {{ $lot->attributionActive->proforma->numero_proforma ?? 'N/A' }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        @if ($lot->attributionActive->date_debut_reelle)
                                            <div>
                                                <p class="text-xs text-gray-500">Début réel</p>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($lot->attributionActive->date_debut_reelle)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                        @endif

                                        @if ($lot->attributionActive->date_fin_reelle)
                                            <div>
                                                <p class="text-xs text-gray-500">Fin réelle</p>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($lot->attributionActive->date_fin_reelle)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                        @endif

                                        @if ($lot->attributionActive->pourcentage_avancement)
                                            <div>
                                                <p class="text-xs text-gray-500">Avancement</p>
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-green-500 h-2 rounded-full"
                                                            style="width: {{ $lot->attributionActive->pourcentage_avancement }}%">
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-semibold text-gray-900">{{ $lot->attributionActive->pourcentage_avancement }}%</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Statistiques -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
                        Statistiques
                    </h3>

                    <div class="space-y-4">
                        <!-- Version -->
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-transparent rounded-lg border-l-4 border-purple-500">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Version</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $lot->version ?? 1 }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-code-branch text-purple-600"></i>
                            </div>
                        </div>

                        <!-- Durée prévue -->
                        @if ($lot->calculerDuree())
                            <div
                                class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-transparent rounded-lg border-l-4 border-blue-500">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Durée prévue</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $lot->calculerDuree() }} <span
                                            class="text-sm font-normal">jour(s)</span></p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-day text-blue-600"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations système -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cog text-gray-500 mr-2"></i>
                        Informations système
                    </h3>

                    <div class="space-y-4 text-sm">
                        <!-- Enregistré par -->
                        @if ($lot->creator)
                            <div>
                                <p class="text-gray-600 font-medium mb-1">Enregistré par</p>
                                <p class="text-gray-900">{{ $lot->creator->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $lot->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif

                        <!-- Modifié par -->
                        @if ($lot->updater && $lot->updated_at != $lot->created_at)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-gray-600 font-medium mb-1">Dernière modification</p>
                                <p class="text-gray-900">{{ $lot->updater->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $lot->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif

                        @can('lots.update')
                            <!-- Version parent -->
                            @if ($lot->parent_id)
                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-gray-600 font-medium mb-1">Version parente</p>
                                    <a href="{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->parent_id]) }}"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs flex items-center">
                                        <i class="fas fa-link mr-1"></i>
                                        Voir la version parente
                                    </a>
                                </div>
                            @endif
                        @endcan
                    </div>
                </div>

                @canany(['attributions_lots.assign', 'lots.view-history', 'lots.duplicate'])
                    <!-- Actions rapides -->
                    <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-lg p-6 border border-indigo-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-bolt text-indigo-500 mr-2"></i>
                            Actions rapides
                        </h3>

                        <div class="space-y-2">
                            @can('attributions_lots.assign')
                                @if (
                                    !$lot->attribution_lot &&
                                        !$lot->isRetire() &&
                                        $lot->criteresEvaluation->count() > 0 &&
                                        $lot->criteresEvaluation->sum('note_reference_critere_evaluation') == 100)
                                    <button onclick="openAttributionModal()"
                                        class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-green-50 border border-green-200 rounded-lg transition-all duration-200 group">
                                        <div
                                            class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                            <i class="fas fa-user-check text-green-600"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">Attribuer le lot</span>
                                    </button>
                                @endif
                            @endcan

                            @can('lots.view-history')
                                <a href="{{ route('lots-appels-offres.historique', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                    class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-blue-50 border border-blue-200 rounded-lg transition-all duration-200 group">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-history text-blue-600"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Voir l'historique</span>
                                </a>
                            @endcan


                        </div>
                    </div>
                @endcanany
            </div>
        </div>
    </main>

    <!-- Modal Confirmation Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-hidden"
        onclick="if(event.target === this) closeDeleteModal()">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
                        <p id="deleteMessage" class="text-sm text-gray-600 mb-6"></p>

                        <div class="flex items-center justify-center space-x-3">
                            <button onclick="closeDeleteModal()"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                                Annuler
                            </button>
                            @can('lots.delete')
                                <button onclick="executeDelete()"
                                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                                    Supprimer
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL ATTRIBUTION - AMÉLIORÉ -->
    <!-- ========================================== -->
    <div id="attributionModal" class="hidden fixed inset-0 z-50 overflow-hidden"
        aria-labelledby="attribution-modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeAttributionModal()"></div>

        <!-- Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">

                <!-- Contenu du modal -->
                <div id="attributionModalContent"
                    class="relative w-full max-w-3xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4">

                    <!-- Header -->
                    <div class="relative bg-gradient-to-r from-orange-600 to-orange-400 rounded-t-2xl px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg">
                                    <i class="fas fa-user-check text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 id="attribution-modal-title" class="text-xl font-bold text-white">Attribuer le lot
                                    </h3>
                                    <p class="text-green-100 text-sm">Associer un prestataire et une proforma</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeAttributionModal()"
                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 transition-colors duration-200 group">
                                <i
                                    class="fas fa-times text-white group-hover:rotate-90 transition-transform duration-200"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Corps du formulaire -->
                    <form id="attributionForm">
                        @csrf
                        <div class="px-6 py-6 max-h-[calc(100vh-280px)] overflow-y-auto custom-scrollbar">

                            <!-- Info Lot -->
                            <div
                                class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl">
                                <div class="flex items-start space-x-3">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-indigo-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-indigo-800">{{ $lot->numero }} -
                                            {{ $lot->libelle }}</p>
                                        <p class="text-xs text-indigo-600 mt-0.5">AO:
                                            {{ $lot->appelOffre->numero_appel_offre }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">

                                <!-- ================================ -->
                                <!-- SECTION PRESTATAIRE -->
                                <!-- ================================ -->
                                <div class="space-y-3">
                                    <label class="flex items-center text-sm font-semibold text-gray-700">
                                        <i class="fas fa-building text-green-500 mr-2 text-xs"></i>
                                        Prestataire
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>

                                    <!-- Champ de recherche prestataire -->
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" id="searchPrestataire"
                                            class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all duration-200 text-gray-800 placeholder-gray-400"
                                            placeholder="Rechercher un prestataire par nom ou numéro..."
                                            autocomplete="off">
                                    </div>

                                    <!-- Liste des prestataires -->
                                    <div id="prestataireListContainer" class="relative">
                                        <select name="prestataire_id" id="prestataire_id" required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all duration-200 text-gray-800 appearance-none bg-white cursor-pointer"
                                            size="4">
                                            @forelse ($prestataires as $prestataire)
                                                <option value="{{ $prestataire->id_prestataire }}"
                                                    data-numero="{{ $prestataire->numero_cc_prestataire }}"
                                                    data-raison="{{ $prestataire->raison_sociale_prestataire }}">
                                                    ({{ $prestataire->numero_cc_prestataire }})
                                                    - {{ $prestataire->raison_sociale_prestataire }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Aucun prestataire disponible</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- Prestataire sélectionné -->
                                    <div id="selectedPrestataire"
                                        class="hidden p-3 bg-green-50 border border-green-200 rounded-xl">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-check-circle text-green-500"></i>
                                                <span id="selectedPrestataireName"
                                                    class="text-sm font-medium text-green-800"></span>
                                            </div>
                                            <button type="button" onclick="clearPrestataireSelection()"
                                                class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div id="error_prestataire_id"
                                        class="hidden mt-2 text-red-500 text-sm flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        <span></span>
                                    </div>
                                </div>

                                <!-- ================================ -->
                                <!-- SECTION PROFORMA -->
                                <!-- ================================ -->
                                <div class="space-y-4">
                                    <label class="flex items-center text-sm font-semibold text-gray-700">
                                        <i class="fas fa-file-invoice-dollar text-blue-500 mr-2 text-xs"></i>
                                        Proforma
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>


                                    <!-- ================================ -->
                                    <div id="proformaCreateMode" class="">
                                        <div
                                            class="border-2 border-dashed border-blue-300 rounded-xl overflow-hidden bg-gradient-to-br from-blue-50/50 to-white">
                                            <!-- Header accordéon -->
                                            <div
                                                class="px-5 py-4 bg-gradient-to-r from-blue-100 to-blue-50 border-b border-blue-200">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-file-medical text-blue-600"></i>
                                                    <span class="font-semibold text-blue-800">Informations de la nouvelle
                                                        proforma</span>
                                                </div>
                                                <p class="text-xs text-blue-600 mt-1">Tous les champs marqués * sont
                                                    obligatoires</p>
                                            </div>

                                            <!-- Contenu accordéon -->
                                            <div class="p-5 space-y-5">

                                                <!-- Dates de la proforma -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label
                                                            class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                            <i class="fas fa-calendar text-blue-500 mr-2 text-xs"></i>
                                                            Date proforma <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <input type="date" required name="new_date_proforma"
                                                            id="new_date_proforma" value="{{ date('Y-m-d') }}"
                                                            class="proforma-required w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                                        <div id="error_new_date_proforma"
                                                            class="hidden mt-1 text-red-500 text-xs"></div>
                                                    </div>
                                                </div>

                                                <!-- Montants -->
                                                <div
                                                    class="p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-200">
                                                    <h4 class="text-sm font-bold text-emerald-800 mb-4 flex items-center">
                                                        <i class="fas fa-coins mr-2"></i>
                                                        Montants et calculs
                                                    </h4>

                                                    <div class="space-y-4">

                                                        <!-- Budget hors taxe du lot (HT) -->
                                                        <div>
                                                            <label
                                                                class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                Budget hors taxe du lot (HT) <span
                                                                    class="text-red-500 ml-1">*</span>
                                                            </label>

                                                            <!-- Rappel du budget du lot -->
                                                            <div
                                                                class="mb-3 p-3 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl border border-indigo-200">
                                                                <!-- Ligne supérieure : Budget lot + Montant restant AO -->
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                    <!-- Budget total du lot -->
                                                                    <div class="flex items-center space-x-3">
                                                                        <div
                                                                            class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                                            <i class="fas fa-box text-indigo-600"></i>
                                                                        </div>
                                                                        <div>
                                                                            <p class="text-xs text-indigo-600 font-medium">
                                                                                Budget du lot</p>
                                                                            <p class="text-base font-bold text-indigo-800">
                                                                                {{ number_format(floor($lot->budget_lot), 0, ',', ' ') }}
                                                                                <span
                                                                                    class="text-sm font-semibold">FCFA</span>
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Montant restant AO -->
                                                                    <div
                                                                        class="flex items-center space-x-3 sm:justify-end">
                                                                        <div
                                                                            class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                                                            <i class="fas fa-coins text-emerald-600"></i>
                                                                        </div>
                                                                        <div class="sm:text-right">
                                                                            <p
                                                                                class="text-xs text-emerald-600 font-medium flex items-center sm:justify-end flex-wrap gap-1">
                                                                                Restant sur AO
                                                                                <span
                                                                                    class="inline-flex items-center px-1.5 py-0.5 bg-emerald-200 text-emerald-800 text-[10px] font-bold rounded">
                                                                                    {{ $lot->appelOffre->numero_appel_offre }}
                                                                                </span>
                                                                            </p>
                                                                            {{-- {{ dd($lot) }} --}}
                                                                            <p
                                                                                class="text-base font-bold text-emerald-700">
                                                                                {{ number_format($montantRestant, 0, ',', ' ') }}
                                                                                <span
                                                                                    class="text-sm font-semibold">FCFA</span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Barre de progression -->
                                                                @php
                                                                    $montantUtilise =
                                                                        $lot->budget_lot - $montantRestant;
                                                                    $pourcentageUtilise =
                                                                        $lot->budget_lot > 0
                                                                            ? ($montantUtilise / $lot->budget_lot) * 100
                                                                            : 0;
                                                                @endphp
                                                                @if ($pourcentageUtilise > 0)
                                                                    <div class="mt-3 pt-3 border-t border-indigo-200">
                                                                        <div
                                                                            class="flex items-center justify-between text-xs text-gray-600 mb-1.5">
                                                                            <span class="flex items-center">
                                                                                <i
                                                                                    class="fas fa-chart-pie mr-1 text-indigo-400"></i>
                                                                                Déjà attribué : <span
                                                                                    class="font-semibold text-indigo-700 ml-1">{{ number_format(floor($montantUtilise), 0, ',', ' ') }}
                                                                                    FCFA</span>
                                                                            </span>
                                                                            <span
                                                                                class="font-bold text-indigo-600">{{ number_format($pourcentageUtilise, 2) }}%</span>
                                                                        </div>
                                                                        <div
                                                                            class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                                            <div class="h-2 rounded-full transition-all duration-500 ease-out
                                                                                @if ($pourcentageUtilise < 50) bg-emerald-500
                                                                                @elseif($pourcentageUtilise < 80) bg-amber-500
                                                                                @else bg-red-500 @endif"
                                                                                style="width: {{ min($pourcentageUtilise, 100) }}%">
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="flex justify-between text-[10px] text-gray-400 mt-1">
                                                                            <span>0%</span>
                                                                            <span>50%</span>
                                                                            <span>100%</span>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <!-- Message si aucune attribution -->
                                                                    <div class="mt-3 pt-3 border-t border-indigo-200">
                                                                        <div
                                                                            class="flex items-center text-xs text-gray-500">
                                                                            <i
                                                                                class="fas fa-info-circle mr-2 text-indigo-400"></i>
                                                                            <span>Aucune attribution effectuée - Budget
                                                                                intégralement disponible</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- Champ de saisie du montant -->
                                                            <div class="relative">
                                                                <input type="text" required inputmode="decimal"
                                                                    name="new_montant_retenu" id="new_montant_retenu"
                                                                    data-min="5" data-max="{{ $montantRestant }}"
                                                                    value="{{ old('new_montant_retenu') }}"
                                                                    class="proforma-required w-full px-4 py-2.5 pr-16 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                                                                    placeholder="Saisir le montant HT de cette attribution..."
                                                                    autocomplete="off">
                                                                <span
                                                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                                            </div>

                                                            <!-- Message d'aide pour les limites -->
                                                            <div class="mt-2 flex items-center justify-between text-xs">
                                                                <p class="text-gray-500 flex items-center">
                                                                    <i class="fas fa-arrow-down mr-1 text-gray-400"></i>
                                                                    Min: <span class="font-semibold text-gray-700 ml-1">5
                                                                        FCFA</span>
                                                                </p>
                                                                <p class="text-emerald-600 flex items-center">
                                                                    <i class="fas fa-arrow-up mr-1"></i>
                                                                    Max: <span
                                                                        class="font-semibold ml-1">{{ number_format(floor($montantRestant), 0, ',', ' ') }}
                                                                        FCFA</span>
                                                                </p>
                                                            </div>

                                                            <div id="error_new_montant_retenu"
                                                                class="hidden mt-1 text-red-500 text-xs flex items-center">
                                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                                <span></span>
                                                            </div>
                                                        </div>



                                                        <!-- TVA -->
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label
                                                                    class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Taux TVA <span class="text-red-500 ml-1">*</span>
                                                                </label>

                                                                <!-- Case à cocher Exonération -->
                                                                <div
                                                                    class="flex items-center mb-2 p-2 bg-amber-50 rounded-lg border border-amber-200">
                                                                    <input type="checkbox" id="new_exoneration_tva"
                                                                        class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500 focus:ring-2 cursor-pointer"
                                                                        onchange="toggleExonerationTVA(this)">
                                                                    <label for="new_exoneration_tva"
                                                                        class="ml-2 text-sm font-medium text-amber-800 cursor-pointer select-none">
                                                                        <i class="fas fa-certificate mr-1"></i>
                                                                        Exonération de TVA
                                                                    </label>
                                                                </div>

                                                                <div class="relative">
                                                                    <input type="text" required inputmode="decimal"
                                                                        name="new_taux_tva" id="new_taux_tva"
                                                                        data-min="0" data-max="100"
                                                                        value="{{ old('new_taux_tva', '18') }}"
                                                                        class="proforma-required w-full px-4 py-2.5 pr-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                                                                        autocomplete="off">
                                                                    <span
                                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Montant TVA
                                                                    <span
                                                                        class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full">Auto</span>
                                                                </label>
                                                                <div class="relative mt-8">
                                                                    <input type="text" inputmode="decimal"
                                                                        name="new_taxe_montant" id="new_taxe_montant"
                                                                        value="{{ old('new_taxe_montant') }}" readonly
                                                                        tabindex="-1"
                                                                        class="w-full px-4 py-2.5 pr-16 border-2 border-gray-100 rounded-xl bg-gray-50 text-gray-700 font-medium cursor-not-allowed"
                                                                        placeholder="0,00">
                                                                    <span
                                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Remise -->
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label
                                                                    class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Taux remise
                                                                    <span
                                                                        class="ml-2 text-xs text-gray-500">(optionnel)</span>
                                                                </label>
                                                                <div class="relative">
                                                                    <input type="text" inputmode="decimal"
                                                                        name="new_taux_remise" id="new_taux_remise"
                                                                        data-min="0" data-max="100"
                                                                        value="{{ old('new_taux_remise', '0') }}"
                                                                        class="w-full px-4 py-2.5 pr-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                                                                        autocomplete="off">
                                                                    <span
                                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Montant remise
                                                                    <span
                                                                        class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full">Auto</span>
                                                                </label>
                                                                <div class="relative">
                                                                    <input type="text" inputmode="decimal"
                                                                        name="new_remise_montant" id="new_remise_montant"
                                                                        value="{{ old('new_remise_montant', '0') }}"
                                                                        readonly tabindex="-1"
                                                                        class="w-full px-4 py-2.5 pr-16 border-2 border-gray-100 rounded-xl bg-gray-50 text-gray-700 font-medium cursor-not-allowed"
                                                                        placeholder="0,00">
                                                                    <span
                                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Total TTC -->
                                                        <div class="pt-4 border-t-2 border-emerald-200">
                                                            <div
                                                                class="flex items-center justify-between p-4 bg-emerald-100 rounded-xl shadow-sm">
                                                                <div>
                                                                    <span
                                                                        class="font-bold text-emerald-800 text-base">TOTAL
                                                                        TTC</span>
                                                                    <p class="text-xs text-emerald-600 mt-0.5">Montant
                                                                        toutes taxes comprises</p>
                                                                </div>
                                                                <div class="flex items-baseline space-x-2">
                                                                    <span id="displayTotalTTC"
                                                                        class="text-2xl font-bold text-emerald-700">0</span>
                                                                    <span
                                                                        class="text-emerald-600 font-medium text-sm">FCFA</span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="new_total_ttc" id="new_total_ttc"
                                                                value="{{ old('new_total_ttc') }}">

                                                            <!-- Détails du calcul (optionnel, peut être masqué) -->
                                                            <div
                                                                class="mt-3 p-3 bg-white rounded-lg border border-emerald-100">
                                                                <details class="group">
                                                                    <summary
                                                                        class="cursor-pointer text-xs font-medium text-gray-600 hover:text-emerald-600 transition-colors flex items-center">
                                                                        <i class="fas fa-calculator mr-2"></i>
                                                                        Voir le détail du calcul
                                                                        <i
                                                                            class="fas fa-chevron-down ml-auto text-xs group-open:rotate-180 transition-transform"></i>
                                                                    </summary>
                                                                    <div class="mt-3 space-y-1.5 text-xs text-gray-600">
                                                                        <div class="flex justify-between">
                                                                            <span>Montant HT:</span>
                                                                            <span id="detail_ht" class="font-medium">0
                                                                                FCFA</span>
                                                                        </div>
                                                                        <div class="flex justify-between text-green-600">
                                                                            <span>+ TVA (<span
                                                                                    id="detail_tva_rate">18</span>%):</span>
                                                                            <span id="detail_tva" class="font-medium">0
                                                                                FCFA</span>
                                                                        </div>
                                                                        <div class="flex justify-between text-orange-600">
                                                                            <span>- Remise (<span
                                                                                    id="detail_remise_rate">0</span>%):</span>
                                                                            <span id="detail_remise" class="font-medium">0
                                                                                FCFA</span>
                                                                        </div>
                                                                        <div
                                                                            class="flex justify-between pt-2 border-t border-gray-200 font-semibold text-emerald-700">
                                                                            <span>Total TTC:</span>
                                                                            <span id="detail_total">0 FCFA</span>
                                                                        </div>
                                                                    </div>
                                                                </details>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modalités de paiement -->
                                                <div>
                                                    <label
                                                        class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-credit-card text-purple-500 mr-2 text-xs"></i>
                                                        Modalités de paiement
                                                    </label>
                                                    <textarea name="new_modalite_paiement" id="new_modalite_paiement" rows="2"
                                                        class="proforma-required w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 resize-none"
                                                        placeholder="Ex: 30% à la commande, 70% à la livraison"></textarea>
                                                    <div id="error_new_modalite_paiement"
                                                        class="hidden mt-1 text-red-500 text-xs"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ================================ -->
                                <!-- DATE D'ATTRIBUTION -->
                                <!-- ================================ -->

                                <!-- Dates -->
                                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b">
                                        <h2 class="text-lg font-bold text-gray-800"><i
                                                class="fas fa-calendar-alt text-blue-500 mr-2"></i>Planification</h2>
                                    </div>
                                    <div class="p-6">
                                        <div class="grid grid-cols-3 gap-5">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date
                                                    d'attribution <span class="text-red-500">*</span></label>
                                                <input type="date" name="date_attribution" id="date_attribution"
                                                    required value="{{ old('date_attribution', date('Y-m-d')) }}"
                                                    max="{{ date('Y-m-d') }}"
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date début
                                                    <span class="text-red-500">*</span></label>
                                                <input type="date" name="date_debut_prevue"
                                                    value="{{ old('date_debut_prevue', $lot->date_debut_prevue?->format('Y-m-d')) }}"
                                                    onchange="updateDateFinMin()"

                                                    id="date_debut_prevue" required
                                                    value="{{ old('date_debut_prevue') }}"
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date fin
                                                    <span class="text-red-500">*</span></label>
                                                <input type="date" name="date_fin_prevue"
                                                    onchange="updateDateFinMin()"
                                                    value="{{ old('date_fin_prevue', $lot->date_fin_prevue?->format('Y-m-d')) }}"

                                                    id="date_fin_prevue" required value="{{ old('date_fin_prevue') }}"
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                            </div>
                                        </div>
                                        <div class="mt-4 p-3 bg-gray-50 rounded-lg flex justify-between"><span
                                                class="text-sm text-gray-600">Durée prévue:</span><span id="dureeCalculee"
                                                class="font-semibold">-</span></div>
                                    </div>
                                </div>

                                <script>
                                    function updateDateFinMin() {
                                        const dateDebut = document.getElementById('date_debut_prevue').value;
                                        const dateFin = document.getElementById('date_fin_prevue');

                                        if (dateDebut) {
                                            dateFin.min = dateDebut;

                                            // Réinitialiser si date fin < date début
                                            if (dateFin.value && dateFin.value < dateDebut) {
                                                dateFin.value = '';
                                            }
                                        }
                                    }
                                </script>

                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                                <p class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Les champs marqués <span class="text-red-500 mx-1">*</span> sont obligatoires
                                </p>
                                <div class="flex items-center space-x-3">
                                    <button type="button" onclick="closeAttributionModal()"
                                        class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 font-medium text-sm">
                                        <i class="fas fa-times mr-2"></i>Annuler
                                    </button>
                                    <button type="submit" id="submitAttributionBtn"
                                        class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl transition-all duration-200 font-medium text-sm shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                        <i class="fas fa-check mr-2" id="submitAttributionIcon"></i>
                                        <span id="submitAttributionText">Attribuer le lot</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Retrait -->
    <div id="retraitModal" class="hidden fixed inset-0 z-50 overflow-hidden"
        onclick="if(event.target === this) closeRetraitModal()">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full relative">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900">Retirer le lot</h3>
                        <button onclick="closeRetraitModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form id="retraitForm" class="p-6">
                        @csrf
                        <div class="space-y-5">
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-700">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Cette action retirera le lot du prestataire actuel.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Motif du retrait <span class="text-red-500">*</span>
                                </label>
                                <textarea name="motif_retrait" id="motif_retrait" rows="4" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none"
                                    placeholder="Veuillez indiquer la raison du retrait..."></textarea>
                                <div id="error_motif_retrait" class="hidden text-red-500 text-sm mt-1"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                            <button type="button" onclick="closeRetraitModal()"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                                Annuler
                            </button>
                            @can('attributions_lots.withdraw')
                                <button type="submit" id="submitRetraitBtn"
                                    class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                                    <i class="fas fa-ban mr-2"></i>
                                    <span id="submitRetraitText">Retirer</span>
                                </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const lotId = '{{ $lot->id_lot }}';
            const appelOffreId = '{{ $lot->appel_offre_id }}';

            // ==========================================
            // DONNÉES POUR LA RECHERCHE
            // ==========================================
            const prestatairesData = @json(
                $prestataires->map(function ($p) {
                    return [
                        'id' => $p->id_prestataire,
                        'numero' => $p->numero_cc_prestataire,
                        'raison' => $p->raison_sociale_prestataire
                    ];
                }));

            const proformasData = @json(
                $proformas->map(function ($p) {
                    return [
                        'id' => $p->id_proforma,
                        'numero' => $p->numero_proforma,
                        'montant' => $p->montant_retenu_proforma
                    ];
                }));

            // ==========================================
            // GESTION DU MODAL ATTRIBUTION
            // ==========================================
            function openAttributionModal() {
                const modal = document.getElementById('attributionModal');
                const content = document.getElementById('attributionModalContent');

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                requestAnimationFrame(() => {
                    content.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                    content.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                });
            }

            function closeAttributionModal() {
                const modal = document.getElementById('attributionModal');
                const content = document.getElementById('attributionModalContent');

                content.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                content.classList.add('opacity-0', 'scale-95', 'translate-y-4');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                    resetAttributionForm();
                }, 200);
            }

            function resetAttributionForm() {
                document.getElementById('attributionForm').reset();
                clearAttributionErrors();

                // Reset mode proforma
                toggleProformaMode('select');

                // Reset sélections
                document.getElementById('selectedPrestataire').classList.add('hidden');
                document.getElementById('selectedProforma').classList.add('hidden');

                // Reset calculs
                calculerMontants();
            }

            function clearAttributionErrors() {
                const errorDivs = document.querySelectorAll('[id^="error_"]');
                errorDivs.forEach(div => {
                    div.classList.add('hidden');
                    const span = div.querySelector('span');
                    if (span) span.textContent = '';
                    else div.textContent = '';
                });
            }

            // ==========================================
            // RECHERCHE PRESTATAIRE
            // ==========================================
            document.getElementById('searchPrestataire').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const select = document.getElementById('prestataire_id');
                const options = select.options;

                for (let i = 0; i < options.length; i++) {
                    const option = options[i];
                    const numero = (option.dataset.numero || '').toLowerCase();
                    const raison = (option.dataset.raison || '').toLowerCase();
                    const text = option.text.toLowerCase();

                    if (text.includes(searchTerm) || numero.includes(searchTerm) || raison.includes(searchTerm)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });

            document.getElementById('prestataire_id').addEventListener('change', function(e) {
                const selected = this.options[this.selectedIndex];
                if (selected && selected.value) {
                    document.getElementById('selectedPrestataireName').textContent = selected.text;
                    document.getElementById('selectedPrestataire').classList.remove('hidden');
                }
            });

            function clearPrestataireSelection() {
                document.getElementById('prestataire_id').value = '';
                document.getElementById('selectedPrestataire').classList.add('hidden');
                document.getElementById('searchPrestataire').value = '';
                // Réafficher toutes les options
                const options = document.getElementById('prestataire_id').options;
                for (let i = 0; i < options.length; i++) {
                    options[i].style.display = '';
                }
            }



            function clearProformaSelection() {
                document.getElementById('proforma_id').value = '';
                document.getElementById('selectedProforma').classList.add('hidden');
                document.getElementById('searchProforma').value = '';
                // Réafficher toutes les options
                const options = document.getElementById('proforma_id').options;
                for (let i = 0; i < options.length; i++) {
                    options[i].style.display = '';
                }
            }


            // ==========================================
            // SOUMISSION FORMULAIRE ATTRIBUTION
            // ==========================================
            document.getElementById('attributionForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submitAttributionBtn');
                const submitIcon = document.getElementById('submitAttributionIcon');
                const submitText = document.getElementById('submitAttributionText');

                submitBtn.disabled = true;
                submitIcon.className = 'fas fa-spinner fa-spin mr-2';
                submitText.textContent = 'Attribution en cours...';

                clearAttributionErrors();

                const formData = new FormData(this);
                formData.append('lot_id', lotId);

                // Ajouter le mode proforma
                formData.append('proforma_mode', 'create');

                fetch("{{ route('attributions.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            submitIcon.className = 'fas fa-check mr-2';
                            submitText.textContent = 'Attribution réussie !';
                            submitBtn.classList.remove('from-green-600', 'to-emerald-600');
                            submitBtn.classList.add('from-emerald-500', 'to-green-500');

                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    const errorDiv = document.getElementById(`error_${key}`);
                                    if (errorDiv) {
                                        const span = errorDiv.querySelector('span');
                                        if (span) span.textContent = data.errors[key][0];
                                        else errorDiv.textContent = data.errors[key][0];
                                        errorDiv.classList.remove('hidden');
                                    }
                                });
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                    })
                    .finally(() => {
                        if (!submitBtn.classList.contains('from-emerald-500')) {
                            submitBtn.disabled = false;
                            submitIcon.className = 'fas fa-check mr-2';
                            submitText.textContent = 'Attribuer le lot';
                        }
                    });
            });

            // ==========================================
            // AUTRES FONCTIONS
            // ==========================================
            function toggleMenu() {
                document.getElementById('actionMenu').classList.toggle('hidden');
            }

            document.addEventListener('click', function(e) {
                if (!e.target.closest('#menuBtn') && !e.target.closest('#actionMenu')) {
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });



            function viewStatistiques() {
                fetch("{{ route('api.lots-appels-offres.statistiques', [':appelOffre', ':id']) }}".replace(':appelOffre',
                        appelOffreId).replace(':id', lotId), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const stats = data.data;
                            let message = 'Statistiques du lot:\n\n';
                            message += `Numéro: ${stats.general.numero}\n`;
                            message += `Libellé: ${stats.general.libelle}\n`;
                            if (stats.general.duree_prevue_jours) {
                                message += `Durée prévue: ${stats.general.duree_prevue_jours} jour(s)\n`;
                            }
                            message += `Attribué: ${stats.general.est_attribue ? 'Oui' : 'Non'}\n`;
                            message += `Retiré: ${stats.general.est_retire ? 'Oui' : 'Non'}\n`;
                            alert(message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la récupération des statistiques');
                    });
            }

            function confirmDelete() {
                const message = `Êtes-vous sûr de vouloir supprimer le lot "{{ $lot->numero }}" ?`;
                document.getElementById('deleteMessage').textContent = message;
                document.getElementById('deleteModal').classList.remove('hidden');
            }


            /**
             * Gère l'activation/désactivation de l'exonération TVA
             */
            function toggleExonerationTVA(checkbox) {
                const tauxTvaInput = document.getElementById('new_taux_tva');

                if (checkbox.checked) {
                    // Exonération activée : TVA à 0 et champ grisé
                    tauxTvaInput.value = '0';
                    tauxTvaInput.readOnly = true;
                    tauxTvaInput.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                    tauxTvaInput.classList.remove('focus:border-emerald-500', 'focus:ring-4', 'focus:ring-emerald-500/10');
                } else {
                    // Exonération désactivée : TVA à 18% et champ actif
                    tauxTvaInput.value = '18';
                    tauxTvaInput.readOnly = false;
                    tauxTvaInput.classList.remove('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                    tauxTvaInput.classList.add('focus:border-emerald-500', 'focus:ring-4', 'focus:ring-emerald-500/10');
                }

                calculerMontants();

            }

            function executeDelete() {
                fetch("{{ route('api.lots-appels-offres.destroy', [':appelOffre', ':id']) }}".replace(':appelOffre',
                        appelOffreId).replace(':id', lotId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('lots.index') }}';
                        } else {
                            alert(data.message || 'Une erreur est survenue');
                            closeDeleteModal();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                        closeDeleteModal();
                    });
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Gestion modal retrait
            function openRetraitModal() {
                document.getElementById('retraitModal').classList.remove('hidden');
            }

            function closeRetraitModal() {
                document.getElementById('retraitModal').classList.add('hidden');
                document.getElementById('retraitForm').reset();
            }

            // Soumettre retrait
            document.getElementById('retraitForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submitRetraitBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Retrait en cours...';

                const formData = new FormData(this);

                fetch("{{ route('api.lots-appels-offres.retirer', [':appelOffre', ':id']) }}".replace(':appelOffre',
                        appelOffreId).replace(':id', lotId), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Une erreur est survenue');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });

            // Fermer modales avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                    closeAttributionModal();
                    closeRetraitModal();
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });

            // Initialiser les calculs au chargement
            document.addEventListener('DOMContentLoaded', function() {
                calculerMontants();
            });
        </script>




        <script>
            // ==========================================
            // FORMATAGE ET VALIDATION DES MONTANTS
            // Version complète et optimisée
            // ==========================================

            /**
             * Formate un nombre avec séparateur de milliers (espaces) et virgule pour les décimales
             */
            function formatWithSpaces(value, decimals = 2) {
                if (!value && value !== 0) return '';

                const numValue = parseFloat(value);
                if (isNaN(numValue)) return '';

                const fixed = numValue.toFixed(decimals);
                const parts = fixed.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

                return decimals > 0 ? parts.join(',') : parts[0];
            }

            /**
             * Parse un nombre formaté en valeur numérique
             */
            function parseFormattedNumber(value) {
                if (!value) return 0;
                return parseFloat(value.toString().replace(/\s/g, '').replace(',', '.')) || 0;
            }

            /**
             * Affiche un message d'erreur pour un champ
             */
            function showError(fieldId, message) {
                const errorDiv = document.getElementById(`error_${fieldId}`);
                if (errorDiv) {
                    errorDiv.querySelector('span').textContent = message;
                    errorDiv.classList.remove('hidden');
                }

                const input = document.getElementById(fieldId);
                if (input) {
                    input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/10');
                    input.classList.remove('border-gray-200', 'focus:border-emerald-500', 'focus:ring-emerald-500/10');
                }
            }

            /**
             * Cache le message d'erreur pour un champ
             */
            function hideError(fieldId) {
                const errorDiv = document.getElementById(`error_${fieldId}`);
                if (errorDiv) {
                    errorDiv.classList.add('hidden');
                }

                const input = document.getElementById(fieldId);
                if (input) {
                    input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/10');
                    input.classList.add('border-gray-200', 'focus:border-emerald-500', 'focus:ring-emerald-500/10');
                }
            }

            /**
             * Valide un champ numérique selon ses contraintes
             */
            function validateNumberField(fieldId) {
                const input = document.getElementById(fieldId);
                if (!input) return true;

                const value = parseFormattedNumber(input.value);
                const min = parseFloat(input.dataset.min || input.getAttribute('data-min'));
                const max = parseFloat(input.dataset.max || input.getAttribute('data-max'));

                let isValid = true;
                let errorMessage = '';


                if (!isNaN(max) && value > max) {
                    isValid = false;
                    errorMessage = `La valeur doit être inférieure ou égale à ${formatWithSpaces(max, 0)} FCFA`;
                }

                if (!isValid) {
                    showError(fieldId, errorMessage);
                    // Flash visuel
                    input.classList.add('animate-shake');
                    setTimeout(() => input.classList.remove('animate-shake'), 500);
                } else {
                    hideError(fieldId);
                }

                return isValid;
            }

            /**
             * Configure un champ input pour le formatage automatique
             */
            function setupNumberInput(inputId) {
                const input = document.getElementById(inputId);
                if (!input) return;

                // Changer le type en text si nécessaire
                if (input.type === 'number') {
                    input.type = 'text';
                    input.inputMode = 'decimal';
                }

                // Formatage en temps réel
                input.addEventListener('input', function(e) {
                    const cursorPosition = this.selectionStart;
                    const oldValue = this.value;
                    const oldLength = oldValue.length;

                    // Nettoyer et ne garder que les chiffres, virgules et points
                    let rawValue = oldValue.replace(/[^\d,.-]/g, '');

                    // Gérer le signe négatif
                    const isNegative = rawValue.startsWith('-');
                    rawValue = rawValue.replace(/-/g, '');
                    if (isNegative) rawValue = '-' + rawValue;

                    // Unifier les séparateurs décimaux
                    rawValue = rawValue.replace(/,/g, '.');

                    // Gérer plusieurs points décimaux
                    const dotCount = (rawValue.match(/\./g) || []).length;
                    if (dotCount > 1) {
                        const parts = rawValue.split('.');
                        rawValue = parts[0] + '.' + parts.slice(1).join('');
                    }

                    // Séparer partie entière et décimale
                    const parts = rawValue.split('.');
                    let integerPart = parts[0];
                    let decimalPart = parts[1];

                    // Formater la partie entière avec espaces
                    if (integerPart) {
                        const isNeg = integerPart.startsWith('-');
                        let absInteger = integerPart.replace('-', '');
                        absInteger = absInteger.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
                        integerPart = (isNeg ? '-' : '') + absInteger;
                    }

                    // Reconstruire la valeur formatée
                    let formatted = integerPart || '0';
                    if (decimalPart !== undefined) {
                        formatted += ',' + decimalPart.substring(0, 2);
                    } else if (oldValue.endsWith(',') || oldValue.endsWith('.')) {
                        formatted += ',';
                    }

                    this.value = formatted;

                    // Ajuster la position du curseur
                    try {
                        const newLength = this.value.length;
                        const diff = newLength - oldLength;
                        const newPosition = Math.min(Math.max(0, cursorPosition + diff), newLength);
                        this.setSelectionRange(newPosition, newPosition);
                    } catch (e) {
                        // Ignorer si setSelectionRange échoue
                    }

                    // Déclencher le calcul
                    calculerMontants();
                });

                // Validation et formatage au blur
                input.addEventListener('blur', function() {
                    if (this.value && !this.readOnly) {
                        const numValue = parseFormattedNumber(this.value);
                        this.value = formatWithSpaces(numValue, 2);

                        // Valider selon les contraintes
                        validateNumberField(this.id);

                        // Recalculer
                        calculerMontants();
                    }
                });

                // Formater la valeur initiale
                if (input.value) {
                    const numValue = parseFormattedNumber(input.value);
                    input.value = formatWithSpaces(numValue, 2);
                }
            }

            /**
             * Met à jour le détail du calcul (si le bloc existe)
             */
            function updateCalculDetail(montantHT, tauxTVA, montantTVA, tauxRemise, montantRemise, totalTTC) {
                const detailElements = {
                    ht: document.getElementById('detail_ht'),
                    tva_rate: document.getElementById('detail_tva_rate'),
                    tva: document.getElementById('detail_tva'),
                    remise_rate: document.getElementById('detail_remise_rate'),
                    remise: document.getElementById('detail_remise'),
                    total: document.getElementById('detail_total')
                };

                if (detailElements.ht) detailElements.ht.textContent = formatWithSpaces(montantHT, 0) + ' FCFA';
                if (detailElements.tva_rate) detailElements.tva_rate.textContent = tauxTVA.toFixed(2);
                if (detailElements.tva) detailElements.tva.textContent = formatWithSpaces(montantTVA, 0) + ' FCFA';
                if (detailElements.remise_rate) detailElements.remise_rate.textContent = tauxRemise.toFixed(2);
                if (detailElements.remise) detailElements.remise.textContent = formatWithSpaces(montantRemise, 0) + ' FCFA';
                if (detailElements.total) detailElements.total.textContent = formatWithSpaces(totalTTC, 0) + ' FCFA';
            }

            /**
             * Calcule automatiquement les montants
             */
            function calculerMontants() {
                // Récupérer les valeurs
                const montantRetenu = parseFormattedNumber(document.getElementById('new_montant_retenu')?.value || 0);
                const tauxTVA = parseFormattedNumber(document.getElementById('new_taux_tva')?.value || 0);
                const tauxRemise = parseFormattedNumber(document.getElementById('new_taux_remise')?.value || 0);

                // Calcul TVA
                const montantTVA = montantRetenu * (tauxTVA / 100);
                const taxeMontantInput = document.getElementById('new_taxe_montant');
                if (taxeMontantInput) {
                    taxeMontantInput.value = formatWithSpaces(montantTVA, 2);
                }

                // Calcul Remise
                const montantRemise = montantRetenu * (tauxRemise / 100);
                const remiseMontantInput = document.getElementById('new_remise_montant');
                if (remiseMontantInput) {
                    remiseMontantInput.value = formatWithSpaces(montantRemise, 2);
                }

                // Calcul Total TTC
                const totalTTC = montantRetenu + montantTVA - montantRemise;

                // Mettre à jour le champ caché
                const totalTTCInput = document.getElementById('new_total_ttc');
                if (totalTTCInput) {
                    totalTTCInput.value = totalTTC.toFixed(2);
                }

                // Mettre à jour l'affichage principal
                const displayTotalTTC = document.getElementById('displayTotalTTC');
                if (displayTotalTTC) {
                    displayTotalTTC.textContent = formatWithSpaces(totalTTC, 0);

                    // Animation du montant
                    displayTotalTTC.classList.add('scale-110');
                    setTimeout(() => displayTotalTTC.classList.remove('scale-110'), 200);
                }

                // Mettre à jour le détail (si présent)
                updateCalculDetail(montantRetenu, tauxTVA, montantTVA, tauxRemise, montantRemise, totalTTC);
            }

            // ==========================================
            // INITIALISATION
            // ==========================================
            document.addEventListener('DOMContentLoaded', function() {
                // Champs à formater
                const fieldsToFormat = [
                    'new_montant_retenu',
                    'new_taux_tva',
                    'new_taux_remise'
                ];

                fieldsToFormat.forEach(fieldId => setupNumberInput(fieldId));

                // Formater les champs readonly
                const readonlyFields = ['new_taxe_montant', 'new_remise_montant'];
                readonlyFields.forEach(fieldId => {
                    const input = document.getElementById(fieldId);
                    if (input && input.type === 'number') {
                        input.type = 'text';
                        input.inputMode = 'decimal';
                    }
                });

                // Calcul initial
                setTimeout(() => calculerMontants(), 100);

                // Ajouter l'animation shake au CSS si elle n'existe pas
                if (!document.getElementById('number-format-styles')) {
                    const style = document.createElement('style');
                    style.id = 'number-format-styles';
                    style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
            .animate-shake {
                animation: shake 0.3s ease-in-out;
            }
            #displayTotalTTC {
                transition: transform 0.2s ease;
            }
        `;
                    document.head.appendChild(style);
                }
            });

            // ==========================================
            // NETTOYAGE AVANT SOUMISSION
            // ==========================================
            const attributionForm = document.getElementById('attributionForm');
            if (attributionForm) {
                attributionForm.addEventListener('submit', function(e) {
                    // Valider tous les champs avant soumission
                    let isFormValid = true;

                    const fieldsToValidate = [
                        'new_montant_retenu',
                        'new_taux_tva',
                        'new_taux_remise'
                    ];

                    fieldsToValidate.forEach(fieldId => {
                        if (!validateNumberField(fieldId)) {
                            isFormValid = false;
                        }
                    });

                    if (!isFormValid) {
                        e.preventDefault();

                        // Scroller vers le premier champ en erreur
                        const firstError = document.querySelector('.border-red-500');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            firstError.focus();
                        }

                        return false;
                    }

                    // Nettoyer les valeurs pour la soumission
                    const fieldsToClean = [
                        'new_montant_retenu',
                        'new_taux_tva',
                        'new_taux_remise',
                        'new_taxe_montant',
                        'new_remise_montant'
                    ];

                    fieldsToClean.forEach(fieldId => {
                        const input = document.getElementById(fieldId);
                        if (input && input.value) {
                            const numValue = parseFormattedNumber(input.value);
                            input.value = numValue.toString();
                        }
                    });
                });
            }
        </script>



        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
            }

            /* Custom scrollbar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 3px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            /* Style pour les selects multi-lignes */
            select[size] {
                overflow-y: auto;
            }

            select[size] option {
                padding: 10px 12px;
                border-bottom: 1px solid #e5e7eb;
                cursor: pointer;
            }

            select[size] option:hover {
                background-color: #f3f4f6;
            }

            select[size] option:checked {
                background: linear-gradient(to right, #dcfce7, #d1fae5);
                color: #166534;
                font-weight: 600;
            }

            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush
@endsection
