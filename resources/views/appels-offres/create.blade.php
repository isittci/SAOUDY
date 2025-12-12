@extends('layouts.main')
@section('title', 'Créer un Appel d\'Offres')
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouveau</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('appels-offres.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-plus-circle text-orange-500"></i>
                        <span>Nouvel Appel d'Offres</span>
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm">Remplissez les informations ci-dessous</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Container pour les notifications Toast -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

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

        <form action="{{ route('appels-offres.store') }}" method="POST" id="aoForm">
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

                            <!-- Type d'AO avec bouton d'ajout et recherche -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Type d'Appel d'Offres <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <select name="type_appel_offre_id" id="type_appel_offre_id" required
                                            class="type-ao-select w-full"
                                            placeholder="Rechercher un type d'appel d'offres...">
                                            <option value="">-- Sélectionner un type --</option>
                                            @foreach ($typesAO as $type)
                                                <option value="{{ $type->id_type_appel_offre }}"
                                                    data-min="{{ $type->valeur_minimuim_type_appel_offre }}"
                                                    data-max="{{ $type->valeur_maximuim_type_appel_offre }}"
                                                    data-code="{{ $type->code_type_appel_offre }}"
                                                    data-libelle="{{ $type->libelle_type_appel_offre }}"
                                                    {{ old('type_appel_offre_id') == $type->id_type_appel_offre ? 'selected' : '' }}>
                                                    {{ $type->code_type_appel_offre }} -
                                                    {{ $type->libelle_type_appel_offre }}
                                                    ({{ number_format($type->valeur_minimuim_type_appel_offre, 0, ',', ' ') }}
                                                    -
                                                    {{ number_format($type->valeur_maximuim_type_appel_offre, 0, ',', ' ') }}
                                                    FCFA)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" onclick="window.openAddTypeModal()"
                                        class="px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2 whitespace-nowrap">
                                        <i class="fas fa-plus"></i>
                                        <span class="hidden sm:inline">Nouveau Type</span>
                                    </button>
                                </div>
                                @error('type_appel_offre_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div id="typeInfo"
                                    class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <span id="typeInfoText"></span>
                                </div>
                            </div>


                            <!-- Libellé -->
                            <div>
                                <label for="numero_appel_offre" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Numéro de l'appel d'offre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="numero_appel_offre" id="numero_appel_offre" required
                                    maxlength="35" value="{{ old('numero_appel_offre') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="Ex: AOCO-2025-001">
                                <div class="flex justify-between mt-1">
                                    @error('numero_appel_offre')
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 ml-auto"><span id="numeroCount">0</span>/35</p>
                                </div>
                            </div>




                            <!-- Libellé -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Libellé <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="libelle_critere_appel_offre" id="libelle" required maxlength="160" value="{{ old('libelle_critere_appel_offre') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent" placeholder="Ex: Construction d'un immeuble administratif">
                                <div class="flex justify-between mt-1">
                                    @error('libelle_critere_appel_offre')
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 ml-auto"><span id="libelleCount">0</span>/160</p>
                                </div>
                            </div>

                            <!-- Montant Global -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Montant Global (FCFA) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="montant_global_appel_offre" id="montant_global" required
                                    min="0" step="0.01" value="{{ old('montant_global_appel_offre') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="0.00">
                                @error('montant_global_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                <!-- Info sur le type sélectionné automatiquement -->
                                <div id="autoSelectInfo"
                                    class="hidden mt-2 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                                    <i class="fas fa-magic mr-1"></i>
                                    <span id="autoSelectInfoText"></span>
                                </div>

                                <!-- Warning si montant hors fourchette -->
                                <div id="montantWarning"
                                    class="hidden mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <span id="montantWarningText"></span>
                                </div>

                                <!-- Aucun type correspondant -->
                                <div id="noMatchWarning"
                                    class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    <span id="noMatchWarningText"></span>
                                </div>
                            </div>

                            <!-- Objet -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Objet de l'Appel d'Offres <span class="text-red-500">*</span>
                                </label>
                                <textarea name="objet_critere_appel_offre" id="objet" required rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Description officielle de ce qui est demandé...">{{ old('objet_critere_appel_offre') }}</textarea>
                                @error('objet_critere_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description détaillée -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Description Détaillée <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description_critere_critere_appel_offre" id="description" required rows="6"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Détails complets de l'appel d'offres...">{{ old('description_critere_critere_appel_offre') }}</textarea>
                                @error('description_critere_critere_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dates importantes -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Dates Importantes
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de Publication <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_publication_critere_appel_offre"
                                        id="date_publication" required
                                        value="{{ old('date_publication_critere_appel_offre', date('Y-m-d')) }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date Limite de Dépôt <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_limite_depot_critere_appel_offre" id="date_limite"
                                        required value="{{ old('date_limite_depot_critere_appel_offre') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date d'Ouverture <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_ouverture_plis_critere_appel_offre"
                                        id="date_ouverture" required
                                        value="{{ old('date_ouverture_plis_critere_appel_offre') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                </div>
                            </div>

                            <div id="dateInfo"
                                class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span id="dateInfoText"></span>
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
                            <p><i class="fas fa-check text-green-500 mr-1"></i> Le type peut être auto-sélectionné selon le montant</p>
                            <p><i class="fas fa-check text-green-500 mr-1"></i> Les dates doivent être cohérentes</p>
                        </div>
                    </div>

                    <!-- Récapitulatif Type sélectionné -->
                    <div id="typeRecap" class="hidden bg-white rounded-2xl shadow-lg p-6 border-l-4 border-orange-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-file-contract text-orange-500 mr-2"></i>
                            Type sélectionné
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p class="font-semibold text-gray-800" id="recapCode"></p>
                            <p class="text-gray-600" id="recapLibelle"></p>
                            <div class="pt-2 border-t border-gray-200 mt-2">
                                <p class="text-gray-500">
                                    <i class="fas fa-coins mr-1"></i>
                                    Fourchette : <span id="recapFourchette" class="font-medium text-gray-700"></span>
                                </p>
                            </div>
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
                        <input type="text" name="code_type_appel_offre" id="modal_code" required maxlength="10"
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
            // =====================================================
            // CONFIGURATION ET DONNÉES DES TYPES D'APPEL D'OFFRES
            // =====================================================

            // Stocker les données des types pour la recherche automatique
            const typesAOData = [
                @foreach ($typesAO as $type)
                {
                    id: '{{ $type->id_type_appel_offre }}',
                    code: '{{ $type->code_type_appel_offre }}',
                    libelle: '{{ addslashes($type->libelle_type_appel_offre) }}',
                    min: {{ $type->valeur_minimuim_type_appel_offre }},
                    max: {{ $type->valeur_maximuim_type_appel_offre }}
                },
                @endforeach
            ];

            // Variable pour tracker si l'utilisateur a manuellement sélectionné un type
            let typeSelectionneManuelement = false;

            // =====================================================
            // FONCTIONS DE NOTIFICATION TOAST
            // =====================================================

            function showToast(message, type = 'info', duration = 5000) {
                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');

                // Définir les styles selon le type
                const styles = {
                    success: {
                        bg: 'bg-green-500',
                        icon: 'fa-check-circle',
                        border: 'border-green-600'
                    },
                    warning: {
                        bg: 'bg-yellow-500',
                        icon: 'fa-exclamation-triangle',
                        border: 'border-yellow-600'
                    },
                    error: {
                        bg: 'bg-red-500',
                        icon: 'fa-times-circle',
                        border: 'border-red-600'
                    },
                    info: {
                        bg: 'bg-blue-500',
                        icon: 'fa-info-circle',
                        border: 'border-blue-600'
                    },
                    auto: {
                        bg: 'bg-gradient-to-r from-green-500 to-teal-500',
                        icon: 'fa-magic',
                        border: 'border-teal-600'
                    }
                };

                const style = styles[type] || styles.info;

                toast.className = `${style.bg} text-white px-6 py-4 rounded-lg shadow-xl border-l-4 ${style.border} transform transition-all duration-300 ease-out translate-x-full opacity-0 max-w-md`;
                toast.innerHTML = `
                    <div class="flex items-start">
                        <i class="fas ${style.icon} mr-3 mt-0.5 text-lg"></i>
                        <div class="flex-1">
                            <p class="font-medium">${message}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/80 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                container.appendChild(toast);

                // Animation d'entrée
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                });

                // Auto-suppression
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }

            // =====================================================
            // FONCTIONS DE GESTION DU TYPE D'APPEL D'OFFRES
            // =====================================================

            /**
             * Trouver le type d'AO correspondant à un montant
             */
            function trouverTypeParMontant(montant) {
                return typesAOData.find(type => montant >= type.min && montant <= type.max);
            }

            /**
             * Vérifier si un montant est dans la fourchette d'un type
             */
            function estDansFourchette(montant, typeId) {
                const type = typesAOData.find(t => t.id === typeId);
                if (!type) return false;
                return montant >= type.min && montant <= type.max;
            }

            /**
             * Mettre à jour le récapitulatif du type sélectionné
             */
            function updateTypeRecap(typeId) {
                const recap = document.getElementById('typeRecap');
                const type = typesAOData.find(t => t.id === typeId);

                if (type) {
                    document.getElementById('recapCode').textContent = type.code;
                    document.getElementById('recapLibelle').textContent = type.libelle;
                    document.getElementById('recapFourchette').textContent =
                        `${type.min.toLocaleString('fr-FR')} - ${type.max.toLocaleString('fr-FR')} FCFA`;
                    recap.classList.remove('hidden');
                } else {
                    recap.classList.add('hidden');
                }
            }

            /**
             * Masquer tous les messages d'info/warning du montant
             */
            function masquerTousLesMessages() {
                document.getElementById('autoSelectInfo').classList.add('hidden');
                document.getElementById('montantWarning').classList.add('hidden');
                document.getElementById('noMatchWarning').classList.add('hidden');
            }

            /**
             * Gérer le changement de montant
             */
            function gererChangementMontant() {
                const montantInput = document.getElementById('montant_global');
                const typeSelect = document.getElementById('type_appel_offre_id');
                const montant = parseFloat(montantInput.value);

                masquerTousLesMessages();

                if (!montant || isNaN(montant)) return;

                const typeSelectionne = typeSelect.value;

                // CAS 1: Aucun type sélectionné - Sélection automatique
                if (!typeSelectionne) {
                    const typeCorrespondant = trouverTypeParMontant(montant);

                    if (typeCorrespondant) {
                        // Sélectionner automatiquement le type
                        if (window.typeSelectInstance) {
                            window.typeSelectInstance.setValue(typeCorrespondant.id);
                        } else {
                            typeSelect.value = typeCorrespondant.id;
                        }

                        // Afficher le message d'auto-sélection
                        const autoSelectInfo = document.getElementById('autoSelectInfo');
                        const autoSelectInfoText = document.getElementById('autoSelectInfoText');
                        autoSelectInfoText.textContent =
                            `Type "${typeCorrespondant.code} - ${typeCorrespondant.libelle}" sélectionné automatiquement (fourchette: ${typeCorrespondant.min.toLocaleString('fr-FR')} - ${typeCorrespondant.max.toLocaleString('fr-FR')} FCFA)`;
                        autoSelectInfo.classList.remove('hidden');

                        // Notification toast
                        showToast(
                            `Type d'AO sélectionné automatiquement : ${typeCorrespondant.code} - ${typeCorrespondant.libelle}`,
                            'auto',
                            6000
                        );

                        // Mettre à jour le récapitulatif
                        updateTypeRecap(typeCorrespondant.id);
                        afficherInfoType(typeCorrespondant.id);

                    } else {
                        // Aucun type correspondant trouvé
                        const noMatchWarning = document.getElementById('noMatchWarning');
                        const noMatchWarningText = document.getElementById('noMatchWarningText');
                        noMatchWarningText.textContent =
                            `Aucun type d'appel d'offres ne correspond à ce montant (${montant.toLocaleString('fr-FR')} FCFA). Veuillez sélectionner manuellement ou créer un nouveau type.`;
                        noMatchWarning.classList.remove('hidden');

                        showToast(
                            'Aucun type d\'AO ne correspond à ce montant. Sélection manuelle requise.',
                            'warning',
                            5000
                        );
                    }
                }
                // CAS 2: Type déjà sélectionné - Vérifier la conformité
                else {
                    const type = typesAOData.find(t => t.id === typeSelectionne);

                    if (type) {
                        if (!estDansFourchette(montant, typeSelectionne)) {
                            // Le montant n'est pas dans la fourchette
                            const montantWarning = document.getElementById('montantWarning');
                            const montantWarningText = document.getElementById('montantWarningText');
                            montantWarningText.innerHTML =
                                `<strong>Attention :</strong> Le montant saisi (${montant.toLocaleString('fr-FR')} FCFA) n'est pas conforme à la fourchette du type sélectionné "${type.code}" (${type.min.toLocaleString('fr-FR')} - ${type.max.toLocaleString('fr-FR')} FCFA). <br><em>Le type d'AO actuel est conservé.</em>`;
                            montantWarning.classList.remove('hidden');

                            // Chercher s'il existe un type plus approprié
                            const typeRecommande = trouverTypeParMontant(montant);
                            if (typeRecommande && typeRecommande.id !== typeSelectionne) {
                                showToast(
                                    `Montant hors fourchette ! Type recommandé : ${typeRecommande.code} - ${typeRecommande.libelle}`,
                                    'warning',
                                    7000
                                );
                            } else {
                                showToast(
                                    'Le montant n\'est pas conforme au type d\'AO sélectionné',
                                    'warning',
                                    5000
                                );
                            }
                        } else {
                            // Le montant est dans la fourchette - tout est OK
                            showToast(
                                'Montant conforme au type d\'AO sélectionné',
                                'success',
                                3000
                            );
                        }
                    }
                }
            }

            /**
             * Afficher les informations du type sélectionné
             */
            function afficherInfoType(value) {
                const typeInfo = document.getElementById('typeInfo');
                const typeInfoText = document.getElementById('typeInfoText');

                if (!value) {
                    typeInfo.classList.add('hidden');
                    document.getElementById('typeRecap').classList.add('hidden');
                    return;
                }

                const type = typesAOData.find(t => t.id === value);
                if (type) {
                    typeInfoText.textContent = `Intervalle de valeur : ${type.min.toLocaleString('fr-FR')} - ${type.max.toLocaleString('fr-FR')} FCFA`;
                    typeInfo.classList.remove('hidden');
                    updateTypeRecap(value);
                }
            }

            // =====================================================
            // INITIALISATION DU DOM
            // =====================================================

            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('type_appel_offre_id');
                const montantInput = document.getElementById('montant_global');

                // Initialiser Tom Select
                const tomSelectInstance = new TomSelect('#type_appel_offre_id', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: 'Rechercher un type d\'appel d\'offres...',
                    allowEmptyOption: true,
                    render: {
                        option: function(data, escape) {
                            const option = data.$option;
                            if (!option || !data.value) {
                                return '<div class="py-2 px-3 text-gray-500">-- Sélectionner un type --</div>';
                            }

                            const code = option.dataset.code || '';
                            const libelle = option.dataset.libelle || '';
                            const min = option.dataset.min ? Number(option.dataset.min).toLocaleString('fr-FR') : '0';
                            const max = option.dataset.max ? Number(option.dataset.max).toLocaleString('fr-FR') : '0';

                            return `
                                <div class="py-2 px-3">
                                    <div class="font-semibold text-gray-800">
                                        <span class="text-orange-600">${escape(code)}</span> - ${escape(libelle)}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-coins mr-1"></i>
                                        ${min} - ${max} FCFA
                                    </div>
                                </div>
                            `;
                        },
                        item: function(data, escape) {
                            const option = data.$option;
                            if (!option || !data.value) {
                                return '<div>-- Sélectionner un type --</div>';
                            }

                            const code = option.dataset.code || '';
                            const libelle = option.dataset.libelle || '';

                            return `<div><span class="font-semibold text-orange-600">${escape(code)}</span> - ${escape(libelle)}</div>`;
                        }
                    },
                    onChange: function(value) {
                        typeSelectionneManuelement = true;
                        afficherInfoType(value);

                        // Revalider le montant si déjà saisi
                        const montant = parseFloat(montantInput.value);
                        if (montant && !isNaN(montant) && value) {
                            masquerTousLesMessages();
                            if (!estDansFourchette(montant, value)) {
                                const type = typesAOData.find(t => t.id === value);
                                if (type) {
                                    const montantWarning = document.getElementById('montantWarning');
                                    const montantWarningText = document.getElementById('montantWarningText');
                                    montantWarningText.innerHTML =
                                        `<strong>Attention :</strong> Le montant actuel (${montant.toLocaleString('fr-FR')} FCFA) n'est pas conforme à la fourchette du type sélectionné "${type.code}" (${type.min.toLocaleString('fr-FR')} - ${type.max.toLocaleString('fr-FR')} FCFA).`;
                                    montantWarning.classList.remove('hidden');
                                }
                            }
                        }
                    }
                });

                // Stocker l'instance globalement
                window.typeSelectInstance = tomSelectInstance;

                // Écouter les changements de montant avec debounce
                let debounceTimer;
                montantInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(gererChangementMontant, 500);
                });

                // Écouter aussi le blur pour une validation immédiate
                montantInput.addEventListener('blur', function() {
                    clearTimeout(debounceTimer);
                    gererChangementMontant();
                });

                // Initialiser si des valeurs existent
                const currentValue = typeSelect.value;
                if (currentValue) {
                    afficherInfoType(currentValue);
                    typeSelectionneManuelement = true;
                }

                // Vérifier le montant initial si présent
                if (montantInput.value) {
                    gererChangementMontant();
                }
            });

            // =====================================================
            // FONCTIONS DU MODAL ET AUTRES
            // =====================================================

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
                const saveText = document.getElementById('saveTypeText');
                const errorsDiv = document.getElementById('typeFormErrors');
                const errorsList = document.getElementById('typeFormErrorsList');

                saveBtn.disabled = true;
                saveText.textContent = 'Enregistrement...';
                errorsDiv.classList.add('hidden');
                errorsList.innerHTML = '';

                const formData = new FormData(this);

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
                            // Ajouter aux données locales
                            typesAOData.push({
                                id: data.data.id_type_appel_offre,
                                code: data.data.code_type_appel_offre,
                                libelle: data.data.libelle_type_appel_offre,
                                min: parseFloat(data.data.valeur_minimuim_type_appel_offre),
                                max: parseFloat(data.data.valeur_maximuim_type_appel_offre)
                            });

                            // Ajouter l'option au select natif
                            const typeSelect = document.getElementById('type_appel_offre_id');
                            const newOption = document.createElement('option');
                            newOption.value = data.data.id_type_appel_offre;
                            newOption.setAttribute('data-min', data.data.valeur_minimuim_type_appel_offre);
                            newOption.setAttribute('data-max', data.data.valeur_maximuim_type_appel_offre);
                            newOption.setAttribute('data-code', data.data.code_type_appel_offre);
                            newOption.setAttribute('data-libelle', data.data.libelle_type_appel_offre);
                            newOption.textContent =
                                `${data.data.code_type_appel_offre} - ${data.data.libelle_type_appel_offre} (${Number(data.data.valeur_minimuim_type_appel_offre).toLocaleString('fr-FR')} - ${Number(data.data.valeur_maximuim_type_appel_offre).toLocaleString('fr-FR')} FCFA)`;
                            typeSelect.appendChild(newOption);

                            // Rafraîchir Tom Select
                            if (window.typeSelectInstance) {
                                window.typeSelectInstance.addOption({
                                    value: data.data.id_type_appel_offre,
                                    text: newOption.textContent,
                                    $option: newOption
                                });
                                window.typeSelectInstance.setValue(data.data.id_type_appel_offre);
                            }

                            window.closeAddTypeModal();
                            showToast('Type d\'appel d\'offres créé avec succès', 'success');
                        } else {
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
                        saveBtn.disabled = false;
                        saveText.textContent = 'Enregistrer';
                    });
            });

            // Compteur de caractères
            const libelleInput = document.getElementById('libelle');
            const libelleCount = document.getElementById('libelleCount');

            libelleInput.addEventListener('input', function() {
                libelleCount.textContent = this.value.length;
            });
            libelleCount.textContent = libelleInput.value.length;

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

            // Soumission du formulaire principal
            document.getElementById('aoForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');

                submitBtn.disabled = true;
                submitText.textContent = 'Enregistrement...';
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + submitText.textContent;
            });

            // Fermer le modal avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('addTypeModal');
                    if (!modal.classList.contains('hidden')) {
                        window.closeAddTypeModal();
                    }
                }
            });

            // Initialiser les validations si des valeurs existent
            validateDates();
        </script>

        <style>
            /* Personnalisation Tom Select pour correspondre au thème orange */
            .ts-wrapper.single .ts-control {
                padding: 0.625rem 1rem;
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                background: white;
                min-height: 44px;
            }

            .ts-wrapper.single .ts-control:focus,
            .ts-wrapper.single.focus .ts-control {
                outline: none;
                border-color: transparent;
                box-shadow: 0 0 0 2px rgba(251, 146, 60, 0.5);
            }

            .ts-dropdown {
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                margin-top: 4px;
            }

            .ts-dropdown .option.active {
                background-color: #fff7ed;
                color: #ea580c;
            }

            .ts-dropdown .option:hover {
                background-color: #ffedd5;
            }

            .ts-wrapper .ts-control input {
                font-size: 0.875rem;
            }

            .ts-dropdown .ts-dropdown-content {
                max-height: 300px;
                overflow-y: auto;
            }

            .ts-wrapper.single.input-active .ts-control {
                background: white;
            }

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

            #addTypeModal {
                backdrop-filter: blur(4px);
            }
        </style>
    @endpush
@endsection
