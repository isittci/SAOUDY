@extends('layouts.main')
@section('title', 'Détails AO - ' . $appelOffre->numero_appel_offre)
@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium"
        title="{{ $appelOffre->libelle_critere_appel_offre }}">{{ \Illuminate\Support\Str::limit($appelOffre->libelle_critere_appel_offre, 50) }}</span>
@endsection

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('appels-offres.index') }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $appelOffre->numero_appel_offre }}</h1>
                            @if ($appelOffre->statut_evaluation_critere_appel_offre)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Actif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inactif
                                </span>
                            @endif
                            @if ($appelOffre->isCloture())
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-lock mr-1"></i> Clôturé
                                </span>
                            @elseif($appelOffre->isEnCours())
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-clock mr-1"></i> En cours
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-1">{{ $appelOffre->libelle_critere_appel_offre }}</p>
                    </div>
                </div>

                @canany(['appels_offres.update', 'caracteristiques_appels_offres.read', 'appels_offres.update',
                    'appels_offres.delete'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap">
                        @can('appels_offres.update')
                            @if (!$appelOffre->date_publication_critere_appel_offre)
                                <button onclick="publier()"
                                    class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                    <span class="text-sm font-medium">Publier</span>
                                </button>
                            @endif
                        @endcan


                        @can('caracteristiques_appels_offres.read')
                            <a href="{{ route('caracteristiques-appels-offres.index', [$appelOffre->id_appel_offre]) }}"
                                class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-list-check text-sm"></i>
                                <span class="text-sm font-medium">Caractéristiques</span>
                            </a>
                        @endcan

                        @can('appels_offres.update')
                            @if ($appelOffre->isEnCours())
                                <button onclick="cloturer()"
                                    class="px-4 py-2.5 bg-white border border-yellow-300 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-lock text-sm"></i>
                                    <span class="text-sm font-medium">Clôturer</span>
                                </button>
                            @endif

                            @if (!$appelOffre->isCloture() && $appelOffre->statut_evaluation_critere_appel_offre)
                                <button
                                    onclick="window.location.href='{{ route('appels-offres.edit', $appelOffre->id_appel_offre) }}'"
                                    class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-edit text-sm"></i>
                                    <span class="text-sm font-medium">Modifier</span>
                                </button>
                            @endif

                            <button
                                onclick="toggleStatus({{ $appelOffre->statut_evaluation_critere_appel_offre ? 'true' : 'false' }})"
                                class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-power-off text-sm"></i>
                                <span
                                    class="text-sm font-medium">{{ $appelOffre->statut_evaluation_critere_appel_offre ? 'Désactiver' : 'Activer' }}</span>
                            </button>
                        @endcan

                        @can('appels_offres.delete')
                            <!-- Menu dropdown -->
                            <div class="relative">
                                <button onclick="toggleMenu()" id="menuBtn"
                                    class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                                <div id="actionMenu"
                                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                                    <div class="py-1">
                                        <button onclick="confirmDelete()"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                            <i class="fas fa-trash mr-2"></i>
                                            Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endcan
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
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                            Informations générales
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Type et Numéro -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Type</label>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-blue-100 text-blue-700">
                                        {{ $appelOffre->typeAppelOffre->code_type_appel_offre }}
                                    </span>
                                    <span
                                        class="text-sm text-gray-700">{{ $appelOffre->typeAppelOffre->libelle_type_appel_offre }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Numéro</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                    {{ $appelOffre->numero_appel_offre }}
                                </span>
                            </div>
                        </div>

                        <!-- Libellé -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Libellé</label>
                            <p class="text-gray-900 font-medium">{{ $appelOffre->libelle_critere_appel_offre }}</p>
                        </div>

                        <!-- Montant Retenu -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Montant Retenu</label>
                            <div class="flex items-center space-x-3">
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ number_format($appelOffre->montant_global_appel_offre, 0, ',', ' ') }}
                                </p>
                                <span class="text-sm text-gray-500">FCFA</span>
                            </div>
                        </div>

                        <!-- Objet -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Objet</label>
                            <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg">
                                {{ $appelOffre->objet_critere_appel_offre }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Description détaillée</label>
                            <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">
                                {{ $appelOffre->description_critere_critere_appel_offre }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dates et délais -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                            Dates et Délais
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Date de publication -->
                            <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Publication</span>
                                    <i class="fas fa-paper-plane text-blue-500"></i>
                                </div>
                                @if ($appelOffre->date_publication_critere_appel_offre)
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $appelOffre->date_publication_critere_appel_offre->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $appelOffre->date_publication_critere_appel_offre->diffForHumans() }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">Non publié</p>
                                @endif
                            </div>

                            <!-- Date limite -->
                            <div class="bg-gradient-to-br from-orange-50 to-white p-5 rounded-xl border border-orange-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Limite de dépôt</span>
                                    <i class="fas fa-clock text-orange-500"></i>
                                </div>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $appelOffre->date_limite_depot_critere_appel_offre->format('d/m/Y') }}
                                </p>
                                @if ($appelOffre->joursRestants() > 0)
                                    <p class="text-xs text-orange-600 font-semibold mt-1">
                                        {{ $appelOffre->joursRestants() }} jour(s) restant(s)
                                    </p>
                                @else
                                    <p class="text-xs text-red-600 font-semibold mt-1">
                                        Clôturé
                                    </p>
                                @endif
                            </div>

                            <!-- Date d'ouverture -->
                            <div class="bg-gradient-to-br from-green-50 to-white p-5 rounded-xl border border-green-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Ouverture plis</span>
                                    <i class="fas fa-folder-open text-green-500"></i>
                                </div>
                                @if ($appelOffre->date_ouverture_plis_critere_appel_offre)
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $appelOffre->date_ouverture_plis_critere_appel_offre->format('d/m/Y') }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">Non définie</p>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>


                <!-- Planning et Dates -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                            Planning et Dates
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                            <!-- Date de démarrage -->
                            <div class="bg-gradient-to-br from-green-50 to-white p-4 rounded-lg border border-green-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-play text-green-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-600 mb-1">Date de démarrage</p>
                                        @if ($appelOffre->caracteristiqueActive?->date_demarrage_prevue_caracteristique_appel_offre)
                                            <p class="text-lg font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($appelOffre->caracteristiqueActive->date_demarrage_prevue_caracteristique_appel_offre)->format('d/m/Y') }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400">Non définie</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Durée estimée -->
                            <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-lg border border-blue-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-hourglass-half text-blue-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-600 mb-1">Durée estimée</p>
                                        @if ($appelOffre->caracteristiqueActive?->duree_estimee_jours_caracteristique_appel_offre)
                                            <p class="text-lg font-bold text-gray-900">
                                                {{ number_format($appelOffre->caracteristiqueActive->duree_estimee_jours_caracteristique_appel_offre, 0, ',', ' ') }}
                                                jours
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400">Non définie</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Date de livraison -->
                            <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-lg border border-purple-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-flag-checkered text-purple-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-600 mb-1">Date de livraison</p>
                                        @if ($appelOffre->caracteristiqueActive?->date_livraison_previsionnelle_caracteristique_appel_offre)
                                            <p class="text-lg font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($appelOffre->caracteristiqueActive->date_livraison_previsionnelle_caracteristique_appel_offre)->format('d/m/Y') }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400">Non définie</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Lieu d'exécution -->
                            <div class="bg-gradient-to-br from-orange-50 to-white p-4 rounded-lg border border-orange-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-orange-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-600 mb-1">Lieu d'exécution</p>
                                        @if ($appelOffre->caracteristiqueActive?->lieu_execution_caracteristique_appel_offre)
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $appelOffre->caracteristiqueActive->lieu_execution_caracteristique_appel_offre }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400">Non spécifié</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Critères et Conditions -->
                @if ($appelOffre->conditions_participation_critere_appel_offre || $appelOffre->criteres_selection_critere_appel_offre)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-list-check text-purple-500 mr-2"></i>
                                Critères et Conditions
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            @if ($appelOffre->conditions_participation_critere_appel_offre)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">
                                        <i class="fas fa-user-check text-purple-500 mr-1"></i>
                                        Conditions de participation
                                    </label>
                                    <p
                                        class="text-gray-700 leading-relaxed bg-purple-50 p-4 rounded-lg whitespace-pre-wrap">
                                        {{ $appelOffre->conditions_participation_critere_appel_offre }}
                                    </p>
                                </div>
                            @endif

                            @if ($appelOffre->criteres_selection_critere_appel_offre)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">
                                        <i class="fas fa-star text-purple-500 mr-1"></i>
                                        Critères de sélection
                                    </label>
                                    <p
                                        class="text-gray-700 leading-relaxed bg-purple-50 p-4 rounded-lg whitespace-pre-wrap">
                                        {{ $appelOffre->criteres_selection_critere_appel_offre }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif


                @canany(['lots.read', 'lots.create', 'lots.update', 'lots.view-details'])
                    @if ($appelOffre->caracteristiqueActive)
                        <!-- Lots associés -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-boxes text-orange-500 mr-2"></i>
                                        Lots associés
                                        <span
                                            class="ml-2 px-2.5 py-1 bg-orange-100 text-orange-800 text-sm font-semibold rounded-full">
                                            {{ $appelOffre->lots_count }}
                                        </span>
                                    </h2>

                                    <div class="flex items-center gap-2">

                                        @can('lots.read')
                                            {{-- Bouton pour voir tous les lots --}}
                                            <a href="{{ route('lots-appels-offres.index', $appelOffre->id_appel_offre) }}"
                                                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all text-sm font-medium border border-gray-300 hover:border-gray-400">
                                                <i class="fas fa-list-ul mr-2"></i>
                                                Voir tous
                                            </a>
                                        @endcan

                                        @can('lots.create')
                                            {{-- Bouton pour ajouter un lot (uniquement si non clôturé) --}}
                                            @if ($appelOffre->caracteristiqueActive)
                                                @if (!$appelOffre->isCloture())
                                                    <button onclick="openCreateLotModal()"
                                                        class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all text-sm font-medium shadow-md hover:shadow-lg">
                                                        <i class="fas fa-plus-circle mr-2"></i>
                                                        Ajouter un lot
                                                    </button>
                                                @endif
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>


                            <div class="p-6">
                                @if ($appelOffre->lots->count() > 0)
                                    {{-- <div class="space-y-3">
                                        @foreach ($appelOffre->lots as $lot)
                                            <div
                                                class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 border border-gray-200">
                                                <div class="flex-1">
                                                    @php
                                                        $allPaiements = $lot->attributionActive?->proforma?->facture?->paiements ?? null;
                                                        $proforma = $lot->attributionActive?->proforma?->facture ?? null;

                                                        $sommesReferencesCriteresEvaluations = $lot->criteresEvaluation->sum('note_reference_critere_evaluation');
                                                        $sommesNotesEvaluations = $lot->criteresEvaluation->flatMap->evaluations->sum('resultat_evaluation');

                                                        $paiementTermine = $allPaiements ? $allPaiements->sum('montant_net_paye_paiement') == $proforma->montant_facture : false;
                                                        $evaluationTerminee = $sommesReferencesCriteresEvaluations > 0 && $sommesNotesEvaluations > 0 ? $sommesReferencesCriteresEvaluations == $sommesNotesEvaluations : false;

                                                        // dd($sommesReferencesCriteresEvaluations, $sommesNotesEvaluations, $toutSolder, $evaluationTerminee);
                                                    @endphp
                                                    <div class="flex items-center space-x-3 flex-wrap gap-2">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-orange-100 text-orange-700">
                                                            {{ $lot->numero }}
                                                        </span>
                                                        <p class="font-medium text-gray-900">{{ $lot->libelle }}</p>
                                                        @if ($lot->attribution_lot)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                                <i class="fas fa-check mr-1"></i> Attribué
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                                <i class="fas fa-clock mr-1"></i> Non attribué
                                                            </span>
                                                        @endif
                                                        @if ($lot->statut_lot)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                                <i class="fas fa-power-off mr-1"></i> Actif
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($lot->description_critere)
                                                        <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                                            {{ $lot->description_critere }}</p>
                                                    @endif
                                                    @if ($lot->date_debut_prevue && $lot->date_fin_prevue)
                                                        <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                            <span><i
                                                                    class="fas fa-calendar mr-1"></i>{{ $lot->date_debut_prevue->format('d/m/Y') }}</span>
                                                            <span><i class="fas fa-arrow-right mx-1"></i></span>
                                                            <span>{{ $lot->date_fin_prevue->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @canany(['lots.view-details', 'lots.update'])
                                                    <div class="flex items-center space-x-2 ml-4">
                                                        @can('lots.view-details')
                                                            <button
                                                                onclick="window.location.href='{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->id_lot]) }}'"
                                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                                title="Voir détails">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        @endcan

                                                        @can('lots.update')
                                                            @if (!$lot->attributionActive)
                                                                <button
                                                                    onclick="window.location.href='{{ route('lots-appels-offres.edit', [$lot->appel_offre_id, $lot->id_lot]) }}'"
                                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                                    title="Modifier">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                @endcanany
                                            </div>
                                        @endforeach
                                    </div> --}}

                                    <div class="space-y-3">
                                        @foreach ($appelOffre->lots as $lot)
                                            <div
                                                class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 border border-gray-200">
                                                <div class="flex-1">
                                                    @php
                                                        $allPaiements =
                                                            $lot->attributionActive?->proforma?->facture?->paiements ??
                                                            null;
                                                        $facture = $lot->attributionActive?->proforma?->facture ?? null;
                                                        $proforma = $lot->attributionActive?->proforma ?? null;

                                                        // Calcul du montant payé et reste
                                                        $montantPaye = $allPaiements
                                                            ? $allPaiements->sum('montant_net_paye_paiement')
                                                            : 0;
                                                        $montantFacture = $facture?->montant_facture ?? 0;
                                                        $resteAPayer = max(0, $montantFacture - $montantPaye);
                                                        $tauxPaiement =
                                                            $montantFacture > 0
                                                                ? ($montantPaye / $montantFacture) * 100
                                                                : 0;

                                                        // États
                                                        $paiementTermine =
                                                            $montantFacture > 0 && $montantPaye >= $montantFacture;
                                                        $paiementEnCours =
                                                            $montantPaye > 0 && $montantPaye < $montantFacture;
                                                        $paiementNonCommence = $montantFacture > 0 && $montantPaye == 0;
                                                        $pasPaiementPrevu = !$facture;

                                                        // Évaluation
                                                        $sommesReferencesCriteresEvaluations = $lot->criteresEvaluation->sum(
                                                            'note_reference_critere_evaluation',
                                                        );
                                                        $sommesNotesEvaluations = $lot->criteresEvaluation->flatMap->evaluations->sum(
                                                            'resultat_evaluation',
                                                        );

                                                        $evaluationTerminee =
                                                            $sommesReferencesCriteresEvaluations > 0 &&
                                                            $sommesNotesEvaluations >=
                                                                $sommesReferencesCriteresEvaluations;
                                                        $evaluationEnCours =
                                                            $sommesNotesEvaluations > 0 &&
                                                            $sommesNotesEvaluations <
                                                                $sommesReferencesCriteresEvaluations;
                                                        $evaluationNonCommencee =
                                                            $sommesReferencesCriteresEvaluations > 0 &&
                                                            $sommesNotesEvaluations == 0;
                                                        $pasEvaluationPrevue =
                                                            $sommesReferencesCriteresEvaluations == 0;
                                                    @endphp

                                                    {{-- Ligne 1: Numéro, Libellé et Badges de statut --}}
                                                    <div class="flex items-center space-x-3 flex-wrap gap-2">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-orange-100 text-orange-700">
                                                            {{ $lot->numero }}
                                                        </span>
                                                        <p class="font-medium text-gray-900">{{ $lot->libelle }}</p>

                                                        {{-- Badge Attribution --}}
                                                        @if ($lot->attribution_lot)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                                <i class="fas fa-check mr-1"></i> Attribué
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                                <i class="fas fa-clock mr-1"></i> Non attribué
                                                            </span>
                                                        @endif

                                                        {{-- Badge Actif --}}
                                                        @if ($lot->statut_lot)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                                <i class="fas fa-power-off mr-1"></i> Actif
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Ligne 2: États Paiement et Évaluation --}}
                                                    @if ($lot->attribution_lot)
                                                        <div class="flex items-center space-x-3 flex-wrap gap-2 mt-2">
                                                            {{-- État du Paiement --}}
                                                            @if ($paiementTermine)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                                    <i class="fas fa-check-circle mr-1.5"></i> Paiement soldé
                                                                </span>
                                                            @elseif ($paiementEnCours)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                                                    <i class="fas fa-spinner mr-1.5"></i> Paiement en cours
                                                                    ({{ number_format($tauxPaiement, 0) }}%)
                                                                </span>
                                                            @elseif ($paiementNonCommence)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                                                    <i class="fas fa-times-circle mr-1.5"></i> Non payé
                                                                </span>
                                                            @elseif ($pasPaiementPrevu)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                                    <i class="fas fa-minus-circle mr-1.5"></i> Pas de facture
                                                                </span>
                                                            @endif

                                                            {{-- État de l'Évaluation --}}
                                                            @if ($evaluationTerminee)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                                    <i class="fas fa-clipboard-check mr-1.5"></i> Évaluation
                                                                    terminée
                                                                </span>
                                                            @elseif ($evaluationEnCours)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                                                    <i class="fas fa-clipboard-list mr-1.5"></i> Évaluation en
                                                                    cours
                                                                </span>
                                                            @elseif ($evaluationNonCommencee)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                                                    <i class="fas fa-clipboard mr-1.5"></i> Non évalué
                                                                </span>
                                                            @elseif ($pasEvaluationPrevue)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                                    <i class="fas fa-minus-circle mr-1.5"></i> Pas de critères
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Ligne 3: Détails financiers (optionnel - si facture existe) --}}
                                                        @if ($facture && $montantFacture > 0)
                                                            <div class="flex items-center space-x-4 mt-2 text-xs">
                                                                <span class="text-gray-600">
                                                                    <i class="fas fa-file-invoice mr-1 text-orange-500"></i>
                                                                    Facture:
                                                                    <strong>{{ number_format($montantFacture, 0, ',', ' ') }}
                                                                        FCFA</strong>
                                                                </span>
                                                                <span class="text-emerald-600">
                                                                    <i class="fas fa-check mr-1"></i>
                                                                    Payé:
                                                                    <strong>{{ number_format($montantPaye, 0, ',', ' ') }}
                                                                        FCFA</strong>
                                                                </span>
                                                                @if ($resteAPayer > 0)
                                                                    <span class="text-red-600">
                                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                        Reste:
                                                                        <strong>{{ number_format($resteAPayer, 0, ',', ' ') }}
                                                                            FCFA</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endif

                                                    {{-- Description --}}
                                                    @if ($lot->description_critere)
                                                        <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                                            {{ $lot->description_critere }}
                                                        </p>
                                                    @endif

                                                    {{-- Dates --}}
                                                    @if ($lot->date_debut_prevue && $lot->date_fin_prevue)
                                                        <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                            <span><i
                                                                    class="fas fa-calendar mr-1"></i>{{ $lot->date_debut_prevue->format('d/m/Y') }}</span>
                                                            <span><i class="fas fa-arrow-right mx-1"></i></span>
                                                            <span>{{ $lot->date_fin_prevue->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Actions --}}
                                                @canany(['lots.view-details', 'lots.update'])
                                                    <div class="flex items-center space-x-2 ml-4">
                                                        @can('lots.view-details')
                                                            <button
                                                                onclick="window.location.href='{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->id_lot]) }}'"
                                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                                title="Voir détails">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        @endcan

                                                        @can('lots.update')
                                                            @if (!$lot->attributionActive)
                                                                <button
                                                                    onclick="window.location.href='{{ route('lots-appels-offres.edit', [$lot->appel_offre_id, $lot->id_lot]) }}'"
                                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                                    title="Modifier">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                @endcanany
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                        <p class="text-gray-500 font-medium mb-3">Aucun lot pour cet appel d'offres</p>
                                        @can('lots.create')
                                            <button onclick="openCreateLotModal()"
                                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all text-sm shadow-md">
                                                <i class="fas fa-plus mr-1"></i> Créer le premier lot
                                            </button>
                                        @endcan
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endif
                @endcanany
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Statistiques -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-orange-500 mr-2"></i>
                        Statistiques
                    </h3>

                    <div class="space-y-4">
                        <!-- Total Lots -->
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-transparent rounded-lg border-l-4 border-blue-500">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Lots</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $appelOffre->lots_count }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-boxes text-blue-600"></i>
                            </div>
                        </div>

                        <!-- Lots attribués -->
                        @php
                            $lotsAttribues = $appelOffre->lots->where('attribution_lot', 1)->count();
                        @endphp
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-transparent rounded-lg border-l-4 border-green-500">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Lots attribués</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $lotsAttribues }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>

                        <!-- Jours restants -->
                        @if ($appelOffre->joursRestants() > 0)
                            <div
                                class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-transparent rounded-lg border-l-4 border-orange-500">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Jours restants</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $appelOffre->joursRestants() }}</p>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-600"></i>
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
                        @if ($appelOffre->creator)
                            <div>
                                <p class="text-gray-600 font-medium mb-1">Créé par</p>
                                <p class="text-gray-900">{{ $appelOffre->creator->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $appelOffre->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif

                        <!-- Modifié par -->
                        @if ($appelOffre->updater && $appelOffre->updated_at != $appelOffre->created_at)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-gray-600 font-medium mb-1">Dernière modification</p>
                                <p class="text-gray-900">{{ $appelOffre->updater->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $appelOffre->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                @can('lots.create')
                    @if ($appelOffre->caracteristiqueActive)
                        <!-- Actions rapides -->
                        <div
                            class="bg-gradient-to-br from-orange-50 to-white rounded-2xl shadow-lg p-6 border border-orange-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-bolt text-orange-500 mr-2"></i>
                                Actions rapides
                            </h3>

                            <div class="space-y-2">
                                <button onclick="openCreateLotModal()"
                                    class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-orange-50 border border-orange-200 rounded-lg transition-all duration-200 group">
                                    <div
                                        class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                                        <i class="fas fa-plus text-orange-600"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Créer un lot</span>
                                </button>
                            </div>
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </main>

    <!-- ========================================== -->
    <!-- MODAL CRÉATION LOT - AMÉLIORÉ -->
    <!-- ========================================== -->
    <div id="createLotModal" class="hidden fixed inset-0 z-50 overflow-hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        <!-- Overlay avec fermeture au clic -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300" id="modalOverlay"
            onclick="closeLotModal()"></div>

        <!-- Container du modal -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">

                <!-- Contenu du modal -->
                <div id="modalContent"
                    class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4">

                    <!-- Header du modal -->
                    <div class="relative bg-gradient-to-r from-orange-500 to-orange-600 rounded-t-2xl px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg">
                                    <i class="fas fa-box text-white text-lg"></i>
                                </div>

                                <div>
                                    <h3 id="modal-title" class="text-xl font-bold text-white">Nouveau Lot</h3>
                                    <p class="text-orange-100 text-sm">Ajoutez un lot à cet appel d'offres</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeLotModal()"
                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 transition-colors duration-200 group">
                                <i
                                    class="fas fa-times text-white group-hover:rotate-90 transition-transform duration-200"></i>
                            </button>
                        </div>

                        <!-- Barre de progression -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-orange-700">
                            <div id="progressBar" class="h-full bg-white/50 transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                    </div>

                    @can('lots.create')
                        <!-- Corps du formulaire -->
                        <form id="lotForm" method="POST" action="{{ route('lots.store') }}">
                            @csrf
                            <input type="hidden" name="appel_offre_id" value="{{ $appelOffre->id_appel_offre }}">

                            <div class="px-6 py-6 max-h-[calc(100vh-280px)] overflow-y-auto custom-scrollbar">

                                <!-- Info AO -->
                                <div
                                    class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-blue-50 border border-orange-200 rounded-xl">
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-contract text-orange-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-orange-800">
                                                {{ $appelOffre->numero_appel_offre }}</p>
                                            <p class="text-xs text-orange-600 mt-0.5">
                                                {{ Str::limit($appelOffre->libelle_critere_appel_offre, 60) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-5">

                                    <!-- Numréro - Champ principal -->
                                    <div class="group">
                                        <label for="lot_numero"
                                            class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-tag text-orange-500 mr-2 text-xs"></i>
                                            Numéro du lot
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="numero" id="lot_numero" required maxlength="160"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-600 focus:ring-4 focus:ring-orange-600/10 transition-all duration-200 text-gray-800 placeholder-gray-400"
                                                placeholder="Ex: LOT-{{ date('Y') }}-AZ{{ date('m') }}{{ date('d') }}"
                                                oninput="updateProgress()">
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                                                <span id="numero_count">0</span>/160
                                            </div>
                                        </div>
                                        <div id="error_lot_numero" class="hidden mt-2 text-red-500 text-sm flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <span></span>
                                        </div>
                                    </div>

                                    <!-- Libellé - Champ principal -->
                                    <div class="group">
                                        <label for="lot_libelle"
                                            class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-tag text-orange-500 mr-2 text-xs"></i>
                                            Libellé du lot
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="libelle" id="lot_libelle" required maxlength="160"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-600 focus:ring-4 focus:ring-orange-600/10 transition-all duration-200 text-gray-800 placeholder-gray-400"
                                                placeholder="Ex: Travaux de gros œuvre" oninput="updateProgress()">
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                                                <span id="libelle_count">0</span>/160
                                            </div>
                                        </div>
                                        <div id="error_lot_libelle"
                                            class="hidden mt-2 text-red-500 text-sm flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            <span></span>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="group">
                                        <label for="lot_description"
                                            class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-align-left text-orange-600 mr-2 text-xs"></i>
                                            Description
                                        </label>
                                        <textarea name="description_critere" id="lot_description" rows="3"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-600 focus:ring-4 focus:ring-orange-600/10 transition-all duration-200 text-gray-800 placeholder-gray-400 resize-none"
                                            placeholder="Décrivez le contenu et les objectifs de ce lot..." oninput="updateProgress()"></textarea>
                                    </div>

                                    <!-- Spécifications techniques -->
                                    <div class="group">
                                        <label for="lot_specifications"
                                            class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-cogs text-orange-600 mr-2 text-xs"></i>
                                            Spécifications techniques
                                        </label>
                                        <textarea name="specifications_techniques" id="lot_specifications" rows="3"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-600 focus:ring-4 focus:ring-orange-600/10 transition-all duration-200 text-gray-800 placeholder-gray-400 resize-none"
                                            placeholder="Détaillez les exigences techniques..."></textarea>
                                    </div>


                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="group">
                                            <label for="lot_date_debut"
                                                class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-calendar-plus text-green-500 mr-2 text-xs"></i>
                                                Date de début prévue <span class="text-red-500 px-1"> *</span>
                                            </label>
                                            {{-- {{ dd($appelOffre?->caracteristiqueActive) }} --}}
                                            <input type="date" required name="date_debut_prevue"
                                                min="{{ \Carbon\Carbon::parse($appelOffre?->caracteristiqueActive?->date_demarrage_prevue_caracteristique_appel_offre)->toDateString() }}"
                                                max="{{ \Carbon\Carbon::parse($appelOffre?->caracteristiqueActive?->date_livraison_previsionnelle_caracteristique_appel_offre)->toDateString() }}"
                                                id="lot_date_debut"  onchange="updateProgress()"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-600 focus:ring-4 focus:ring-orange-600/10 transition-all duration-200 text-gray-800">
                                            <div id="error_lot_date_debut"
                                                class="hidden mt-2 text-red-500 text-sm flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                <span></span>
                                            </div>
                                        </div>

                                        <div class="group">
                                            <label for="lot_date_fin"
                                                class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-calendar-check text-red-500 mr-2 text-xs"></i>
                                                Date de fin prévue <span class="text-red-500 px-1"> *</span>
                                            </label>
                                            <input type="date" required name="date_fin_prevue"
                                                min="{{ \Carbon\Carbon::parse($appelOffre?->caracteristiqueActive?->date_demarrage_prevue_caracteristique_appel_offre)->toDateString() }}"
                                                max="{{ \Carbon\Carbon::parse($appelOffre?->caracteristiqueActive?->date_livraison_previsionnelle_caracteristique_appel_offre)->toDateString() }}"
                                                id="lot_date_fin" onchange="updateProgress()"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-600 focus:ring-4 focus:ring-orange-600/10 transition-all duration-200 text-gray-800">
                                            <div id="error_lot_date_fin"
                                                class="hidden mt-2 text-red-500 text-sm flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function updateDateFinMin() {
                                            const dateDebut = document.getElementById('lot_date_debut').value;
                                            const dateFin = document.getElementById('lot_date_fin');

                                            if (dateDebut) {
                                                dateFin.min = dateDebut;

                                                // Réinitialiser si date fin < date début
                                                if (dateFin.value && dateFin.value < dateDebut) {
                                                    dateFin.value = '';
                                                }
                                            }
                                        }
                                    </script>

                                    <!-- Montant du lot -->
                                    <div class="group">
                                        <label for="budget_lot"
                                            class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-hand-holding-usd text-orange-500 mr-2 text-xs"></i>
                                            Budget du lot <span class="text-red-500 px-1"> *</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" id="budget_lot" min="0" step="5" required
                                                name="budget_lot" oninput="updateProgress()"
                                                class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-orange-600 focus:ring-4 focus:ring-orange-600/10 transition-all duration-200 text-gray-800 placeholder-gray-400"
                                                placeholder="Ex: 5 500 000 000">
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">
                                                FCFA</div>
                                        </div>

                                    </div>

                                    <!-- Statut actif -->
                                    <div class="group">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="statut_lot" id="lot_statut" value="1" checked
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-600/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600">
                                            </div>
                                            <span class="ml-3 text-sm font-medium text-gray-700">Lot actif</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer du modal -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                                    <p class="text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Les champs marqués <span class="text-red-500 mx-1">*</span> sont obligatoires
                                    </p>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" onclick="closeLotModal()"
                                            class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 font-medium text-sm">
                                            <i class="fas fa-times mr-2"></i>
                                            Annuler
                                        </button>
                                        @can('lots.create')
                                            <button type="submit" id="submitLotBtn"
                                                class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-xl transition-all duration-200 font-medium text-sm shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                                <i class="fas fa-save mr-2" id="submitIcon"></i>
                                                <span id="submitLotText">Créer le lot</span>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

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

    @push('scripts')
        <script>
            const aoId = '{{ $appelOffre->id_appel_offre }}';

            // ==========================================
            // GESTION DU MODAL LOT - AMÉLIORÉ
            // ==========================================

            function openCreateLotModal() {
                const modal = document.getElementById('createLotModal');
                const content = document.getElementById('modalContent');

                // Afficher le modal
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Animation d'entrée
                requestAnimationFrame(() => {
                    content.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                    content.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                });

                // Focus sur le premier champ
                setTimeout(() => {
                    document.getElementById('lot_numero').focus();
                }, 300);
            }

            function closeLotModal() {
                const modal = document.getElementById('createLotModal');
                const content = document.getElementById('modalContent');

                // Animation de sortie
                content.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                content.classList.add('opacity-0', 'scale-95', 'translate-y-4');

                // Masquer après l'animation
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                    resetLotForm();
                }, 200);
            }

            function resetLotForm() {
                document.getElementById('lotForm').reset();
                clearLotErrors();
                updateProgress();
                document.getElementById('libelle_count').textContent = '0';
            }

            function clearLotErrors() {
                const errorDivs = document.querySelectorAll('[id^="error_lot_"]');
                errorDivs.forEach(div => {
                    div.classList.add('hidden');
                    const span = div.querySelector('span');
                    if (span) span.textContent = '';
                });
            }

            function showError(fieldName, message) {
                const errorDiv = document.getElementById(`error_lot_${fieldName}`);
                if (errorDiv) {
                    const span = errorDiv.querySelector('span');
                    if (span) {
                        span.textContent = message;
                    } else {
                        errorDiv.textContent = message;
                    }
                    errorDiv.classList.remove('hidden');
                }
            }

            // Mise à jour de la barre de progression
            function updateProgress() {
                const numero = document.getElementById('lot_numero').value;
                const libelle = document.getElementById('lot_libelle').value;
                const budget = document.getElementById('budget_lot').value;
                const description = document.getElementById('lot_description').value;
                const date_debut = document.getElementById('lot_date_debut');
                const date_fin = document.getElementById('lot_date_fin');


                let progress = 0;
                if(numero.length > 2) progress += 20;
                if (libelle.length > 0) progress += 20;
                if (description.length > 0) progress += 20;
                if (budget) progress += 20;
                if (date_debut.value) progress += 10;
                if (date_fin.value) progress += 10;

                document.getElementById('progressBar').style.width = progress + '%';


                if (date_debut) {
                    date_fin.min = date_debut.value;

                    // Réinitialiser si date fin < date début
                    if (date_fin.value && date_fin.value < date_debut.value) {
                        date_fin.value = '';
                    }
                }
            }

            document.getElementById('lot_numero').addEventListener('input', function() {
                document.getElementById('numero_count').textContent = this.value.length;
            });

            

            // Compteur de caractères pour le libellé
            document.getElementById('lot_libelle').addEventListener('input', function() {
                document.getElementById('libelle_count').textContent = this.value.length;
            });

            // Validation date fin > date début
            document.getElementById('lot_date_fin').addEventListener('change', function() {
                const dateDebut = document.getElementById('lot_date_debut').value;
                const dateFin = this.value;

                if (dateDebut && dateFin && new Date(dateFin) <= new Date(dateDebut)) {
                    showError('date_fin', 'La date de fin doit être après la date de début');
                } else {
                    document.getElementById('error_lot_date_fin').classList.add('hidden');
                }
            });

            // Soumission du formulaire
            document.getElementById('lotForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submitLotBtn');
                const submitIcon = document.getElementById('submitIcon');
                const submitText = document.getElementById('submitLotText');

                // État de chargement
                submitBtn.disabled = true;
                submitIcon.className = 'fas fa-spinner fa-spin mr-2';
                submitText.textContent = 'Création en cours...';

                clearLotErrors();

                const formData = new FormData(this);

                fetch(this.action, {
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
                            // Animation de succès
                            submitIcon.className = 'fas fa-check mr-2';
                            submitText.textContent = 'Lot créé !';
                            submitBtn.classList.remove('from-orange-600', 'to-orange-700');
                            submitBtn.classList.add('from-green-500', 'to-green-600');

                            setTimeout(() => {
                                window.location = "{{ route('lots-appels-offres.show', [':appelOffre', ':id']) }}".replace(':appelOffre', data.data.appel_offre_id).replace(':id', data.data.id_lot)
                                // location.reload();
                            }, 500);
                        } else {
                            // Afficher les erreurs de validation
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    showError(key, data.errors[key][0]);
                                });

                                // Focus sur le premier champ en erreur
                                const firstErrorField = document.querySelector('[id^="error_lot_"]:not(.hidden)');
                                if (firstErrorField) {
                                    const fieldId = firstErrorField.id.replace('error_lot_', 'lot_');
                                    document.getElementById(fieldId)?.focus();
                                }
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue lors de la création du lot');
                    })
                    .finally(() => {
                        // Réinitialiser le bouton si pas de succès
                        if (!document.getElementById('submitLotBtn').classList.contains('from-green-500')) {
                            submitBtn.disabled = false;
                            submitIcon.className = 'fas fa-save mr-2';
                            submitText.textContent = 'Créer le lot';
                        }
                    });
            });

            // ==========================================
            // AUTRES FONCTIONS
            // ==========================================

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

            // Toggle statut
            function toggleStatus(isActive) {
                const action = isActive ? 'désactiver' : 'activer';
                if (confirm(`Voulez-vous vraiment ${action} cet appel d'offres ?`)) {
                    // `/appels-offres/${aoId}/toggle-status`
                    fetch("{{ route('appels-offres.toggle-status', ':id') }}".replace(':id', aoId), {
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
                                location.reload();
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

            // Publier
            function publier() {
                if (confirm('Voulez-vous publier cet appel d\'offres ? La date de publication sera définie à maintenant.')) {
                    // `/appels-offres/${aoId}/publier`
                    fetch("{{ route('appels-offres.publier', ':id') }}".replace(':id', aoId), {
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
                                location.reload();
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

            // Clôturer
            function cloturer() {
                if (confirm(
                        'Voulez-vous clôturer cet appel d\'offres ? Cette action modifiera la date limite de dépôt à maintenant.'
                    )) {
                    fetch("{{ route('appels-offres.cloturer', ':id') }}".replace(':id', aoId), {
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
                                location.reload();
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

            // Dupliquer
            function duplicate() {
                if (confirm(
                        'Voulez-vous dupliquer cet appel d\'offres ? Un nouvel appel d\'offres sera créé avec les mêmes informations.'
                    )) {
                    fetch("{{ route('appels-offres.duplicate', ':id') }}".replace(':id', aoId), {
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
                                window.location.href = `/appels-offres/${data.data.id_appel_offre}/edit`;
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

            // Voir statistiques
            function viewStatistiques() {
                fetch("{{ route('appels-offres.statistiques', ':id') }}".replace(':id', aoId), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const stats = data.data;
                            let message = `Statistiques de l'appel d'offres:\n\n`;
                            message += `Numéro: ${stats.general.numero}\n`;
                            message += `Montant global: ${stats.general.montant_global.toLocaleString('fr-FR')} FCFA\n`;
                            message += `Jours restants: ${stats.general.jours_restants}\n\n`;
                            message += `Total lots: ${stats.lots.total}\n`;
                            message += `Lots attribués: ${stats.lots.attribues}\n`;
                            message += `Lots non attribués: ${stats.lots.non_attribues}\n`;

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
                const nbLots = {{ $appelOffre->lots_count }};
                let message =
                    `Êtes-vous sûr de vouloir supprimer l'appel d'offres {{ $appelOffre->numero_appel_offre }} ?`;

                if (nbLots > 0) {
                    message = `Impossible de supprimer cet appel d'offres car il contient ${nbLots} lot(s).`;
                    document.getElementById('deleteMessage').innerHTML = `<strong class="text-red-600">${message}</strong>`;
                    document.querySelector('#deleteModal button[onclick="executeDelete()"]').classList.add('hidden');
                } else {
                    document.getElementById('deleteMessage').textContent = message;
                    document.querySelector('#deleteModal button[onclick="executeDelete()"]').classList.remove('hidden');
                }

                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Exécuter suppression
            function executeDelete() {
                fetch("{{ route('appels-offres.destroy', ':id') }}".replace(':id', aoId), {
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
                            window.location.href = '{{ route('appels-offres.index') }}';
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

            // Fermer modales avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLotModal();
                    closeDeleteModal();
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

            .line-clamp-1 {
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Custom scrollbar pour le modal */
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

            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush
@endsection
