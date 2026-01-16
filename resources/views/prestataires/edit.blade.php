@extends('layouts.main')
@section('title', 'Modifier Prestataire - ' . $prestataire->raison_sociale_prestataire)
@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}</a>
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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Actif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
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

        @can('prestataires.update')
        <form action="{{ route('prestataires.update', $prestataire->id_prestataire) }}" method="POST" id="prestataireForm" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Navigation par onglets -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                        <button type="button" onclick="showTab('general')" id="tabGeneral"
                            class="tab-btn whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm border-orange-500 text-orange-600">
                            <i class="fas fa-building mr-2"></i>Informations générales
                        </button>
                        <button type="button" onclick="showTab('contact')" id="tabContact"
                            class="tab-btn whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-address-book mr-2"></i>Contact & Adresse
                        </button>
                        <button type="button" onclick="showTab('representant')" id="tabRepresentant"
                            class="tab-btn whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-user-tie mr-2"></i>Représentant légal
                        </button>
                    </nav>
                </div>

                <!-- Contenu des onglets -->

                <!-- Onglet Informations générales -->
                <div id="contentGeneral" class="tab-content p-6">
                    <div class="space-y-6">
                        <!-- Raison sociale -->
                        <div>
                            <label for="raison_sociale_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                Raison sociale <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="raison_sociale_prestataire" id="raison_sociale_prestataire"
                                value="{{ old('raison_sociale_prestataire', $prestataire->raison_sociale_prestataire) }}" required maxlength="255"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('raison_sociale_prestataire') border-red-500 @enderror">
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
                                value="{{ old('numero_identification_prestataire', $prestataire->numero_identification_prestataire) }}" required maxlength="25"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_identification_prestataire') border-red-500 @enderror">
                            @error('numero_identification_prestataire')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Numéro CC -->
                            <div>
                                <label for="numero_cc_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    N° Compte Contribuable <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="numero_cc_prestataire" id="numero_cc_prestataire"
                                    value="{{ old('numero_cc_prestataire', $prestataire->numero_cc_prestataire) }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_cc_prestataire') border-red-500 @enderror">
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
                                    value="{{ old('numero_rccm_prestataire', $prestataire->numero_rccm_prestataire) }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('numero_rccm_prestataire') border-red-500 @enderror">
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
                </div>

                <!-- Onglet Contact & Adresse -->
                <div id="contentContact" class="tab-content p-6 hidden">
                    <div class="space-y-6">
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
                                    value="{{ old('email_prestataire', $prestataire->email_prestataire) }}" required maxlength="255"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('email_prestataire') border-red-500 @enderror">
                            </div>
                            @error('email_prestataire')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Téléphone principal -->
                            <div>
                                <label for="telephone_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Téléphone principal <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="telephone_prestataire" id="telephone_prestataire"
                                        value="{{ old('telephone_prestataire', $prestataire->telephone_principal_prestataire ?? $prestataire->telephone_prestataire) }}" required maxlength="20"
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('telephone_prestataire') border-red-500 @enderror">
                                </div>
                                @error('telephone_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact secondaire -->
                        <div>
                            <label for="contact_secondaire_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                Téléphone secondaire
                            </label>
                            <input type="text" name="contact_secondaire_prestataire" id="contact_secondaire_prestataire"
                                value="{{ old('contact_secondaire_prestataire', $prestataire->telephone_secondaire_prestataire) }}" maxlength="20"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
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
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all resize-none @error('adresse_prestataire') border-red-500 @enderror">{{ old('adresse_prestataire', $prestataire->adresse_prestataire) }}</textarea>
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
                                    value="{{ old('ville_prestataire', $prestataire->ville_prestataire) }}" required maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('ville_prestataire') border-red-500 @enderror">
                                @error('ville_prestataire')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>





                                 <!-- Pays -->
                                <div>
                                    <label for="pays_prestataire" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pays
                                    </label>
                                    <select name="pays_prestataire" id="pays_prestataire"
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
                </div>

                <!-- Onglet Représentant légal -->
                <div id="contentRepresentant" class="tab-content p-6 hidden">
                    @php
                        $representants = json_decode($prestataire->representant_legal_prestataire, true) ?? [];
                        $representantActif = collect($representants)->firstWhere('statut', 1);
                    @endphp

                    @if($representantActif)
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <span class="text-blue-700 text-sm">
                                    Représentant légal actuel: <strong>{{ $representantActif['nom'] ?? '' }} {{ $representantActif['prenoms'] ?? '' }}</strong>
                                    - Modifier les informations ci-dessous pour mettre à jour.
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <!-- Info: Modification ou ajout -->
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-0.5"></i>
                                <div class="text-sm text-yellow-700">
                                    <p class="font-medium">Important :</p>
                                    <p>Si vous modifiez l'email du représentant légal, un nouveau représentant sera créé et l'ancien sera désactivé (conservé dans l'historique).</p>
                                </div>
                            </div>
                        </div>

                        <!-- Représentant légal en JSON -->
                        <input type="hidden" name="representant_legal_prestataire" id="representant_legal_json">

                        <!-- Nom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="rep_nom" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nom <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="rep_nom"
                                    value="{{ $representantActif['nom'] ?? '' }}" maxlength="100"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="rep_prenoms" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Prénoms
                                </label>
                                <input type="text" id="rep_prenoms"
                                    value="{{ $representantActif['prenoms'] ?? '' }}" maxlength="150"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label for="rep_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="rep_email"
                                    value="{{ $representantActif['email'] ?? '' }}" maxlength="255"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                            </div>

                            <!-- Contact -->
                            <div>
                                <label for="rep_contact" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Contact <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="rep_contact"
                                    value="{{ $representantActif['contact'] ?? '' }}" maxlength="20"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nationalité -->
                            <div>
                                <label for="rep_nationalite" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nationalité <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="rep_nationalite"
                                    value="{{ $representantActif['nationalite'] ?? '' }}" maxlength="50"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                            </div>

{{-- {{ dd($representantActif['pays']) }} --}}

                             <!-- Pays -->
                                <div>
                                    <label for="pays" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pays de résidence <span class="text-red-500">*</span>
                                    </label>
                                    <select name="pays" id="pays"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('pays') border-red-500 @enderror">
                                        <option value="">Sélectionner un pays</option>
                                        @forelse ($pays as $p)
                                            <option value="{{ $p->id }}" data-indicatif="{{ $p->indicatif }}"
                                                data-code="{{ $p->code_iso_2 }}"
                                                {{ old('pays', $representantActif['pays']) == $p->id ? 'selected' : '' }}>
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
                            <label for="rep_adresse" class="block text-sm font-semibold text-gray-700 mb-2">
                                Adresse <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="rep_adresse"
                                value="{{ $representantActif['adresse'] ?? '' }}" maxlength="255"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                        </div>

                        <!-- Profession -->
                        <div>
                            <label for="rep_profession" class="block text-sm font-semibold text-gray-700 mb-2">
                                Profession <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="rep_profession"
                                value="{{ $representantActif['profession'] ?? '' }}" maxlength="100"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date de naissance -->
                            <div>
                                <label for="rep_date_naissance" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date de naissance
                                </label>
                                <input type="date" id="rep_date_naissance"
                                    value="{{ $representantActif['date_naissance'] ?? '' }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                            </div>

                            <!-- Lieu de naissance -->
                            <div>
                                <label for="rep_lieu_naissance" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Lieu de naissance
                                </label>
                                <input type="text" id="rep_lieu_naissance"
                                    value="{{ $representantActif['lieu_naissance'] ?? '' }}" maxlength="100"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
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
                                    <label for="rep_type_piece" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Type
                                    </label>
                                    <select id="rep_type_piece"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                                        <option value="">Sélectionner</option>
                                        <option value="CNI" {{ ($representantActif['type_piece_identite'] ?? '') == 'CNI' ? 'selected' : '' }}>CNI</option>
                                        <option value="Passeport" {{ ($representantActif['type_piece_identite'] ?? '') == 'Passeport' ? 'selected' : '' }}>Passeport</option>
                                        <option value="Carte Consulaire" {{ ($representantActif['type_piece_identite'] ?? '') == 'Carte Consulaire' ? 'selected' : '' }}>Carte Consulaire</option>
                                        <option value="Attestation" {{ ($representantActif['type_piece_identite'] ?? '') == 'Attestation' ? 'selected' : '' }}>Attestation d'identité</option>
                                    </select>
                                </div>

                                <!-- Numéro pièce -->
                                <div>
                                    <label for="rep_numero_piece" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro
                                    </label>
                                    <input type="text" id="rep_numero_piece"
                                        value="{{ $representantActif['numero_piece_identite'] ?? '' }}" maxlength="50"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Date délivrance -->
                                <div>
                                    <label for="rep_date_delivrance" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de délivrance
                                    </label>
                                    <input type="date" id="rep_date_delivrance"
                                        value="{{ $representantActif['date_delivrance'] ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                                </div>

                                <!-- Lieu délivrance -->
                                <div>
                                    <label for="rep_lieu_delivrance" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Lieu de délivrance
                                    </label>
                                    <input type="text" id="rep_lieu_delivrance"
                                        value="{{ $representantActif['lieu_delivrance'] ?? '' }}" maxlength="100"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                                </div>

                                <!-- Date expiration -->
                                <div>
                                    <label for="rep_date_expiration" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date d'expiration
                                    </label>
                                    <input type="date" id="rep_date_expiration"
                                        value="{{ $representantActif['date_expiration'] ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer avec boutons -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 font-medium flex items-center space-x-2">
                        <i class="fas fa-times"></i>
                        <span>Annuler</span>
                    </a>
                    <button type="submit" id="submitBtn"
                        class="px-8 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium flex items-center space-x-2 shadow-md hover:shadow-lg">
                        <i class="fas fa-save"></i>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </div>
        </form>
        @endcan
    </main>

     @can('prestataires.update')
    @push('scripts')
        <script>
            // Gestion des onglets
            function showTab(tabName) {
                // Masquer tous les contenus
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

                // Réinitialiser tous les boutons
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('border-orange-500', 'text-orange-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                // Afficher le contenu sélectionné
                document.getElementById(`content${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`).classList.remove('hidden');

                // Activer le bouton
                const activeBtn = document.getElementById(`tab${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`);
                activeBtn.classList.remove('border-transparent', 'text-gray-500');
                activeBtn.classList.add('border-orange-500', 'text-orange-600');
            }

            // Préparer le JSON du représentant légal avant soumission
            document.getElementById('prestataireForm').addEventListener('submit', function(e) {
                // Construire l'objet représentant légal
                const representant = {
                    nom: document.getElementById('rep_nom')?.value || '',
                    prenoms: document.getElementById('rep_prenoms')?.value || '',
                    email: document.getElementById('rep_email')?.value || '',
                    contact: document.getElementById('rep_contact')?.value || '',
                    nationalite: document.getElementById('rep_nationalite')?.value || '',
                    pays: document.getElementById('pays')?.value || '',
                    adresse: document.getElementById('rep_adresse')?.value || '',
                    profession: document.getElementById('rep_profession')?.value || '',
                    date_naissance: document.getElementById('rep_date_naissance')?.value || '',
                    lieu_naissance: document.getElementById('rep_lieu_naissance')?.value || '',
                    type_piece_identite: document.getElementById('rep_type_piece')?.value || '',
                    numero_piece_identite: document.getElementById('rep_numero_piece')?.value || '',
                    date_delivrance: document.getElementById('rep_date_delivrance')?.value || '',
                    lieu_delivrance: document.getElementById('rep_lieu_delivrance')?.value || '',
                    date_expiration: document.getElementById('rep_date_expiration')?.value || ''
                };

                // Mettre à jour le champ caché
                document.getElementById('representant_legal_json').value = JSON.stringify(representant);

                // Désactiver le bouton et afficher le chargement
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

            .tab-content {
                animation: fadeIn 0.3s ease-out;
            }
        </style>
    @endpush
    @endcan
@endsection
