@extends('layouts.main')

@section('title', 'Nouvelle Facture')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-wrapper.form-control { padding: 0; border: none; }
        .ts-control { border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem; min-height: 48px; }
        .ts-control:focus, .ts-wrapper.focus .ts-control { border-color: #f97316; box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2); }

        /* Force z-index élevé pour le dropdown */
        .ts-dropdown {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            z-index: 9999 !important;
            position: absolute !important;
        }

        .ts-wrapper {
            position: relative;
            z-index: 1;
        }

        .ts-wrapper.dropdown-active {
            z-index: 9998 !important;
        }

        .ts-dropdown .option { padding: 0.75rem 1rem; }
        .ts-dropdown .option.active { background-color: #fff7ed; color: #ea580c; }

        .info-card { transition: all 0.3s ease; }
        .info-card.loading { opacity: 0.5; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dashed #e5e7eb; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; font-size: 0.875rem; }
        .detail-value { color: #1f2937; font-weight: 500; text-align: right; max-width: 60%; }
    </style>
@endpush

@section('breadcrumb')
    <a @can('factures.read') href="{{ route('factures.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle Facture</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                @can('factures.read')
                <a href="{{ route('factures.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                @endcan
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

        @can('factures.create')
        <form action="{{ route('factures.store') }}" method="POST" id="factureForm">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="xl:col-span-2 space-y-6">

                    <!-- Section Sélection Hiérarchique -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-visible">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-sitemap text-blue-500 mr-2"></i>
                                Sélection de la Proforma
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">Sélectionnez l'appel d'offre, puis le lot concerné</p>
                        </div>
                        <div class="p-6 space-y-6" style="overflow: visible;">

                            <!-- Étape 1: Appel d'Offre -->
                            <div class="relative">
                                <div class="flex items-center mb-3">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm mr-3">1</span>
                                    <label for="appel_offre" class="block text-sm font-semibold text-gray-700">
                                        Sélectionnez l'Appel d'Offre <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <select id="appel_offre"
                                    class="w-full tom-select-ao"
                                    placeholder="Rechercher un appel d'offre...">
                                    <option value="">Sélectionnez un appel d'offre</option>
                                    @foreach($appelsOffres as $ao)
                                        <option value="{{ $ao->id_appel_offre }}"
                                            data-numero="{{ $ao->numero_appel_offre }}"
                                            data-objet="{{ $ao->libelle_critere_appel_offre }}"
                                            data-montant="{{ number_format($ao->montant_global_appel_offre, 0, ',', ' ') }}"
                                            data-date-pub="{{ $ao->date_publication_critere_appel_offre }}"
                                            data-date-limite="{{ $ao->date_limite_depot_critere_appel_offre }}"
                                            data-type="{{ $ao->typeAppelOffre->code_type_appel_offre ?? '' }}"
                                            data-lots-count="{{ $ao->lots->count() }}">
                                            {{ $ao->numero_appel_offre }} - {{ Str::limit($ao->libelle_critere_appel_offre, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Info Appel d'Offre -->
                            <div id="infoAO" class="hidden info-card bg-green-50 rounded-xl p-4 border border-green-100 fade-in">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-file-contract text-green-500 mr-2"></i>
                                    <span class="font-semibold text-green-800">Informations Appel d'Offre</span>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="detail-row">
                                        <span class="detail-label">Numéro</span>
                                        <span id="infoAONumero" class="detail-value font-mono bg-green-100 px-2 py-0.5 rounded">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Type</span>
                                        <span id="infoAOType" class="detail-value">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">-</span>
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Objet</span>
                                        <span id="infoAOObjet" class="detail-value">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Montant Global</span>
                                        <span id="infoAOMontant" class="detail-value text-green-700 font-bold">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Date Publication</span>
                                        <span id="infoAODatePub" class="detail-value">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Date Limite Dépôt</span>
                                        <span id="infoAODateLimite" class="detail-value text-orange-600">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Nombre de Lots</span>
                                        <span id="infoAOLotsCount" class="detail-value">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">-</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Étape 2: Lot -->
                            <div class="relative">
                                <div class="flex items-center mb-3">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 font-bold text-sm mr-3" id="step2Badge">2</span>
                                    <label for="lot" class="block text-sm font-semibold text-gray-700">
                                        Sélectionnez le lot concerné <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <select id="lot"
                                    class="w-full tom-select-lot"
                                    placeholder="Sélectionnez d'abord un appel d'offre..."
                                    disabled>
                                    <option value="">Sélectionnez un lot</option>
                                </select>
                                <p id="lotHelpText" class="mt-2 text-xs text-gray-500 hidden">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Seuls les lots avec une attribution et une proforma sont affichés
                                </p>
                            </div>

                            <!-- Info Lot -->
                            <div id="infoLot" class="hidden info-card bg-purple-50 rounded-xl p-4 border border-purple-100 fade-in">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-cubes text-purple-500 mr-2"></i>
                                    <span class="font-semibold text-purple-800">Informations du Lot</span>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="detail-row">
                                        <span class="detail-label">Numéro Lot</span>
                                        <span id="infoLotNumero" class="detail-value font-mono bg-purple-100 px-2 py-0.5 rounded">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Libellé</span>
                                        <span id="infoLotLibelle" class="detail-value">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Date Attribution</span>
                                        <span id="infoLotDateAttrib" class="detail-value">-</span>
                                    </div>
                                    
                                </div>
                            </div>

                            <!-- Info Attribution & Prestataire -->
                            <div id="infoAttribution" class="hidden info-card bg-indigo-50 rounded-xl p-4 border border-indigo-100 fade-in">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-handshake text-indigo-500 mr-2"></i>
                                    <span class="font-semibold text-indigo-800">Attribution & Prestataire</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Attribution -->
                                    <div class="space-y-2 text-sm">
                                        <h4 class="font-semibold text-indigo-700 border-b border-indigo-200 pb-1">Attribution</h4>
                                        <div class="detail-row">
                                            <span class="detail-label">N° Attribution</span>
                                            <span id="infoAttribNumero" class="detail-value font-mono text-xs bg-indigo-100 px-2 py-0.5 rounded">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Date</span>
                                            <span id="infoAttribDate" class="detail-value">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Avancement</span>
                                            <span id="infoAttribAvancement" class="detail-value">-</span>
                                        </div>
                                    </div>
                                    <!-- Prestataire -->
                                    <div class="space-y-2 text-sm">
                                        <h4 class="font-semibold text-indigo-700 border-b border-indigo-200 pb-1">Prestataire</h4>
                                        <div class="detail-row">
                                            <span class="detail-label">Raison Sociale</span>
                                            <span id="infoPrestaRaison" class="detail-value text-xs">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">N° Identification</span>
                                            <span id="infoPrestaNumero" class="detail-value font-mono text-xs">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Email</span>
                                            <span id="infoPrestaEmail" class="detail-value text-xs truncate">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Téléphone</span>
                                            <span id="infoPrestaTel" class="detail-value">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Proforma -->
                            <div id="infoProforma" class="hidden info-card bg-orange-50 rounded-xl p-4 border border-orange-100 fade-in">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-file-invoice text-orange-500 mr-2"></i>
                                    <span class="font-semibold text-orange-800">Proforma associée</span>
                                    <span class="ml-auto px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                        <i class="fas fa-check mr-1"></i>Disponible
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2">
                                        <div class="detail-row">
                                            <span class="detail-label">Numéro</span>
                                            <span id="infoProformaNumero" class="detail-value font-mono bg-orange-100 px-2 py-0.5 rounded">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Date</span>
                                            <span id="infoProformaDate" class="detail-value">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Modalité</span>
                                            <span id="infoProformaModalite" class="detail-value text-xs">-</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="detail-row">
                                            <span class="detail-label">Montant HT</span>
                                            <span id="infoProformaMontant" class="detail-value text-orange-700 font-bold">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">TVA</span>
                                            <span id="infoProformaTaxe" class="detail-value text-blue-600">-</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Remise</span>
                                            <span id="infoProformaRemise" class="detail-value text-green-600">-</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Champ hidden pour l'ID de la proforma -->
                                <input type="hidden" name="proforma_id" id="proforma_id" value="">
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
                                        Numéro de facture <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="numero_facture" id="numero_facture" required
                                        value="{{ old('numero_facture') }}"
                                        placeholder="FACT-REF-001"
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
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-sm text-green-700">Appel d'Offre</span>
                                <span id="resumeAO" class="font-medium text-green-900 text-right truncate ml-2 max-w-[140px]">-</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <span class="text-sm text-purple-700">Lot</span>
                                <span id="resumeLot" class="font-medium text-purple-900 text-right truncate ml-2 max-w-[140px]">-</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Proforma</span>
                                <span id="resumeProforma" class="font-medium text-gray-900 text-right truncate ml-2 max-w-[140px]">Non sélectionnée</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                                <span class="text-sm text-indigo-700">Prestataire</span>
                                <span id="resumePrestataire" class="font-medium text-indigo-900 text-right truncate ml-2 max-w-[140px] text-xs">-</span>
                            </div>

                            <hr class="border-gray-200">

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
                                <button type="submit" id="btnSubmit"
                                    disabled
                                    class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
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
        @endcan
    </main>
@endsection

@can('appels_offres.create')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    // Données des appels d'offres avec leurs lots
    const appelsOffres = @json($appelsOffres);

    // Variables globales pour Tom Select
    let selectAO, selectLot;

    // Données sélectionnées
    let selectedAO = null;
    let selectedLot = null;

    // Fonctions utilitaires
    function formatNumber(num) {
        if (!num) return '0';
        return parseFloat(num).toLocaleString('fr-FR');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR');
    }

    function truncateText(text, maxLength = 50) {
        if (!text) return '-';
        return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    }

    // Initialisation des Tom Select
    document.addEventListener('DOMContentLoaded', function() {

        // Tom Select pour Appel d'Offre
        selectAO = new TomSelect('#appel_offre', {
            placeholder: 'Rechercher un appel d\'offre...',
            searchField: ['text'],
            maxOptions: null,
            render: {
                option: function(data, escape) {
                    return `<div class="py-2">
                        <div class="font-semibold">${escape(data.text)}</div>
                        <div class="text-xs text-gray-500">
                            Type: ${escape(data.type || '-')} | Montant: ${escape(data.montant || '-')} FCFA
                        </div>
                    </div>`;
                },
                item: function(data, escape) {
                    return `<div>${escape(data.text)}</div>`;
                }
            },
            onChange: function(value) {
                onAOChange(value);
            }
        });

        // Ajouter les données supplémentaires aux options
        appelsOffres.forEach(ao => {
            const option = selectAO.options[ao.id_appel_offre];
            if (option) {
                option.type = ao.type_appel_offre?.code_type_appel_offre || '-';
                option.montant = formatNumber(ao.montant_global_appel_offre);
            }
        });

        // Tom Select pour Lot
        selectLot = new TomSelect('#lot', {
            placeholder: 'Sélectionnez d\'abord un appel d\'offre...',
            searchField: ['text'],
            maxOptions: null,
            render: {
                option: function(data, escape) {
                    return `<div class="py-2">
                        <div class="font-semibold">${escape(data.text)}</div>
                        <div class="text-xs text-gray-500">${escape(data.description || '')}</div>
                    </div>`;
                }
            },
            onChange: function(value) {
                onLotChange(value);
            }
        });
        selectLot.disable();

        // Écouteurs pour les champs de facture
        document.getElementById('montant_facture').addEventListener('input', updateResume);
        document.getElementById('date_facture').addEventListener('change', updateResume);
    });

    // Changement d'Appel d'Offre
    function onAOChange(aoId) {
        // Reset le lot
        resetLot();
        hideAllInfos();

        if (!aoId) {
            document.getElementById('step2Badge').className = 'flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 font-bold text-sm mr-3';
            document.getElementById('resumeAO').textContent = '-';
            return;
        }

        // Trouver l'appel d'offre sélectionné
        selectedAO = appelsOffres.find(ao => ao.id_appel_offre === aoId);

        if (selectedAO) {
            // Afficher les infos de l'AO
            showAOInfo(selectedAO);

            // Charger les lots
            loadLots(selectedAO.lots || []);

            // Activer l'étape 2
            document.getElementById('step2Badge').className = 'flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm mr-3';

            // Mettre à jour le résumé
            document.getElementById('resumeAO').textContent = selectedAO.numero_appel_offre;
        }
    }

    // Afficher les infos de l'AO
    function showAOInfo(ao) {
        document.getElementById('infoAONumero').textContent = ao.numero_appel_offre || '-';
        document.getElementById('infoAOType').innerHTML = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${ao.type_appel_offre?.code_type_appel_offre || '-'}</span>`;
        document.getElementById('infoAOObjet').textContent = truncateText(ao.libelle_critere_appel_offre, 60);
        document.getElementById('infoAOMontant').textContent = formatNumber(ao.montant_global_appel_offre) + ' FCFA';
        document.getElementById('infoAODatePub').textContent = formatDate(ao.date_publication_critere_appel_offre);
        document.getElementById('infoAODateLimite').textContent = formatDate(ao.date_limite_depot_critere_appel_offre);
        document.getElementById('infoAOLotsCount').innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">${ao.lots?.length || 0} lot(s)</span>`;

        document.getElementById('infoAO').classList.remove('hidden');
    }

    // Charger les lots dans le select
    function loadLots(lots) {
        selectLot.clear();
        selectLot.clearOptions();

        // Filtrer uniquement les lots qui ont une attribution active avec proforma
        const lotsAvecProforma = lots.filter(lot =>
            lot.attribution_active && lot.attribution_active.proforma
        );

        if (lotsAvecProforma.length === 0) {
            selectLot.settings.placeholder = 'Aucun lot avec proforma disponible';
            selectLot.disable();
            document.getElementById('lotHelpText').classList.remove('hidden');
            document.getElementById('lotHelpText').innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> Aucun lot avec proforma n\'est disponible pour cet appel d\'offre';
            return;
        }

        lotsAvecProforma.forEach(lot => {
            selectLot.addOption({
                value: lot.id_lot,
                text: lot.numero + ' - ' + truncateText(lot.libelle, 30),
                description: truncateText(lot.description_critere, 50)
            });
        });

        selectLot.settings.placeholder = 'Rechercher un lot...';
        selectLot.enable();
        selectLot.refreshOptions(false);

        document.getElementById('lotHelpText').classList.remove('hidden');
        document.getElementById('lotHelpText').innerHTML = `<i class="fas fa-info-circle mr-1"></i> ${lotsAvecProforma.length} lot(s) avec proforma disponible(s)`;

        updateResume();
    }

    // Changement de Lot
    function onLotChange(lotId) {
        hideProformaInfo();

        if (!lotId || !selectedAO) {
            disableSubmit();
            return;
        }

        // Trouver le lot sélectionné
        selectedLot = selectedAO.lots.find(l => l.id_lot === lotId);

        if (selectedLot && selectedLot.attribution_active) {
            // Afficher les infos du lot
            showLotInfo(selectedLot);

            // Afficher les infos de l'attribution et prestataire
            showAttributionInfo(selectedLot.attribution_active);

            // Afficher les infos de la proforma
            if (selectedLot.attribution_active.proforma) {
                showProformaInfo(selectedLot.attribution_active.proforma);
                enableSubmit();
            }

            // Mettre à jour le résumé
            document.getElementById('resumeLot').textContent = selectedLot.numero;
        }
        updateResume();
    }

    // Afficher les infos du lot
    function showLotInfo(lot) {
        document.getElementById('infoLotNumero').textContent = lot.numero || '-';
        document.getElementById('infoLotLibelle').textContent = truncateText(lot.libelle, 50);
        document.getElementById('infoLotDateAttrib').textContent = formatDate(lot.date_attribution);
        // document.getElementById('infoLotPenalite').textContent = (lot.taux_penalites || '0') + ' %';

        document.getElementById('infoLot').classList.remove('hidden');
    }

    // Afficher les infos de l'attribution et prestataire
    function showAttributionInfo(attribution) {
        // Attribution
        document.getElementById('infoAttribNumero').textContent = attribution.numero_attribution || '-';
        document.getElementById('infoAttribDate').textContent = formatDate(attribution.date_attribution);
        document.getElementById('infoAttribAvancement').innerHTML = `
            <div class="flex items-center">
                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: ${attribution.pourcentage_avancement || 0}%"></div>
                </div>
                <span>${attribution.pourcentage_avancement || 0}%</span>
            </div>
        `;

        // Prestataire
        if (attribution.prestataire) {
            const p = attribution.prestataire;
            document.getElementById('infoPrestaRaison').textContent = truncateText(p.raison_sociale_prestataire, 30);
            document.getElementById('infoPrestaNumero').textContent = p.numero_identification_prestataire || '-';
            document.getElementById('infoPrestaEmail').textContent = p.email_prestataire || '-';
            document.getElementById('infoPrestaTel').textContent = p.telephone_principal_prestataire || '-';

            // Résumé
            document.getElementById('resumePrestataire').textContent = truncateText(p.raison_sociale_prestataire, 20);
        }

        document.getElementById('infoAttribution').classList.remove('hidden');
    }

    // Afficher les infos de la proforma
    function showProformaInfo(proforma) {
        document.getElementById('infoProformaNumero').textContent = proforma.numero_proforma || '-';
        document.getElementById('infoProformaDate').textContent = formatDate(proforma.date_proforma);
        document.getElementById('infoProformaMontant').textContent = formatNumber(proforma.montant_retenu_proforma) + ' FCFA';
        document.getElementById('infoProformaTaxe').textContent = '+ ' + formatNumber(proforma.taxe_montant) + ' FCFA';
        document.getElementById('infoProformaRemise').textContent = '- ' + formatNumber(proforma.remise_montant_proforma) + ' FCFA';
        document.getElementById('infoProformaModalite').textContent = truncateText(proforma.modalite_proforma, 40);

        // Calculer le montant TTC
        const montantTTC = parseInt(proforma.montant_retenu_proforma || 0) + parseInt(proforma.taxe_montant || 0) - parseInt(proforma.remise_montant_proforma || 0);
        document.getElementById('montant_facture').value = montantTTC;

        // Définir l'ID de la proforma dans le champ hidden
        document.getElementById('proforma_id').value = proforma.id_proforma;

        // Mettre à jour le résumé
        document.getElementById('resumeProforma').textContent = proforma.numero_proforma;

        document.getElementById('infoProforma').classList.remove('hidden');

        updateResume();
    }

    // Reset functions
    function resetLot() {
        selectLot.clear();
        selectLot.clearOptions();
        selectLot.disable();
        selectLot.settings.placeholder = 'Sélectionnez d\'abord un appel d\'offre...';
        selectedLot = null;
        document.getElementById('resumeLot').textContent = '-';
        document.getElementById('resumeProforma').textContent = 'Non sélectionnée';
        document.getElementById('resumePrestataire').textContent = '-';
        document.getElementById('proforma_id').value = '';
        document.getElementById('lotHelpText').classList.add('hidden');
        disableSubmit();
    }

    function hideAllInfos() {
        document.getElementById('infoAO').classList.add('hidden');
        document.getElementById('infoLot').classList.add('hidden');
        document.getElementById('infoAttribution').classList.add('hidden');
        document.getElementById('infoProforma').classList.add('hidden');
    }

    function hideProformaInfo() {
        document.getElementById('infoLot').classList.add('hidden');
        document.getElementById('infoAttribution').classList.add('hidden');
        document.getElementById('infoProforma').classList.add('hidden');
        document.getElementById('resumeLot').textContent = '-';
        document.getElementById('resumeProforma').textContent = 'Non sélectionnée';
        document.getElementById('resumePrestataire').textContent = '-';
    }

    function enableSubmit() {
        document.getElementById('btnSubmit').disabled = false;
    }

    function disableSubmit() {
        document.getElementById('btnSubmit').disabled = true;
    }

    // Mise à jour du résumé
    function updateResume() {
        const montant = document.getElementById('montant_facture').value;
        const dateFacture = document.getElementById('date_facture').value;

        // Formater le montant
        if (montant) {
            const montantNum = parseFloat(montant.replace(/\s/g, '').replace(',', '.')) || 0;
            document.getElementById('resumeMontant').textContent = formatNumber(montantNum) + ' FCFA';
        } else {
            document.getElementById('resumeMontant').textContent = '0 FCFA';
        }

        // Formater la date
        if (dateFacture) {
            document.getElementById('resumeDateFacture').textContent = formatDate(dateFacture);
        }
    }
</script>
@endpush
@endcan
