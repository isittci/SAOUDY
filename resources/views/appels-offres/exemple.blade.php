@extends('layouts.main')
@section('title', 'Détails AO - ' . $appelOffre->numero_appel_offre)
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $appelOffre->numero_appel_offre }}</span>
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

                <!-- Actions -->
                <div class="flex items-center space-x-2 flex-wrap">
                    @if (!$appelOffre->date_publication_critere_appel_offre)
                        <button onclick="publier()"
                            class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-paper-plane text-sm"></i>
                            <span class="text-sm font-medium">Publier</span>
                        </button>
                    @endif


                    <a href="{{ route('caracteristiques-appels-offres.index', [$appelOffre->id_appel_offre]) }}"
                        class="px-4 py-2.5 bg-white border border-yellow-300 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-thumbs-up text-sm"></i>
                        <span class="text-sm font-medium">Caractéristiques</span>
                    </a>


                    @if ($appelOffre->isEnCours())
                        <button onclick="cloturer()"
                            class="px-4 py-2.5 bg-white border border-yellow-300 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-lock text-sm"></i>
                            <span class="text-sm font-medium">Clôturer</span>
                        </button>
                    @endif

                    <button onclick="window.location.href='{{ route('appels-offres.edit', $appelOffre->id_appel_offre) }}'"
                        class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-edit text-sm"></i>
                        <span class="text-sm font-medium">Modifier</span>
                    </button>

                    <button
                        onclick="toggleStatus({{ $appelOffre->statut_evaluation_critere_appel_offre ? 'true' : 'false' }})"
                        class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-power-off text-sm"></i>
                        <span
                            class="text-sm font-medium">{{ $appelOffre->statut_evaluation_critere_appel_offre ? 'Désactiver' : 'Activer' }}</span>
                    </button>

                    <!-- Menu dropdown -->
                    <div class="relative">
                        <button onclick="toggleMenu()" id="menuBtn"
                            class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-ellipsis-v text-sm"></i>
                        </button>
                        <div id="actionMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                            <div class="py-1">
                                <button onclick="duplicate()"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-copy mr-2 text-purple-500"></i>
                                    Dupliquer
                                </button>
                                <button onclick="viewStatistiques()"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                                    Statistiques
                                </button>
                                <button onclick="confirmDelete()"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Supprimer
                                </button>
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

                        <!-- Montant Global -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Montant Global</label>
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

                <!-- Section Lots -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-boxes text-indigo-500 mr-2"></i>
                                Lots ({{ $appelOffre->lots_count }})
                            </h2>
                            <button onclick="openCreateLotModal()"
                                class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all text-sm shadow-md flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Nouveau lot</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($appelOffre->lots->count() > 0)
                            <div class="space-y-4">
                                @foreach ($appelOffre->lots as $lot)
                                    <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-200 bg-gradient-to-r from-white to-gray-50">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-800">
                                                        {{ $lot->numero_lot }}
                                                    </span>
                                                    <h4 class="font-semibold text-gray-900">{{ $lot->libelle_lot }}</h4>
                                                    @if ($lot->statut_lot)
                                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    @else
                                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                    @endif
                                                </div>
                                                @if ($lot->description_critere)
                                                    <p class="text-gray-600 text-sm mt-2 line-clamp-1">
                                                        {{ $lot->description_critere }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-2 ml-4">
                                                <a href="{{ route('lots-appels-offres.show', [$appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"
                                                    title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('lots.edit', $lot->id_lot) }}"
                                                    class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 font-medium mb-3">Aucun lot pour cet appel d'offres</p>
                                <button onclick="openCreateLotModal()"
                                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-all text-sm shadow-md">
                                    <i class="fas fa-plus mr-1"></i> Créer le premier lot
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Création Lot avec Proforma -->
                <div id="createLotModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 overflow-y-auto">
                    <div class="flex items-start justify-center min-h-screen p-4 pt-10 sm:pt-20">
                        <div
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl transform transition-all animate-modalSlideIn">
                            <!-- Header -->
                            <div
                                class="flex items-center justify-between p-5 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-layer-group text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Nouveau Lot</h3>
                                        <p class="text-sm text-gray-500">Créez un lot avec sa proforma associée</p>
                                    </div>
                                </div>
                                <button onclick="closeLotModal()"
                                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all">
                                    <i class="fas fa-times text-lg"></i>
                                </button>
                            </div>

                            <!-- Form -->
                            <form id="lotForm" method="POST" action="{{ route('lots.store') }}"
                                class="max-h-[70vh] overflow-y-auto">
                                @csrf
                                <input type="hidden" name="appel_offre_id" value="{{ $appelOffre->id_appel_offre }}">

                                <div class="p-5 space-y-5">
                                    <!-- Info AO -->
                                    <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl">
                                        <p class="text-sm text-indigo-700">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Appel d'offres:</strong> {{ $appelOffre->numero_appel_offre }} -
                                            {{ $appelOffre->libelle_critere_appel_offre }}
                                        </p>
                                    </div>

                                    <!-- =============================================== -->
                                    <!-- SECTION PROFORMA (ACCORDÉON) - AVANT LE LOT -->
                                    <!-- =============================================== -->
                                    <div class="border border-orange-200 rounded-xl overflow-hidden">
                                        <!-- Accordéon Header -->
                                        <button type="button" onclick="toggleProformaAccordion()"
                                            class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-white hover:from-orange-100 hover:to-orange-50 transition-all duration-200">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-invoice-dollar text-orange-600 text-sm"></i>
                                                </div>
                                                <div class="text-left">
                                                    <span class="font-semibold text-gray-800">Proforma associée</span>
                                                    <span class="text-xs text-gray-500 block">Optionnel - Cliquez pour
                                                        ajouter une proforma</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span id="proformaStatusBadge"
                                                    class="hidden px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                                    <i class="fas fa-check mr-1"></i>Renseignée
                                                </span>
                                                <i id="proformaAccordionIcon"
                                                    class="fas fa-chevron-down text-gray-400 transform transition-transform duration-300"></i>
                                            </div>
                                        </button>

                                        <!-- Accordéon Content -->
                                        <div id="proformaAccordionContent" class="hidden border-t border-orange-200">
                                            <div class="p-4 bg-orange-50/30 space-y-4">
                                                <!-- Checkbox pour activer la proforma -->
                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-orange-100">
                                                    <input type="checkbox" name="creer_proforma" id="creer_proforma"
                                                        value="1"
                                                        class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-400"
                                                        onchange="toggleProformaFields()">
                                                    <label for="creer_proforma" class="flex-1">
                                                        <span class="font-medium text-gray-800">Créer une proforma pour ce
                                                            lot</span>
                                                        <span class="block text-xs text-gray-500">Cochez pour ajouter les
                                                            informations financières</span>
                                                    </label>
                                                </div>

                                                <!-- Champs Proforma (initialement masqués) -->
                                                <div id="proformaFields" class="hidden space-y-4">
                                                    <!-- Numéro et Date Proforma -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Numéro de proforma
                                                            </label>
                                                            <div class="relative">
                                                                <input type="text" name="numero_proforma"
                                                                    id="proforma_numero"
                                                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent bg-gray-50"
                                                                    placeholder="Auto-généré" readonly>
                                                                <span
                                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                                                    <i class="fas fa-magic text-sm"></i>
                                                                </span>
                                                            </div>
                                                            <p class="text-xs text-gray-500 mt-1">Généré automatiquement
                                                            </p>
                                                            <div id="error_proforma_numero"
                                                                class="hidden text-red-500 text-sm mt-1"></div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Date de la proforma
                                                            </label>
                                                            <input type="date" name="date_proforma" id="proforma_date"
                                                                value="{{ date('Y-m-d') }}"
                                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                                            <div id="error_proforma_date"
                                                                class="hidden text-red-500 text-sm mt-1"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Montants -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Montant HT <span class="text-red-500">*</span>
                                                            </label>
                                                            <div class="relative">
                                                                <input type="number" name="montant_ht_proforma"
                                                                    id="proforma_montant_ht" min="0"
                                                                    step="0.01"
                                                                    class="w-full px-4 py-2.5 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                                                    placeholder="0.00" onchange="calculerTotauxProforma()"
                                                                    onkeyup="calculerTotauxProforma()">
                                                                <span
                                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                                            </div>
                                                            <div id="error_proforma_montant_ht"
                                                                class="hidden text-red-500 text-sm mt-1"></div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Remise
                                                            </label>
                                                            <div class="flex space-x-2">
                                                                <div class="relative flex-1">
                                                                    <input type="number" name="remise_proforma"
                                                                        id="proforma_remise" min="0"
                                                                        step="0.01" value="0"
                                                                        class="w-full px-4 py-2.5 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                                                        onchange="calculerTotauxProforma()"
                                                                        onkeyup="calculerTotauxProforma()">
                                                                    <span
                                                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                                                </div>
                                                                <div class="relative w-20">
                                                                    <input type="number" id="proforma_remise_pct"
                                                                        min="0" max="100" step="0.01"
                                                                        class="w-full px-2 py-2.5 pr-6 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-center text-sm"
                                                                        placeholder="0" onchange="calculerRemiseFromPct()"
                                                                        onkeyup="calculerRemiseFromPct()">
                                                                    <span
                                                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Taxes -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Taxes (TVA)
                                                            </label>
                                                            <div class="flex space-x-2">
                                                                <div class="relative flex-1">
                                                                    <input type="number" name="taxe_proforma"
                                                                        id="proforma_taxe" min="0" step="0.01"
                                                                        value="0"
                                                                        class="w-full px-4 py-2.5 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                                                        onchange="calculerTotauxProforma()"
                                                                        onkeyup="calculerTotauxProforma()">
                                                                    <span
                                                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                                                </div>
                                                                <div class="relative w-20">
                                                                    <input type="number" id="proforma_taxe_pct"
                                                                        min="0" max="100" step="0.01"
                                                                        value="18"
                                                                        class="w-full px-2 py-2.5 pr-6 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-center text-sm"
                                                                        onchange="calculerTaxeFromPct()"
                                                                        onkeyup="calculerTaxeFromPct()">
                                                                    <span
                                                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs">%</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Résumé Total TTC -->
                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Total TTC
                                                            </label>
                                                            <div
                                                                class="px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-bold text-lg flex items-center justify-between">
                                                                <span>Total:</span>
                                                                <span id="proforma_total_ttc">0 FCFA</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modalité de paiement -->
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                            Modalités de paiement
                                                        </label>
                                                        <select id="modalite_select"
                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent mb-2"
                                                            onchange="selectModalite(this.value)">
                                                            <option value="">-- Choisir une modalité --</option>
                                                            <option value="Paiement à la livraison">Paiement à la livraison
                                                            </option>
                                                            <option value="Paiement à 30 jours">Paiement à 30 jours
                                                            </option>
                                                            <option value="Paiement à 60 jours">Paiement à 60 jours
                                                            </option>
                                                            <option value="50% à la commande, 50% à la livraison">50% à la
                                                                commande, 50% à la livraison</option>
                                                            <option value="custom">Autre (personnalisé)</option>
                                                        </select>
                                                        <textarea name="modalite_proforma" id="proforma_modalite" rows="2"
                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                                            placeholder="Détails des modalités de paiement..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- =============================================== -->
                                    <!-- SECTION LOT - APRÈS LA PROFORMA -->
                                    <!-- =============================================== -->
                                    <div class="border border-indigo-200 rounded-xl overflow-hidden">
                                        <div class="p-4 bg-gradient-to-r from-indigo-50 to-white">
                                            <div class="flex items-center space-x-3 mb-4">
                                                <div
                                                    class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-indigo-600 text-sm"></i>
                                                </div>
                                                <span class="font-semibold text-gray-800">Informations du lot</span>
                                            </div>

                                            <div class="space-y-4">
                                                <!-- Libellé -->
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        Libellé <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="libelle" id="lot_libelle" required
                                                        maxlength="160"
                                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                                        placeholder="Ex: Gros œuvre">
                                                    <div id="error_lot_libelle" class="hidden text-red-500 text-sm mt-1">
                                                    </div>
                                                </div>

                                                <!-- Description -->
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        Description
                                                    </label>
                                                    <textarea name="description_critere" id="lot_description" rows="2"
                                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                                                        placeholder="Description détaillée du lot..."></textarea>
                                                </div>

                                                <!-- Spécifications techniques -->
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        Spécifications techniques
                                                    </label>
                                                    <textarea name="specifications_techniques" id="lot_specifications" rows="2"
                                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                                                        placeholder="Spécifications techniques..."></textarea>
                                                </div>

                                                <!-- Dates -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                            Date de début validée
                                                        </label>
                                                        <input type="date" name="date_debut_validee" required
                                                            id="lot_date_debut"
                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                            Date de rédémarrage
                                                        </label>
                                                        <input type="date" name="date_redemarrage" required
                                                            id="lot_date_fin"
                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                                        <div id="error_lot_date_fin"
                                                            class="hidden text-red-500 text-sm mt-1"></div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                            Date de fin validée
                                                        </label>
                                                        <input type="date" name="date_fin_validee" required
                                                            id="lot_date_fin"
                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                                        <div id="error_lot_date_fin"
                                                            class="hidden text-red-500 text-sm mt-1"></div>
                                                    </div>




                                                </div>

                                                <!-- Taux pénalités et Statut -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                            Taux de pénalités (%)
                                                        </label>
                                                        <input type="number" name="taux_penalites"
                                                            id="lot_taux_penalites" min="0" max="100"
                                                            step="0.01"
                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                                            placeholder="Ex: 1.5">
                                                        <p class="text-xs text-gray-500 mt-1">Par jour de retard</p>
                                                    </div>

                                                    <div class="flex items-center">
                                                        <div
                                                            class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg w-full">
                                                            <input type="checkbox" name="statut_lot" id="lot_statut"
                                                                value="1" checked
                                                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                            <label for="lot_statut"
                                                                class="text-sm font-medium text-gray-700">
                                                                Lot actif
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div
                                    class="flex items-center justify-end space-x-3 p-5 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                                    <button type="button" onclick="closeLotModal()"
                                        class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-all duration-200 font-medium">
                                        <i class="fas fa-times mr-2"></i>Annuler
                                    </button>
                                    <button type="submit" id="submitLotBtn"
                                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                                        <i class="fas fa-save mr-2"></i>
                                        <span id="submitLotText">Créer le lot</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
            </div>
        </div>
    </main>

    @push('scripts')
        <script>
            const aoId = '{{ $appelOffre->id_appel_offre }}';

            // Toggle menu dropdown
            function toggleMenu() {
                const menu = document.getElementById('actionMenu');
                menu.classList.toggle('hidden');
            }



            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                const menu = document.getElementById('actionMenu');
                const btn = document.getElementById('menuBtn');
                if (!menu.contains(event.target) && !btn.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });

            // ===============================================
            // GESTION DU MODAL DE CRÉATION DE LOT
            // ===============================================
            function openCreateLotModal() {
                document.getElementById('createLotModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeLotModal() {
                document.getElementById('createLotModal').classList.add('hidden');
                document.body.style.overflow = '';
                document.getElementById('lotForm').reset();
                clearLotErrors();
                // Reset proforma accordion
                document.getElementById('proformaAccordionContent').classList.add('hidden');
                document.getElementById('proformaAccordionIcon').classList.remove('rotate-180');
                document.getElementById('proformaFields').classList.add('hidden');
                document.getElementById('proformaStatusBadge').classList.add('hidden');
            }

            function clearLotErrors() {
                const errorDivs = document.querySelectorAll('[id^="error_"]');
                errorDivs.forEach(div => {
                    div.classList.add('hidden');
                    div.textContent = '';
                });
            }

                        // Toggle statut
            function toggleStatus(isActive) {
                const action = isActive ? 'désactiver' : 'activer';
                if (confirm(`Voulez-vous vraiment ${action} cet appel d'offres ?`)) {
                    fetch("{{ route('appels-offres.toggle-status', ':id') }}".replace(':id', aoId ), {
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
                    fetch("{{ route('appels-offres.publier', ':id') }}".replace(':id', aoId ), {
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
                    fetch("{{ route('appels-offres.cloturer', ':id') }}".replace(':id', aoId ), {
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
            function duplicate()
            {
                if (confirm(
                        'Voulez-vous dupliquer cet appel d\'offres ? Un nouvel appel d\'offres sera créé avec les mêmes informations.'
                    )) {
                    fetch("{{ route('appels-offres.duplicate', ':id') }}".replace(':id', aoId ), {
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
                                window.location.href = "{{ route('appels-offres.edit', ':id') }}".replace(':id', data.data.id_appel_offre );
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
                    `Êtes-vous sûr de vouloir supprimer l'appel d'offres $appelOffre->numero_appel_offre ?`;

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

            // Gestion du modal de création de lot
            function openCreateLotModal() {
                document.getElementById('createLotModal').classList.remove('hidden');
            }

            function closeLotModal() {
                document.getElementById('createLotModal').classList.add('hidden');
                document.getElementById('lotForm').reset();
                clearLotErrors();
            }

            function clearLotErrors() {
                const errorDivs = document.querySelectorAll('[id^="error_lot_"]');
                errorDivs.forEach(div => {
                    div.classList.add('hidden');
                    div.textContent = '';
                });
            }












            // ===============================================
            // GESTION DE L'ACCORDÉON PROFORMA
            // ===============================================
            function toggleProformaAccordion() {
                const content = document.getElementById('proformaAccordionContent');
                const icon = document.getElementById('proformaAccordionIcon');

                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            }

            function toggleProformaFields() {
                const checkbox = document.getElementById('creer_proforma');
                const fields = document.getElementById('proformaFields');
                const badge = document.getElementById('proformaStatusBadge');

                if (checkbox.checked) {
                    fields.classList.remove('hidden');
                    badge.classList.remove('hidden');
                } else {
                    fields.classList.add('hidden');
                    badge.classList.add('hidden');
                }
            }

            // ===============================================
            // CALCULS PROFORMA
            // ===============================================
            function calculerTotauxProforma() {
                const montantHT = parseFloat(document.getElementById('proforma_montant_ht').value) || 0;
                const remise = parseFloat(document.getElementById('proforma_remise').value) || 0;
                const taxe = parseFloat(document.getElementById('proforma_taxe').value) || 0;

                const sousTotal = montantHT - remise;
                const totalTTC = sousTotal + taxe;

                // Mettre à jour le pourcentage de remise
                if (montantHT > 0) {
                    document.getElementById('proforma_remise_pct').value = ((remise / montantHT) * 100).toFixed(2);
                }

                // Mettre à jour le pourcentage de taxe
                if (sousTotal > 0) {
                    document.getElementById('proforma_taxe_pct').value = ((taxe / sousTotal) * 100).toFixed(2);
                }

                // Mettre à jour le total TTC
                document.getElementById('proforma_total_ttc').textContent = formatMontant(totalTTC) + ' FCFA';
            }

            function calculerRemiseFromPct() {
                const montantHT = parseFloat(document.getElementById('proforma_montant_ht').value) || 0;
                const pourcentage = parseFloat(document.getElementById('proforma_remise_pct').value) || 0;
                const remise = (montantHT * pourcentage) / 100;

                document.getElementById('proforma_remise').value = remise.toFixed(2);
                calculerTotauxProforma();
            }

            function calculerTaxeFromPct() {
                const montantHT = parseFloat(document.getElementById('proforma_montant_ht').value) || 0;
                const remise = parseFloat(document.getElementById('proforma_remise').value) || 0;
                const sousTotal = montantHT - remise;
                const pourcentage = parseFloat(document.getElementById('proforma_taxe_pct').value) || 0;
                const taxe = (sousTotal * pourcentage) / 100;

                document.getElementById('proforma_taxe').value = taxe.toFixed(2);
                calculerTotauxProforma();
            }

            function formatMontant(montant) {
                return new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(montant);
            }

            function selectModalite(value) {
                const textarea = document.getElementById('proforma_modalite');
                if (value && value !== 'custom') {
                    textarea.value = value;
                } else if (value === 'custom') {
                    textarea.value = '';
                    textarea.focus();
                }
            }

            // ===============================================
            // SOUMISSION DU FORMULAIRE
            // ===============================================
            document.getElementById('lotForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submitLotBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Création en cours...';

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
                            location.reload();
                        } else {
                            // Afficher les erreurs de validation
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    const errorDiv = document.getElementById(`error_${key}`) ||
                                        document.getElementById(`error_lot_${key}`) ||
                                        document.getElementById(`error_proforma_${key}`);
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
                        alert('Une erreur est survenue lors de la création du lot');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });

            // Validation date fin > date début
            document.getElementById('lot_date_fin').addEventListener('change', function() {
                const dateDebut = document.getElementById('lot_date_debut').value;
                const dateFin = this.value;
                const errorDiv = document.getElementById('error_lot_date_fin');

                if (dateDebut && dateFin && new Date(dateFin) <= new Date(dateDebut)) {
                    errorDiv.textContent = 'La date de fin doit être après la date de début';
                    errorDiv.classList.remove('hidden');
                } else {
                    errorDiv.classList.add('hidden');
                }
            });

            // Fermer modales avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLotModal();
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });

            // Fermer modal en cliquant sur le backdrop
            document.getElementById('createLotModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeLotModal();
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

            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-30px) scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
            }

            .animate-modalSlideIn {
                animation: modalSlideIn 0.3s ease-out;
            }

            .line-clamp-1 {
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Scroll personnalisé pour le modal */
            #lotForm::-webkit-scrollbar {
                width: 6px;
            }

            #lotForm::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            #lotForm::-webkit-scrollbar-thumb {
                background: #c7c7c7;
                border-radius: 3px;
            }

            #lotForm::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush
@endsection
