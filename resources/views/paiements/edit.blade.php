@extends('layouts.main')
@section('title', 'Modifier le paiement')
@section('breadcrumb')
    <a href="{{ route('factures.index') }}" class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('factures.show', $factureId) }}" class="text-white/80 hover:text-white transition-colors">{{ $paiement->facture->numero_facture }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('paiements.index', parameters: $factureId) }}" class="text-white/80 hover:text-white transition-colors">Paiements</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('paiements.show', [$factureId, $paiement->id_paiement]) }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Modifier le paiement</h1>
                    <p class="text-sm text-gray-600">{{ $paiement->reference_paiement }}</p>
                </div>
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

        <form action="{{ route('paiements.update', [ $factureId,  $paiement->id_paiement]) }}"
            method="POST" class="max-w-4xl mx-auto">
            @csrf
            @method('PUT')

            <!-- Info facture (non modifiable) -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Facture : {{ $paiement->facture->numero_facture }}</p>
                        <p class="text-xs text-blue-600">Prestataire : {{ $paiement->facture->marche->prestataire->raison_sociale_prestataire }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit text-orange-500 mr-2"></i>
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
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                @foreach($banques as $banque)
                                    <option value="{{ $banque->id_banque }}"
                                        {{ old('banque_id', $paiement->banque_id) == $banque->id_banque ? 'selected' : '' }}>
                                        {{ $banque->nom_banque }} - {{ $banque->numero_compte_banque }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Montant -->
                        <div class="md:col-span-2">
                            <label for="montant_net_paye_paiement" class="block text-sm font-medium text-gray-700 mb-2">
                                Montant du paiement (FCFA) *
                            </label>
                            <input type="number" name="montant_net_paye_paiement" id="montant_net_paye_paiement"
                                value="{{ old('montant_net_paye_paiement', $paiement->montant_net_paye_paiement) }}"
                                step="0.01" min="0.01" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                        </div>

                        <!-- Observations -->
                        <div class="md:col-span-2">
                            <label for="observations_paiement" class="block text-sm font-medium text-gray-700 mb-2">
                                Observations
                            </label>
                            <textarea name="observations_paiement" id="observations_paiement" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent">{{ old('observations_paiement', $paiement->observations_paiement) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('paiements.show', [$factureId,  $paiement->id_paiement]) }}"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </main>
@endsection
