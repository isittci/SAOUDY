@extends('layouts.main')

@section('title', 'Nouvelle Facture')

@section('breadcrumb')
    <a href="{{ route('factures.index') }}" class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle Facture</span>
@endsection

@push('styles')

<style>
    /* Personnalisation Tom Select pour correspondre au design */
    .ts-wrapper.single .ts-control {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border-color: #d1d5db;
        min-height: 48px;
        background: #fff;
    }
    .ts-wrapper.single .ts-control:focus,
    .ts-wrapper.single.focus .ts-control {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }
    .ts-dropdown {
        border-radius: 0.5rem;
        border-color: #e5e7eb;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        margin-top: 4px;
        z-index: 9999 !important;
        position: absolute !important;
    }
    .ts-wrapper {
        position: relative;
        z-index: 100;
    }
    .ts-wrapper.dropdown-active {
        z-index: 9999;
    }
    .ts-dropdown .option {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .ts-dropdown .option:last-child {
        border-bottom: none;
    }
    .ts-dropdown .option.active {
        background-color: #fff7ed;
        color: #ea580c;
    }
    .ts-dropdown .option:hover {
        background-color: #fef3e2;
    }
    .ts-wrapper.single .ts-control input {
        font-size: 0.9375rem;
    }
    .ts-control > input::placeholder {
        color: #9ca3af;
    }
    .proforma-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .proforma-option .numero {
        font-weight: 600;
        color: #1f2937;
    }
    .proforma-option .montant {
        font-size: 0.875rem;
        color: #ea580c;
        font-weight: 600;
    }
    .proforma-option .details {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 2px;
    }
    .ts-wrapper.has-error .ts-control {
        border-color: #ef4444;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('factures.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Nouvelle Facture</h1>
                    <p class="text-gray-600 mt-1">Créer une nouvelle facture à partir d'une proforma validée</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <form action="{{ route('factures.store') }}" method="POST" id="factureForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Sélection de la proforma -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-alt text-indigo-500 mr-2"></i>
                                Proforma Source
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <label for="proforma_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Rechercher et sélectionner une proforma <span class="text-red-500">*</span>
                                </label>
                                <select name="proforma_id" id="proforma_id" required
                                    class="w-full @error('proforma_id') border-red-500 @enderror"
                                    placeholder="Tapez pour rechercher une proforma...">
                                    <option value="">-- Rechercher une proforma --</option>
                                    @foreach($proformas as $proforma)
                                        @php
                                            $montantTTC = $proforma->montant_retenu_proforma + $proforma->taxe_montant - $proforma->remise_montant_proforma + $proforma->penalites_proforma;
                                        @endphp
                                        <option value="{{ $proforma->id_proforma }}"
                                            data-montant="{{ $montantTTC }}"
                                            data-numero="{{ $proforma->numero_proforma }}"
                                            data-date="{{ $proforma->date_proforma ? \Carbon\Carbon::parse($proforma->date_proforma)->format('d/m/Y') : 'N/A' }}"
                                            {{ old('proforma_id', $proformaSelectionnee->id_proforma ?? '') == $proforma->id_proforma ? 'selected' : '' }}>
                                            {{ $proforma->numero_proforma }} - {{ number_format($montantTTC, 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                                @error('proforma_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                                    Tapez le numéro de proforma ou le montant pour filtrer rapidement la liste
                                </p>
                            </div>

                            <!-- Détails de la proforma sélectionnée -->
                            <div id="proformaDetails" class="hidden mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                                <h3 class="text-sm font-semibold text-indigo-800 mb-3 flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Détails de la proforma sélectionnée
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-indigo-600 block text-xs uppercase tracking-wide">Numéro</span>
                                        <span id="proformaNumero" class="font-semibold text-gray-900">-</span>
                                    </div>
                                    <div>
                                        <span class="text-indigo-600 block text-xs uppercase tracking-wide">Montant TTC</span>
                                        <span id="proformaMontant" class="font-bold text-indigo-700">-</span>
                                    </div>
                                    <div>
                                        <span class="text-indigo-600 block text-xs uppercase tracking-wide">Date</span>
                                        <span id="proformaDate" class="font-medium text-gray-900">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de la facture -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-invoice-dollar text-orange-500 mr-2"></i>
                                Informations de la Facture
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Numéro de facture -->
                                <div>
                                    <label for="numero_facture" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro de facture
                                        <span class="text-gray-400 font-normal">(optionnel)</span>
                                    </label>
                                    <input type="text" name="numero_facture" id="numero_facture"
                                        value="{{ old('numero_facture') }}"
                                        placeholder="Auto-généré si vide"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('numero_facture') border-red-500 @enderror">
                                    @error('numero_facture')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Montant -->
                                <div>
                                    <label for="montant_facture" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Montant TTC <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="montant_facture" id="montant_facture"
                                            value="{{ old('montant_facture') }}"
                                            required
                                            placeholder="0"
                                            class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('montant_facture') border-red-500 @enderror">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                    </div>
                                    @error('montant_facture')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date de facture -->
                                <div>
                                    <label for="date_facture" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de la facture <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_facture" id="date_facture"
                                        value="{{ old('date_facture', date('Y-m-d')) }}"
                                        required
                                        max="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('date_facture') border-red-500 @enderror">
                                    @error('date_facture')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date de réception -->
                                <div>
                                    <label for="date_reception_facture" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de réception <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_reception_facture" id="date_reception_facture"
                                        value="{{ old('date_reception_facture', date('Y-m-d')) }}"
                                        required
                                        max="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('date_reception_facture') border-red-500 @enderror">
                                    @error('date_reception_facture')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Commentaire -->
                            <div>
                                <label for="comment_facture" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Commentaire / Observations
                                </label>
                                <textarea name="comment_facture" id="comment_facture" rows="4"
                                    placeholder="Notes ou observations sur cette facture..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('comment_facture') border-red-500 @enderror">{{ old('comment_facture') }}</textarea>
                                @error('comment_facture')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">

                    <!-- Résumé -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-6">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calculator text-green-500 mr-2"></i>
                                Résumé
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Proforma</span>
                                <span id="resumeProforma" class="font-medium text-gray-900 text-right truncate ml-2 max-w-[140px]">Non sélectionnée</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm text-orange-700">Montant TTC</span>
                                <span id="resumeMontant" class="font-bold text-orange-700">0 FCFA</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Date facture</span>
                                <span id="resumeDateFacture" class="text-gray-900">{{ date('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                <span class="text-sm text-gray-600">Statut initial</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> En attente
                                </span>
                            </div>

                            <div class="pt-4 border-t border-gray-200 space-y-3">
                                <button type="submit"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center shadow-md">
                                    <i class="fas fa-save mr-2"></i>
                                    Créer la Facture
                                </button>
                                <a href="{{ route('factures.index') }}"
                                    class="w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all duration-200 flex items-center justify-center">
                                    <i class="fas fa-times mr-2"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
@endsection

@push('scripts')
<!-- Tom Select JS -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Éléments du formulaire
        const montantInput = document.getElementById('montant_facture');
        const dateFactureInput = document.getElementById('date_facture');
        const dateReceptionInput = document.getElementById('date_reception_facture');
        const proformaDetails = document.getElementById('proformaDetails');
        const proformaNumero = document.getElementById('proformaNumero');
        const proformaMontant = document.getElementById('proformaMontant');
        const proformaDate = document.getElementById('proformaDate');
        const resumeProforma = document.getElementById('resumeProforma');
        const resumeMontant = document.getElementById('resumeMontant');
        const resumeDateFacture = document.getElementById('resumeDateFacture');

        // Fonction de formatage du montant
        function formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
        }

        // Fonction de formatage de la date
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR');
        }

        // Initialisation de Tom Select pour la recherche de proforma
        const proformaSelect = new TomSelect('#proforma_id', {
            placeholder: 'Tapez pour rechercher une proforma...',
            searchField: ['text'],
            maxOptions: 100,
            dropdownParent: 'body',
            render: {
                option: function(data, escape) {
                    const optionEl = document.querySelector(`option[value="${data.value}"]`);
                    const montant = optionEl ? formatMontant(optionEl.dataset.montant) : '';
                    const date = optionEl ? optionEl.dataset.date : '';
                    const numero = optionEl ? optionEl.dataset.numero : data.text;

                    return `
                        <div class="proforma-option">
                            <div>
                                <div class="numero">${escape(numero)}</div>
                                <div class="details"><i class="fas fa-calendar-alt mr-1"></i>${escape(date)}</div>
                            </div>
                            <div class="montant">${escape(montant)}</div>
                        </div>
                    `;
                },
                item: function(data, escape) {
                    const optionEl = document.querySelector(`option[value="${data.value}"]`);
                    const numero = optionEl ? optionEl.dataset.numero : data.text;
                    return `<div><i class="fas fa-file-alt text-indigo-500 mr-2"></i>${escape(numero)}</div>`;
                },
                no_results: function(data, escape) {
                    return '<div class="no-results p-4 text-gray-500 text-center"><i class="fas fa-search mr-2"></i>Aucune proforma trouvée</div>';
                }
            },
            onChange: function(value) {
                if (value) {
                    const optionEl = document.querySelector(`option[value="${value}"]`);
                    if (optionEl) {
                        const montant = optionEl.dataset.montant || 0;
                        const numero = optionEl.dataset.numero || '';
                        const date = optionEl.dataset.date || '';

                        // Afficher les détails
                        proformaDetails.classList.remove('hidden');
                        proformaNumero.textContent = numero;
                        proformaMontant.textContent = formatMontant(montant);
                        proformaDate.textContent = date;

                        // Pré-remplir le montant
                        montantInput.value = montant;

                        // Mettre à jour le résumé
                        resumeProforma.textContent = numero;
                        resumeMontant.textContent = formatMontant(montant);
                    }
                } else {
                    proformaDetails.classList.add('hidden');
                    montantInput.value = '';
                    resumeProforma.textContent = 'Non sélectionnée';
                    resumeMontant.textContent = '0 FCFA';
                }
            }
        });

        // Mise à jour du résumé lors de la saisie du montant
        montantInput.addEventListener('input', function() {
            const montant = parseFloat(this.value.replace(/\s/g, '').replace(',', '.')) || 0;
            resumeMontant.textContent = formatMontant(montant);
        });

        // Mise à jour du résumé lors du changement de date
        dateFactureInput.addEventListener('change', function() {
            resumeDateFacture.textContent = formatDate(this.value);
        });

        // Validation: date de réception >= date de facture
        dateFactureInput.addEventListener('change', function() {
            dateReceptionInput.min = this.value;
            if (dateReceptionInput.value && dateReceptionInput.value < this.value) {
                dateReceptionInput.value = this.value;
            }
        });

        // Initialiser si une proforma est pré-sélectionnée
        if (proformaSelect.getValue()) {
            const value = proformaSelect.getValue();
            const optionEl = document.querySelector(`option[value="${value}"]`);
            if (optionEl) {
                proformaDetails.classList.remove('hidden');
                proformaNumero.textContent = optionEl.dataset.numero;
                proformaMontant.textContent = formatMontant(optionEl.dataset.montant);
                proformaDate.textContent = optionEl.dataset.date;
                resumeProforma.textContent = optionEl.dataset.numero;
                resumeMontant.textContent = formatMontant(optionEl.dataset.montant);
            }
        }

        // Initialiser la date
        if (dateFactureInput.value) {
            resumeDateFacture.textContent = formatDate(dateFactureInput.value);
        }
    });
</script>
@endpush
