@extends('layouts.main')
@section('title', 'Détails Lot - ' . $lot->numero)
@section('breadcrumb')
<a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
<a href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">{{ $lot->appelOffre->numero_appel_offre }}</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
<a href="{{ route('lots-appels-offres.index', [$lot->appelOffre->id_appel_offre]) }}" class="text-white/80 hover:text-white transition-colors">Lots</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
<span class="text-white font-medium">{{ $lot->numero }}</span>
@endsection

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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            <i class="fas fa-check-circle mr-1"></i> Actif
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                            <i class="fas fa-times-circle mr-1"></i> Inactif
                        </span>
                        @endif
                        @if ($lot->attribution_lot)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <i class="fas fa-user-check mr-1"></i> Attribué
                        </span>
                        @endif
                        @if ($lot->isRetire())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                            <i class="fas fa-ban mr-1"></i> Retiré
                        </span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1">{{ $lot->libelle }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2 flex-wrap">

                @if (!$lot->attribution_lot)
                <a href="{{ route('criteres-evaluations.index', [$lot->appel_offre_id, $lot->id_lot]) }}"
                    class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                    <i class="fas fa-user-check text-sm"></i>
                    <span class="text-sm font-medium">Critère d'évaluation</span>
                </a>
                @endif


                @if (!$lot->attribution_lot && !$lot->isRetire())
                <button onclick="openAttributionModal()"
                    class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                    <i class="fas fa-user-check text-sm"></i>
                    <span class="text-sm font-medium">Attribuer</span>
                </button>
                @endif

                {{-- @if ($lot->isAttribue() && !$lot->isRetire())
                        <button onclick="openRetraitModal()"
                            class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-ban text-sm"></i>
                            <span class="text-sm font-medium">Retirer</span>
                        </button>
                    @endif --}}

                @if (!$lot->attribution_lot)
                <button onclick="window.location.href='{{ route('lots-appels-offres.edit', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}'"
                    class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                    <i class="fas fa-edit text-sm"></i>
                    <span class="text-sm font-medium">Modifier</span>
                </button>
                @endif

                <!-- Menu dropdown -->
                <div class="relative">
                    <button onclick="toggleMenu()" id="menuBtn"
                        class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                    <div id="actionMenu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                        <div class="py-1">
                            {{-- <a href="{{ route('criteres-evaluations.index', [$lot->appel_offre_id, $lot->id_lot]) }}"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-history mr-2 text-blue-500"></i>
                            Critère d'évaluation
                            </a> --}}

                            <a href="{{ route('lots-appels-offres.historique', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-history mr-2 text-blue-500"></i>
                                Historique
                            </a>
                            <button onclick="viewStatistiques()"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
                                Statistiques
                            </button>
                            <button onclick="duplicate()"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-copy mr-2 text-indigo-500"></i>
                                Dupliquer
                            </button>
                            @if(!$lot->isAttribue())
                            <button onclick="confirmDelete()"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
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
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                {{ $lot->appelOffre->numero_appel_offre }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $lot->appelOffre->libelle_critere_appel_offre }}</p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-tag mr-1"></i>{{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
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
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                {{ $lot->numero }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Libellé</label>
                            <p class="text-gray-900 font-medium">{{ $lot->libelle }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($lot->description_critere)
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Description</label>
                        <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">
                            {{ $lot->description_critere }}
                        </p>
                    </div>
                    @endif

                    <!-- Spécifications techniques -->
                    @if($lot->specifications_techniques)
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Spécifications techniques</label>
                        <p class="text-gray-700 leading-relaxed bg-blue-50 p-4 rounded-lg whitespace-pre-wrap">
                            {{ $lot->specifications_techniques }}
                        </p>
                    </div>
                    @endif

                    <!-- Taux de pénalités -->
                    @if($lot->taux_penalites)
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Taux de pénalités</label>
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($lot->taux_penalites, 2) }}</span>
                            <span class="text-sm text-gray-500">% par jour de retard</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dates et délais -->
            @if($lot->date_debut_prevue || $lot->date_fin_prevue || $lot->date_attribution)
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
                        @if($lot->date_attribution)
                        <div class="bg-gradient-to-br from-green-50 to-white p-5 rounded-xl border border-green-100">
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
                        @if($lot->date_debut_prevue)
                        <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
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
                        @if($lot->date_fin_prevue)
                        <div class="bg-gradient-to-br from-orange-50 to-white p-5 rounded-xl border border-orange-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-600">Fin prévue</span>
                                <i class="fas fa-flag-checkered text-orange-500"></i>
                            </div>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $lot->date_fin_prevue->format('d/m/Y') }}
                            </p>
                            @if($lot->calculerDuree())
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
            @if($lot->attributionActive)
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
                                {{-- {{ dd($lot->attributionActive->prestataire) }} --}}
                            </h3>

                            @if($lot->attributionActive->proforma)
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                                <span>
                                    <i class="fas fa-file-invoice-dollar mr-1"></i>
                                    Proforma: {{ $lot->attributionActive->proforma->numero_proforma ?? 'N/A' }}
                                </span>
                            </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4 mt-4">
                                @if($lot->attributionActive->date_debut_reelle)
                                <div>
                                    <p class="text-xs text-gray-500">Début réel</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($lot->attributionActive->date_debut_reelle)->format('d/m/Y') }}
                                    </p>
                                </div>
                                @endif

                                @if($lot->attributionActive->date_fin_reelle)
                                <div>
                                    <p class="text-xs text-gray-500">Fin réelle</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($lot->attributionActive->date_fin_reelle)->format('d/m/Y') }}
                                    </p>
                                </div>
                                @endif

                                @if($lot->attributionActive->pourcentage_avancement)
                                <div>
                                    <p class="text-xs text-gray-500">Avancement</p>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full"
                                                style="width: {{ $lot->attributionActive->pourcentage_avancement  }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $lot->attributionActive->pourcentage_avancement }}%
                                        </span>
                                    </div>
                                </div>
                                @endif

                                @if($lot->attributionActive->jours_retard)
                                <div>
                                    <p class="text-xs text-gray-500">Retard</p>
                                    <p class="text-sm font-semibold text-red-600">
                                        {{ $lot->attributionActive->jours_retard }} jour(s)
                                    </p>
                                </div>
                                @endif
                            </div>

                            @if($lot->attributionActive->observations)
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Observations</p>
                                <p class="text-sm text-gray-700">{{ $lot->attributionActive->observations }}</p>
                            </div>
                            @endif
                        </div>

                        <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $lot->attributionActive->statut_attribution == 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $lot->attributionActive->statut_attribution == 1 ? 'Attribué' : 'Suspendu' }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Historique des attributions -->
            @if($lot->historiqueAttributions->count() > 1)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history text-purple-500 mr-2"></i>
                        Historique des attributions
                    </h2>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($lot->historiqueAttributions->take(5) as $attribution)
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">
                                    {{ $attribution->prestataire->nom_complet ?? 'N/A' }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $attribution->created_at->format('d/m/Y à H:i') }}
                                </p>
                                @if($attribution->trashed())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800 mt-2">
                                    Retiré
                                </span>
                                @endif
                            </div>
                            <span class="text-xs {{ $attribution->statut_attribution == 1 ? 'text-green-600' : 'text-gray-600' }} font-medium">
                                {{ $attribution->statut_attribution == 1 ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Informations de retrait -->
            @if($lot->isRetire())
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3 mt-1"></i>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-800 mb-2">Lot retiré</h3>
                        <div class="text-sm text-red-700 space-y-2">
                            <p><strong>Date de retrait:</strong> {{ $lot->date_retrait->format('d/m/Y') }}</p>
                            @if($lot->motif_retrait)
                            <p><strong>Motif:</strong></p>
                            <p class="bg-red-100 p-3 rounded mt-1">{{ $lot->motif_retrait }}</p>
                            @endif
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
                    <!-- Durée -->
                    @if($lot->calculerDuree())
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-transparent rounded-lg border-l-4 border-blue-500">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Durée prévue</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $lot->calculerDuree() }}</p>
                            <p class="text-xs text-gray-500">jour(s)</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-day text-blue-600"></i>
                        </div>
                    </div>
                    @endif

                    <!-- Critères d'évaluation -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-transparent rounded-lg border-l-4 border-purple-500">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Critères</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $lot->criteresEvaluation->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-list-check text-purple-600"></i>
                        </div>
                    </div>

                    <!-- Versions -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-transparent rounded-lg border-l-4 border-orange-500">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Versions</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $lot->versions->count() + 1 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-code-branch text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cog text-gray-500 mr-2"></i>
                    Informations système
                </h3>

                <div class="space-y-4 text-sm">
                    <!-- Créé par -->
                    @if ($lot->creator)
                    <div>
                        <p class="text-gray-600 font-medium mb-1">Créé par</p>
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

                    <!-- Version parent -->
                    @if($lot->parent_id)
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-gray-600 font-medium mb-1">Version parente</p>
                        <a href="{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->parent_id]) }}"
                            class="text-indigo-600 hover:text-indigo-800 text-xs flex items-center">
                            <i class="fas fa-link mr-1"></i>
                            Voir la version parente
                        </a>
                    </div>
                    @endif


                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-lg p-6 border border-indigo-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-indigo-500 mr-2"></i>
                    Actions rapides
                </h3>

                <div class="space-y-2">
                    @if(!$lot->attribution_lot && !$lot->isRetire())
                    <button onclick="openAttributionModal()"
                        class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-green-50 border border-green-200 rounded-lg transition-all duration-200 group">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-user-check text-green-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Attribuer le lot</span>
                    </button>
                    @endif

                    <a href="{{ route('lots-appels-offres.historique', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                        class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-blue-50 border border-blue-200 rounded-lg transition-all duration-200 group">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-history text-blue-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Voir l'historique</span>
                    </a>

                    <button onclick="duplicate()"
                        class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-purple-50 border border-purple-200 rounded-lg transition-all duration-200 group">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-copy text-purple-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Dupliquer le lot</span>
                    </button>

                    <button onclick="window.print()"
                        class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                            <i class="fas fa-print text-gray-600"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">Imprimer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Confirmation Suppression -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
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
                    <button onclick="executeDelete()"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Attribution -->
<div id="attributionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Attribuer le lot</h3>
                <button onclick="closeAttributionModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="attributionForm" class="p-6">
                @csrf
                <div class="space-y-5">
                    <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                        <p class="text-sm text-indigo-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Lot:</strong> {{ $lot->numero }} - {{ $lot->libelle }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Prestataire <span class="text-red-500">*</span>
                        </label>
                        <select name="prestataire_id" id="prestataire_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                            <option value="">Sélectionner un prestataire</option>
                            @if ($prestataires->count() > 0)
                            @foreach ($prestataires as $prestataire)
                            <option value="{{ $prestataire->id_prestataire }}">
                                ({{ $prestataire->numero_identification_prestataire }}) - {{ Str::limit($prestataire->raison_sociale_prestataire, 50) }}
                            </option>
                            @endforeach
                            @else
                            <option value="" disabled>Aucun prestataire disponible</option>

                            @endif
                        </select>
                        <div id="error_prestataire_id" class="hidden text-red-500 text-sm mt-1"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Proforma <span class="text-red-500">*</span>
                        </label>
                        <select name="proforma_id" id="proforma_id" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                            <option value="">Sélectionner une proforma</option>
                            @if( $proformas->count() > 0)
                            @foreach ( $proformas as $proforma)
                            <option value="{{ $proforma->id_proforma }}">
                                {{ $proforma->numero_proforma }} - {{ Str::limit($proforma->objet_proforma, 50) }}
                            </option>
                            @endforeach
                            @else
                            <option value="" disabled>Aucune proforma disponible</option>
                            @endif
                        </select>
                        <div id="error_proforma_id" class="hidden text-red-500 text-sm mt-1"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Date d'attribution
                        </label>
                        <input type="date" name="date_attribution" id="date_attribution"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeAttributionModal()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Annuler
                    </button>
                    <button type="submit" id="submitAttributionBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                        <i class="fas fa-check mr-2"></i>
                        <span id="submitAttributionText">Attribuer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Retrait -->
<div id="retraitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
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
                    <button type="submit" id="submitRetraitBtn"
                        class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                        <i class="fas fa-ban mr-2"></i>
                        <span id="submitRetraitText">Retirer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const lotId = '{{ $lot->id_lot }}';
    const appelOffreId = '{{ $lot->appel_offre_id }}';

    // Toggle menu
    function toggleMenu() {
        document.getElementById('actionMenu').classList.toggle('hidden');
    }

    // Fermer le menu en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#menuBtn') && !e.target.closest('#actionMenu')) {
            document.getElementById('actionMenu').classList.add('hidden');
        }
    });

    // Dupliquer
    function duplicate() {
        if (confirm('Voulez-vous dupliquer ce lot ?')) {
            fetch("{{route('api.lots-appels-offres.duplicate', [':appelOffre', ':id'])}}".replace(':appelOffre', appelOffreId).replace(':id', lotId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/lots/${data.data.id_lot}/edit`;
                    } else {
                        alert(data.message || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                });
        }
    }

    // Voir historique
    function viewHistorique() {
        fetch("{{route('api.lots-appels-offres.historique', [':appelOffre', ':id'])}}".replace(':appelOffre', appelOffreId).replace(':id', lotId), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Créer un affichage simple de l'historique
                    let message = 'Historique des versions:\n\n';
                    data.data.forEach((version, index) => {
                        message += `Version ${index + 1}:\n`;
                        message += `- Créée le: ${new Date(version.created_at).toLocaleString('fr-FR')}\n`;
                        if (version.creator) {
                            message += `- Par: ${version.creator.nom_complet}\n`;
                        }
                        message += '\n';
                    });
                    alert(message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la récupération de l\'historique');
            });
    }

    // Voir statistiques
    function viewStatistiques() {
        fetch("{{route('api.lots-appels-offres.statistiques', [':appelOffre', ':id'])}}".replace(':appelOffre', appelOffreId).replace(':id', lotId), {
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

    // Confirmer suppression
    function confirmDelete() {
        const message = `Êtes-vous sûr de vouloir supprimer le lot "{{ $lot->numero }}" ?`;
        document.getElementById('deleteMessage').textContent = message;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    // Exécuter suppression
    function executeDelete() {
        fetch("{{route('api.lots-appels-offres.destroy', [':appelOffre', ':id'])}}".replace(':appelOffre', appelOffreId).replace(':id', lotId), {
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
                    window.location.href = '{{ route('
                    lots.index ') }}';
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

    // Fermer modal suppression
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Gestion modal attribution
    function openAttributionModal() {
        document.getElementById('attributionModal').classList.remove('hidden');
        // TODO: Charger les prestataires et proformas via AJAX
    }

    function closeAttributionModal() {
        document.getElementById('attributionModal').classList.add('hidden');
        document.getElementById('attributionForm').reset();
        clearAttributionErrors();
    }

    function clearAttributionErrors() {
        const errorDivs = document.querySelectorAll('[id^="error_"]');
        errorDivs.forEach(div => {
            div.classList.add('hidden');
            div.textContent = '';
        });
    }

    // Soumettre attribution
    document.getElementById('attributionForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitAttributionBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Attribution en cours...';

        clearAttributionErrors();

        const formData = new FormData(this);

        fetch("{{route('api.lots-appels-offres.attribuer', [':appelOffre', ':id'])}}".replace(':appelOffre', appelOffreId).replace(':id', lotId), {
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
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorDiv = document.getElementById(`error_${key}`);
                            if (errorDiv) {
                                errorDiv.textContent = data.errors[key][0];
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    });

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

        fetch("{{route('api.lots-appels-offres.retirer', [':appelOffre', ':id'])}}".replace(':appelOffre', appelOffreId).replace(':id', lotId), {
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

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
@endsection
