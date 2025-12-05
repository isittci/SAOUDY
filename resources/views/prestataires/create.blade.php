@extends('layouts.main')
@section('title', 'Nouveau Prestataire')
@section('breadcrumb')
    <a href="{{ route('prestataires.index') }}" class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouveau</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('prestataires.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-plus-circle text-orange-500 mr-2"></i>
                        Nouveau Prestataire
                    </h1>
                    <p class="text-gray-600 mt-1">Remplissez les informations du prestataire</p>
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

        <form action="{{ route('prestataires.store') }}" method="POST" id="prestataireForm" class="space-y-6">
            @csrf

            <!-- Étapes -->
            <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
                <div class="flex items-center justify-center flex-wrap gap-2">
                    <button type="button" onclick="showStep(1)" id="step1Btn"
                        class="step-btn flex items-center space-x-2 px-4 py-2 rounded-lg bg-orange-500 text-white font-medium transition-all">
                        <span class="w-6 h-6 bg-white text-orange-500 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        <span class="hidden sm:inline">Informations générales</span>
                    </button>
                    <div class="w-8 h-0.5 bg-gray-300 hidden sm:block"></div>
                    <button type="button" onclick="showStep(2)" id="step2Btn"
                        class="step-btn flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-600 font-medium transition-all">
                        <span class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        <span class="hidden sm:inline">Contact & Adresse</span>
                    </button>
                    <div class="w-8 h-0.5 bg-gray-300 hidden sm:block"></div>
                    <button type="button" onclick="showStep(3)" id="step3Btn"
                        class="step-btn flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-600 font-medium transition-all">
                        <span class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                        <span class="hidden sm:inline">Représentant légal</span>
                    </button>
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
                        <!-- Raison sociale -->
                        <div>
                            <label for="raison_sociale_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                Raison sociale <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="raison_sociale_prestataire" id="raison_sociale_prestataire"
                                value="{{ old('raison_sociale_prestataire') }}" required maxlength="255"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('raison_sociale_prestataire') border-red-500 @enderror"
                                placeholder="Ex: Société Générale de Construction SARL">
                            @error('raison_sociale_prestataire')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Numéro d'identification -->
                        <div>
                            <label for="numero_identification_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                Numéro d'identification <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="numero_identification_prestataire" id="numero_identification_prestataire"
                                value="{{ old('numero_identification_prestataire') }}" required maxlength="25"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_identification_prestataire') border-red-500 @enderror"
                                placeholder="Ex: CI-ABJ-2024-001">
                            @error('numero_identification_prestataire')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Numéro CC -->
                            <div>
                                <label for="numero_cc_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    N° Carte de Contribuable <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="numero_cc_prestataire" id="numero_cc_prestataire"
                                    value="{{ old('numero_cc_prestataire') }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_cc_prestataire') border-red-500 @enderror"
                                    placeholder="Ex: CC-123456789">
                                @error('numero_cc_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Numéro RCCM -->
                            <div>
                                <label for="numero_rccm_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    N° RCCM <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="numero_rccm_prestataire" id="numero_rccm_prestataire"
                                    value="{{ old('numero_rccm_prestataire') }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_rccm_prestataire') border-red-500 @enderror"
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
                                        {{ old('statut_prestataire', '1') == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-400">
                                    <span class="ml-2 text-sm text-gray-700">Actif</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="statut_prestataire" value="0"
                                        {{ old('statut_prestataire') == '0' ? 'checked' : '' }}
                                        class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-400">
                                    <span class="ml-2 text-sm text-gray-700">Inactif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button type="button" onclick="showStep(2)"
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
                        <!-- Email -->
                        <div>
                            <label for="email_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="email_prestataire" id="email_prestataire"
                                    value="{{ old('email_prestataire') }}" required maxlength="255"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('email_prestataire') border-red-500 @enderror"
                                    placeholder="contact@entreprise.com">
                            </div>
                            @error('email_prestataire')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Téléphone principal -->
                            <div>
                                <label for="telephone_principal_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Téléphone principal <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="telephone_principal_prestataire" id="telephone_principal_prestataire"
                                        value="{{ old('telephone_principal_prestataire') }}" required maxlength="20"
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('telephone_principal_prestataire') border-red-500 @enderror"
                                        placeholder="+225 07 XX XX XX XX">
                                </div>
                                @error('telephone_principal_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone secondaire -->
                            <div>
                                <label for="telephone_secondaire_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Téléphone secondaire
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-mobile-alt text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="telephone_secondaire_prestataire" id="telephone_secondaire_prestataire"
                                        value="{{ old('telephone_secondaire_prestataire') }}" maxlength="20"
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
                                <textarea name="adresse_prestataire" id="adresse_prestataire" rows="2" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all resize-none @error('adresse_prestataire') border-red-500 @enderror"
                                    placeholder="Adresse complète du siège social">{{ old('adresse_prestataire') }}</textarea>
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
                                    value="{{ old('ville_prestataire') }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('ville_prestataire') border-red-500 @enderror"
                                    placeholder="Ex: Abidjan">
                                @error('ville_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pays -->
                            <div>
                                <label for="pays_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Pays <span class="text-red-500">*</span>
                                </label>
                                <select name="pays_prestataire" id="pays_prestataire" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('pays_prestataire') border-red-500 @enderror">
                                    <option value="">Sélectionner un pays</option>
                                    <option value="Côte d'Ivoire" {{ old('pays_prestataire') == "Côte d'Ivoire" ? 'selected' : '' }}>Côte d'Ivoire</option>
                                    <option value="Sénégal" {{ old('pays_prestataire') == "Sénégal" ? 'selected' : '' }}>Sénégal</option>
                                    <option value="Mali" {{ old('pays_prestataire') == "Mali" ? 'selected' : '' }}>Mali</option>
                                    <option value="Burkina Faso" {{ old('pays_prestataire') == "Burkina Faso" ? 'selected' : '' }}>Burkina Faso</option>
                                    <option value="Guinée" {{ old('pays_prestataire') == "Guinée" ? 'selected' : '' }}>Guinée</option>
                                    <option value="Togo" {{ old('pays_prestataire') == "Togo" ? 'selected' : '' }}>Togo</option>
                                    <option value="Bénin" {{ old('pays_prestataire') == "Bénin" ? 'selected' : '' }}>Bénin</option>
                                    <option value="Niger" {{ old('pays_prestataire') == "Niger" ? 'selected' : '' }}>Niger</option>
                                    <option value="Cameroun" {{ old('pays_prestataire') == "Cameroun" ? 'selected' : '' }}>Cameroun</option>
                                    <option value="Gabon" {{ old('pays_prestataire') == "Gabon" ? 'selected' : '' }}>Gabon</option>
                                    <option value="France" {{ old('pays_prestataire') == "France" ? 'selected' : '' }}>France</option>
                                    <option value="Autre" {{ old('pays_prestataire') == "Autre" ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('pays_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                        <button type="button" onclick="showStep(1)"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 font-medium flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Précédent</span>
                        </button>
                        <button type="button" onclick="showStep(3)"
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

                    <div class="p-6 space-y-6">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nom" id="nom"
                                value="{{ old('nom') }}" required maxlength="100"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('nom') border-red-500 @enderror"
                                placeholder="Ex: KOUASSI Jean-Marc">
                            @error('nom')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email représentant -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email') }}" required maxlength="255"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
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
                                    value="{{ old('contact') }}" required maxlength="20"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('contact') border-red-500 @enderror"
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
                                    Nationalité <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nationalite" id="nationalite"
                                    value="{{ old('nationalite') }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('nationalite') border-red-500 @enderror"
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
                                <input type="text" name="pays" id="pays"
                                    value="{{ old('pays') }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('pays') border-red-500 @enderror"
                                    placeholder="Ex: Côte d'Ivoire">
                                @error('pays')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label for="adresse" class="block text-sm font-semibold text-gray-700 mb-2">
                                Adresse <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="adresse" id="adresse"
                                value="{{ old('adresse') }}" required maxlength="255"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('adresse') border-red-500 @enderror"
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
                                value="{{ old('profession') }}" required maxlength="100"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('profession') border-red-500 @enderror"
                                placeholder="Ex: Gérant, Directeur Général">
                            @error('profession')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date de naissance -->
                            <div>
                                <label for="date_naissance" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date de naissance <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date_naissance" id="date_naissance"
                                    value="{{ old('date_naissance') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('date_naissance') border-red-500 @enderror">
                                @error('date_naissance')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lieu de naissance -->
                            <div>
                                <label for="lieu_naissance" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Lieu de naissance <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="lieu_naissance" id="lieu_naissance"
                                    value="{{ old('lieu_naissance') }}" required maxlength="100"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('lieu_naissance') border-red-500 @enderror"
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
                                    <label for="type_piece_identite" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="type_piece_identite" id="type_piece_identite" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('type_piece_identite') border-red-500 @enderror">
                                        <option value="">Sélectionner</option>
                                        <option value="CNI" {{ old('type_piece_identite') == "CNI" ? 'selected' : '' }}>CNI</option>
                                        <option value="Passeport" {{ old('type_piece_identite') == "Passeport" ? 'selected' : '' }}>Passeport</option>
                                        <option value="Carte Consulaire" {{ old('type_piece_identite') == "Carte Consulaire" ? 'selected' : '' }}>Carte Consulaire</option>
                                        <option value="Attestation" {{ old('type_piece_identite') == "Attestation" ? 'selected' : '' }}>Attestation d'identité</option>
                                    </select>
                                    @error('type_piece_identite')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Numéro pièce -->
                                <div>
                                    <label for="numero_piece_identite" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="numero_piece_identite" id="numero_piece_identite"
                                        value="{{ old('numero_piece_identite') }}" required maxlength="50"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('numero_piece_identite') border-red-500 @enderror"
                                        placeholder="Numéro de la pièce">
                                    @error('numero_piece_identite')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Date délivrance -->
                                <div>
                                    <label for="date_delivrance" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de délivrance <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_delivrance" id="date_delivrance"
                                        value="{{ old('date_delivrance') }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('date_delivrance') border-red-500 @enderror">
                                    @error('date_delivrance')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Lieu délivrance -->
                                <div>
                                    <label for="lieu_delivrance" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Lieu de délivrance <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="lieu_delivrance" id="lieu_delivrance"
                                        value="{{ old('lieu_delivrance') }}" required maxlength="100"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('lieu_delivrance') border-red-500 @enderror"
                                        placeholder="Ex: Abidjan">
                                    @error('lieu_delivrance')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date expiration -->
                                <div>
                                    <label for="date_expiration" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date d'expiration <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_expiration" id="date_expiration"
                                        value="{{ old('date_expiration') }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all @error('date_expiration') border-red-500 @enderror">
                                    @error('date_expiration')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                        <button type="button" onclick="showStep(2)"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 font-medium flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Précédent</span>
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-8 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 font-medium flex items-center space-x-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-save"></i>
                            <span>Enregistrer le prestataire</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    @push('scripts')
        <script>
            let currentStep = 1;

            function showStep(step) {
                // Masquer toutes les étapes
                document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));

                // Afficher l'étape sélectionnée
                document.getElementById(`step${step}`).classList.remove('hidden');

                // Mettre à jour les boutons d'étape
                for (let i = 1; i <= 3; i++) {
                    const btn = document.getElementById(`step${i}Btn`);
                    const numSpan = btn.querySelector('span:first-child');

                    if (i === step) {
                        btn.classList.remove('bg-gray-100', 'text-gray-600');
                        btn.classList.add('bg-orange-500', 'text-white');
                        numSpan.classList.remove('bg-gray-300', 'text-white');
                        numSpan.classList.add('bg-white', 'text-orange-500');
                    } else if (i < step) {
                        btn.classList.remove('bg-gray-100', 'text-gray-600', 'bg-orange-500', 'text-white');
                        btn.classList.add('bg-green-100', 'text-green-700');
                        numSpan.classList.remove('bg-gray-300', 'text-white', 'bg-white', 'text-orange-500');
                        numSpan.classList.add('bg-green-500', 'text-white');
                    } else {
                        btn.classList.remove('bg-orange-500', 'text-white', 'bg-green-100', 'text-green-700');
                        btn.classList.add('bg-gray-100', 'text-gray-600');
                        numSpan.classList.remove('bg-white', 'text-orange-500', 'bg-green-500');
                        numSpan.classList.add('bg-gray-300', 'text-white');
                    }
                }

                currentStep = step;

                // Scroll vers le haut
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // Validation date expiration > date délivrance
            document.getElementById('date_expiration').addEventListener('change', function() {
                const dateDelivrance = document.getElementById('date_delivrance').value;
                const dateExpiration = this.value;

                if (dateDelivrance && dateExpiration && new Date(dateExpiration) <= new Date(dateDelivrance)) {
                    alert('La date d\'expiration doit être postérieure à la date de délivrance');
                    this.value = '';
                }
            });

            // Soumission du formulaire
            document.getElementById('prestataireForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement en cours...';
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

            .step-content {
                animation: fadeIn 0.3s ease-out;
            }
        </style>
    @endpush
@endsection
