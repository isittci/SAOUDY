@extends('layouts.main')

@section('title', 'Modifier la Situation Financière - ' . $situation->exercice_fiscal_situation_financiere)

@push('styles')
<style>
    .form-section {
        transition: all 0.3s ease;
    }
    .form-section:hover {
        box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .input-amount {
        text-align: right;
    }
</style>
@endpush

@section('breadcrumb')
    <a href="{{ route('prestataires.index') }}" class="text-white/80 hover:text-white transition-colors">Prestataires</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('prestataires.situations-financieres.index', $prestataire->id_prestataire) }}" class="text-white/80 hover:text-white transition-colors">Situations Financières</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier {{ $situation->exercice_fiscal_situation_financiere }}</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('prestataires.situations-financieres.index', $prestataire->id_prestataire) }}"
                   class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-edit text-orange-500 mr-2"></i>
                        Modifier la Situation Financière
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">Exercice {{ $situation->exercice_fiscal_situation_financiere }} - {{ $prestataire->raison_sociale_prestataire }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages d'erreur -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-3 mt-0.5 text-red-500"></i>
                    <div>
                        <p class="font-medium">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Score actuel -->
        @php
            $niveau = $situation->getNiveau();
            $score = $situation->calculerScore();
        @endphp
        <div class="mb-6 bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Score de santé financière</h3>
                    <p class="text-sm text-gray-500">Basé sur les données renseignées</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-{{ $niveau['classe'] }}-600">{{ $score }}/100</div>
                    <div class="text-sm font-medium text-{{ $niveau['classe'] }}-600">
                        <i class="fas fa-{{ $niveau['icon'] }} mr-1"></i>{{ $niveau['niveau'] }}
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('prestataires.situations-financieres.update', [$prestataire->id_prestataire, $situation->id_situation_financiere]) }}" method="POST" id="situationForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Colonne gauche -->
                <div class="space-y-6">
                    <!-- Exercice Fiscal -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Exercice Fiscal
                            </h2>
                        </div>
                        <div class="p-6">
                            <label for="exercice_fiscal_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                Année d'exercice <span class="text-red-500">*</span>
                            </label>
                            <select name="exercice_fiscal_situation_financiere" id="exercice_fiscal_situation_financiere" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                @foreach($exercices as $annee => $label)
                                    <option value="{{ $annee }}" {{ old('exercice_fiscal_situation_financiere', $situation->exercice_fiscal_situation_financiere) == $annee ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Chiffre d'Affaires et Résultat -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-coins text-green-500 mr-2"></i>
                                Performance
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label for="chiffre_affaire_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                    Chiffre d'affaires (FCFA)
                                </label>
                                <input type="text" name="chiffre_affaire_situation_financiere" id="chiffre_affaire_situation_financiere"
                                       value="{{ old('chiffre_affaire_situation_financiere', number_format($situation->chiffre_affaire_situation_financiere ?? 0, 0, ',', ' ')) }}"
                                       placeholder="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors input-amount"
                                       oninput="formatNumber(this)">
                            </div>
                            <div>
                                <label for="resultat_net_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                    Résultat net (FCFA)
                                </label>
                                <input type="text" name="resultat_net_situation_financiere" id="resultat_net_situation_financiere"
                                       value="{{ old('resultat_net_situation_financiere', number_format($situation->resultat_net_situation_financiere ?? 0, 0, ',', ' ')) }}"
                                       placeholder="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors input-amount"
                                       oninput="formatNumber(this)">
                                <p class="mt-1 text-xs text-gray-400">Peut être négatif en cas de perte</p>
                            </div>
                        </div>
                    </div>

                    <!-- Structure Financière -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-balance-scale text-purple-500 mr-2"></i>
                                Structure Financière
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label for="fonds_propres_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fonds propres (FCFA)
                                </label>
                                <input type="text" name="fonds_propres_situation_financiere" id="fonds_propres_situation_financiere"
                                       value="{{ old('fonds_propres_situation_financiere', number_format($situation->fonds_propres_situation_financiere ?? 0, 0, ',', ' ')) }}"
                                       placeholder="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors input-amount"
                                       oninput="formatNumber(this)">
                            </div>
                            <div>
                                <label for="capacite_emprunt_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                    Capacité d'emprunt (FCFA)
                                </label>
                                <input type="text" name="capacite_emprunt_situation_financiere" id="capacite_emprunt_situation_financiere"
                                       value="{{ old('capacite_emprunt_situation_financiere', number_format($situation->capacite_emprunt_situation_financiere ?? 0, 0, ',', ' ')) }}"
                                       placeholder="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors input-amount"
                                       oninput="formatNumber(this)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite -->
                <div class="space-y-6">
                    <!-- Ratios -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-percentage text-orange-500 mr-2"></i>
                                Ratios Financiers
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="ratio_solvabilite_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                        Ratio de solvabilité (%)
                                    </label>
                                    <input type="number" name="ratio_solvabilite_situation_financiere" id="ratio_solvabilite_situation_financiere"
                                           step="0.01" min="0" max="1000"
                                           value="{{ old('ratio_solvabilite_situation_financiere', $situation->ratio_solvabilite_situation_financiere) }}"
                                           placeholder="0.00"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                </div>
                                <div>
                                    <label for="ratio_liquidite_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                        Ratio de liquidité
                                    </label>
                                    <input type="number" name="ratio_liquidite_situation_financiere" id="ratio_liquidite_situation_financiere"
                                           step="0.01" min="0" max="1000"
                                           value="{{ old('ratio_liquidite_situation_financiere', $situation->ratio_liquidite_situation_financiere) }}"
                                           placeholder="0.00"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bilan -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-invoice-dollar text-teal-500 mr-2"></i>
                                Bilan
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label for="total_actif_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Actif (FCFA)
                                </label>
                                <input type="text" name="total_actif_situation_financiere" id="total_actif_situation_financiere"
                                       value="{{ old('total_actif_situation_financiere', number_format($situation->total_actif_situation_financiere ?? 0, 0, ',', ' ')) }}"
                                       placeholder="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors input-amount"
                                       oninput="formatNumber(this)">
                            </div>
                            <div>
                                <label for="total_passif_situation_financiere" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Passif (FCFA)
                                </label>
                                <input type="text" name="total_passif_situation_financiere" id="total_passif_situation_financiere"
                                       value="{{ old('total_passif_situation_financiere', number_format($situation->total_passif_situation_financiere ?? 0, 0, ',', ' ')) }}"
                                       placeholder="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors input-amount"
                                       oninput="formatNumber(this)">
                            </div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Observations
                            </h2>
                        </div>
                        <div class="p-6">
                            <textarea name="observations_situation_financiere" id="observations_situation_financiere"
                                      rows="4"
                                      placeholder="Notes, commentaires..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('observations_situation_financiere', $situation->observations_situation_financiere) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-8 flex flex-col sm:flex-row justify-between gap-3">
                <button type="button" onclick="confirmDelete()"
                        class="px-6 py-3 bg-red-100 hover:bg-red-200 text-red-600 font-medium rounded-xl transition-colors">
                    <i class="fas fa-trash mr-2"></i>Supprimer
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('prestataires.situations-financieres.index', $prestataire->id_prestataire) }}"
                       class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition-colors text-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </div>
        </form>

        <!-- Formulaire de suppression caché -->
        <form id="deleteForm" action="{{ route('prestataires.situations-financieres.destroy', [$prestataire->id_prestataire, $situation->id_situation_financiere]) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </main>
@endsection

@push('scripts')
<script>
    function formatNumber(input) {
        let value = input.value.replace(/[^\d-]/g, '');
        let isNegative = value.startsWith('-');
        value = value.replace(/-/g, '');

        if (value) {
            value = parseInt(value).toLocaleString('fr-FR');
            if (isNegative) {
                value = '-' + value;
            }
        }

        input.value = value;
    }

    document.getElementById('situationForm').addEventListener('submit', function(e) {
        const amountFields = document.querySelectorAll('.input-amount');
        amountFields.forEach(function(field) {
            field.value = field.value.replace(/\s/g, '').replace(/\u00A0/g, '');
        });
    });

    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette situation financière ?')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
