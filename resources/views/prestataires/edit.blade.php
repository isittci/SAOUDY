@extends('layouts.main')
@section('title', 'Modifier Prestataire - ' . $prestataire->raison_sociale_prestataire)
@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @can('prestataires.view-details')
                        <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-edit text-orange-500 mr-2"></i>
                            Modifier le prestataire
                        </h1>
                        <p class="text-gray-600 mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
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
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-red-700 font-medium mb-2">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Message de validation d'étape (caché par défaut) -->
        <div id="validationAlert"
            class="hidden mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg shadow-sm animate-fadeIn">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-amber-500 text-xl mr-3 mt-0.5"></i>
                <div>
                    <p class="text-amber-700 font-medium mb-2">Champs obligatoires manquants :</p>
                    <ul id="validationList" class="list-disc list-inside text-amber-600 text-sm space-y-1">
                    </ul>
                </div>
            </div>
        </div>

        @can('prestataires.update')
            <form action="{{ route('prestataires.update', $prestataire->id_prestataire) }}" method="POST"
                id="prestataireForm" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Champ caché pour le représentant légal -->
                <input type="hidden" name="representant_legal_prestataire" id="representant_legal_json">

                <!-- Étapes -->
                <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
                    <div class="flex items-center justify-center flex-wrap gap-2">
                        <button type="button" onclick="goToStep(1)" id="step1Btn"
                            class="step-btn flex items-center space-x-2 px-4 py-2 rounded-lg bg-orange-500 text-white font-medium transition-all">
                            <span
                                class="w-6 h-6 bg-white text-orange-500 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            <span class="hidden sm:inline">Informations générales</span>
                        </button>
                        <div class="w-8 h-0.5 bg-gray-300 hidden sm:block step-line" id="line1"></div>
                        <button type="button" onclick="goToStep(2)" id="step2Btn"
                            class="step-btn flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-600 font-medium transition-all">
                            <span
                                class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            <span class="hidden sm:inline">Contact & Adresse</span>
                        </button>
                        <div class="w-8 h-0.5 bg-gray-300 hidden sm:block step-line" id="line2"></div>
                        <button type="button" onclick="goToStep(3)" id="step3Btn"
                            class="step-btn flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-600 font-medium transition-all">
                            <span
                                class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                            <span class="hidden sm:inline">Représentant légal</span>
                        </button>
                    </div>

                    <!-- Barre de progression -->
                    <div class="mt-4 px-4">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div id="progressBar"
                                class="h-full bg-gradient-to-r from-orange-400 to-orange-600 rounded-full transition-all duration-500"
                                style="width: 33.33%"></div>
                        </div>
                        <p class="text-center text-sm text-gray-500 mt-2">Étape <span id="currentStepText">1</span> sur 3
                        </p>
                    </div>
                </div>

                <!-- Étape 1: Informations générales -->
                <div id="step1" class="step-content">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-building text-orange-500 mr-2"></i>
                                Informations générales
                            </h2>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Nom du prestataire -->
                            <div>
                                <label for="raison_sociale_prestataire"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nom du prestataire <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="raison_sociale_prestataire" id="raison_sociale_prestataire"
                                    value="{{ old('raison_sociale_prestataire', $prestataire->raison_sociale_prestataire) }}"
                                    required maxlength="255" data-label="Nom du prestataire"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('raison_sociale_prestataire') border-red-500 @enderror"
                                    placeholder="Ex: Société Générale de Construction SARL">
                                @error('raison_sociale_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Numéro CC -->
                                <div>
                                    <label for="numero_cc_prestataire"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        N° Compte Contribuable <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="numero_cc_prestataire" id="numero_cc_prestataire"
                                        value="{{ old('numero_cc_prestataire', $prestataire->numero_cc_prestataire) }}"
                                        required maxlength="50" data-label="N° Compte Contribuable"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_cc_prestataire') border-red-500 @enderror"
                                        placeholder="Ex: CC-123456789">
                                    @error('numero_cc_prestataire')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Numéro RCCM -->
                                <div>
                                    <label for="numero_rccm_prestataire"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        N° RCCM
                                    </label>
                                    <input type="text" name="numero_rccm_prestataire" id="numero_rccm_prestataire"
                                        value="{{ old('numero_rccm_prestataire', $prestataire->numero_rccm_prestataire) }}"
                                        maxlength="50"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_rccm_prestataire') border-red-500 @enderror"
                                        placeholder="Ex: RCCM-ABJ-2024-B-12345">
                                    @error('numero_rccm_prestataire')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Statut -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Statut</label>
                                <div class="flex items-center space-x-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="statut_prestataire" value="1"
                                            {{ old('statut_prestataire', $prestataire->statut_prestataire) == '1' || old('statut_prestataire', $prestataire->statut_prestataire) === true ? 'checked' : '' }}
                                            class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-400">
                                        <span class="ml-2 text-sm text-gray-700">Actif</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="statut_prestataire" value="0"
                                            {{ old('statut_prestataire', $prestataire->statut_prestataire) == '0' || old('statut_prestataire', $prestataire->statut_prestataire) === false ? 'checked' : '' }}
                                            class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-400">
                                        <span class="ml-2 text-sm text-gray-700">Inactif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <button type="button" onclick="nextStep(1)"
                                class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all duration-200 font-medium flex items-center space-x-2">
                                <span>Suivant</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Étape 2: Contact & Adresse -->
                <div id="step2" class="step-content hidden">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-address-book text-blue-500 mr-2"></i>
                                Contact & Adresse
                            </h2>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Téléphone principal -->
                                <div>
                                    <label for="telephone_principal_prestataire"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        Téléphone principal <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input type="tel" name="telephone_principal_prestataire"
                                            id="telephone_principal_prestataire"
                                            value="{{ old('telephone_principal_prestataire', $prestataire->telephone_principal_prestataire) }}"
                                            required maxlength="20" data-label="Téléphone principal"
                                            class="form-input w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('telephone_principal_prestataire') border-red-500 @enderror"
                                            placeholder="+225 07 XX XX XX XX">
                                    </div>
                                    @error('telephone_principal_prestataire')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Téléphone secondaire -->
                                <div>
                                    <label for="telephone_secondaire_prestataire"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        Téléphone secondaire
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-mobile-alt text-gray-400"></i>
                                        </div>
                                        <input type="tel" name="telephone_secondaire_prestataire"
                                            id="telephone_secondaire_prestataire"
                                            value="{{ old('telephone_secondaire_prestataire', $prestataire->telephone_secondaire_prestataire) }}"
                                            maxlength="20"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
                                            placeholder="+225 05 XX XX XX XX">
                                    </div>
                                </div>
                            </div>

                            <!-- Adresse -->
                            <div>
                                <label for="adresse_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Adresse <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-0 pl-3 pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <textarea name="adresse_prestataire" id="adresse_prestataire" rows="2" required data-label="Adresse"
                                        class="form-input w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all resize-none @error('adresse_prestataire') border-red-500 @enderror"
                                        placeholder="Adresse complète du siège social">{{ old('adresse_prestataire', $prestataire->adresse_prestataire) }}</textarea>
                                </div>
                                @error('adresse_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Ville -->
                                <div>
                                    <label for="ville_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Ville <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="ville_prestataire" id="ville_prestataire"
                                        value="{{ old('ville_prestataire', $prestataire->ville_prestataire) }}" required
                                        maxlength="50" data-label="Ville"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('ville_prestataire') border-red-500 @enderror"
                                        placeholder="Ex: Yamoussoukro">
                                    @error('ville_prestataire')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pays -->
                                <div>
                                    <label for="pays_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pays <span class="text-red-500">*</span>
                                    </label>
                                    <select name="pays_prestataire" id="pays_prestataire" required data-label="Pays"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('pays_prestataire') border-red-500 @enderror">
                                        <option value="">Sélectionner un pays</option>
                                        @forelse ($pays as $p)
                                            <option value="{{ $p->id }}" data-indicatif="{{ $p->indicatif }}"
                                                data-code="{{ $p->code_iso_2 }}"
                                                {{ old('pays_prestataire', $prestataire->pays_prestataire ?? '') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nom }} ({{ $p->indicatif }})
                                            </option>
                                        @empty
                                            <option value="" disabled>Aucun pays disponible</option>
                                        @endforelse
                                    </select>
                                    @error('pays_prestataire')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                            <button type="button" onclick="prevStep(2)"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 font-medium flex items-center space-x-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Précédent</span>
                            </button>
                            <button type="button" onclick="nextStep(2)"
                                class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all duration-200 font-medium flex items-center space-x-2">
                                <span>Suivant</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Étape 3: Représentant légal -->
                <div id="step3" class="step-content hidden">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-user-tie text-purple-500 mr-2"></i>
                                Représentant légal
                            </h2>
                        </div>

                        @php
                            // Récupération robuste du représentant légal actif
                            $representantActif = null;

                            // Récupérer les données du représentant légal
                            $representantsData = $prestataire->representant_legal_prestataire;

                            // Si c'est une chaîne JSON, la décoder
                            if (is_string($representantsData)) {
                                $representantsData = json_decode($representantsData, true);
                            }

                            // Si c'est un tableau valide, chercher le représentant actif
                            if (is_array($representantsData) && !empty($representantsData)) {
                                // Chercher le représentant avec statut actif (gère int, bool, string)
                                foreach ($representantsData as $rep) {
                                    $statut = $rep['statut'] ?? null;
                                    if ($statut === 1 || $statut === true || $statut === '1' || $statut === 'true') {
                                        $representantActif = $rep;
                                        break;
                                    }
                                }

                                // Si aucun représentant actif trouvé, prendre le premier
                                if (!$representantActif) {
                                    $representantActif = $representantsData[0] ?? null;
                                }
                            }
                        @endphp

                        <div class="p-6 space-y-6">
                            @if ($representantActif)
                                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                        <span class="text-blue-700 text-sm">
                                            Représentant légal actuel:
                                            <strong>{{ $representantActif['nom'] ?? '' }}</strong>
                                            - Modifier les informations ci-dessous pour mettre à jour.
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Info: Modification ou ajout -->
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-0.5"></i>
                                    <div class="text-sm text-yellow-700">
                                        <p class="font-medium">Important :</p>
                                        <p>Si vous modifiez l'email du représentant légal, un nouveau représentant sera
                                            créé et l'ancien sera désactivé (conservé dans l'historique).</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Nom -->
                            <div>
                                <label for="nom" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nom" id="nom"
                                    value="{{ old('nom', $representantActif['nom'] ?? '') }}" required maxlength="100"
                                    data-label="Nom complet"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('nom') border-red-500 @enderror"
                                    placeholder="Ex: KOUASSI Jean-Marc">
                                @error('nom')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Email représentant -->
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email
                                    </label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $representantActif['email'] ?? '') }}" maxlength="255"
                                        data-label="Email du représentant"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                                        placeholder="representant@email.com">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Contact -->
                                <div>
                                    <label for="contact" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Contact <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" name="contact" id="contact"
                                        value="{{ old('contact', $representantActif['contact'] ?? '') }}" required
                                        maxlength="20" data-label="Contact du représentant"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('contact') border-red-500 @enderror"
                                        placeholder="+225 XX XX XX XX XX">
                                    @error('contact')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nationalité -->
                                <div>
                                    <label for="nationalite" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nationalité
                                    </label>
                                    <input type="text" name="nationalite" id="nationalite"
                                        value="{{ old('nationalite', $representantActif['nationalite'] ?? '') }}"
                                        maxlength="50" data-label="Nationalité"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('nationalite') border-red-500 @enderror"
                                        placeholder="Ex: Ivoirienne">
                                    @error('nationalite')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pays -->
                                <div>
                                    <label for="pays" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pays de résidence <span class="text-red-500">*</span>
                                    </label>
                                    <select name="pays" id="pays" required data-label="Pays de résidence"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('pays') border-red-500 @enderror">
                                        <option value="">Sélectionner un pays</option>
                                        @forelse ($pays as $p)
                                            <option value="{{ $p->id }}" data-indicatif="{{ $p->indicatif }}"
                                                data-code="{{ $p->code_iso_2 }}"
                                                {{ old('pays', $representantActif['pays'] ?? '') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nom }} ({{ $p->indicatif }})
                                            </option>
                                        @empty
                                            <option value="" disabled>Aucun pays disponible</option>
                                        @endforelse
                                    </select>
                                    @error('pays')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Adresse -->
                            <div>
                                <label for="adresse" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Adresse
                                </label>
                                <input type="text" name="adresse" id="adresse"
                                    value="{{ old('adresse', $representantActif['adresse'] ?? '') }}" maxlength="255"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('adresse') border-red-500 @enderror"
                                    placeholder="Adresse du représentant légal">
                                @error('adresse')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Profession -->
                            <div>
                                <label for="profession" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Profession <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="profession" id="profession"
                                    value="{{ old('profession', $representantActif['profession'] ?? '') }}" required
                                    maxlength="100" data-label="Profession"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('profession') border-red-500 @enderror"
                                    placeholder="Ex: Gérant, Directeur Général">
                                @error('profession')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Date de naissance -->
                                <div>
                                    <label for="date_naissance" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de naissance
                                    </label>
                                    <input type="date" name="date_naissance" id="date_naissance"
                                        value="{{ old('date_naissance', $representantActif['date_naissance'] ?? '') }}"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('date_naissance') border-red-500 @enderror">
                                    @error('date_naissance')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Lieu de naissance -->
                                <div>
                                    <label for="lieu_naissance" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Lieu de naissance
                                    </label>
                                    <input type="text" name="lieu_naissance" id="lieu_naissance"
                                        value="{{ old('lieu_naissance', $representantActif['lieu_naissance'] ?? '') }}"
                                        maxlength="100"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('lieu_naissance') border-red-500 @enderror"
                                        placeholder="Ex: Abidjan">
                                    @error('lieu_naissance')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pièce d'identité -->
                            <div class="p-4 bg-gray-50 rounded-lg space-y-4">
                                <h4 class="font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-id-card text-gray-500 mr-2"></i>
                                    Pièce d'identité
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Type pièce -->
                                    <div>
                                        <label for="type_piece_identite"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Type
                                        </label>
                                        <select name="type_piece_identite" id="type_piece_identite"
                                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('type_piece_identite') border-red-500 @enderror">
                                            <option value="">Sélectionner</option>
                                            <option value="CNI"
                                                {{ old('type_piece_identite', $representantActif['type_piece_identite'] ?? '') == 'CNI' ? 'selected' : '' }}>
                                                CNI</option>
                                            <option value="Passeport"
                                                {{ old('type_piece_identite', $representantActif['type_piece_identite'] ?? '') == 'Passeport' ? 'selected' : '' }}>
                                                Passeport</option>
                                            <option value="Carte Consulaire"
                                                {{ old('type_piece_identite', $representantActif['type_piece_identite'] ?? '') == 'Carte Consulaire' ? 'selected' : '' }}>
                                                Carte Consulaire</option>
                                            <option value="Attestation"
                                                {{ old('type_piece_identite', $representantActif['type_piece_identite'] ?? '') == 'Attestation' ? 'selected' : '' }}>
                                                Attestation d'identité</option>
                                        </select>
                                        @error('type_piece_identite')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Numéro pièce -->
                                    <div>
                                        <label for="numero_piece_identite"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Numéro
                                        </label>
                                        <input type="text" name="numero_piece_identite" id="numero_piece_identite"
                                            value="{{ old('numero_piece_identite', $representantActif['numero_piece_identite'] ?? '') }}"
                                            maxlength="50"
                                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('numero_piece_identite') border-red-500 @enderror"
                                            placeholder="Numéro de la pièce">
                                        @error('numero_piece_identite')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Date délivrance -->
                                    <div>
                                        <label for="date_delivrance"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Date de délivrance
                                        </label>
                                        <input type="date" name="date_delivrance" id="date_delivrance"
                                            value="{{ old('date_delivrance', $representantActif['date_delivrance'] ?? '') }}"
                                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('date_delivrance') border-red-500 @enderror">
                                        @error('date_delivrance')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Lieu délivrance -->
                                    <div>
                                        <label for="lieu_delivrance"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Lieu de délivrance
                                        </label>
                                        <input type="text" name="lieu_delivrance" id="lieu_delivrance"
                                            value="{{ old('lieu_delivrance', $representantActif['lieu_delivrance'] ?? '') }}"
                                            maxlength="100"
                                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('lieu_delivrance') border-red-500 @enderror"
                                            placeholder="Ex: Abidjan">
                                        @error('lieu_delivrance')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Date expiration -->
                                    <div>
                                        <label for="date_expiration"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Date d'expiration
                                        </label>
                                        <input type="date" name="date_expiration" id="date_expiration"
                                            value="{{ old('date_expiration', $representantActif['date_expiration'] ?? '') }}"
                                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('date_expiration') border-red-500 @enderror">
                                        @error('date_expiration')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                            <button type="button" onclick="prevStep(3)"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 font-medium flex items-center space-x-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Précédent</span>
                            </button>
                            <button type="submit" id="submitBtn"
                                class="px-8 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium flex items-center space-x-2 shadow-md hover:shadow-lg">
                                <i class="fas fa-save"></i>
                                <span>Enregistrer les modifications</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endcan
    </main>

    @can('prestataires.update')
        @push('scripts')
            <script>
                let currentStep = 1;
                const totalSteps = 3;

                // Définition des champs obligatoires par étape
                const requiredFieldsByStep = {
                    1: [
                        'raison_sociale_prestataire',
                        'numero_cc_prestataire',
                    ],
                    2: [
                        'telephone_principal_prestataire',
                        'adresse_prestataire',
                        'ville_prestataire',
                        'pays_prestataire'
                    ],
                    3: [
                        'nom',
                        'contact',
                        'pays',
                        'profession'
                    ]
                };

                // Fonction de validation d'une étape
                function validateStep(step) {
                    const fields = requiredFieldsByStep[step] || [];
                    const errors = [];

                    fields.forEach(fieldName => {
                        const field = document.querySelector(`[name="${fieldName}"]`);
                        if (field) {
                            const value = field.value.trim();
                            if (!value) {
                                const label = field.getAttribute('data-label') || fieldName;
                                errors.push(label);
                                field.classList.add('border-red-500', 'bg-red-50');
                                field.classList.remove('border-gray-300');
                            } else {
                                field.classList.remove('border-red-500', 'bg-red-50');
                                field.classList.add('border-gray-300');
                            }
                        }
                    });

                    return errors;
                }

                // Afficher les erreurs de validation
                function showValidationErrors(errors) {
                    const alertDiv = document.getElementById('validationAlert');
                    const listDiv = document.getElementById('validationList');

                    if (errors.length > 0) {
                        listDiv.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
                        alertDiv.classList.remove('hidden');
                        alertDiv.classList.add('animate-shake');
                        setTimeout(() => alertDiv.classList.remove('animate-shake'), 500);

                        // Scroll vers l'alerte
                        alertDiv.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    } else {
                        alertDiv.classList.add('hidden');
                    }
                }

                // Passer à l'étape suivante
                function nextStep(currentStepNum) {
                    const errors = validateStep(currentStepNum);

                    if (errors.length > 0) {
                        showValidationErrors(errors);
                        return;
                    }

                    document.getElementById('validationAlert').classList.add('hidden');
                    showStep(currentStepNum + 1);
                }

                // Revenir à l'étape précédente
                function prevStep(currentStepNum) {
                    document.getElementById('validationAlert').classList.add('hidden');
                    showStep(currentStepNum - 1);
                }

                // Aller directement à une étape (depuis les boutons d'étape)
                function goToStep(targetStep) {
                    // On peut toujours revenir en arrière
                    if (targetStep < currentStep) {
                        document.getElementById('validationAlert').classList.add('hidden');
                        showStep(targetStep);
                        return;
                    }

                    // Pour avancer, valider les étapes intermédiaires
                    for (let step = currentStep; step < targetStep; step++) {
                        const errors = validateStep(step);
                        if (errors.length > 0) {
                            showValidationErrors(errors);
                            showStep(step);
                            return;
                        }
                    }

                    document.getElementById('validationAlert').classList.add('hidden');
                    showStep(targetStep);
                }

                // Afficher une étape spécifique
                function showStep(step) {
                    // Masquer toutes les étapes
                    document.querySelectorAll('.step-content').forEach(el => {
                        el.classList.add('hidden');
                    });

                    // Afficher l'étape cible
                    document.getElementById(`step${step}`).classList.remove('hidden');

                    // Mettre à jour les boutons d'étape
                    for (let i = 1; i <= totalSteps; i++) {
                        const btn = document.getElementById(`step${i}Btn`);
                        const numSpan = btn.querySelector('span:first-child');

                        if (i < step) {
                            // Étape complétée
                            btn.classList.remove('bg-orange-500', 'text-white', 'bg-gray-100', 'text-gray-600');
                            btn.classList.add('bg-green-100', 'text-green-700');
                            numSpan.classList.remove('bg-white', 'text-orange-500', 'bg-gray-300');
                            numSpan.classList.add('bg-green-500', 'text-white');
                            numSpan.innerHTML = '<i class="fas fa-check text-xs"></i>';
                        } else if (i === step) {
                            // Étape courante
                            btn.classList.remove('bg-gray-100', 'text-gray-600', 'bg-green-100', 'text-green-700');
                            btn.classList.add('bg-orange-500', 'text-white');
                            numSpan.classList.remove('bg-gray-300', 'bg-green-500');
                            numSpan.classList.add('bg-white', 'text-orange-500');
                            numSpan.innerHTML = i;
                        } else {
                            // Étape future
                            btn.classList.remove('bg-orange-500', 'text-white', 'bg-green-100', 'text-green-700');
                            btn.classList.add('bg-gray-100', 'text-gray-600');
                            numSpan.classList.remove('bg-white', 'text-orange-500', 'bg-green-500');
                            numSpan.classList.add('bg-gray-300', 'text-white');
                            numSpan.innerHTML = i;
                        }
                    }

                    // Mettre à jour la barre de progression
                    const progress = (step / totalSteps) * 100;
                    document.getElementById('progressBar').style.width = `${progress}%`;
                    document.getElementById('currentStepText').textContent = step;

                    // Mettre à jour les lignes de connexion
                    for (let i = 1; i < totalSteps; i++) {
                        const line = document.getElementById(`line${i}`);
                        if (line) {
                            if (i < step) {
                                line.classList.remove('bg-gray-300');
                                line.classList.add('bg-green-500');
                            } else {
                                line.classList.remove('bg-green-500');
                                line.classList.add('bg-gray-300');
                            }
                        }
                    }

                    currentStep = step;

                    // Scroll vers le haut
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }

                // Validation en temps réel des champs
                document.querySelectorAll('.form-input').forEach(field => {
                    field.addEventListener('input', function() {
                        if (this.value.trim()) {
                            this.classList.remove('border-red-500', 'bg-red-50');
                            this.classList.add('border-gray-300');
                        }
                    });

                    field.addEventListener('blur', function() {
                        if (this.hasAttribute('required') && !this.value.trim()) {
                            this.classList.add('border-red-500', 'bg-red-50');
                            this.classList.remove('border-gray-300');
                        }
                    });
                });

                // Validation date expiration > date délivrance
                document.getElementById('date_expiration').addEventListener('change', function() {
                    const dateDelivrance = document.getElementById('date_delivrance').value;
                    const dateExpiration = this.value;

                    if (dateDelivrance && dateExpiration && new Date(dateExpiration) <= new Date(dateDelivrance)) {
                        alert('La date d\'expiration doit être postérieure à la date de délivrance');
                        this.value = '';
                        this.classList.add('border-red-500', 'bg-red-50');
                    }
                });

                // Validation date de naissance (doit être majeur)
                document.getElementById('date_naissance').addEventListener('change', function() {
                    const dateNaissance = new Date(this.value);
                    const today = new Date();
                    const age = Math.floor((today - dateNaissance) / (365.25 * 24 * 60 * 60 * 1000));

                    if (age < 18) {
                        alert('Le représentant légal doit être majeur (18 ans minimum)');
                        this.value = '';
                        this.classList.add('border-red-500', 'bg-red-50');
                    }
                });

                // Préparation des données du représentant légal avant soumission
                function prepareRepresentantData() {
                    const representant = {
                        nom: document.getElementById('nom')?.value || '',
                        email: document.getElementById('email')?.value || '',
                        contact: document.getElementById('contact')?.value || '',
                        nationalite: document.getElementById('nationalite')?.value || '',
                        pays: document.getElementById('pays')?.value || '',
                        adresse: document.getElementById('adresse')?.value || '',
                        profession: document.getElementById('profession')?.value || '',
                        date_naissance: document.getElementById('date_naissance')?.value || '',
                        lieu_naissance: document.getElementById('lieu_naissance')?.value || '',
                        type_piece_identite: document.getElementById('type_piece_identite')?.value || '',
                        numero_piece_identite: document.getElementById('numero_piece_identite')?.value || '',
                        date_delivrance: document.getElementById('date_delivrance')?.value || '',
                        lieu_delivrance: document.getElementById('lieu_delivrance')?.value || '',
                        date_expiration: document.getElementById('date_expiration')?.value || ''
                    };

                    document.getElementById('representant_legal_json').value = JSON.stringify(representant);
                }

                // Soumission du formulaire avec validation finale
                document.getElementById('prestataireForm').addEventListener('submit', function(e) {
                    // Valider toutes les étapes avant soumission
                    for (let step = 1; step <= totalSteps; step++) {
                        const errors = validateStep(step);
                        if (errors.length > 0) {
                            e.preventDefault();
                            showValidationErrors(errors);
                            showStep(step);
                            return;
                        }
                    }

                    // Préparer les données du représentant légal
                    prepareRepresentantData();

                    const submitBtn = document.getElementById('submitBtn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement en cours...';
                });

                // Initialisation au chargement
                document.addEventListener('DOMContentLoaded', function() {
                    // Réinitialiser l'affichage des numéros d'étape
                    for (let i = 1; i <= totalSteps; i++) {
                        const btn = document.getElementById(`step${i}Btn`);
                        const numSpan = btn.querySelector('span:first-child');
                        if (i > 1) {
                            numSpan.innerHTML = i;
                        }
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

                @keyframes shake {

                    0%,
                    100% {
                        transform: translateX(0);
                    }

                    10%,
                    30%,
                    50%,
                    70%,
                    90% {
                        transform: translateX(-5px);
                    }

                    20%,
                    40%,
                    60%,
                    80% {
                        transform: translateX(5px);
                    }
                }

                .animate-fadeIn {
                    animation: fadeIn 0.3s ease-out;
                }

                .animate-shake {
                    animation: shake 0.5s ease-in-out;
                }

                .step-content {
                    animation: fadeIn 0.3s ease-out;
                }

                /* Transition pour les champs invalides */
                .form-input {
                    transition: all 0.3s ease;
                }

                .form-input.border-red-500 {
                    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
                }

                /* Style pour les étapes complétées */
                .step-btn.bg-green-100 {
                    position: relative;
                }

                .step-btn.bg-green-100::after {
                    content: '';
                    position: absolute;
                    bottom: -2px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 80%;
                    height: 2px;
                    background: linear-gradient(90deg, transparent, #22c55e, transparent);
                }
            </style>
        @endpush
    @endcan
@endsection
