@extends('layouts.main')

@section('title', 'Facture ' . $facture->numero_facture)

@section('breadcrumb')
    <a @can('factures.read') href="{{ route('factures.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $facture->numero_facture }}</span>
@endsection

@section('content')
    {{-- Récupération des données via PrestataireLot --}}
    @php
        $attribution = null;
        $prestataire = null;
        $lot = null;
        $appelOffre = null;
        $representantsLegaux = [];

        if ($facture->proforma) {
            $attribution = \App\Models\PrestataireLot::where('proforma_id', $facture->proforma->id_proforma)
                ->with(['prestataire', 'lot.appelOffre'])
                ->first();

            if ($attribution) {
                $prestataire = $attribution->prestataire;
                $lot = $attribution->lot;
                $appelOffre = $lot?->appelOffre;

                if ($prestataire && $prestataire->representant_legal_prestataire) {
                    $representantsLegaux = json_decode($prestataire->representant_legal_prestataire, true) ?? [];
                }
            }
        }
    @endphp

    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('factures.read')
                    <a href="{{ route('factures.index') }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $facture->numero_facture }}</h1>
                            @php
                                $statutClasses = [
                                    'en_attente' => 'bg-yellow-100 text-yellow-800',
                                    'validee' => 'bg-blue-100 text-blue-800',
                                    'rejetee' => 'bg-red-100 text-red-800',
                                    'payee' => 'bg-green-100 text-green-800',
                                    'partiellement_payee' => 'bg-orange-100 text-orange-800',
                                    'annulee' => 'bg-gray-100 text-gray-800',
                                ];
                                $statutIcons = [
                                    'en_attente' => 'clock',
                                    'validee' => 'check-circle',
                                    'rejetee' => 'times-circle',
                                    'payee' => 'check-double',
                                    'partiellement_payee' => 'adjust',
                                    'annulee' => 'ban',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statutClasses[$facture->statut_facture] ?? 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-{{ $statutIcons[$facture->statut_facture] ?? 'question' }} mr-1"></i>
                                {{ $facture->statut_libelle }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1 font-medium">
                            Proforma: {{ $facture->proforma->numero_proforma ?? 'Non définie' }}
                        </p>
                    </div>
                </div>

                @canany(['factures.update', 'factures.validate', 'factures.reject', 'paiements.create', 'factures.pending', 'factures.duplicate'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap gap-2">
                        @can('factures.update')
                        @if($facture->peutEtreModifiee())
                            <a href="{{ route('factures.edit', $facture->id_facture) }}"
                                class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                                <i class="fas fa-edit text-sm"></i>
                                <span class="text-sm font-bold">Modifier</span>
                            </a>
                        @endif
                        @endcan

                        @can('factures.validate')
                        @if($facture->peutEtreValidee())
                            <form action="{{ route('factures.valider', $facture->id_facture) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-check text-sm"></i>
                                    <span class="text-sm font-bold">Valider</span>
                                </button>
                            </form>
                        @endif
                        @endcan

                        @can('factures.reject')
                        @if($facture->peutEtreRejetee())
                            <button onclick="openRejeterModal()"
                                class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-times text-sm"></i>
                                <span class="text-sm font-bold">Rejeter</span>
                            </button>
                        @endif
                        @endcan

                        @can('paiements.create')
                        @if($facture->peutRecevoirPaiement())
                            <a href="{{ route('paiements.create', $facture->id_facture) }}"
                                class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-money-bill-wave text-sm"></i>
                                <span class="text-sm font-bold">Ajouter Paiement</span>
                            </a>
                        @endif
                        @endcan

                        @can('factures.pending')
                        @if($facture->statut_facture === 'rejetee')
                            <form action="{{ route('factures.remettre-en-attente', $facture->id_facture) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-redo text-sm"></i>
                                    <span class="text-sm font-bold">Remettre en attente</span>
                                </button>
                            </form>
                        @endif
                        @endcan

                        @can('factures.duplicate')
                        <form action="{{ route('factures.dupliquer', $facture->id_facture) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-copy text-sm"></i>
                                <span class="text-sm font-bold">Dupliquer</span>
                            </button>
                        </form>
                        @endcan
                    </div>
                @endcanany
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Alerte si facture rejetée -->
        @if($facture->statut_facture === 'rejetee' && $facture->comment_facture)
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-red-800 font-bold mb-2">Facture Rejetée</h3>
                        <p class="text-red-700 text-sm font-medium">{{ $facture->comment_facture }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informations de la facture -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>
                            <span>Informations de la Facture</span>
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="block text-sm font-bold text-gray-500 mb-2">Numéro de facture</label>
                                <p class="text-lg font-bold text-gray-900">{{ $facture->numero_facture }}</p>
                            </div>
                            <div class="p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200">
                                <label class="block text-sm font-bold text-gray-500 mb-2">Montant TTC</label>
                                <p class="text-2xl font-bold text-orange-600">{{ $facture->montant_formate }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="block text-sm font-bold text-gray-500 mb-2">Date de facture</label>
                                <p class="text-gray-900 font-bold">{{ $facture->date_facture->format('d/m/Y') }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="block text-sm font-bold text-gray-500 mb-2">Date de réception</label>
                                <p class="text-gray-900 font-bold">{{ $facture->date_reception_facture->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        @if($facture->comment_facture && $facture->statut_facture !== 'rejetee')
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="block text-sm font-bold text-gray-500 mb-2">Commentaire</label>
                                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg font-medium">{{ $facture->comment_facture }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Progression du paiement -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-500">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-chart-pie mr-2"></i>
                            <span>Progression du Paiement</span>
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-4 bg-green-50 rounded-xl flex-1 mr-4">
                                <p class="text-sm font-bold text-gray-500 mb-1">Montant payé</p>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ number_format($statistiquesPaiements['montant_total_paye'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            <div class="p-4 bg-red-50 rounded-xl flex-1">
                                <p class="text-sm font-bold text-gray-500 mb-1 text-right">Reste à payer</p>
                                <p class="text-2xl font-bold text-red-600 text-right">
                                    {{ number_format($statistiquesPaiements['montant_restant'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold inline-block py-1 px-2 uppercase rounded-full {{ $statistiquesPaiements['pourcentage_paye'] >= 100 ? 'text-green-600 bg-green-200' : 'text-orange-600 bg-orange-200' }}">
                                        {{ $statistiquesPaiements['pourcentage_paye'] }}% payé
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-gray-600">
                                        {{ $statistiquesPaiements['total'] }} paiement(s)
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-4 text-xs flex rounded-full bg-gray-200">
                                <div style="width: {{ $statistiquesPaiements['pourcentage_paye'] }}%"
                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $statistiquesPaiements['pourcentage_paye'] >= 100 ? 'bg-gradient-to-r from-green-400 to-emerald-500' : 'bg-gradient-to-r from-orange-400 to-amber-500' }} transition-all duration-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des paiements -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-money-check-alt mr-2"></i>
                            <span>Paiements</span>
                            <span class="ml-2 px-2 py-1 text-xs font-bold bg-white/20 text-white rounded-full">
                                {{ $facture->paiements->count() }}
                            </span>
                        </h2>
                        @can('paiements.create')
                        @if($facture->peutRecevoirPaiement())
                            <a href="{{ route('paiements.create', $facture->id_facture) }}"
                                class="px-3 py-1.5 bg-white text-emerald-600 text-sm rounded-lg hover:bg-emerald-50 transition-colors font-bold">
                                <i class="fas fa-plus mr-1"></i> Ajouter
                            </a>
                        @endif
                        @endcan
                    </div>

                    @if($facture->paiements->isEmpty())
                        <div class="p-8 text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-money-check-alt text-4xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Aucun paiement enregistré</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Référence</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Banque</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Statut</th>
                                        @can('paiements.view-details')
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Actions</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($facture->paiements as $paiement)
                                        @php
                                            $paiementStatutClasses = [
                                                0 => 'bg-yellow-100 text-yellow-800',
                                                1 => 'bg-blue-100 text-blue-800',
                                                2 => 'bg-indigo-100 text-indigo-800',
                                                3 => 'bg-green-100 text-green-800',
                                                4 => 'bg-red-100 text-red-800',
                                                5 => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="font-bold text-gray-900">{{ $paiement->reference_paiement }}</span>
                                                <p class="text-xs text-gray-500 font-medium">{{ $paiement->created_at->format('d/m/Y') }}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900 font-medium">{{ $paiement->banque->nom_banque ?? 'N/A' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="font-bold text-gray-900">{{ $paiement->montant_formate }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $paiementStatutClasses[$paiement->statut_paiement] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $paiement->statut_libelle }}
                                                </span>
                                            </td>
                                            @can('paiements.view-details')
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="{{ route('paiements.show', [$paiement->facture_id, $paiement->id_paiement]) }}"
                                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-flex">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                {{-- ========== Carte Prestataire ========== --}}
                @if($prestataire)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="px-6 py-4 bg-gradient-to-r from-teal-500 to-emerald-500">
                            <h2 class="text-lg font-bold text-orange-600 flex items-center">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-building text-orange-600 text-lg"></i>
                                </div>
                                <span>Prestataire</span>
                            </h2>
                        </div>
                        <div class="p-6">
                            {{-- En-tête avec avatar --}}
                            <div class="flex items-start space-x-4 pb-4 border-b border-gray-100">
                                <div class="w-14 h-14  bg-gradient-to-br from-teal-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-200 flex-shrink-0">
                                    <span class="text-xl  font-bold text-black">
                                        {{ strtoupper(substr($prestataire->raison_sociale_prestataire, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 text-base leading-tight">
                                        {{ $prestataire->raison_sociale_prestataire }}
                                    </h3>
                                    <div class="flex items-center flex-wrap gap-2 mt-2">
                                        @if($prestataire->statut_prestataire)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                                <span>Actif</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                                <span>Inactif</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Informations détaillées --}}
                            <div class="space-y-3 mt-4">
                                {{-- NCC --}}
                                @if($prestataire->numero_cc_prestataire)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-id-card text-teal-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-600">N° CC</span>
                                        </div>
                                        <span class="font-mono font-bold text-gray-800 bg-white px-2.5 py-1 rounded-lg border border-gray-200 text-sm">
                                            {{ $prestataire->numero_cc_prestataire }}
                                        </span>
                                    </div>
                                @endif

                                {{-- RCCM --}}
                                @if($prestataire->numero_rccm_prestataire)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-file-contract text-indigo-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-600">RCCM</span>
                                        </div>
                                        <span class="font-mono font-bold text-gray-800 bg-white px-2.5 py-1 rounded-lg border border-gray-200 text-sm">
                                            {{ $prestataire->numero_rccm_prestataire }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Téléphone --}}
                                @if($prestataire->telephone_principal_prestataire)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-phone text-green-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-600">Téléphone</span>
                                        </div>
                                        <a href="tel:{{ $prestataire->telephone_principal_prestataire }}"
                                           class="font-bold text-teal-600 hover:text-teal-700 text-sm">
                                            {{ $prestataire->telephone_principal_prestataire }}
                                        </a>
                                    </div>
                                @endif

                                {{-- Email --}}
                                @if($prestataire->email_prestataire)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-envelope text-blue-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-600">Email</span>
                                        </div>
                                        <a href="mailto:{{ $prestataire->email_prestataire }}"
                                           class="font-bold text-teal-600 hover:text-teal-700 text-sm truncate max-w-[140px]"
                                           title="{{ $prestataire->email_prestataire }}">
                                            {{ Str::limit($prestataire->email_prestataire, 20) }}
                                        </a>
                                    </div>
                                @endif

                                {{-- Localisation --}}
                                @if($prestataire->ville_prestataire || $prestataire->pays_prestataire)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-map-marker-alt text-orange-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-600">Localisation</span>
                                        </div>
                                        <span class="font-bold text-gray-800 text-sm">
                                            {{ collect([$prestataire->ville_prestataire, $prestataire->pays_prestataire])->filter()->implode(', ') }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Adresse --}}
                                @if($prestataire->adresse_prestataire)
                                    <div class="p-3 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-100">
                                        <div class="flex items-center space-x-2 text-gray-500 mb-2">
                                            <i class="fas fa-home text-sm"></i>
                                            <span class="text-xs font-bold uppercase tracking-wide">Adresse</span>
                                        </div>
                                        <p class="text-gray-800 text-sm font-medium leading-relaxed">{{ $prestataire->adresse_prestataire }}</p>
                                    </div>
                                @endif

                                {{-- Représentants légaux --}}
                                @if(!empty($representantsLegaux))
                                    <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-100">
                                        <div class="flex items-center space-x-2 text-purple-600 mb-4">
                                            <i class="fas fa-user-tie text-sm"></i>
                                            <span class="text-xs font-bold uppercase tracking-wide">
                                                Représentant{{ count($representantsLegaux) > 1 ? 's' : '' }} Légal{{ count($representantsLegaux) > 1 ? 'aux' : '' }}
                                            </span>
                                        </div>

                                        @foreach($representantsLegaux as $index => $representant)
                                            @if($index > 0)
                                                <hr class="my-4 border-purple-200">
                                            @endif

                                            <div class="space-y-3">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <p class="font-bold text-gray-900 text-base">{{ $representant['nom'] ?? 'N/A' }}</p>
                                                        @if(!empty($representant['profession']))
                                                            <p class="text-purple-600 text-sm font-bold">{{ $representant['profession'] }}</p>
                                                        @endif
                                                    </div>
                                                    @if(isset($representant['statut']) && $representant['statut'] == 1)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                                            <span>Actif</span>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="grid grid-cols-1 gap-2">
                                                    @if(!empty($representant['contact']))
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-phone text-purple-400 text-xs w-4"></i>
                                                            <a href="tel:{{ $representant['contact'] }}" class="text-sm font-bold text-gray-800 hover:text-purple-600">
                                                                {{ $representant['contact'] }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if(!empty($representant['email']))
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-envelope text-purple-400 text-xs w-4"></i>
                                                            <a href="mailto:{{ $representant['email'] }}" class="text-sm font-bold text-gray-800 hover:text-purple-600 truncate">
                                                                {{ $representant['email'] }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if(!empty($representant['nationalite']) || !empty($representant['adresse']))
                                                    <div class="grid grid-cols-1 gap-2 pt-2 border-t border-purple-100">
                                                        @if(!empty($representant['nationalite']))
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-flag text-purple-400 text-xs w-4"></i>
                                                                <span class="text-sm font-medium text-gray-700">{{ $representant['nationalite'] }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!empty($representant['adresse']))
                                                            <div class="flex items-start space-x-2">
                                                                <i class="fas fa-map-marker-alt text-purple-400 text-xs w-4 mt-1"></i>
                                                                <span class="text-sm font-medium text-gray-700">{{ $representant['adresse'] }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(!empty($representant['type_piece_identite']) || !empty($representant['numero_piece_identite']))
                                                    <div class="p-3 bg-white rounded-lg border border-purple-100 mt-2">
                                                        <p class="text-xs font-bold text-purple-600 uppercase tracking-wide mb-2">
                                                            <i class="fas fa-id-card mr-1"></i> Pièce d'identité
                                                        </p>
                                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                                            @if(!empty($representant['type_piece_identite']))
                                                                <div>
                                                                    <span class="text-gray-500 text-xs font-medium">Type</span>
                                                                    <p class="font-bold text-gray-800">{{ $representant['type_piece_identite'] }}</p>
                                                                </div>
                                                            @endif
                                                            @if(!empty($representant['numero_piece_identite']))
                                                                <div>
                                                                    <span class="text-gray-500 text-xs font-medium">Numéro</span>
                                                                    <p class="font-mono font-bold text-gray-800">{{ $representant['numero_piece_identite'] }}</p>
                                                                </div>
                                                            @endif
                                                            @if(!empty($representant['date_delivrance']))
                                                                <div>
                                                                    <span class="text-gray-500 text-xs font-medium">Délivré le</span>
                                                                    <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($representant['date_delivrance'])->format('d/m/Y') }}</p>
                                                                </div>
                                                            @endif
                                                            @if(!empty($representant['date_expiration']))
                                                                <div>
                                                                    <span class="text-gray-500 text-xs font-medium">Expire le</span>
                                                                    @php
                                                                        $dateExp = \Carbon\Carbon::parse($representant['date_expiration']);
                                                                        $isExpired = $dateExp->isPast();
                                                                    @endphp
                                                                    <p class="font-bold {{ $isExpired ? 'text-red-600' : 'text-gray-800' }}">
                                                                        {{ $dateExp->format('d/m/Y') }}
                                                                        @if($isExpired)
                                                                            <span class="text-xs text-red-500">(Expiré)</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @can('prestataires.view-details')
                            {{-- Lien vers fiche prestataire --}}
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-teal-500 to-emerald-500 text-orange-600 rounded-xl hover:from-teal-600 hover:to-emerald-600 transition-all duration-200 shadow-md hover:shadow-lg font-bold text-sm">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    <span>Voir la fiche complète</span>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                @endif

                {{-- ========== Carte Lot ========== --}}
                @if($lot)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500">
                            <h2 class="text-lg font-bold text-green-600 flex items-center">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-green-600 text-lg"></i>
                                </div>
                                <span>Lot</span>
                            </h2>
                        </div>
                        <div class="p-6">
                            {{-- En-tête du lot --}}
                            <div class="flex items-start space-x-4 pb-4 border-b border-gray-100">
                                <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-200 flex-shrink-0">
                                    <span class="text-xl font-bold text-black">
                                        {{ strtoupper(substr($lot->libelle, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 text-base leading-tight">
                                        {{ $lot->libelle }}
                                    </h3>
                                    <div class="flex items-center flex-wrap gap-2 mt-2">
                                        @if($lot->attribution_lot)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1 text-[10px]"></i>
                                                <span>Attribué</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-clock mr-1 text-[10px]"></i>
                                                <span>Non attribué</span>
                                            </span>
                                        @endif
                                        @if($lot->statut_lot)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5 animate-pulse"></span>
                                                <span>Actif</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Informations du lot --}}
                            <div class="space-y-3 mt-4">
                                {{-- Numéro du lot --}}
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-hashtag text-amber-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-600">Numéro</span>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 border border-amber-200">
                                        Lot N°{{ $lot->numero }}
                                    </span>
                                </div>

                                {{-- Date d'attribution --}}
                                @if($lot->date_attribution)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-calendar-check text-blue-600 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-600">Date d'attribution</span>
                                        </div>
                                        <span class="font-bold text-gray-800 text-sm">
                                            {{ $lot->date_attribution->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Période d'exécution --}}
                                @if($lot->date_debut_prevue || $lot->date_fin_prevue)
                                    <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                        <div class="flex items-center space-x-2 text-blue-600 mb-3">
                                            <i class="fas fa-calendar-alt text-sm"></i>
                                            <span class="text-xs font-bold uppercase tracking-wide">Période d'exécution</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="text-center flex-1">
                                                <p class="text-xs font-bold text-gray-500 mb-1">Début</p>
                                                <p class="font-bold text-gray-900 text-sm">
                                                    {{ $lot->date_debut_prevue ? $lot->date_debut_prevue->format('d/m/Y') : '—' }}
                                                </p>
                                            </div>
                                            <div class="px-4">
                                                <i class="fas fa-arrow-right text-gray-300"></i>
                                            </div>
                                            <div class="text-center flex-1">
                                                <p class="text-xs font-bold text-gray-500 mb-1">Fin</p>
                                                <p class="font-bold text-gray-900 text-sm">
                                                    {{ $lot->date_fin_prevue ? $lot->date_fin_prevue->format('d/m/Y') : '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif


                            </div>

                            {{-- Appel d'offre associé --}}
                            @if($appelOffre)
                                <div class="mt-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                                    <div class="flex items-center space-x-2 text-indigo-600 mb-3">
                                        <i class="fas fa-gavel text-sm"></i>
                                        <span class="text-xs font-bold uppercase tracking-wide">Appel d'offre</span>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="font-bold text-gray-900 text-base">{{ $appelOffre->numero_appel_offre }}</p>
                                        @if($appelOffre->objet_appel_offre)
                                            <p class="text-gray-700 text-sm leading-relaxed font-medium">
                                                {{ Str::limit($appelOffre->objet_appel_offre, 100) }}
                                            </p>
                                        @endif
                                        @can('appels_offres.view-details')
                                        <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}"
                                           class="inline-flex items-center text-indigo-600 hover:text-indigo-700 text-sm font-bold mt-2">
                                            <span>Voir l'appel d'offre</span>
                                            <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            @endif

                            @can('lots.view-details')
                            {{-- Lien vers fiche lot --}}
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <a href="{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->id_lot]) }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-indigo-600 rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all duration-200 shadow-md hover:shadow-lg font-bold text-sm">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    <span>Voir le détail du lot</span>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                @endif

                {{-- ========== Carte Attribution ========== --}}
                @if($attribution)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gradient-to-r from-violet-500 to-purple-500">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-handshake text-white text-lg"></i>
                                </div>
                                <span>Attribution</span>
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            {{-- Statut --}}
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm font-bold text-gray-600">Statut</span>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold {{ $attribution->statut_badge_class }}">
                                    {{ $attribution->statut_label }}
                                </span>
                            </div>

                            {{-- Numéro attribution --}}
                            @if($attribution->numero_attribution)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-sm font-bold text-gray-600">N° Attribution</span>
                                    <span class="font-mono font-bold text-gray-800 text-sm">{{ $attribution->numero_attribution }}</span>
                                </div>
                            @endif

                            {{-- Date attribution --}}
                            @if($attribution->date_attribution)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-sm font-bold text-gray-600">Date</span>
                                    <span class="font-bold text-gray-800 text-sm">{{ $attribution->date_attribution->format('d/m/Y') }}</span>
                                </div>
                            @endif

                            {{-- Avancement --}}
                            @if($attribution->pourcentage_avancement > 0)
                                <div class="p-4 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-sm font-bold text-gray-700">Avancement</span>
                                        <span class="font-bold text-lg {{ $attribution->pourcentage_avancement >= 100 ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ number_format($attribution->pourcentage_avancement, 0) }}%
                                        </span>
                                    </div>
                                    <div class="overflow-hidden h-3 rounded-full bg-gray-200">
                                        <div style="width: {{ min($attribution->pourcentage_avancement, 100) }}%"
                                             class="h-full rounded-full {{ $attribution->pourcentage_avancement >= 100 ? 'bg-gradient-to-r from-green-400 to-emerald-500' : 'bg-gradient-to-r from-amber-400 to-orange-500' }} transition-all duration-500">
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

                <!-- Proforma associée -->
                @if($facture->proforma)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-blue-500">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-file-alt mr-2"></i>
                                <span>Proforma</span>
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm font-bold text-gray-600">Numéro</span>
                                <span class="font-bold text-gray-900">{{ $facture->proforma->numero_proforma }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm font-bold text-gray-600">Montant HT</span>
                                <span class="font-bold text-gray-900">{{ number_format($facture->proforma->montant_ht_apres_remise, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($facture->proforma->remise_montant_proforma > 0)
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl">
                                    <span class="text-sm font-bold text-gray-600">Remise ({{ $facture->proforma?->pourcentage_remise ?? 0 }}%)</span>
                                    <span class="font-bold text-green-600">-{{ number_format($facture->proforma->remise_montant_proforma, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm font-bold text-gray-600">TVA ({{ $facture->proforma?->taux_taxe ?? 0 }}%)</span>
                                <span class="font-bold text-gray-900">{{ number_format($facture->proforma->taxe_montant, 0, ',', ' ') }} FCFA</span>
                            </div>
                            
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl border border-indigo-200">
                                <span class="text-sm font-bold text-gray-700">Montant TTC</span>
                                @php
                                    $montantTTC = $facture->proforma->montant_retenu_proforma + $facture->proforma->taxe_montant - $facture->proforma->remise_montant_proforma + $facture->proforma->penalites_proforma;
                                @endphp
                                <span class="font-bold text-lg text-indigo-600">{{ number_format($montantTTC, 0, ',', ' ') }} FCFA</span>
                            </div>

                            @can('proformas.view-details')
                            <div class="pt-4 border-t border-gray-200">
                                <a href="{{ route('proformas.show', $facture->proforma->id_proforma) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-xl hover:from-indigo-600 hover:to-blue-600 transition-all font-bold text-sm">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    <span>Voir la proforma</span>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                @endif

                <!-- Résumé rapide -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-500">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Résumé</span>
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm font-bold text-gray-600">Facture soldée</span>
                            @if($facture->est_soldee)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Oui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Non
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm font-bold text-gray-600">Modifiable</span>
                            @if($facture->peutEtreModifiee())
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Oui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                    <i class="fas fa-lock mr-1"></i> Non
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm font-bold text-gray-600">Paiements</span>
                            <span class="font-bold text-gray-900">{{ $statistiquesPaiements['total'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Audit -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-500 to-slate-500">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-fingerprint mr-2"></i>
                            <span>Audit</span>
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                            <span class="text-sm font-bold text-gray-500">Créé le</span>
                            <span class="font-bold text-gray-900">{{ $facture->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($facture->createur)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm font-bold text-gray-500">Créé par</span>
                                <span class="font-bold text-gray-900">{{ $facture->createur->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if($facture->updated_at && $facture->updated_at != $facture->created_at)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm font-bold text-gray-500">Modifié le</span>
                                <span class="font-bold text-gray-900">{{ $facture->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if($facture->modificateur)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm font-bold text-gray-500">Modifié par</span>
                                <span class="font-bold text-gray-900">{{ $facture->modificateur->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Rejeter -->
    <div id="rejeterModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeRejeterModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-rose-500">
                    <h3 class="text-lg font-bold text-white">
                        <i class="fas fa-times-circle mr-2"></i>Rejeter la facture
                    </h3>
                </div>
                <form @can('factures.reject') action="{{ route('factures.rejeter', $facture->id_facture) }}" @endcan method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="bg-red-50 p-4 rounded-lg text-sm text-red-800 font-medium">
                            <i class="fas fa-info-circle mr-2"></i>
                            Cette action marquera la facture comme rejetée. Le prestataire devra soumettre une nouvelle facture corrigée.
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Motif du rejet *</label>
                            <textarea name="motif" rows="4" required minlength="10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-transparent font-medium"
                                placeholder="Expliquez la raison du rejet (minimum 10 caractères)..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeRejeterModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50 font-bold">
                            Annuler
                        </button>
                        @can('factures.reject')
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold">
                            Rejeter
                        </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Annuler -->
    <div id="annulerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeAnnulerModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-500 to-slate-500">
                    <h3 class="text-lg font-bold text-white">
                        <i class="fas fa-ban mr-2"></i>Annuler la facture
                    </h3>
                </div>
                <form @can('factures.cancel') action="{{ route('factures.annuler', $facture->id_facture) }}" @endcan method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="bg-yellow-50 p-4 rounded-lg text-sm text-yellow-800 font-medium">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Cette action annulera définitivement la facture. Cette opération est irréversible.
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Motif d'annulation *</label>
                            <textarea name="motif" rows="4" required minlength="10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:border-transparent font-medium"
                                placeholder="Expliquez la raison de l'annulation (minimum 10 caractères)..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeAnnulerModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50 font-bold">
                            Retour
                        </button>
                        @can('factures.cancel')
                        <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold">
                            Confirmer l'annulation
                        </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@can('factures.view-details')
@push('scripts')
<script>
    function openRejeterModal() {
        document.getElementById('rejeterModal').classList.remove('hidden');
    }
    function closeRejeterModal() {
        document.getElementById('rejeterModal').classList.add('hidden');
    }
    function openAnnulerModal() {
        document.getElementById('annulerModal').classList.remove('hidden');
    }
    function closeAnnulerModal() {
        document.getElementById('annulerModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejeterModal();
            closeAnnulerModal();
        }
    });
</script>
@endpush
@endcan
