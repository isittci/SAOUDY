@extends('layouts.main')
@section('title', 'Nouvelle Proforma')
@section('breadcrumb')
    <a href="{{ route('proformas.index') }}" class="text-white/80 hover:text-white transition-colors">Proformas</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('proformas.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-invoice-dollar text-orange-500 mr-3"></i>
                        Nouvelle Proforma
                    </h1>
                    <p class="text-gray-600 mt-1">Créez une nouvelle proforma pour vos transactions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

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

        <form action="{{ route('proformas.store') }}" method="POST" id="proformaForm">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Numéro proforma -->
                                <div>
                                    <label for="numero_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro de proforma
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="numero_proforma" name="numero_proforma"
                                            value="{{ old('numero_proforma', $numeroSuggere) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent bg-gray-50 @error('numero_proforma') border-red-500 @enderror"
                                            placeholder="PROF-2025-0001" readonly>
                                        <button type="button" onclick="regenererNumero()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-gray-400 hover:text-orange-500 transition-colors"
                                            title="Régénérer le numéro">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Généré automatiquement</p>
                                    @error('numero_proforma')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date proforma -->
                                <div>
                                    <label for="date_proforma_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de la proforma <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="date_proforma_proforma" name="date_proforma_proforma"
                                        value="{{ old('date_proforma_proforma', date('Y-m-d')) }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('date_proforma_proforma') border-red-500 @enderror">
                                    @error('date_proforma_proforma')
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
                                        <option value="{{ $modalite }}">{{ $modalite }}</option>
                                    @endforeach
                                    <option value="custom">Autre (personnalisé)</option>
                                </select>
                                <textarea id="modalite_proforma" name="modalite_proforma" rows="2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none @error('modalite_proforma') border-red-500 @enderror"
                                    placeholder="Détails des modalités de paiement...">{{ old('modalite_proforma') }}</textarea>
                                @error('modalite_proforma')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                            value="{{ old('montant_retenu_proforma', 0) }}" required min="0" step="0.01"
                                            class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('montant_retenu_proforma') border-red-500 @enderror"
                                            onchange="calculerTotaux()" onkeyup="calculerTotaux()">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                    </div>
                                    @error('montant_retenu_proforma')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Remise -->
                                <div>
                                    <label for="remise_montant_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Remise
                                    </label>
                                    <div class="flex space-x-2">
                                        <div class="relative flex-1">
                                            <input type="number" id="remise_montant_proforma" name="remise_montant_proforma"
                                                value="{{ old('remise_montant_proforma', 0) }}" min="0" step="0.01"
                                                class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('remise_montant_proforma') border-red-500 @enderror"
                                                onchange="calculerTotaux()" onkeyup="calculerTotaux()">
                                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                        </div>
                                        <div class="relative w-24">
                                            <input type="number" id="remise_pourcentage" min="0" max="100" step="0.01"
                                                class="w-full px-3 py-3 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-center"
                                                onchange="calculerRemiseFromPourcentage()" onkeyup="calculerRemiseFromPourcentage()"
                                                placeholder="0">
                                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">%</span>
                                        </div>
                                    </div>
                                    @error('remise_montant_proforma')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Taxes -->
                                <div>
                                    <label for="taxe_montant" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Taxes (TVA, etc.)
                                    </label>
                                    <div class="flex space-x-2">
                                        <div class="relative flex-1">
                                            <input type="number" id="taxe_montant" name="taxe_montant"
                                                value="{{ old('taxe_montant', 0) }}" min="0" step="0.01"
                                                class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('taxe_montant') border-red-500 @enderror"
                                                onchange="calculerTotaux()" onkeyup="calculerTotaux()">
                                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                        </div>
                                        <div class="relative w-24">
                                            <input type="number" id="taxe_pourcentage" min="0" max="100" step="0.01"
                                                class="w-full px-3 py-3 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent text-center"
                                                onchange="calculerTaxeFromPourcentage()" onkeyup="calculerTaxeFromPourcentage()"
                                                placeholder="18" value="18">
                                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">%</span>
                                        </div>
                                    </div>
                                    @error('taxe_montant')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pénalités -->
                                <div>
                                    <label for="penalites_proforma" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pénalités éventuelles
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="penalites_proforma" name="penalites_proforma"
                                            value="{{ old('penalites_proforma', 0) }}" min="0" step="0.01"
                                            class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('penalites_proforma') border-red-500 @enderror">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">FCFA</span>
                                    </div>
                                    @error('penalites_proforma')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
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
                            <!-- Montant HT -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-600">Montant HT</span>
                                <span id="resume_montant_ht" class="font-semibold text-gray-900">0 FCFA</span>
                            </div>

                            <!-- Remise -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-600">Remise</span>
                                <span id="resume_remise" class="font-semibold text-red-600">- 0 FCFA</span>
                            </div>

                            <!-- Sous-total -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 bg-gray-50 -mx-6 px-6">
                                <span class="text-gray-700 font-medium">Sous-total HT</span>
                                <span id="resume_sous_total" class="font-bold text-gray-900">0 FCFA</span>
                            </div>

                            <!-- Taxes -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-gray-600">Taxes</span>
                                <span id="resume_taxes" class="font-semibold text-yellow-600">+ 0 FCFA</span>
                            </div>

                            <!-- Total TTC -->
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
                                    <input type="checkbox" name="actif_proforma" value="1" class="sr-only peer" checked>
                                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 space-y-3">
                            <button type="submit"
                                class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg font-semibold">
                                <i class="fas fa-save"></i>
                                <span>Créer la proforma</span>
                            </button>

                            <button type="button" onclick="window.location.href='{{ route('proformas.index') }}'"
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

    @push('scripts')
        <script>
            // Sélection de modalité
            window.selectModalite = function(value) {
                const textarea = document.getElementById('modalite_proforma');
                if (value && value !== 'custom') {
                    textarea.value = value;
                } else if (value === 'custom') {
                    textarea.value = '';
                    textarea.focus();
                }
            }

            // Calcul des totaux
            window.calculerTotaux = function() {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const remise = parseFloat(document.getElementById('remise_montant_proforma').value) || 0;
                const taxe = parseFloat(document.getElementById('taxe_montant').value) || 0;

                const sousTotal = montantHT - remise;
                const totalTTC = sousTotal + taxe;

                // Mettre à jour le pourcentage de remise
                if (montantHT > 0) {
                    document.getElementById('remise_pourcentage').value = ((remise / montantHT) * 100).toFixed(2);
                }

                // Mettre à jour le pourcentage de taxe
                if (sousTotal > 0) {
                    document.getElementById('taxe_pourcentage').value = ((taxe / sousTotal) * 100).toFixed(2);
                }

                // Mettre à jour le résumé
                document.getElementById('resume_montant_ht').textContent = formatMontant(montantHT) + ' FCFA';
                document.getElementById('resume_remise').textContent = '- ' + formatMontant(remise) + ' FCFA';
                document.getElementById('resume_sous_total').textContent = formatMontant(sousTotal) + ' FCFA';
                document.getElementById('resume_taxes').textContent = '+ ' + formatMontant(taxe) + ' FCFA';
                document.getElementById('resume_total_ttc').textContent = formatMontant(totalTTC) + ' FCFA';
            }

            // Calculer remise depuis pourcentage
            window.calculerRemiseFromPourcentage = function() {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const pourcentage = parseFloat(document.getElementById('remise_pourcentage').value) || 0;
                const remise = (montantHT * pourcentage) / 100;

                document.getElementById('remise_montant_proforma').value = remise.toFixed(2);
                calculerTotaux();
            }

            // Calculer taxe depuis pourcentage
            window.calculerTaxeFromPourcentage = function() {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const remise = parseFloat(document.getElementById('remise_montant_proforma').value) || 0;
                const sousTotal = montantHT - remise;
                const pourcentage = parseFloat(document.getElementById('taxe_pourcentage').value) || 0;
                const taxe = (sousTotal * pourcentage) / 100;

                document.getElementById('taxe_montant').value = taxe.toFixed(2);
                calculerTotaux();
            }

            // Formater montant
            function formatMontant(montant) {
                return new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(montant);
            }

            // Régénérer numéro
            window.regenererNumero = function() {
                // Le numéro est généré côté serveur, on affiche juste un message
                alert('Le numéro sera généré automatiquement lors de la création.');
            }

            // Calculer au chargement
            document.addEventListener('DOMContentLoaded', function() {
                calculerTotaux();
            });

            // Validation avant soumission
            document.getElementById('proformaForm').addEventListener('submit', function(e) {
                const montantHT = parseFloat(document.getElementById('montant_retenu_proforma').value) || 0;
                const remise = parseFloat(document.getElementById('remise_montant_proforma').value) || 0;

                if (remise > montantHT) {
                    e.preventDefault();
                    alert('La remise ne peut pas être supérieure au montant HT.');
                    return false;
                }

                // Désactiver le bouton pour éviter double soumission
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Création en cours...';
            });
        </script>

        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
        </style>
    @endpush
@endsection
