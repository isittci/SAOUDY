@extends('layouts.main')
@section('title', 'Créer un prestataire')
@section('breadcrumb')
    <a href="{{ route('prestataires.index') }}" class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouveau prestataire</span>
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
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-plus-circle text-orange-500"></i>
                        <span>Nouveau prestataire</span>
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm">Remplissez les informations ci-dessous</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-red-700 font-medium mb-2">Erreurs de validation :</p>
                        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('prestataires.store') }}" method="POST" id="prestataireForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Informations générales -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                                Informations générales
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            <!-- Libellé -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Raison sociale <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="raison_sociale_prestataire" id="raison_sociale_prestataire"
                                    required maxlength="160" value="{{ old('raison_sociale_prestataire', 'Société de Construction ABC') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="Ex: Société de Construction ABC">
                                <div class="flex justify-between mt-1">
                                    @error('libelle_critere_appel_offre')
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 ml-auto"><span
                                            id="raisonSocialePrestataire">0</span>/160</p>
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro d'identification <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="numero_identification_prestataire"
                                        id="numero_identification_prestataire" required maxlength="50"
                                        value="{{ old('numero_identification_prestataire', 'CI-U123456789') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: CI-U123456789">
                                    <div class="flex justify-between mt-1">
                                        @error('numero_identification_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto">
                                            <span id="numeroIdentificationPrestataire">0</span>/50
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="email_prestataire" id="email_prestataire" required
                                        maxlength="100" value="{{ old('email_prestataire', 'email@exemple.com') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: email@exemple.com">
                                    <div class="flex justify-between mt-1">
                                        @error('email_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto">
                                            <span id="emailPrestataire">0</span>/100
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro de la carte de contribuable <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="numero_cc_prestataire" id="numero_cc_prestataire" required
                                        maxlength="50" value="{{ old('numero_cc_prestataire', '123456789') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: 123456789">
                                    <div class="flex justify-between mt-1">
                                        @error('numero_cc_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto"><span id="emailPrestataire">0</span>/50</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro RCCM <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="numero_rccm_prestataire" id="numero_rccm_prestataire"
                                        required maxlength="50" value="{{ old('numero_rccm_prestataire', 'CI-A-2020-B-12345') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: CI-A-2020-B-12345">
                                    <div class="flex justify-between mt-1">
                                        @error('numero_rccm_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto"><span
                                                id="numeroRccmPrestataire">0</span>/50</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Téléphone principal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="telephone_principal_prestataire"
                                        id="telephone_principal_prestataire" required maxlength="50"

                                        value="{{ old('telephone_principal_prestataire', '+225 05 00 00 00 00') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: +225 05 XX XX XX XX">
                                    <div class="flex justify-between mt-1">
                                        @error('telephone_principal_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto"><span
                                                id="telephonePrincipalPrestataire">0</span>/50</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Téléphone secondaire
                                    </label>
                                    <input type="text" name="telephone_secondaire_prestataire"
                                        id="telephone_secondaire_prestataire" maxlength="50"
                                        value="{{ old('telephone_secondaire_prestataire', '+225 05 00 00 00 55') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: +225 07 XX XX XX XX">
                                    <div class="flex justify-between mt-1">
                                        @error('telephone_secondaire_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto"><span
                                                id="telephoneSecondairePrestataire">0</span>/50</p>
                                    </div>
                                </div>
                            </div>



                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pays
                                    </label>
                                    <input type="text" name="pays_prestataire" id="pays_prestataire" maxlength="50"
                                        value="{{ old('pays_prestataire', 'Côte d\'Ivoire') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: Côte d'Ivoire">
                                    <div class="flex justify-between mt-1">
                                        @error('pays_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto"><span id="paysPrestataire">0</span>/50
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Ville
                                    </label>
                                    <input type="text" name="ville_prestataire" id="ville_prestataire" maxlength="50"
                                        value="{{ old('ville_prestataire', 'Yamoussoukro') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                        placeholder="Ex: Yamoussoukro">
                                    <div class="flex justify-between mt-1">
                                        @error('ville_prestataire')
                                            <p class="text-red-500 text-sm">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 ml-auto"><span id="villePrestataire">0</span>/50
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Adresse du prestataire -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Adresse du prestataire <span class="text-red-500">*</span>
                                </label>
                                <textarea name="adresse_prestataire" id="adresse_prestataire" required rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Ex: Zone 4, Rue des Jardins, Immeuble XYZ, Appartement 12B">{{ old('adresse_prestataire', 'Zone 4, Rue des Jardins, Immeuble XYZ, Appartement 12B') }}</textarea>
                                @error('adresse_prestataire')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>



                        </div>
                    </div>



                    <!-- Informations du représentant légal du prestataire -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Informations du représentant légal du prestataire
                            </h2>
                        </div>

                        <div class="p-6 space-y-5" id="representantLegalSection">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">



                                <!-- Nom -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                                    <input type="text" name="nom" value="{{ old('nom', 'KOUADIO Jean Marc') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : KOUADIO Jean Marc">
                                </div>



                                <!-- Contact -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact *</label>
                                    <input type="text" name="contact" value="{{ old('contact', '+225 07 07 07 07 07') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : 0708070708">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" value="{{ old('email', 'exemple@mail.com')}}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : exemple@mail.com">
                                </div>

                                <!-- Nationalité -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nationalité *</label>
                                    <input type="text" name="nationalite" value="{{ old('nationalite', 'Ivoirienne') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : Ivoirienne">
                                </div>

                                <!-- Pays -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pays *</label>
                                    <input type="text" name="pays" value="{{ old('pays', 'Côte d\'Ivoire') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : Côte d'Ivoire">
                                </div>

                                <!-- Adresse -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse *</label>
                                    <input type="text" name="adresse" value="{{ old('adresse', 'Yamoussoukro, Zone 4, Rue des Jardins') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : Cocody, Riviera Palmeraie">
                                </div>

                                <!-- Profession -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Profession *</label>
                                    <input type="text" name="profession" value="{{ old('profession', 'Ingénieur en bâtiment') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : Ingénieur en bâtiment">
                                </div>

                                <!-- Date naissance -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date de naissance
                                        *</label>
                                    <input type="date" name="date_naissance" value="{{ old('date_naissance', '1980-01-15') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                </div>

                                <!-- Lieu naissance -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de naissance
                                        *</label>
                                    <input type="text" name="lieu_naissance" value="{{ old('lieu_naissance', 'Yamoussoukro') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : Yamoussoukro">
                                </div>

                                <!-- Numéro pièce d'identité -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro pièce d'identité
                                        *</label>
                                    <input type="text" name="numero_piece_identite" value="{{ old('numero_piece_identite', 'CI012345678901') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : CI012345678901">
                                </div>

                                <!-- Type pièce identité -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type de pièce *</label>
                                    <select name="type_piece_identite"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                        <option value="">-- Sélectionner --</option>
                                        <option value="CNI" selected>Carte Nationale d'Identité</option>
                                        <option value="PASSPORT">Passeport</option>
                                        <option value="ATTESTATION">Attestation d'identité</option>
                                    </select>
                                </div>

                                <!-- Date de délivrance -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date de délivrance
                                        *</label>
                                    <input type="date" name="date_delivrance" value="{{ old('date_delivrance', '2015-06-20') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                </div>

                                <!-- Lieu de délivrance -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de délivrance
                                        *</label>
                                    <input type="text" name="lieu_delivrance" value="{{ old('lieu_delivrance', 'Abidjan') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        placeholder="Ex : Abidjan">
                                </div>

                                <!-- Date d'expiration -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date d'expiration
                                        *</label>
                                    <input type="date" name="date_expiration" value="{{ old('date_expiration', '2035-06-20') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">
                    <!-- Actions -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tasks text-orange-500 mr-2"></i>
                            Actions
                        </h3>

                        <div class="space-y-3">
                            <button type="submit" id="submitBtn"
                                class="w-full px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                <span id="submitText">Enregistrer</span>
                            </button>

                            <a href="{{ route('appels-offres.index') }}"
                                class="w-full px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                        </div>
                    </div>

                    <!-- Aide -->
                    <div class="bg-blue-50 rounded-2xl shadow-lg p-6 border border-blue-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                            Aide
                        </h3>

                        <div class="space-y-2 text-sm text-gray-600">
                            <p><i class="fas fa-check text-green-500 mr-1"></i> Le numéro sera généré automatiquement</p>
                            <p><i class="fas fa-check text-green-500 mr-1"></i> Vérifiez que le montant correspond au type
                            </p>
                            <p><i class="fas fa-check text-green-500 mr-1"></i> Les dates doivent être cohérentes</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Modal d'ajout de Type d'Appel d'Offre -->
    <div id="addTypeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="window.closeAddTypeModal()">
            </div>

            <!-- Modal -->
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-50 to-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-plus-circle text-green-500 mr-2"></i>
                            Nouveau Type d'Appel d'Offres
                        </h3>
                        <button onclick="window.closeAddTypeModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <form id="addTypeForm" class="p-6 space-y-4">
                    @csrf

                    <!-- Messages d'erreur -->
                    <div id="typeFormErrors" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-red-700 font-medium mb-2">Erreurs de validation :</p>
                                <ul id="typeFormErrorsList" class="list-disc list-inside text-red-600 text-sm space-y-1">
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Libellé -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Libellé <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="libelle_type_appel_offre" id="modal_libelle" required
                            maxlength="160"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                            placeholder="Ex: Appel d'Offres Ouvert">
                    </div>

                    <!-- Code -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code_type_appel_offre" id="modal_code" required maxlength="50"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent uppercase"
                            placeholder="Ex: AOO">
                    </div>

                    <!-- Valeurs Min/Max -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Valeur Minimale (FCFA) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="valeur_minimuim_type_appel_offre" id="modal_min" required
                                min="0" step="0.01"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Valeur Maximale (FCFA) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="valeur_maximuim_type_appel_offre" id="modal_max" required
                                min="0" step="0.01"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                placeholder="0.00">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description_critere_type_appel_offre" id="modal_description" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent resize-none"
                            placeholder="Description optionnelle..."></textarea>
                    </div>

                    <!-- Actif -->
                    <div class="flex items-center">
                        <input type="hidden" name="actif_type_appel_offre" value="0">
                        <input type="checkbox" name="actif_type_appel_offre" id="modal_actif" value="1" checked
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <label for="modal_actif" class="ml-2 text-sm text-gray-700">
                            Type actif
                        </label>
                    </div>

                    <!-- Boutons -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" id="saveTypeBtn"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            <span id="saveTypeText">Enregistrer</span>
                        </button>

                        <button type="button" onclick="window.closeAddTypeModal()"
                            class="px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-all duration-200">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Fonctions globales pour le modal
            window.openAddTypeModal = function() {
                document.getElementById('addTypeModal').classList.remove('hidden');
                document.getElementById('modal_libelle').focus();
            };

            window.closeAddTypeModal = function() {
                document.getElementById('addTypeModal').classList.add('hidden');
                document.getElementById('addTypeForm').reset();
                document.getElementById('typeFormErrors').classList.add('hidden');
            };

            // Soumission du formulaire de type via AJAX
            document.getElementById('addTypeForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const saveBtn = document.getElementById('saveTypeBtn');
                // const saveText = document.getElementById('saveTypeText');
                const errorsDiv = document.getElementById('typeFormErrors');
                const errorsList = document.getElementById('typeFormErrorsList');

                // Désactiver le bouton
                saveBtn.disabled = true;
                // saveText.textContent = 'Enregistrement...';
                errorsDiv.classList.add('hidden');
                errorsList.innerHTML = '';

                // Préparer les données
                const formData = new FormData(this);

                // Envoyer la requête AJAX
                fetch('{{ route('types-appels-offres.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Succès : ajouter le nouveau type au select
                            const typeSelect = document.getElementById('type_appel_offre_id');
                            const newOption = document.createElement('option');
                            newOption.value = data.data.id_type_appel_offre;
                            newOption.setAttribute('data-min', data.data.valeur_minimuim_type_appel_offre);
                            newOption.setAttribute('data-max', data.data.valeur_maximuim_type_appel_offre);
                            newOption.textContent =
                                `${data.data.code_type_appel_offre} - ${data.data.libelle_type_appel_offre} (${Number(data.data.valeur_minimuim_type_appel_offre).toLocaleString('fr-FR')} - ${Number(data.data.valeur_maximuim_type_appel_offre).toLocaleString('fr-FR')} FCFA)`;
                            newOption.selected = true;

                            // Ajouter l'option au select
                            typeSelect.appendChild(newOption);

                            // Déclencher l'événement change pour mettre à jour les infos
                            typeSelect.dispatchEvent(new Event('change'));

                            // Fermer le modal
                            window.closeAddTypeModal();

                            // Afficher un message de succès
                            showNotification('Type d\'appel d\'offres créé avec succès', 'success');
                        } else {
                            // Erreur : afficher les messages
                            if (data.errors) {
                                Object.values(data.errors).forEach(errorArray => {
                                    errorArray.forEach(error => {
                                        const li = document.createElement('li');
                                        li.textContent = error;
                                        errorsList.appendChild(li);
                                    });
                                });
                            } else {
                                const li = document.createElement('li');
                                li.textContent = data.message || 'Une erreur est survenue';
                                errorsList.appendChild(li);
                            }
                            errorsDiv.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        const li = document.createElement('li');
                        li.textContent = 'Une erreur technique est survenue. Veuillez réessayer.';
                        errorsList.appendChild(li);
                        errorsDiv.classList.remove('hidden');
                    })
                    .finally(() => {
                        // Réactiver le bouton
                        saveBtn.disabled = false;
                        saveText.textContent = 'Enregistrer';
                    });
            });

            // Fonction pour afficher une notification
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className =
                    `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg animate-fadeIn ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-3"></i>
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Compteur de caractères
            const libelleInput = document.getElementById('libelle');
            const libelleCount = document.getElementById('libelleCount');

            libelleInput.addEventListener('input', function() {
                libelleCount.textContent = this.value.length;
            });
            libelleCount.textContent = libelleInput.value.length;

            // Validation du type et montant
            const typeSelect = document.getElementById('type_appel_offre_id');
            const montantInput = document.getElementById('montant_global');
            const typeInfo = document.getElementById('typeInfo');
            const typeInfoText = document.getElementById('typeInfoText');
            const montantWarning = document.getElementById('montantWarning');
            const montantWarningText = document.getElementById('montantWarningText');

            typeSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];

                if (option.value) {
                    const min = parseFloat(option.dataset.min);
                    const max = parseFloat(option.dataset.max);

                    typeInfo.classList.remove('hidden');
                    typeInfoText.textContent =
                        `Intervalle de valeur : ${min.toLocaleString('fr-FR')} - ${max.toLocaleString('fr-FR')} FCFA`;

                    // Valider le montant si déjà saisi
                    if (montantInput.value) {
                        validateMontant(parseFloat(montantInput.value), min, max);
                    }
                } else {
                    typeInfo.classList.add('hidden');
                    montantWarning.classList.add('hidden');
                }
            });

            montantInput.addEventListener('input', function() {
                const option = typeSelect.options[typeSelect.selectedIndex];

                if (option.value && this.value) {
                    const min = parseFloat(option.dataset.min);
                    const max = parseFloat(option.dataset.max);
                    const montant = parseFloat(this.value);

                    validateMontant(montant, min, max);
                } else {
                    montantWarning.classList.add('hidden');
                }
            });

            function validateMontant(montant, min, max) {
                if (montant < min || montant > max) {
                    montantWarning.classList.remove('hidden');
                    montantWarningText.textContent =
                        `Le montant doit être entre ${min.toLocaleString('fr-FR')} et ${max.toLocaleString('fr-FR')} FCFA`;
                } else {
                    montantWarning.classList.add('hidden');
                }
            }

            // Validation des dates
            const datePublication = document.getElementById('date_publication');
            const dateLimite = document.getElementById('date_limite');
            const dateOuverture = document.getElementById('date_ouverture');
            const dateInfo = document.getElementById('dateInfo');
            const dateInfoText = document.getElementById('dateInfoText');

            function validateDates() {
                const pub = datePublication.value ? new Date(datePublication.value) : new Date();
                const limite = dateLimite.value ? new Date(dateLimite.value) : null;
                const ouverture = dateOuverture.value ? new Date(dateOuverture.value) : null;

                if (limite) {
                    const diffJours = Math.ceil((limite - pub) / (1000 * 60 * 60 * 24));

                    if (diffJours > 0) {
                        dateInfo.classList.remove('hidden');
                        dateInfoText.textContent = `Délai de dépôt : ${diffJours} jour(s)`;
                    } else {
                        dateInfo.classList.add('hidden');
                    }
                }
            }

            datePublication.addEventListener('change', validateDates);
            dateLimite.addEventListener('change', validateDates);
            dateOuverture.addEventListener('change', validateDates);

            // Soumission du formulaire
            document.getElementById('aoForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');

                submitBtn.disabled = true;
                submitText.textContent = 'Enregistrement...';
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + submitText.textContent;
            });

            // Initialiser les validations si des valeurs existent
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
            validateDates();

            // Fermer le modal avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('addTypeModal');
                    if (!modal.classList.contains('hidden')) {
                        window.closeAddTypeModal();
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

            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
            }

            /* Style pour le modal */
            #addTypeModal {
                backdrop-filter: blur(4px);
            }
        </style>
    @endpush
@endsection
