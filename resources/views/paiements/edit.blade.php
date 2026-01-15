@extends('layouts.main')
@section('title', 'Modifier le paiement')

@push('styles')
<style>
    .info-card {
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #e5e7eb;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        color: #6b7280;
        font-size: 0.875rem;
    }
    .detail-value {
        color: #1f2937;
        font-weight: 500;
        text-align: right;
    }
    .progress-bar-animated {
        animation: progressAnimation 1.5s ease-in-out;
    }
    @keyframes progressAnimation {
        from { width: 0%; }
    }
    .pulse-orange {
        animation: pulseOrange 2s infinite;
    }
    @keyframes pulseOrange {
        0%, 100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(249, 115, 22, 0); }
    }
</style>
@endpush

@section('breadcrumb')
    <a @can('factures.read') href="{{ route('factures.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('factures.view-details') href="{{ route('factures.show', $factureId) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ $paiement->facture->numero_facture }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('paiements.read') href="{{ route('paiements.index', $factureId) }}" @endcan class="text-white/80 hover:text-white transition-colors">Paiements</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    @php
        $facture = $paiement->facture;
        $proforma = $facture->proforma;
        $attribution = $proforma?->prestatairePrincipal;
        $lot = $attribution?->lot;
        $appelOffre = $lot?->appelOffre;
        $prestataire = $proforma?->getPrestataire();

        // Calculs des montants
        $montantFacture = $facture->montant_facture ?? 0;
        $montantPaye = $facture->montant_paye ?? 0;
        // Pour l'édition, le montant restant inclut le montant actuel du paiement
        $montantActuelPaiement = $paiement->montant_net_paye_paiement ?? 0;
        $montantRestant = ($facture->montant_restant ?? $montantFacture) + $montantActuelPaiement;
        $pourcentagePaye = $montantFacture > 0 ? min(100, (($montantPaye - $montantActuelPaiement) / $montantFacture) * 100) : 0;
    @endphp

    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @can('paiements.view-details')
                    <a href="{{ route('paiements.show', [$factureId, $paiement->id_paiement]) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Modifier le paiement</h1>
                        <p class="text-gray-600 mt-1">
                            <span class="font-mono text-sm bg-orange-100 px-2 py-0.5 rounded">{{ $paiement->reference_paiement }}</span>
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        @if($paiement->statut_paiement == 3) bg-green-100 text-green-800
                        @elseif($paiement->statut_paiement == 1) bg-blue-100 text-blue-800
                        @elseif($paiement->statut_paiement == 2) bg-indigo-100 text-indigo-800
                        @elseif($paiement->statut_paiement == 4) bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        <i class="fas {{ $paiement->statut_icone ?? 'fa-clock' }} text-xs mr-1"></i>
                        {{ $paiement->statut_libelle }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-red-700 font-medium mb-2">Erreurs de validation :</p>
                        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <!-- Colonne principale - Formulaire -->
            <div class="xl:col-span-2 space-y-6">

                <!-- Fil d'Ariane Contextuel -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-sitemap text-indigo-500 mr-2"></i>
                            Contexte du paiement
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <!-- Appel d'Offre -->
                            <div class="flex items-center bg-purple-50 px-3 py-2 rounded-lg">
                                <i class="fas fa-bullhorn text-purple-500 mr-2"></i>
                                <div>
                                    <span class="text-xs text-purple-600 block">Appel d'Offre</span>
                                    <span class="font-semibold text-purple-800">{{ $appelOffre->numero_appel_offre ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <i class="fas fa-chevron-right text-gray-300"></i>

                            <!-- Lot -->
                            <div class="flex items-center bg-blue-50 px-3 py-2 rounded-lg">
                                <i class="fas fa-cubes text-blue-500 mr-2"></i>
                                <div>
                                    <span class="text-xs text-blue-600 block">Lot</span>
                                    <span class="font-semibold text-blue-800">{{ $lot->numero ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <i class="fas fa-chevron-right text-gray-300"></i>

                            <!-- Proforma -->
                            <div class="flex items-center bg-amber-50 px-3 py-2 rounded-lg">
                                <i class="fas fa-file-alt text-amber-500 mr-2"></i>
                                <div>
                                    <span class="text-xs text-amber-600 block">Proforma</span>
                                    <span class="font-semibold text-amber-800">{{ $proforma->numero_proforma ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <i class="fas fa-chevron-right text-gray-300"></i>

                            <!-- Facture -->
                            <div class="flex items-center bg-green-50 px-3 py-2 rounded-lg">
                                <i class="fas fa-file-invoice-dollar text-green-500 mr-2"></i>
                                <div>
                                    <span class="text-xs text-green-600 block">Facture</span>
                                    <span class="font-semibold text-green-800">{{ $facture->numero_facture }}</span>
                                </div>
                            </div>

                            <i class="fas fa-chevron-right text-gray-300"></i>

                            <!-- Paiement actuel -->
                            <div class="flex items-center bg-orange-50 px-3 py-2 rounded-lg border-2 border-orange-200">
                                <i class="fas fa-money-check-alt text-orange-500 mr-2"></i>
                                <div>
                                    <span class="text-xs text-orange-600 block">Paiement</span>
                                    <span class="font-semibold text-orange-800">{{ $paiement->reference_paiement }}</span>
                                </div>
                            </div>
                        </div>

                        @if($lot)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-info-circle text-gray-400 mr-1"></i>
                                <strong>Objet :</strong> {{ Str::limit($lot->libelle, 100) }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Progression des paiements -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-emerald-500 mr-2"></i>
                            État des paiements
                        </h2>
                    </div>
                    <div class="p-6">
                        <!-- Barre de progression -->
                        <div class="mb-6">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Progression du paiement (hors ce paiement)</span>
                                <span class="font-bold text-emerald-600">{{ number_format($pourcentagePaye, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                <div class="h-4 rounded-full progress-bar-animated transition-all duration-500
                                    @if($pourcentagePaye >= 100) bg-green-500
                                    @elseif($pourcentagePaye >= 50) bg-emerald-500
                                    @elseif($pourcentagePaye > 0) bg-amber-500
                                    @else bg-gray-300 @endif"
                                    style="width: {{ $pourcentagePaye }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Montants -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Montant Facture</p>
                                <p class="text-xl font-bold text-gray-800">{{ number_format($montantFacture, 0, ',', ' ') }}</p>
                                <p class="text-xs text-gray-500">FCFA</p>
                            </div>
                            <div class="bg-emerald-50 rounded-xl p-4 text-center">
                                <p class="text-xs text-emerald-600 uppercase tracking-wide mb-1">Déjà Payé (autres)</p>
                                <p class="text-xl font-bold text-emerald-700">{{ number_format($montantPaye - $montantActuelPaiement, 0, ',', ' ') }}</p>
                                <p class="text-xs text-emerald-600">FCFA</p>
                            </div>
                            <div class="bg-orange-50 rounded-xl p-4 text-center pulse-orange">
                                <p class="text-xs text-orange-600 uppercase tracking-wide mb-1">Disponible pour ce paiement</p>
                                <p class="text-xl font-bold text-orange-700">{{ number_format($montantRestant, 0, ',', ' ') }}</p>
                                <p class="text-xs text-orange-600">FCFA</p>
                            </div>
                        </div>

                        <!-- Info modification -->
                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-sm text-blue-800">
                                    <strong>Note :</strong> Le montant disponible inclut le montant actuel de ce paiement
                                    (<span class="font-semibold">{{ number_format($montantActuelPaiement, 0, ',', ' ') }} FCFA</span>).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @can('paiements.update')
                <!-- Formulaire de modification -->
                <form action="{{ route('paiements.update', [$factureId, $paiement->id_paiement]) }}"
                    method="POST" id="paiementForm">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-edit text-orange-500 mr-2"></i>
                                Modifier les informations du paiement
                            </h2>
                        </div>

                        <div class="p-6 space-y-6">

                            <!-- Sélection de la banque -->
                            <div>
                                <label for="banque_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-university text-gray-400 mr-1"></i>
                                    Compte bancaire destinataire <span class="text-red-500">*</span>
                                </label>
                                <select name="banque_id" id="banque_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all">
                                    <option value="">-- Sélectionner un compte bancaire --</option>
                                    @foreach($banques as $banque)
                                        <option value="{{ $banque->id_banque }}"
                                            {{ old('banque_id', $paiement->banque_id) == $banque->id_banque ? 'selected' : '' }}
                                            data-nom="{{ $banque->nom_banque }}"
                                            data-compte="{{ $banque->numero_compte_banque }}"
                                            data-titulaire="{{ $banque->titulaire_compte_banque }}"
                                            data-iban="{{ $banque->iban_banque }}"
                                            data-swift="{{ $banque->swift_bic_banque }}"
                                            data-rib="{{ $banque->rib_complet }}">
                                            {{ $banque->nom_banque }} - {{ $banque->numero_compte_banque }}
                                            @if($banque->titulaire_compte_banque)
                                                ({{ $banque->titulaire_compte_banque }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Détails banque sélectionnée -->
                                <div id="banqueDetails" class="hidden mt-3 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-building text-blue-500 mr-2"></i>
                                        <span class="font-semibold text-blue-800" id="banqueNom">-</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-blue-600">Titulaire:</span>
                                            <p class="font-medium text-gray-800" id="banqueTitulaire">-</p>
                                        </div>
                                        <div>
                                            <span class="text-blue-600">N° Compte:</span>
                                            <p class="font-medium text-gray-800 font-mono" id="banqueCompte">-</p>
                                        </div>
                                        <div>
                                            <span class="text-blue-600">IBAN:</span>
                                            <p class="font-medium text-gray-800 font-mono text-xs" id="banqueIban">-</p>
                                        </div>
                                        <div>
                                            <span class="text-blue-600">SWIFT/BIC:</span>
                                            <p class="font-medium text-gray-800 font-mono" id="banqueSwift">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Montant du paiement -->
                            <div>
                                <label for="montant_net_paye_paiement" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-coins text-gray-400 mr-1"></i>
                                    Montant du paiement <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                        name="montant_net_paye_paiement"
                                        id="montant_net_paye_paiement"
                                        value="{{ old('montant_net_paye_paiement', $paiement->montant_net_paye_paiement) }}"
                                        step="1"
                                        min="1"
                                        max="{{ $montantRestant }}"
                                        required
                                        class="w-full px-4 py-3 pr-20 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent text-lg font-semibold transition-all"
                                        placeholder="0">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                                </div>

                                <!-- Boutons de montant rapide -->
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <button type="button" onclick="setMontant({{ $montantRestant }})"
                                        class="px-3 py-1.5 text-sm bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-all">
                                        <i class="fas fa-check-double mr-1"></i>
                                        Solde total ({{ number_format($montantRestant, 0, ',', ' ') }})
                                    </button>
                                    <button type="button" onclick="setMontant({{ $montantActuelPaiement }})"
                                        class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                                        <i class="fas fa-undo mr-1"></i>
                                        Valeur initiale ({{ number_format($montantActuelPaiement, 0, ',', ' ') }})
                                    </button>
                                    @if($montantRestant > 100000)
                                    <button type="button" onclick="setMontant({{ floor($montantRestant / 2) }})"
                                        class="px-3 py-1.5 text-sm bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-all">
                                        <i class="fas fa-divide mr-1"></i>
                                        50% ({{ number_format(floor($montantRestant / 2), 0, ',', ' ') }})
                                    </button>
                                    @endif
                                </div>

                                <!-- Indicateur du reste après paiement -->
                                <div id="resteApres" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Reste après ce paiement:</span>
                                        <span id="resteApresValeur" class="font-bold text-gray-800">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Date effective du paiement -->
                            <div>
                                <label for="date_effectif_paiement" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                    Date effective du paiement
                                </label>
                                <input type="date"
                                    name="date_effectif_paiement"
                                    id="date_effectif_paiement"
                                    value="{{ old('date_effectif_paiement', $paiement->date_effectif_paiement?->format('Y-m-d')) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all">
                            </div>

                            <!-- Observations -->
                            <div>
                                <label for="observations_paiement" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-comment-alt text-gray-400 mr-1"></i>
                                    Observations / Référence
                                </label>
                                <textarea name="observations_paiement"
                                    id="observations_paiement"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all"
                                    placeholder="Référence de virement, notes particulières, instructions...">{{ old('observations_paiement', $paiement->observations_paiement) }}</textarea>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
                            <a href="{{ route('paiements.show', [$factureId, $paiement->id_paiement]) }}"
                                class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </a>
                            <button type="submit" id="btnSubmit"
                                class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-xl transition-all shadow-md flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
                @endcan

                <!-- Historique des autres paiements de la facture -->
                @if($facture->paiements && $facture->paiements->where('id_paiement', '!=', $paiement->id_paiement)->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history text-gray-500 mr-2"></i>
                            Autres paiements de cette facture
                            <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-gray-200 text-gray-700 rounded-full">
                                {{ $facture->paiements->where('id_paiement', '!=', $paiement->id_paiement)->count() }}
                            </span>
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Référence</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Banque</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Montant</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($facture->paiements->where('id_paiement', '!=', $paiement->id_paiement)->sortByDesc('created_at') as $autrePaiement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $autrePaiement->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-800">
                                        {{ $autrePaiement->reference_paiement }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800">
                                        {{ $autrePaiement->banque->nom_banque ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-800">
                                        {{ number_format($autrePaiement->montant_net_paye_paiement, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($autrePaiement->statut_paiement == 3) bg-green-100 text-green-800
                                            @elseif($autrePaiement->statut_paiement == 1) bg-blue-100 text-blue-800
                                            @elseif($autrePaiement->statut_paiement == 2) bg-indigo-100 text-indigo-800
                                            @elseif($autrePaiement->statut_paiement == 4) bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            <i class="fas {{ $autrePaiement->statut_icone ?? 'fa-clock' }} mr-1 text-xs"></i>
                                            {{ $autrePaiement->statut_libelle }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>

            <!-- Colonne latérale - Informations -->
            <div class="space-y-6">

                <!-- Informations Prestataire -->
                @if($prestataire)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-orange-500 mr-2"></i>
                            Prestataire
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-user-tie text-orange-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $prestataire->raison_sociale_prestataire }}</p>
                                <p class="text-sm text-gray-500">{{ $prestataire->numero_identification_prestataire ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm border-t pt-4">
                            @if($prestataire->email_prestataire)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-envelope w-5 text-gray-400"></i>
                                <span class="ml-2">{{ $prestataire->email_prestataire }}</span>
                            </div>
                            @endif
                            @if($prestataire->telephone_principal_prestataire)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-phone w-5 text-gray-400"></i>
                                <span class="ml-2">{{ $prestataire->telephone_principal_prestataire }}</span>
                            </div>
                            @endif
                            @if($prestataire->adresse_prestataire)
                            <div class="flex items-start text-gray-600">
                                <i class="fas fa-map-marker-alt w-5 text-gray-400 mt-0.5"></i>
                                <span class="ml-2">{{ $prestataire->getAdresseComplete() }}</span>
                            </div>
                            @endif

                            @if($prestataire->representant_legal_prestataire)
                                @php
                                    $representant = is_array($prestataire->representant_legal_prestataire)
                                        ? $prestataire->representant_legal_prestataire[0]
                                        : json_decode($prestataire->representant_legal_prestataire, true)[0];
                                @endphp

                                @if($representant)
                                    <div class="border-t pt-3 mt-3">
                                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">
                                            <i class="fas fa-user-tie text-gray-400 mr-1"></i>
                                            Représentant Légal
                                        </p>
                                        <div class="space-y-2 text-sm">
                                            @if(!empty($representant['nom']) || !empty($representant['prenoms']))
                                                <div class="flex items-center text-gray-700">
                                                    <i class="fas fa-user w-5 text-gray-400"></i>
                                                    <span class="ml-2 font-medium">
                                                        {{ $representant['prenoms'] ?? '' }} {{ $representant['nom'] ?? '' }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if(!empty($representant['profession']))
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-briefcase w-5 text-gray-400"></i>
                                                    <span class="ml-2">{{ $representant['profession'] }}</span>
                                                </div>
                                            @endif

                                            @if(!empty($representant['contact']))
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-phone w-5 text-gray-400"></i>
                                                    <span class="ml-2">{{ $representant['contact'] }}</span>
                                                </div>
                                            @endif

                                            @if(!empty($representant['email']))
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-envelope w-5 text-gray-400"></i>
                                                    <span class="ml-2">{{ $representant['email'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Détails Facture -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-invoice text-blue-500 mr-2"></i>
                            Détails Facture
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="detail-row">
                            <span class="detail-label">Numéro</span>
                            <span class="detail-value font-mono bg-blue-50 px-2 py-0.5 rounded">{{ $facture->numero_facture }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date facture</span>
                            <span class="detail-value">{{ $facture->date_facture?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date réception</span>
                            <span class="detail-value">{{ $facture->date_reception_facture?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Montant</span>
                            <span class="detail-value text-lg font-bold text-blue-600">{{ number_format($facture->montant_facture, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Détails Proforma -->
                @if($proforma)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-alt text-amber-500 mr-2"></i>
                            Détails Proforma
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="detail-row">
                            <span class="detail-label">Numéro</span>
                            <span class="detail-value font-mono">{{ $proforma->numero_proforma }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Montant HT</span>
                            <span class="detail-value">{{ number_format($proforma->montant_retenu_proforma, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">TVA</span>
                            <span class="detail-value text-blue-600">+ {{ number_format($proforma->taxe_montant ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Remise</span>
                            <span class="detail-value text-red-600">- {{ number_format($proforma->remise_montant_proforma ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="detail-row border-t-2 pt-2">
                            <span class="detail-label font-semibold">Total TTC</span>
                            <span class="detail-value text-lg font-bold text-amber-600">{{ number_format($proforma->montant_ttc ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if($proforma->modalite_proforma)
                        <div class="mt-3 p-3 bg-amber-50 rounded-lg">
                            <p class="text-xs text-amber-600 uppercase font-semibold mb-1">Modalités</p>
                            <p class="text-sm text-gray-700">{{ $proforma->modalite_proforma }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Attribution -->
                @if($attribution)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-handshake text-purple-500 mr-2"></i>
                            Attribution
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="detail-row">
                            <span class="detail-label">Numéro</span>
                            <span class="detail-value font-mono text-xs">{{ $attribution->numero_attribution }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">{{ $attribution->date_attribution?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Avancement</span>
                            <span class="detail-value">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $attribution->pourcentage_avancement ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-purple-600 font-semibold">{{ $attribution->pourcentage_avancement ?? 0 }}%</span>
                                </div>
                            </span>
                        </div>
                        @if($attribution->montant_engage > 0)
                        <div class="detail-row">
                            <span class="detail-label">Engagé</span>
                            <span class="detail-value">{{ number_format($attribution->montant_engage, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @endif
                        @if($attribution->montant_paye > 0)
                        <div class="detail-row">
                            <span class="detail-label">Payé</span>
                            <span class="detail-value text-green-600">{{ number_format($attribution->montant_paye, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Note d'information -->
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 mr-3"></i>
                        <div class="text-sm text-amber-800">
                            <p class="font-semibold mb-1">Attention</p>
                            <ul class="list-disc list-inside space-y-1 text-xs text-amber-700">
                                <li>La modification sera enregistrée dans l'historique</li>
                                <li>Le statut du paiement sera conservé</li>
                                <li>Vérifiez les coordonnées bancaires avant validation</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    const montantRestant = {{ $montantRestant ?? 0 }};
    const montantActuel = {{ $montantActuelPaiement ?? 0 }};
    const montantInput = document.getElementById('montant_net_paye_paiement');
    const banqueSelect = document.getElementById('banque_id');
    const banqueDetails = document.getElementById('banqueDetails');
    const resteApres = document.getElementById('resteApres');

    // Afficher les détails de la banque sélectionnée
    if (banqueSelect) {
        banqueSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];

            if (this.value) {
                document.getElementById('banqueNom').textContent = option.dataset.nom || '-';
                document.getElementById('banqueTitulaire').textContent = option.dataset.titulaire || '-';
                document.getElementById('banqueCompte').textContent = option.dataset.compte || '-';
                document.getElementById('banqueIban').textContent = option.dataset.iban || '-';
                document.getElementById('banqueSwift').textContent = option.dataset.swift || '-';
                banqueDetails.classList.remove('hidden');
            } else {
                banqueDetails.classList.add('hidden');
            }
        });

        // Déclencher au chargement si une valeur est déjà sélectionnée
        if (banqueSelect.value) {
            banqueSelect.dispatchEvent(new Event('change'));
        }
    }

    // Calculer le reste après paiement
    if (montantInput) {
        montantInput.addEventListener('input', function() {
            const montant = parseFloat(this.value) || 0;
            const reste = montantRestant - montant;

            if (montant > 0) {
                resteApres.classList.remove('hidden');
                const resteValeur = document.getElementById('resteApresValeur');

                if (reste <= 0) {
                    resteValeur.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Facture soldée</span>';
                } else {
                    resteValeur.textContent = new Intl.NumberFormat('fr-FR').format(reste) + ' FCFA';
                }
            } else {
                resteApres.classList.add('hidden');
            }

            // Validation max
            if (montant > montantRestant) {
                this.value = montantRestant;
                this.dispatchEvent(new Event('input'));
            }
        });

        // Déclencher au chargement pour afficher le reste initial
        montantInput.dispatchEvent(new Event('input'));
    }

    // Fonction pour définir un montant rapidement
    function setMontant(value) {
        if (montantInput) {
            montantInput.value = value;
            montantInput.dispatchEvent(new Event('input'));
            montantInput.focus();
        }
    }

    // Validation du formulaire
    document.getElementById('paiementForm')?.addEventListener('submit', function(e) {
        const montant = parseFloat(montantInput.value) || 0;
        const banque = banqueSelect.value;

        if (!banque) {
            e.preventDefault();
            alert('Veuillez sélectionner un compte bancaire');
            banqueSelect.focus();
            return false;
        }

        if (montant <= 0) {
            e.preventDefault();
            alert('Le montant doit être supérieur à 0');
            montantInput.focus();
            return false;
        }

        if (montant > montantRestant) {
            e.preventDefault();
            alert('Le montant ne peut pas dépasser le reste à payer (' + new Intl.NumberFormat('fr-FR').format(montantRestant) + ' FCFA)');
            montantInput.focus();
            return false;
        }
    });
</script>
@endpush
