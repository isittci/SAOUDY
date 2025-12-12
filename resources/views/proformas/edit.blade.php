@extends('layouts.main')
@section('title', 'Modifier Proforma - ' . $proforma->numero_proforma)
@section('breadcrumb')
    <a href="{{ route('proformas.index') }}" class="text-white/80 hover:text-white transition-colors">Proformas</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('proformas.show', $proforma->id_proforma) }}" class="text-white/80 hover:text-white transition-colors">{{ $proforma->numero_proforma }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('proformas.show', $proforma->id_proforma) }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit text-orange-500 mr-3"></i>
                        Modifier la proforma
                    </h1>
                    <p class="text-gray-600 mt-1 flex items-center flex-wrap gap-2">
                        <span>{{ $proforma->numero_proforma }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            v{{ $proforma->version_proforma }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Avertissement versionnement -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-0.5"></i>
                <div>
                    <p class="text-blue-700 font-medium">Système de versionnement</p>
                    <p class="text-blue-600 text-sm mt-1">
                        Toute modification créera automatiquement une <strong>nouvelle version</strong> de cette proforma.
                        La version actuelle (v{{ $proforma->version_proforma }}) sera conservée dans l'historique mais désactivée.
                    </p>
                </div>
            </div>
        </div>

        <!-- Avertissement si proforma utilisée -->
        @if($proforma->estUtilisee())
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-yellow-700 font-medium">Attention</p>
                        <p class="text-yellow-600 text-sm mt-1">
                            Cette proforma est utilisée dans des attributions. La nouvelle version sera créée mais les attributions existantes resteront liées à la version actuelle.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Affichage des erreurs -->
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

        <form action="{{ route('proformas.update', $proforma->id_proforma) }}" method="POST" id="proformaForm">
            @csrf
            @method('PUT')

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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Numéro proforma -->
                                <div>
                                    <label for="numero_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro de proforma <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="numero_proforma" name="numero_proforma"
                                        value="{{ old('numero_proforma', $proforma->numero_proforma) }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent bg-gray-50"
                                        readonly>
                                    <p class="text-xs text-gray-500 mt-1">Le numéro ne peut pas être modifié</p>
                                </div>

                                <!-- Date proforma -->
                                <div>
                                    <label for="date_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de la proforma <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="date_proforma" name="date_proforma"
                                        value="{{ old('date_proforma', $proforma->date_proforma ? $proforma->date_proforma->format('Y-m-d') : '') }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('date_proforma') border-red-500 @enderror">
                                    @error('date_proforma')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modalité de paiement -->
                            <div>
                                <label for="modalite_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Modalités de paiement
                                </label>
                                <select id="modalite_select"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent mb-2"
                                    onchange="selectModalite(this.value)">
                                    <option value="">-- Choisir une modalité prédéfinie --</option>
                                    @foreach($modalites as $modalite)
                                        <option value="{{ $modalite }}" {{ $proforma->modalite_proforma == $modalite ? 'selected' : '' }}>
                                            {{ $modalite }}
                                        </option>
                                    @endforeach
                                    <option value="custom">Autre (personnalisé)</option>
                                </select>
                                <textarea id="modalite_proforma" name="modalite_proforma" rows="2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Détails des modalités de paiement...">{{ old('modalite_proforma', $proforma->modalite_proforma) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Montants -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calculator text-green-500 mr-2"></i>
                                Montants
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Montant HT -->
                                <div>
                                    <label for="montant_retenu_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Montant HT <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="montant_retenu_proforma" name="montant_retenu_proforma"
                                            value="{{ old('montant_retenu_proforma', $proforma->montant_retenu_proforma) }}" required min="0" step="0.01"
                                            class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                            onchange="calculerTotaux()" onkeyup="calculerTotaux()">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                    </div>
                                </div>

                                <!-- Remise -->
                                <div>
                                    <label for="remise_montant_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Remise
                                    </label>
                                    <div class="flex space-x-2">
                                        <div class="relative flex-1">
                                            <input type="number" id="remise_montant_proforma" name="remise_montant_proforma"
                                                value="{{ old('remise_montant_proforma', $proforma->remise_montant_proforma) }}" min="0" step="0.01"
                                                class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                                onchange="calculerTotaux()" onkeyup="calculerTotaux()">
                                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                        </div>
                                        <div class="relative w-24">
                                            <input type="number" id="remise_pourcentage" min="0" max="100" step="0.01"
                                                class="w-full px-3 py-3 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-center"
                                                onchange="calculerRemiseFromPourcentage()" onkeyup="calculerRemiseFromPourcentage()"
                                                value="{{ $proforma->pourcentage_remise }}">
                                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Taxes -->
                                <div>
                                    <label for="taxe_montant" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Taxes (TVA, etc.)
                                    </label>
                                    <div class="flex space-x-2">
                                        <div class="relative flex-1">
                                            <input type="number" id="taxe_montant" name="taxe_montant"
                                                value="{{ old('taxe_montant', $proforma->taxe_montant) }}" min="0" step="0.01"
                                                class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                                onchange="calculerTotaux()" onkeyup="calculerTotaux()">
                                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                        </div>
                                        <div class="relative w-24">
                                            <input type="number" id="taxe_pourcentage" min="0" max="100" step="0.01"
                                                class="w-full px-3 py-3 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-center"
                                                onchange="calculerTaxeFromPourcentage()" onkeyup="calculerTaxeFromPourcentage()"
                                                value="{{ $proforma->taux_taxe }}">
                                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pénalités -->
                                <div>
                                    <label for="penalites_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pénalités éventuelles
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="penalites_proforma" name="penalites_proforma"
                                            value="{{ old('penalites_proforma', $proforma->penalites_proforma) }}" min="0" step="0.01"
                                            class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motif de modification (optionnel) -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-purple-500 mr-2"></i>
                                Motif de modification
                                <span class="ml-2 text-xs font-normal text-gray-500">(optionnel)</span>
                            </h2>
                        </div>

                        <div class="p-6">
                            <textarea id="motif_modification_proforma" name="motif_modification_proforma" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent resize-none"
                                placeholder="Décrivez la raison de cette modification (ex: Correction d'erreur, Demande client, Ajustement tarifaire...)">{{ old('motif_modification_proforma') }}</textarea>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Si non renseigné, un motif sera généré automatiquement en fonction des modifications détectées.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">

                    <!-- Résumé -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-6">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-receipt text-blue-500 mr-2"></i>
                                Résumé
                            </h2>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-600">Montant HT</span>
                                <span id="resume_montant_ht" class="font-semibold text-gray-900">0 FCFA</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-600">Remise</span>
                                <span id="resume_remise" class="font-semibold text-red-600">- 0 FCFA</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 bg-gray-50 -mx-6 px-6">
                                <span class="text-gray-700 font-medium">Sous-total HT</span>
                                <span id="resume_sous_total" class="font-bold text-gray-900">0 FCFA</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-600">Taxes</span>
                                <span id="resume_taxes" class="font-semibold text-yellow-600">+ 0 FCFA</span>
                            </div>
                            <div class="flex items-center justify-between py-4 bg-gradient-to-r from-green-500 to-green-600 -mx-6 px-6 -mb-6 rounded-b-lg">
                                <span class="text-white font-semibold">Total TTC</span>
                                <span id="resume_total_ttc" class="text-2xl font-bold text-white">0 FCFA</span>
                            </div>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-toggle-on text-gray-500 mr-2"></i>
                                Statut
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">Proforma active</p>
                                    <p class="text-sm text-gray-500">Activez pour utiliser cette proforma</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="actif_proforma" value="1" class="sr-only peer"
                                        {{ old('actif_proforma', $proforma->actif_proforma) ? 'checked' : '' }}>
                                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Version -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-code-branch text-purple-500 mr-2"></i>
                                Version
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Version actuelle</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                                    v{{ $proforma->version_proforma }}
                                </span>
                            </div>
                            @if($proforma->parent_id)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Version originale</span>
                                    <a href="{{ route('proformas.show', $proforma->parent_id) }}" class="text-sm text-purple-600 hover:text-purple-800">
                                        Voir l'originale →
                                    </a>
                                </div>
                            @endif
                            <div class="pt-2">
                                <button type="button" onclick="creerNouvelleVersion()"
                                    class="w-full px-4 py-2.5 border border-purple-300 text-purple-600 hover:bg-purple-50 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 text-sm font-medium">
                                    <i class="fas fa-code-branch"></i>
                                    <span>Créer une nouvelle version</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 space-y-3">
                            <button type="submit"
                                class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg font-semibold">
                                <i class="fas fa-save"></i>
                                <span>Enregistrer les modifications</span>
                            </button>
                            <button type="button" onclick="window.location.href='{{ route('proformas.show', $proforma->id_proforma) }}'"
                                class="w-full px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 font-medium">
                                <i class="fas fa-times"></i>
                                <span>Annuler</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Modal Nouvelle Version -->
    <div id="versionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-code-branch text-indigo-500 mr-2"></i>
                        Créer une nouvelle version
                    </h3>
                    <button onclick="closeVersionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="versionForm" method="POST" action="{{ route('proformas.creer-version', $proforma->id_proforma) }}">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Motif de modification <span class="text-red-500">*</span>
                            </label>
                            <textarea name="motif_modification_proforma" rows="3" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"
                                placeholder="Expliquez la raison de cette nouvelle version..."></textarea>
                        </div>
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                                <p class="text-sm text-yellow-700">
                                    Cette action créera une nouvelle version et désactivera la version actuelle.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                        <button type="button" onclick="closeVersionModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                            Créer la version
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.selectModalite = function(value) {
                const textarea = document.getElementById('modalite_proforma');
                if (value && value !== 'custom') {
                    textarea.value = value;
                } else if (value === 'custom') {
                    textarea.value = '';
                    textarea.focus();
                }
            }

            window.calculerTotaux = function() {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const remise = parseFloat(document.getElementById('remise_montant_proforma').value) || 0;
                const taxe = parseFloat(document.getElementById('taxe_montant').value) || 0;
                const sousTotal = montantHT - remise;
                const totalTTC = sousTotal + taxe;

                if (montantHT > 0) {
                    document.getElementById('remise_pourcentage').value = ((remise / montantHT) * 100).toFixed(2);
                }
                if (sousTotal > 0) {
                    document.getElementById('taxe_pourcentage').value = ((taxe / sousTotal) * 100).toFixed(2);
                }

                document.getElementById('resume_montant_ht').textContent = formatMontant(montantHT) + ' FCFA';
                document.getElementById('resume_remise').textContent = '- ' + formatMontant(remise) + ' FCFA';
                document.getElementById('resume_sous_total').textContent = formatMontant(sousTotal) + ' FCFA';
                document.getElementById('resume_taxes').textContent = '+ ' + formatMontant(taxe) + ' FCFA';
                document.getElementById('resume_total_ttc').textContent = formatMontant(totalTTC) + ' FCFA';
            }

            window.calculerRemiseFromPourcentage = function() {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const pourcentage = parseFloat(document.getElementById('remise_pourcentage').value) || 0;
                document.getElementById('remise_montant_proforma').value = ((montantHT * pourcentage) / 100).toFixed(2);
                calculerTotaux();
            }

            window.calculerTaxeFromPourcentage = function() {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const remise = parseFloat(document.getElementById('remise_montant_proforma').value) || 0;
                const sousTotal = montantHT - remise;
                const pourcentage = parseFloat(document.getElementById('taxe_pourcentage').value) || 0;
                document.getElementById('taxe_montant').value = ((sousTotal * pourcentage) / 100).toFixed(2);
                calculerTotaux();
            }

            function formatMontant(montant) {
                return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(montant);
            }

            window.creerNouvelleVersion = function() {
                document.getElementById('versionModal').classList.remove('hidden');
            }

            window.closeVersionModal = function() {
                document.getElementById('versionModal').classList.add('hidden');
            }

            document.addEventListener('DOMContentLoaded', function() {
                calculerTotaux();
            });

            document.getElementById('proformaForm').addEventListener('submit', function(e) {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const remise = parseFloat(document.getElementById('remise_montant_proforma').value) || 0;
                if (remise > montantHT) {
                    e.preventDefault();
                    alert('La remise ne peut pas être supérieure au montant HT.');
                    return false;
                }
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Enregistrement...';
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeVersionModal();
            });
        </script>
        <style>
            @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
        </style>
    @endpush
@endsection
