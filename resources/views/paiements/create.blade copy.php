@extends('layouts.main')
@section('title', 'Créer un paiement')
@section('breadcrumb')
    <a href="{{ route('factures.index') }}" class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('factures.show', $factureId) }}" class="text-white/80 hover:text-white transition-colors">{{ $facture->numero_facture }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('paiements.index', $factureId) }}" class="text-white/80 hover:text-white transition-colors">Paiements</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Créer</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('paiements.index', $factureId) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Créer un nouveau paiement</h1>
            </div>
        </div>
    </div>

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

        <form action="{{ route('paiements.store',  $factureId) }}" method="POST" class="max-w-4xl mx-auto">
            @csrf

            <!-- Info Facture (non modifiable) -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fas fa-file-invoice text-blue-500 text-2xl mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-800">
                            Paiement pour la facture :
                            <span class="font-bold">{{ $facture->numero_facture }}</span>
                        </p>
                        <p class="text-xs text-blue-600 mt-1">
                            Prestataire : {{ $facture->proforma->getPrestataire()?->raison_sociale_prestataire ?? 'N/A' }}
                            | Montant facture : {{ number_format($facture->montant_facture ?? 0, 0, ',', ' ') }} FCFA
                            @if(method_exists($facture, 'getMontantRestantAttribute'))
                                | Reste à payer : {{ number_format($facture->montant_restant, 0, ',', ' ') }} FCFA
                            @endif
                        </p>
                    </div>
                    <input type="hidden" name="facture_id" value="{{ $facture->id_facture }}">
                    <a href="{{ route('factures.show', $factureId) }}"
                        class="ml-4 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all text-sm">
                        <i class="fas fa-eye mr-1"></i>
                        Voir facture
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-invoice-dollar text-green-500 mr-2"></i>
                        Informations du paiement
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Banque -->
                        <div class="md:col-span-2">
                            <label for="banque_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Banque destinataire *
                            </label>
                            <select name="banque_id" id="banque_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent">
                                <option value="">Sélectionner une banque</option>
                                @foreach($banques as $banque)
                                    <option value="{{ $banque->id_banque }}" {{ old('banque_id') == $banque->id_banque ? 'selected' : '' }}>
                                        {{ $banque->nom_banque }} - {{ $banque->numero_compte_banque }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Compte bancaire du prestataire</p>
                        </div>

                        <!-- Montant -->
                        <div class="md:col-span-2">
                            <label for="montant_net_paye_paiement" class="block text-sm font-medium text-gray-700 mb-2">
                                Montant du paiement (FCFA) *
                            </label>
                            <input type="number" name="montant_net_paye_paiement" id="montant_net_paye_paiement"
                                value="{{ old('montant_net_paye_paiement') }}" step="0.01" min="0.01" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                placeholder="Entrez le montant">
                            @if(method_exists($facture, 'getResteAPayer'))
                                <p class="text-xs text-gray-500 mt-1">
                                    Reste à payer: <span class="font-semibold">{{ number_format($facture->getResteAPayer(), 0, ',', ' ') }} FCFA</span>
                                </p>
                            @endif
                        </div>

                        <!-- Observations -->
                        <div class="md:col-span-2">
                            <label for="observations_paiement" class="block text-sm font-medium text-gray-700 mb-2">
                                Observations
                            </label>
                            <textarea name="observations_paiement" id="observations_paiement" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                placeholder="Notes, instructions particulières, références...">{{ old('observations_paiement') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('paiements.index', $factureId) }}"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Créer le paiement</span>
                </button>
            </div>
        </form>
    </main>
@endsection
