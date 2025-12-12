@extends('layouts.main')
@section('title', 'Détails Prestataire - ' . $prestataire->raison_sociale_prestataire)
@section('breadcrumb')
    <a href="{{ route('prestataires.index') }}" class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ Str::limit($prestataire->raison_sociale_prestataire, 30) }}</span>
@endsection

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('prestataires.index') }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $prestataire->raison_sociale_prestataire }}</h1>
                            @if ($prestataire->statut_prestataire)
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
                        </div>
                        <p class="text-gray-600 mt-1">
                            <i class="fas fa-id-card mr-1"></i>{{ $prestataire->numero_identification_prestataire }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 flex-wrap">
                    <button onclick="window.location.href='{{ route('prestataires.edit', $prestataire->id_prestataire) }}'"
                        class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-edit text-sm"></i>
                        <span class="text-sm font-medium">Modifier</span>
                    </button>

                    <button onclick="toggleStatus({{ $prestataire->statut_prestataire ? 'true' : 'false' }})"
                        class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-power-off text-sm"></i>
                        <span
                            class="text-sm font-medium">{{ $prestataire->statut_prestataire ? 'Désactiver' : 'Activer' }}</span>
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
                                <button onclick="viewStatistiques()"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                                    Statistiques
                                </button>
                                <button onclick="printPrestataire()"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-print mr-2 text-gray-500"></i>
                                    Imprimer
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

                <!-- Informations générales -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-orange-500 mr-2"></i>
                            Informations générales
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Raison sociale et ID -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Raison sociale</label>
                                <p class="text-gray-900 font-medium text-lg">{{ $prestataire->raison_sociale_prestataire }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Numéro
                                    d'identification</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                    {{ $prestataire->numero_identification_prestataire }}
                                </span>
                            </div>
                        </div>

                        <!-- Numéros légaux -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">
                                    <i class="fas fa-file-invoice text-gray-400 mr-1"></i>
                                    N° Carte de Contribuable
                                </label>
                                <p class="text-gray-900 font-medium">
                                    {{ $prestataire->numero_cc_prestataire ?? 'Non renseigné' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">
                                    <i class="fas fa-landmark text-gray-400 mr-1"></i>
                                    N° RCCM
                                </label>
                                <p class="text-gray-900 font-medium">
                                    {{ $prestataire->numero_rccm_prestataire ?? 'Non renseigné' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-address-book text-blue-500 mr-2"></i>
                            Informations de contact
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Email</span>
                                    <i class="fas fa-envelope text-blue-500"></i>
                                </div>
                                <a href="mailto:{{ $prestataire->email_prestataire }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    {{ $prestataire->email_prestataire }}
                                </a>
                            </div>

                            <!-- Téléphone principal -->
                            <div class="bg-gradient-to-br from-green-50 to-white p-5 rounded-xl border border-green-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Téléphone principal</span>
                                    <i class="fas fa-phone text-green-500"></i>
                                </div>
                                <a href="tel:{{ $prestataire->telephone_principal_prestataire ?? $prestataire->telephone_prestataire }}"
                                    class="text-gray-900 font-medium hover:text-green-600 transition-colors">
                                    {{ $prestataire->telephone_principal_prestataire ?? ($prestataire->telephone_prestataire ?? 'Non renseigné') }}
                                </a>
                            </div>

                            @if ($prestataire->telephone_secondaire_prestataire)
                                <!-- Téléphone secondaire -->
                                <div class="bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-600">Téléphone secondaire</span>
                                        <i class="fas fa-mobile-alt text-gray-500"></i>
                                    </div>
                                    <a href="tel:{{ $prestataire->telephone_secondaire_prestataire }}"
                                        class="text-gray-900 font-medium hover:text-gray-600 transition-colors">
                                        {{ $prestataire->telephone_secondaire_prestataire }}
                                    </a>
                                </div>
                            @endif

                            @if ($prestataire->contact_principal_prestataire)
                                <!-- Contact principal -->
                                <div
                                    class="bg-gradient-to-br from-purple-50 to-white p-5 rounded-xl border border-purple-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-600">Contact principal</span>
                                        <i class="fas fa-user text-purple-500"></i>
                                    </div>
                                    <p class="text-gray-900 font-medium">{{ $prestataire->contact_principal_prestataire }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Adresse -->
                        <div
                            class="mt-6 bg-gradient-to-br from-orange-50 to-white p-5 rounded-xl border border-orange-100">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-gray-600">Adresse complète</span>
                                <i class="fas fa-map-marker-alt text-orange-500"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-900 font-medium">
                                    {{ $prestataire->adresse_prestataire ?? 'Non renseignée' }}</p>
                                <p class="text-gray-600">
                                    {{ $prestataire->ville_prestataire ?? '' }}
                                    @if ($prestataire->ville_prestataire && ($prestataire->pays_prestataire ?? $prestataire->pays))
                                        ,
                                    @endif
                                    {{ $prestataire->pays_prestataire ?? ($prestataire->pays ?? '') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Représentant légal -->
                @php
                    $representants = json_decode($prestataire->representant_legal_prestataire, true) ?? [];
                    $representantActif = collect($representants)->firstWhere('statut', 1);
                @endphp

                @if ($representantActif)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-user-tie text-purple-500 mr-2"></i>
                                Représentant légal actif
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Nom complet</label>
                                    <p class="text-gray-900 font-medium">{{ $representantActif['nom'] ?? '' }}
                                        {{ $representantActif['prenoms'] ?? '' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Profession</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ $representantActif['profession'] ?? 'Non renseignée' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Email</label>
                                    <a href="mailto:{{ $representantActif['email'] ?? '' }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        {{ $representantActif['email'] ?? 'Non renseigné' }}
                                    </a>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Contact</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ $representantActif['contact'] ?? 'Non renseigné' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Nationalité</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ $representantActif['nationalite'] ?? 'Non renseignée' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Date de naissance</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ isset($representantActif['date_naissance']) ? \Carbon\Carbon::parse($representantActif['date_naissance'])->format('d/m/Y') : 'Non renseignée' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pièce d'identité -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-id-card mr-1"></i> Pièce d'identité
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Type:</span>
                                        <span
                                            class="ml-2 font-medium text-gray-900">{{ $representantActif['type_piece_identite'] ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Numéro:</span>
                                        <span
                                            class="ml-2 font-medium text-gray-900">{{ $representantActif['numero_piece_identite'] ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Expire le:</span>
                                        <span class="ml-2 font-medium text-gray-900">
                                            {{ isset($representantActif['date_expiration']) ? \Carbon\Carbon::parse($representantActif['date_expiration'])->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Statistiques -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>
                            Statistiques
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm text-gray-600">Lots en cours</span>
                            <span
                                class="text-lg font-bold text-blue-600">{{ $prestataire->attributionsActives()->count() ?? 0 }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-sm text-gray-600">Lots terminés</span>
                            <span class="text-lg font-bold text-green-600">
                                {{ $prestataire->attributions()->where('statut_attribution', \App\Models\PrestataireLot::STATUT_TERMINE)->count() ?? 0 }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <span class="text-sm text-gray-600">Taux de réussite</span>
                            <span
                                class="text-lg font-bold text-orange-600">{{ $prestataire->calculerTauxReussite() ?? 0 }}%</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <span class="text-sm text-gray-600">Pénalités totales</span>
                            <span class="text-lg font-bold text-red-600">
                                {{ number_format($prestataire->calculerPenalitesTotales() ?? 0, 0, ',', ' ') }} FCFA
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Retard moyen</span>
                            <span class="text-lg font-bold text-gray-600">{{ $prestataire->calculerRetardMoyen() ?? 0 }}
                                jours</span>
                        </div>
                    </div>
                </div>

                <!-- Accès rapides -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                            Accès rapides
                        </h2>
                    </div>

                    <div class="p-4 space-y-2">
                        {{-- <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-all group">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-file-alt text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Documents</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->documents()->count() ?? 0 }} fichier(s)</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a> --}}

                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-university text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Lots attribués</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->lots()->count() ?? 0 }} lot(s)</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>

                        {{-- <a href="{{ route('banques.index', $prestataire) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-all group">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-university text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Banques</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->banques()->count() ?? 0 }} lot(s)</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a> --}}

                        <a href="{{ route('banques.index', $prestataire) }}"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-university text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Coordonnées bancaires</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->banques()->count() ?? 0 }} compte(s)</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>

                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-cogs text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Capacités techniques</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->capacitesTechniques()->count() ?? 0 }}
                                    référence(s)</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>

                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-indigo-200 transition-colors">
                                <i class="fas fa-chart-line text-indigo-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Situations financières</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->situationsFinancieres()->count() ?? 0 }}
                                    exercice(s)</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>

                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-yellow-200 transition-colors">
                                <i class="fas fa-star text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Évaluations</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->evaluations()->count() ?? 0 }}
                                    évaluation(s)</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    </div>
                </div>

                <!-- Informations système -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                            Informations système
                        </h2>
                    </div>

                    <div class="p-6 space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créé le</span>
                            <span
                                class="font-medium text-gray-900">{{ $prestataire->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @if ($prestataire->creator)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Créé par</span>
                                <span class="font-medium text-gray-900">{{ $prestataire->creator->nom_complet ?? '-' }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Modifié le</span>
                            <span
                                class="font-medium text-gray-900">{{ $prestataire->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @if ($prestataire->updater)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Modifié par</span>
                                <span class="font-medium text-gray-900">{{ $prestataire->updater->name ?? '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>



<div class="mt-6 bg-white rounded-2xl shadow-lg">
    {{-- En-tête - Retirer overflow-hidden du conteneur parent --}}
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800 flex items-center">
            <i class="fas fa-history text-indigo-500 mr-2"></i>
            Historique des attributions
        </h2>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-700">
                {{ $prestataire->attributions()->count() ?? 0 }} attribution(s)
            </span>
            <a href="{{ route('prestataires.lots.index', $prestataire) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-list mr-2"></i>
                Voir tous les lots
            </a>
        </div>
    </div>

    <div class="p-6">
        @if ($prestataire->attributions()->count() > 0)
            <div class="space-y-4">
                @foreach ($prestataire->attributions()->with('lot')->orderBy('created_at', 'desc')->take(5)->get() as $attribution)
                    {{-- IMPORTANT: Ajouter relative et z-index ici pour créer un nouveau contexte d'empilement --}}
                    <div class="relative flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $attribution->lot->libelle ?? 'Lot inconnu' }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $attribution->lot->numero ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            {{-- Statut --}}
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $attribution->getStatutBadgeClassAttribute() }}">
                                    {{ $attribution->getStatutLabelAttribute() }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $attribution->created_at->format('d/m/Y') }}
                                </p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center space-x-2">
                                {{-- Bouton Voir détails --}}
                                <a href="{{ route('prestataires.lots.show', [$prestataire->id_prestataire, $attribution->lot_id]) }}"
                                    class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Bouton Retirer --}}
                                @if ($attribution->statut_attribution === \App\Models\PrestataireLot::STATUT_ATTRIBUE)
                                    <button type="button"
                                        onclick="confirmerRetrait('{{ $attribution->id_prestataire }}', '{{ $attribution->lot->libelle ?? 'ce lot' }}')"
                                        class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Retirer l'attribution">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                @endif

                                {{-- Menu déroulant pour plus d'actions --}}
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open"
                                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-lg transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    {{-- Menu dropdown avec z-index élevé et positionnement fixe --}}
                                    <div x-show="open"
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50"
                                        style="position: absolute;">

                                        <a href="{{ route('prestataires.lots.show', [$prestataire->id_prestataire, $attribution->lot_id]) }}"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-eye mr-3 text-gray-400"></i>
                                            Voir détails
                                        </a>

                                        @if ($attribution->statut_attribution === \App\Models\PrestataireLot::STATUT_ATTRIBUE)
                                            {{-- <a href="{{ route('prestataires.lots.edit', [$prestataire->id_prestataire, $attribution->lot_id]) }}"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-3 text-gray-400"></i>
                                                Modifier
                                            </a> --}}

                                            <hr class="my-1 border-gray-100">

                                            <button onclick="confirmerSuspension('{{ $attribution->id_prestataire }}', '{{ $attribution->lot->libelle ?? 'ce lot' }}')"
                                                class="w-full flex items-center px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">
                                                <i class="fas fa-pause-circle mr-3 text-amber-500"></i>
                                                Suspendre
                                            </button>

                                            <button onclick="confirmerRetrait('{{ $attribution->id_prestataire }}', '{{ $attribution->lot->libelle ?? 'ce lot' }}')"
                                                class="w-full flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                <i class="fas fa-times-circle mr-3 text-red-500"></i>
                                                Retirer
                                            </button>
                                        @endif

                                        @if ($attribution->statut_attribution === \App\Models\PrestataireLot::STATUT_SUSPENDU)
                                            <button onclick="confirmerReactivation('{{ $attribution->id_prestataire }}')"
                                                class="w-full flex items-center px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                                <i class="fas fa-play-circle mr-3 text-green-500"></i>
                                                Réactiver
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($prestataire->attributions()->count() > 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('prestataires.lots.index', $prestataire) }}"
                        class="inline-flex items-center px-4 py-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg text-sm font-medium transition-colors">
                        Voir tout l'historique ({{ $prestataire->attributions()->count() - 5 }} de plus)
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">Aucune attribution pour ce prestataire</p>
                <p class="text-sm text-gray-400 mt-1">Les attributions de lots apparaîtront ici</p>
            </div>
        @endif
    </div>
</div>

        {{-- Modal de confirmation de retrait --}}
        <div id="modalRetrait" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="fermerModalRetrait()">
                </div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer le retrait</h3>
                        <p class="text-gray-500 mb-6">
                            Êtes-vous sûr de vouloir retirer l'attribution du lot
                            <span id="nomLotRetrait" class="font-semibold text-gray-700"></span> ?
                        </p>

                        <form id="formRetrait" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-2">
                                    Motif du retrait <span class="text-red-500">*</span>
                                </label>
                                <textarea name="motif_retrait" rows="3" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="Indiquez le motif du retrait..."></textarea>
                            </div>

                            <div class="flex space-x-3">
                                <button type="button" onclick="fermerModalRetrait()"
                                    class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Retirer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="modalSuspension" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="fermerModalSuspension()">
                </div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer la suspension</h3>
                        <p class="text-gray-500 mb-6">
                            Êtes-vous sûr de vouloir suspendre l'attribution du lot
                            <span id="nomLotSuspension" class="font-semibold text-gray-700"></span> ?
                        </p>

                        <form id="formSuspension" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-2">
                                    Motif de la suspension <span class="text-red-500">*</span>
                                </label>
                                <textarea name="motif_suspension" rows="3" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="Indiquez le motif du retrait..."></textarea>
                            </div>

                            <div class="flex space-x-3">
                                <button type="button" onclick="fermerModalSuspension()"
                                    class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Suspendre
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmerRetrait(attributionId, nomLot) {
                document.getElementById('nomLotRetrait').textContent = nomLot;
                document.getElementById('formRetrait').action = "{{ route('attributions.retirer', ':id') }}".replace(':id', attributionId);
                    // `/prestataires/{{ $prestataire->id }}/lots/${attributionId}/retirer`;
                document.getElementById('modalRetrait').classList.remove('hidden');
            }

            function fermerModalRetrait() {
                document.getElementById('modalRetrait').classList.add('hidden');
                document.getElementById('formRetrait').reset();
            }

            function confirmerSuspension(attributionId, nomLot) {
                document.getElementById('nomLotSuspension').textContent = nomLot;
                document.getElementById('formSuspension').action = "{{ route('attributions.suspendre', ':id') }}".replace(':id', attributionId);
                    // `/prestataires/{{ $prestataire->id }}/lots/${attributionId}/retirer`;
                document.getElementById('modalSuspension').classList.remove('hidden');
            }

            function fermerModalSuspension() {
                document.getElementById('modalSuspension').classList.add('hidden');
                document.getElementById('formSuspension').reset();
            }

            // function confirmerSuspension(attributionId) {
            //     // if (confirm('Êtes-vous sûr de vouloir suspendre cette attribution ?')) {
            //     //     // Implémenter la logique de suspension
            //     //     window.location.href = "{{ route('attributions.suspendre', ':id') }}".replace(':id', attributionId);
            //     //     // /`/prestataires/{{ $prestataire->id }}/lots/${attributionId}/suspendre`;
            //     // }
            // }

            function confirmerReactivation(attributionId) {
                // if (confirm('Êtes-vous sûr de vouloir réactiver cette attribution ?')) {
                //     // Implémenter la logique de réactivation
                //     // window.location.href = `/prestataires/{{ $prestataire->id }}/lots/${attributionId}/reactiver`;
                // }
            }
        </script>

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
                    <p id="deleteMessage" class="text-sm text-gray-600 mb-6">
                        Êtes-vous sûr de vouloir supprimer le prestataire "{{ $prestataire->raison_sociale_prestataire }}"
                        ?
                    </p>

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
            const prestataireId = '{{ $prestataire->id_prestataire }}';

            // Toggle menu
            function toggleMenu() {
                document.getElementById('actionMenu').classList.toggle('hidden');
            }

            // Fermer menu en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#menuBtn') && !e.target.closest('#actionMenu')) {
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });

            // Toggle statut
            function toggleStatus(isActive) {
                const action = isActive ? 'désactiver' : 'activer';
                if (confirm(`Voulez-vous vraiment ${action} ce prestataire ?`)) {
                    fetch(`/prestataires/${prestataireId}/toggle-status`, {
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

            // Statistiques
            function viewStatistiques() {
                // À implémenter selon les besoins
                alert('Fonctionnalité en cours de développement');
            }

            // Imprimer
            function printPrestataire() {
                window.print();
            }

            // Confirmer suppression
            function confirmDelete() {
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Exécuter suppression
            function executeDelete() {
                fetch(`/prestataires/${prestataireId}`, {
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
                            window.location.href = '{{ route('prestataires.index') }}';
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

            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush
@endsection
