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
                    <a href="{{ route('lots-appels-offres.index', [$lot->appelOffre->id_appel_offre]) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
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
                            {{-- {{ dd($lot->attributionActive) }} --}}

                            {{-- Statut d'attribution du lot --}}
                            @if ($lot->attribution_lot || $lot->attributionActive)
                                <div class="flex flex-wrap items-center gap-2">

                                    {{-- Statut: Attribué (actif, pas de suspension ni retrait) --}}
                                    @if (!$lot->attributionActive->date_suspension && !$lot->attributionActive->date_retrait)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm">
                                            <i class="fas fa-check-circle mr-1.5 text-emerald-500"></i>
                                            Attribué
                                        </span>

                                    {{-- Statut: Retiré (pas de suspension mais date de retrait présente) --}}
                                    @elseif (!$lot->attributionActive->date_suspension && $lot->attributionActive->date_retrait)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                            <i class="fas fa-times-circle mr-1.5 text-red-500"></i>
                                            Retiré
                                        </span>

                                    {{-- Statut: Suspendu --}}
                                    @elseif ($lot->attributionActive->date_suspension)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200 shadow-sm">
                                            <i class="fas fa-pause-circle mr-1.5 text-amber-500"></i>
                                            Suspendu
                                        </span>
                                    @endif

                                    {{-- Bouton Détails attribution --}}
                                    <a href="{{ route('attributions.show', $lot->attributionActive) }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 shadow-sm">
                                        <i class="fas fa-info-circle mr-1.5"></i>
                                        Détails attribution
                                    </a>
                                </div>
                            @else
                                {{-- Lot non attribué --}}
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    <i class="fas fa-user-slash mr-1.5 text-gray-400"></i>
                                    Non attribué
                                </span>
                            @endif

                        </div>
                        <p class="text-gray-600 mt-1">{{ $lot->libelle }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 flex-wrap">

                {{-- Boutons d'action pour les lots non attribués --}}
                <div class="flex flex-wrap items-center gap-3">

                    {{-- {{ dd($lot->criteresEvaluation->count()) }} --}}
                    {{-- Bouton Critère d'évaluation --}}
                    @if (!$lot->attribution_lot)
                        <a href="{{ route('criteres-evaluations.index', [$lot->appel_offre_id, $lot->id_lot]) }}"
                            class="px-4 py-2.5 bg-white border border-purple-300 text-purple-600 hover:bg-purple-50 hover:border-purple-400 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm group">
                            <i class="fas fa-clipboard-check text-sm group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium">Critères d'évaluation</span>
                        </a>
                    @endif

                    {{-- Bouton Attribuer --}}
                    @if (!$lot->attribution_lot && !$lot->isRetire() && $lot->criteresEvaluation->count() > 0 && $lot->criteresEvaluation->sum('note_reference_critere_evaluation') == 100)
                        <button onclick="openAttributionModal()"
                            class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-500 text-white hover:from-emerald-600 hover:to-green-600 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md group">
                            <i class="fas fa-user-plus text-sm group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium">Attribuer</span>
                        </button>
                    @endif

                </div>

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
                                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $lot->attributionActive->pourcentage_avancement }}%"></div>
                                                    </div>
                                                    <span class="text-sm font-semibold text-gray-900">{{ $lot->attributionActive->pourcentage_avancement }}%</span>
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
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-transparent rounded-lg border-l-4 border-purple-500">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Version</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $lot->version ?? 1 }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-code-branch text-purple-600"></i>
                            </div>
                        </div>

                        <!-- Durée prévue -->
                        @if($lot->calculerDuree())
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-transparent rounded-lg border-l-4 border-blue-500">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Durée prévue</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $lot->calculerDuree() }} <span class="text-sm font-normal">jour(s)</span></p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-day text-blue-600"></i>
                                </div>
                            </div>
                        @endif

                        <!-- Taux pénalités -->
                        @if($lot->taux_penalites)
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-transparent rounded-lg border-l-4 border-orange-500">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Taux pénalités</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($lot->taux_penalites, 2) }}%</p>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-percentage text-orange-600"></i>
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
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-hidden" onclick="if(event.target === this) closeDeleteModal()">
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
                            <button onclick="executeDelete()"
                                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL ATTRIBUTION - AMÉLIORÉ -->
    <!-- ========================================== -->
    <div id="attributionModal" class="hidden fixed inset-0 z-50 overflow-hidden" aria-labelledby="attribution-modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300" onclick="closeAttributionModal()"></div>

        <!-- Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">

                <!-- Contenu du modal -->
                <div id="attributionModalContent" class="relative w-full max-w-3xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4">

                    <!-- Header -->
                    <div class="relative bg-gradient-to-r from-green-600 to-emerald-600 rounded-t-2xl px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg">
                                    <i class="fas fa-user-check text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 id="attribution-modal-title" class="text-xl font-bold text-white">Attribuer le lot</h3>
                                    <p class="text-green-100 text-sm">Associer un prestataire et une proforma</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeAttributionModal()" class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 transition-colors duration-200 group">
                                <i class="fas fa-times text-white group-hover:rotate-90 transition-transform duration-200"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Corps du formulaire -->
                    <form id="attributionForm">
                        @csrf
                        <div class="px-6 py-6 max-h-[calc(100vh-280px)] overflow-y-auto custom-scrollbar">

                            <!-- Info Lot -->
                            <div class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-indigo-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-indigo-800">{{ $lot->numero }} - {{ Str::limit($lot->libelle, 50) }}</p>
                                        <p class="text-xs text-indigo-600 mt-0.5">AO: {{ $lot->appelOffre->numero_appel_offre }}</p>
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
                                        <input type="text"
                                            id="searchPrestataire"
                                            class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all duration-200 text-gray-800 placeholder-gray-400"
                                            placeholder="Rechercher un prestataire par nom ou numéro..."
                                            autocomplete="off">
                                    </div>

                                    <!-- Liste des prestataires -->
                                    <div id="prestataireListContainer" class="relative">
                                        <select name="prestataire_id"
                                            id="prestataire_id"
                                            required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all duration-200 text-gray-800 appearance-none bg-white cursor-pointer"
                                            size="4">
                                            @forelse ($prestataires as $prestataire)
                                                <option value="{{ $prestataire->id_prestataire }}"
                                                    data-numero="{{ $prestataire->numero_identification_prestataire }}"
                                                    data-raison="{{ $prestataire->raison_sociale_prestataire }}">
                                                    ({{ $prestataire->numero_identification_prestataire }}) - {{ $prestataire->raison_sociale_prestataire }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Aucun prestataire disponible</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- Prestataire sélectionné -->
                                    <div id="selectedPrestataire" class="hidden p-3 bg-green-50 border border-green-200 rounded-xl">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-check-circle text-green-500"></i>
                                                <span id="selectedPrestataireName" class="text-sm font-medium text-green-800"></span>
                                            </div>
                                            <button type="button" onclick="clearPrestataireSelection()" class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div id="error_prestataire_id" class="hidden mt-2 text-red-500 text-sm flex items-center">
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

                                    <!-- Toggle choix proforma -->
                                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="radio" name="proforma_mode" value="select" checked
                                                class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                                                onchange="toggleProformaMode('select')">
                                            <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-green-600 transition-colors">
                                                <i class="fas fa-list mr-1"></i> Sélectionner une proforma existante
                                            </span>
                                        </label>
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="radio" name="proforma_mode" value="create"
                                                class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                                                onchange="toggleProformaMode('create')">
                                            <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-green-600 transition-colors">
                                                <i class="fas fa-plus mr-1"></i> Créer une nouvelle proforma
                                            </span>
                                        </label>
                                    </div>

                                    <!-- ================================ -->
                                    <!-- MODE SELECTION PROFORMA -->
                                    <!-- ================================ -->
                                    <div id="proformaSelectMode" class="space-y-3">
                                        <!-- Champ de recherche proforma -->
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <i class="fas fa-search text-gray-400"></i>
                                            </div>
                                            <input type="text"
                                                id="searchProforma"
                                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 text-gray-800 placeholder-gray-400"
                                                placeholder="Rechercher une proforma par numéro..."
                                                autocomplete="off">
                                        </div>

                                        <!-- Liste des proformas -->
                                        <select name="proforma_id"
                                            id="proforma_id"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 text-gray-800 appearance-none bg-white cursor-pointer"
                                            size="3">
                                            @forelse ($proformas as $proforma)
                                                <option value="{{ $proforma->id_proforma }}"
                                                    data-numero="{{ $proforma->numero_proforma }}"
                                                    data-montant="{{ $proforma->montant_retenu_proforma }}">
                                                    {{ $proforma->numero_proforma }} - {{ number_format($proforma->montant_retenu_proforma, 0, ',', ' ') }} FCFA
                                                </option>
                                            @empty
                                                <option value="" disabled>Aucune proforma disponible</option>
                                            @endforelse
                                        </select>

                                        <!-- Proforma sélectionnée -->
                                        <div id="selectedProforma" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-check-circle text-blue-500"></i>
                                                    <span id="selectedProformaName" class="text-sm font-medium text-blue-800"></span>
                                                </div>
                                                <button type="button" onclick="clearProformaSelection()" class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="error_proforma_id" class="hidden mt-2 text-red-500 text-sm flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <span></span>
                                        </div>
                                    </div>

                                    <!-- ================================ -->
                                    <!-- MODE CREATION PROFORMA (ACCORDEON) -->
                                    <!-- ================================ -->
                                    <div id="proformaCreateMode" class="hidden">
                                        <div class="border-2 border-dashed border-blue-300 rounded-xl overflow-hidden bg-gradient-to-br from-blue-50/50 to-white">
                                            <!-- Header accordéon -->
                                            <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-blue-50 border-b border-blue-200">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-file-medical text-blue-600"></i>
                                                    <span class="font-semibold text-blue-800">Informations de la nouvelle proforma</span>
                                                </div>
                                                <p class="text-xs text-blue-600 mt-1">Tous les champs marqués * sont obligatoires</p>
                                            </div>

                                            <!-- Contenu accordéon -->
                                            <div class="p-5 space-y-5">

                                                <!-- Dates de la proforma -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                            <i class="fas fa-calendar text-blue-500 mr-2 text-xs"></i>
                                                            Date proforma <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <input type="date"
                                                            name="new_date_proforma"
                                                            id="new_date_proforma"
                                                            value="{{ date('Y-m-d') }}"
                                                            class="proforma-required w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                                        <div id="error_new_date_proforma" class="hidden mt-1 text-red-500 text-xs"></div>
                                                    </div>

                                                    <div>
                                                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                            <i class="fas fa-calendar-check text-green-500 mr-2 text-xs"></i>
                                                            Date début validée <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <input type="date"
                                                            name="new_date_debut_validee"
                                                            id="new_date_debut_validee"
                                                            class="proforma-required w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                                        <div id="error_new_date_debut_validee" class="hidden mt-1 text-red-500 text-xs"></div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                            <i class="fas fa-redo text-orange-500 mr-2 text-xs"></i>
                                                            Date redémarrage <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <input type="date"
                                                            name="new_date_redemarrage"
                                                            id="new_date_redemarrage"
                                                            class="proforma-required w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                                        <div id="error_new_date_redemarrage" class="hidden mt-1 text-red-500 text-xs"></div>
                                                    </div>

                                                    <div>
                                                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                            <i class="fas fa-calendar-times text-red-500 mr-2 text-xs"></i>
                                                            Date fin validée <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <input type="date"
                                                            name="new_date_fin_validee"
                                                            id="new_date_fin_validee"
                                                            class="proforma-required w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                                        <div id="error_new_date_fin_validee" class="hidden mt-1 text-red-500 text-xs"></div>
                                                    </div>
                                                </div>

                                                <!-- Montants -->
                                                <div class="p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-200">
                                                    <h4 class="text-sm font-bold text-emerald-800 mb-4 flex items-center">
                                                        <i class="fas fa-coins mr-2"></i>
                                                        Montants et calculs
                                                    </h4>
{{-- {{ dd($lot->appelOffre->caracteristiqueActive, $lot->appelOffre) }} --}}
                                                    <div class="space-y-4">
                                                        <!-- Montant retenu HT -->
                                                        <div>
                                                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                Montant retenu (HT) <span class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <div class="relative">
                                                                <input type="number"
                                                                    name="new_montant_retenu"
                                                                    id="new_montant_retenu"
                                                                    min="0"
                                                                    max=""
                                                                    step="0.01"
                                                                    class="proforma-required w-full px-4 py-2.5 pr-16 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                                                                    placeholder="0.00"
                                                                    oninput="calculerMontants()">
                                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                                            </div>
                                                            <div id="error_new_montant_retenu" class="hidden mt-1 text-red-500 text-xs"></div>
                                                        </div>

                                                        <!-- TVA -->
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Taux TVA <span class="text-red-500 ml-1">*</span>
                                                                </label>
                                                                <div class="relative">
                                                                    <input type="number"
                                                                        name="new_taux_tva"
                                                                        id="new_taux_tva"
                                                                        min="0"
                                                                        max="100"
                                                                        step="0.01"
                                                                        value="18"
                                                                        class="proforma-required w-full px-4 py-2.5 pr-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                                                                        oninput="calculerMontants()">
                                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Montant TVA
                                                                    <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full">Auto</span>
                                                                </label>
                                                                <div class="relative">
                                                                    <input type="number"
                                                                        name="new_taxe_montant"
                                                                        id="new_taxe_montant"
                                                                        readonly
                                                                        class="w-full px-4 py-2.5 pr-16 border-2 border-gray-100 rounded-xl bg-gray-50 text-gray-700 font-medium"
                                                                        placeholder="0.00">
                                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Remise -->
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Taux remise
                                                                </label>
                                                                <div class="relative">
                                                                    <input type="number"
                                                                        name="new_taux_remise"
                                                                        id="new_taux_remise"
                                                                        min="0"
                                                                        max="100"
                                                                        step="0.01"
                                                                        value="0"
                                                                        class="w-full px-4 py-2.5 pr-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                                                                        oninput="calculerMontants()">
                                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                    Montant remise
                                                                    <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full">Auto</span>
                                                                </label>
                                                                <div class="relative">
                                                                    <input type="number"
                                                                        name="new_remise_montant"
                                                                        id="new_remise_montant"
                                                                        readonly
                                                                        class="w-full px-4 py-2.5 pr-16 border-2 border-gray-100 rounded-xl bg-gray-50 text-gray-700 font-medium"
                                                                        placeholder="0.00">
                                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Pénalités -->
                                                        <div>
                                                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                                Pénalités
                                                            </label>
                                                            <div class="relative">
                                                                <input type="number"
                                                                    name="new_penalites"
                                                                    id="new_penalites"
                                                                    min="0"
                                                                    step="0.01"
                                                                    value="0"
                                                                    class="w-full px-4 py-2.5 pr-16 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200"
                                                                    placeholder="0.00"
                                                                    oninput="calculerMontants()">
                                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                                            </div>
                                                        </div>

                                                        <!-- Total TTC -->
                                                        <div class="pt-4 border-t-2 border-emerald-200">
                                                            <div class="flex items-center justify-between p-4 bg-emerald-100 rounded-xl">
                                                                <span class="font-bold text-emerald-800">TOTAL TTC</span>
                                                                <div class="flex items-center space-x-2">
                                                                    <span id="displayTotalTTC" class="text-2xl font-bold text-emerald-700">0</span>
                                                                    <span class="text-emerald-600 font-medium">FCFA</span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="new_total_ttc" id="new_total_ttc" value="0">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modalités de paiement -->
                                                <div>
                                                    <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-credit-card text-purple-500 mr-2 text-xs"></i>
                                                        Modalités de paiement <span class="text-red-500 ml-1">*</span>
                                                    </label>
                                                    <textarea name="new_modalite_paiement"
                                                        id="new_modalite_paiement"
                                                        rows="2"
                                                        class="proforma-required w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 resize-none"
                                                        placeholder="Ex: 30% à la commande, 70% à la livraison"></textarea>
                                                    <div id="error_new_modalite_paiement" class="hidden mt-1 text-red-500 text-xs"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ================================ -->
                                <!-- DATE D'ATTRIBUTION -->
                                <!-- ================================ -->
                                <div>
                                    <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-day text-indigo-500 mr-2 text-xs"></i>
                                        Date d'attribution
                                    </label>
                                    <input type="date"
                                        name="date_attribution"
                                        id="date_attribution"
                                        value="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all duration-200 text-gray-800">
                                </div>

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
    <div id="retraitModal" class="hidden fixed inset-0 z-50 overflow-hidden" onclick="if(event.target === this) closeRetraitModal()">
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
    </div>

    @push('scripts')
        <script>
            const lotId = '{{ $lot->id_lot }}';
            const appelOffreId = '{{ $lot->appel_offre_id }}';

            // ==========================================
            // DONNÉES POUR LA RECHERCHE
            // ==========================================
            const prestatairesData = @json($prestataires->map(function($p) {
                return [
                    'id' => $p->id_prestataire,
                    'numero' => $p->numero_identification_prestataire,
                    'raison' => $p->raison_sociale_prestataire
                ];
            }));

            const proformasData = @json($proformas->map(function($p) {
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
                document.querySelector('input[name="proforma_mode"][value="select"]').checked = true;
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

            // ==========================================
            // RECHERCHE PROFORMA
            // ==========================================
            document.getElementById('searchProforma').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const select = document.getElementById('proforma_id');
                const options = select.options;

                for (let i = 0; i < options.length; i++) {
                    const option = options[i];
                    const numero = (option.dataset.numero || '').toLowerCase();
                    const text = option.text.toLowerCase();

                    if (text.includes(searchTerm) || numero.includes(searchTerm)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });

            document.getElementById('proforma_id').addEventListener('change', function(e) {
                const selected = this.options[this.selectedIndex];
                if (selected && selected.value) {
                    document.getElementById('selectedProformaName').textContent = selected.text;
                    document.getElementById('selectedProforma').classList.remove('hidden');
                }
            });

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
            // TOGGLE MODE PROFORMA
            // ==========================================
            function toggleProformaMode(mode) {
                const selectMode = document.getElementById('proformaSelectMode');
                const createMode = document.getElementById('proformaCreateMode');
                const proformaIdField = document.getElementById('proforma_id');
                const proformaRequiredFields = document.querySelectorAll('.proforma-required');

                if (mode === 'select') {
                    selectMode.classList.remove('hidden');
                    createMode.classList.add('hidden');
                    proformaIdField.required = true;

                    // Retirer required des champs de création
                    proformaRequiredFields.forEach(field => {
                        field.required = false;
                    });
                } else {
                    selectMode.classList.add('hidden');
                    createMode.classList.remove('hidden');
                    proformaIdField.required = false;

                    // Ajouter required aux champs de création
                    proformaRequiredFields.forEach(field => {
                        field.required = true;
                    });
                }
            }

            // ==========================================
            // CALCULS AUTOMATIQUES
            // ==========================================
            function calculerMontants() {
                const montantRetenu = parseFloat(document.getElementById('new_montant_retenu').value) || 0;
                const tauxTVA = parseFloat(document.getElementById('new_taux_tva').value) || 0;
                const tauxRemise = parseFloat(document.getElementById('new_taux_remise').value) || 0;
                const penalites = parseFloat(document.getElementById('new_penalites').value) || 0;

                // Calcul TVA
                const montantTVA = montantRetenu * (tauxTVA / 100);
                document.getElementById('new_taxe_montant').value = montantTVA.toFixed(2);

                // Calcul Remise
                const montantRemise = montantRetenu * (tauxRemise / 100);
                document.getElementById('new_remise_montant').value = montantRemise.toFixed(2);

                // Calcul Total TTC
                const totalTTC = montantRetenu + montantTVA - montantRemise - penalites;
                document.getElementById('new_total_ttc').value = totalTTC.toFixed(2);
                document.getElementById('displayTotalTTC').textContent = new Intl.NumberFormat('fr-FR').format(totalTTC.toFixed(0));
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
                const proformaMode = document.querySelector('input[name="proforma_mode"]:checked').value;
                formData.append('proforma_mode', proformaMode);

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

            function confirmDelete() {
                const message = `Êtes-vous sûr de vouloir supprimer le lot "{{ $lot->numero }}" ?`;
                document.getElementById('deleteMessage').textContent = message;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

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

            // Initialiser les calculs au chargement
            document.addEventListener('DOMContentLoaded', function() {
                calculerMontants();
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
