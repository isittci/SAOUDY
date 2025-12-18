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


                    {{-- <a href="{{ route('caracteristiques-appels-offres.index', [$appelOffre->id_appel_offre]) }}"
                        class="px-4 py-2.5 bg-white border border-yellow-300 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-thumbs-up text-sm"></i>
                        <span class="text-sm font-medium">Caractéristiques</span>
                    </a> --}}
                    <a href="{{ route('caracteristiques-appels-offres.index', [$appelOffre->id_appel_offre]) }}" class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-list-check text-sm"></i>
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

                <!-- Lots associés -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-boxes text-indigo-500 mr-2"></i>
                                Lots
                                <span
                                    class="ml-2 px-2.5 py-1 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">
                                    {{ $appelOffre->lots_count }}
                                </span>
                            </h2>
                            <button onclick="openCreateLotModal()"
                                class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-all text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-plus mr-1"></i> Ajouter un lot
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($appelOffre->lots->count() > 0)
                            <div class="space-y-3">
                                @foreach ($appelOffre->lots as $lot)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 border border-gray-200">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 flex-wrap gap-2">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700">
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
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button
                                                onclick="window.location.href='{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->id_lot]) }}'"
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"
                                                title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button
                                                onclick="window.location.href='{{ route('lots-appels-offres.edit', [$lot->appel_offre_id, $lot->id_lot]) }}'"
                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
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

                <!-- Modal Création Lot -->
                <div id="createLotModal"
                    class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full transform transition-all">
                            <!-- Header -->
                            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                                <h3 class="text-xl font-bold text-gray-900">Nouveau Lot</h3>
                                <button onclick="closeLotModal()"
                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>

                            <!-- Form -->
                            <form id="lotForm" method="POST" action="{{ route('lots.store') }}" class="p-6">
                                @csrf
                                <input type="hidden" name="appel_offre_id" value="{{ $appelOffre->id_appel_offre }}">

                                <div class="space-y-5">
                                    <!-- Info AO -->
                                    <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                        <p class="text-sm text-indigo-700">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Appel d'offres:</strong> {{ $appelOffre->numero_appel_offre }} -
                                            {{ $appelOffre->libelle_critere_appel_offre }}
                                        </p>
                                    </div>

                                    <!-- Numéro et Libellé -->
                                    <div class="grid grid-cols-1 gap-4">
                                        {{-- <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Numéro du lot <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="numero" id="lot_numero" required maxlength="20"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                                placeholder="Ex: LOT-001">
                                            <div id="error_lot_numero" class="hidden text-red-500 text-sm mt-1"></div>
                                        </div> --}}

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Libellé <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="libelle" id="lot_libelle" required
                                                maxlength="160"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                                placeholder="Ex: Gros œuvre">
                                            <div id="error_lot_libelle" class="hidden text-red-500 text-sm mt-1"></div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Description
                                        </label>
                                        <textarea name="description_critere" id="lot_description" rows="3"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                                            placeholder="Description détaillée du lot..."></textarea>
                                    </div>

                                    <!-- Spécifications techniques -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Spécifications techniques
                                        </label>
                                        <textarea name="specifications_techniques" id="lot_specifications" rows="3"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                                            placeholder="Spécifications techniques..."></textarea>
                                    </div>

                                    <!-- Dates -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Date de début prévue <span class="text-red-500 px-1"> *</span>
                                            </label>
                                            <input type="datet" name="date_debut_prevue" id="lot_date_debut" required
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Date de fin prévue <span class="text-red-500 px-1"> *</span>
                                            </label>
                                            <input type="datetime-local" name="date_fin_prevue" id="lot_date_fin" required
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                                            <div id="error_lot_date_fin" class="hidden text-red-500 text-sm mt-1"></div>
                                        </div>
                                    </div>

                                    <!-- Taux pénalités -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Taux de pénalités (%)
                                        </label>
                                        <input type="number" name="taux_penalites" id="lot_taux_penalites"
                                            min="0" max="100" step="0.01"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                            placeholder="Ex: 1.5">
                                        <p class="text-xs text-gray-500 mt-1">Pourcentage appliqué par jour de retard</p>
                                    </div>

                                    <!-- Statut -->
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" name="statut_lot" id="lot_statut" value="1" checked
                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="lot_statut" class="text-sm font-medium text-gray-700">Lot
                                            actif</label>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                                    <button type="button" onclick="closeLotModal()"
                                        class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                                        Annuler
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

                <!-- Actions rapides -->
                <div class="bg-gradient-to-br from-orange-50 to-white rounded-2xl shadow-lg p-6 border border-orange-100">
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

                        <button onclick="duplicate()"
                            class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-purple-50 border border-purple-200 rounded-lg transition-all duration-200 group">
                            <div
                                class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-copy text-purple-600"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Dupliquer l'AO</span>
                        </button>

                        <button onclick="window.print()"
                            class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-blue-50 border border-blue-200 rounded-lg transition-all duration-200 group">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-print text-blue-600"></i>
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

    @push('scripts')
        <script>
            const aoId = '{{ $appelOffre->id_appel_offre }}';

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
                    fetch(`/appels-offres/${aoId}/toggle-status`, {
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
                    fetch(`/appels-offres/${aoId}/publier`, {
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
                    fetch(`/appels-offres/${aoId}/cloturer`, {
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
                    fetch(`/appels-offres/${aoId}/duplicate`, {
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
                fetch(`/appels-offres/${aoId}/statistiques`, {
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
                fetch(`/appels-offres/${aoId}`, {
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

            // Gestion du formulaire de lot
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
                                    const errorDiv = document.getElementById(`error_lot_${key}`);
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
                    closeDeleteModal();
                    closeLotModal();
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

            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush
@endsection
