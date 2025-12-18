@extends('layouts.main')

@section('title', 'Nouvelle évaluation')

@section('breadcrumb')
    <a href="{{ route('evaluations.index') }}" class="text-white/80 hover:text-white transition-colors">Évaluations</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('evaluations.pour-attribution', $attribution->id_attribution) }}" class="text-white/80 hover:text-white transition-colors">Attribution</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle évaluation</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('evaluations.pour-attribution', $attribution->id_attribution) }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Nouvelle évaluation</h1>
                    <p class="text-gray-600 mt-1">
                        Lot {{ $attribution->lot->numero }} - {{ $attribution->prestataire->raison_sociale_prestataire }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <form action="{{ route('evaluations.store', $attribution->id_attribution) }}" method="POST" id="evaluationForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Sélection du critère -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-list-check text-indigo-500 mr-2"></i>
                                Sélection du critère à évaluer
                            </h2>
                        </div>
                        <div class="p-6">
                            @if($critereSelectionne)
                                {{-- Critère pré-sélectionné --}}
                                <input type="hidden" name="critere_id" value="{{ $critereSelectionne->id_critere_evaluation }}">
                                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <span class="px-3 py-1 text-sm font-bold bg-indigo-100 text-indigo-700 rounded-lg">
                                                {{ $critereSelectionne->numero_critere_evaluation }}
                                            </span>
                                            <h4 class="font-semibold text-gray-900">{{ $critereSelectionne->libelle_critere_evaluation }}</h4>
                                        </div>
                                        <span class="text-lg font-bold text-indigo-600">
                                            {{ $critereSelectionne->note_reference_critere_evaluation }} pts
                                        </span>
                                    </div>
                                    @if($critereSelectionne->description_critere_evaluation)
                                        <p class="text-sm text-gray-600 mb-3">{{ $critereSelectionne->description_critere_evaluation }}</p>
                                    @endif
                                    <div class="flex items-center justify-between pt-3 border-t border-indigo-200">
                                        <span class="text-sm text-indigo-700">Reste à évaluer:</span>
                                        <span class="text-xl font-bold text-indigo-600">{{ number_format($resteAEvaluer, 2) }} pts</span>
                                    </div>
                                </div>
                            @else
                                {{-- Liste des critères à sélectionner --}}
                                <div class="space-y-3">
                                    @foreach($criteresAvecReste as $item)
                                        @if($item['peut_evaluer'])
                                            <label class="block cursor-pointer">
                                                <input type="radio" name="critere_id" value="{{ $item['critere']->id_critere_evaluation }}"
                                                    class="sr-only peer"
                                                    data-note-ref="{{ $item['critere']->note_reference_critere_evaluation }}"
                                                    data-reste="{{ $item['reste_a_evaluer'] }}"
                                                    {{ old('critere_id') == $item['critere']->id_critere_evaluation ? 'checked' : '' }}
                                                    required>
                                                <div class="p-4 border-2 border-gray-200 rounded-xl transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-3">
                                                            <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded">
                                                                {{ $item['critere']->numero_critere_evaluation }}
                                                            </span>
                                                            <h4 class="font-semibold text-gray-900">{{ $item['critere']->libelle_critere_evaluation }}</h4>
                                                        </div>
                                                        <span class="font-bold text-indigo-600">{{ $item['critere']->note_reference_critere_evaluation }} pts</span>
                                                    </div>
                                                    <div class="flex items-center justify-between text-sm">
                                                        <div class="flex items-center space-x-4">
                                                            <span class="text-gray-500">
                                                                Évalué: <strong class="text-gray-700">{{ number_format($item['total_evalue'], 2) }} pts</strong>
                                                            </span>
                                                            <span class="text-green-600">
                                                                Reste: <strong>{{ number_format($item['reste_a_evaluer'], 2) }} pts</strong>
                                                            </span>
                                                        </div>
                                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ min($item['pourcentage_complete'], 100) }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @else
                                            <div class="p-4 border-2 border-gray-200 rounded-xl bg-gray-50 opacity-60">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center space-x-3">
                                                        <span class="px-2 py-1 text-xs font-bold bg-gray-200 text-gray-500 rounded">
                                                            {{ $item['critere']->numero_critere_evaluation }}
                                                        </span>
                                                        <h4 class="font-semibold text-gray-500">{{ $item['critere']->libelle_critere_evaluation }}</h4>
                                                    </div>
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                                        <i class="fas fa-check mr-1"></i> Complet
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @error('critere_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <!-- Résultat de l'évaluation -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-star text-green-500 mr-2"></i>
                                Résultat de l'évaluation
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Note attribuée *
                                    <span id="maxNoteInfo" class="text-gray-400 font-normal">
                                        @if($critereSelectionne)
                                            (max: {{ number_format($resteAEvaluer, 2) }})
                                        @else
                                            (sélectionnez un critère)
                                        @endif
                                    </span>
                                </label>
                                <div class="flex items-center space-x-4">
                                    <input type="number"
                                        name="resultat_evaluation"
                                        id="resultatEvaluation"
                                        class="w-40 px-4 py-3 text-xl font-bold border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-400 focus:border-green-400"
                                        min="0"
                                        step="0.01"
                                        max="{{ $resteAEvaluer ?? '' }}"
                                        value="{{ old('resultat_evaluation', 0) }}"
                                        required>
                                    <span class="text-gray-500 text-lg">pts</span>
                                    <div id="pourcentageDisplay" class="text-lg font-semibold text-gray-600"></div>
                                </div>
                                @error('resultat_evaluation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Observation -->
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Observation</label>
                                <textarea name="observation" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400"
                                    placeholder="Observations sur cette évaluation...">{{ old('observation') }}</textarea>
                            </div>

                            <!-- Justification -->
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Justification</label>
                                <textarea name="justification" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400"
                                    placeholder="Justification de la note attribuée...">{{ old('justification') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Commentaire général -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Commentaire général
                            </h2>
                        </div>
                        <div class="p-6">
                            <textarea name="commentaire_general" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400"
                                placeholder="Commentaire général sur l'évaluation...">{{ old('commentaire_general') }}</textarea>
                        </div>
                    </div>

                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">

                    <!-- Informations attribution -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-contract text-orange-500 mr-2"></i>
                                Attribution
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Numéro</label>
                                <p class="text-gray-900 font-medium">{{ $attribution->numero_attribution }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Lot</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                    {{ $attribution->lot->numero ?? 'N/A' }}
                                </span>
                                <p class="text-sm text-gray-600 mt-1">{{ $attribution->lot->libelle ?? '' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Prestataire</label>
                                <p class="text-gray-900 font-medium">{{ $attribution->prestataire->raison_sociale_prestataire ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Responsables (OBLIGATOIRES) -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-users text-red-500 mr-2"></i>
                                Responsables *
                            </h2>
                            <p class="text-xs text-red-600 mt-1">Tous les responsables sont obligatoires</p>
                        </div>
                        <div class="p-6 space-y-6">

                            <!-- Responsable technique -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-user-cog text-blue-500 mr-1"></i>
                                    Responsable technique *
                                </label>
                                <div class="space-y-2">
                                    <input type="text" name="respo_technique[nom_complet]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 @error('respo_technique.nom_complet') border-red-500 @enderror"
                                        placeholder="Nom complet *"
                                        value="{{ old('respo_technique.nom_complet') }}"
                                        required>
                                    @error('respo_technique.nom_complet')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <input type="email" name="respo_technique[email]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                                        placeholder="Email"
                                        value="{{ old('respo_technique.email') }}">
                                    <input type="text" name="respo_technique[telephone]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                                        placeholder="Téléphone"
                                        value="{{ old('respo_technique.telephone') }}">
                                </div>
                            </div>

                            <!-- Superviseur -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-user-shield text-purple-500 mr-1"></i>
                                    Superviseur *
                                </label>
                                <div class="space-y-2">
                                    <input type="text" name="superviseur[nom_complet]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400 @error('superviseur.nom_complet') border-red-500 @enderror"
                                        placeholder="Nom complet *"
                                        value="{{ old('superviseur.nom_complet') }}"
                                        required>
                                    @error('superviseur.nom_complet')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <input type="email" name="superviseur[email]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400"
                                        placeholder="Email"
                                        value="{{ old('superviseur.email') }}">
                                    <input type="text" name="superviseur[telephone]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400"
                                        placeholder="Téléphone"
                                        value="{{ old('superviseur.telephone') }}">
                                </div>
                            </div>

                            <!-- Évaluateur -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-user-check text-green-500 mr-1"></i>
                                    Évaluateur *
                                </label>
                                <div class="space-y-2">
                                    <input type="text" name="evalue_par[nom_complet]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400 @error('evalue_par.nom_complet') border-red-500 @enderror"
                                        placeholder="Nom complet *"
                                        value="{{ old('evalue_par.nom_complet') }}"
                                        required>
                                    @error('evalue_par.nom_complet')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <input type="email" name="evalue_par[email]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Email"
                                        value="{{ old('evalue_par.email') }}">
                                    <input type="text" name="evalue_par[telephone]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Téléphone"
                                        value="{{ old('evalue_par.telephone') }}">
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 space-y-3">
                            <button type="submit"
                                class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg transition-all shadow-md">
                                <i class="fas fa-save mr-2"></i>Enregistrer l'évaluation
                            </button>
                            <a href="{{ route('evaluations.pour-attribution', $attribution->id_attribution) }}"
                                class="block w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all text-center">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </a>
                        </div>
                    </div>

                    <!-- Note d'information -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-1">Logique d'évaluation</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Chaque évaluation correspond à <strong>un seul critère</strong></li>
                                    <li>Plusieurs évaluations partielles sont possibles pour un même critère</li>
                                    <li>La somme des résultats doit atteindre la note de référence du critère</li>
                                    <li>Les 3 responsables sont <strong>obligatoires</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const critereRadios = document.querySelectorAll('input[name="critere_id"]');
        const resultatInput = document.getElementById('resultatEvaluation');
        const maxNoteInfo = document.getElementById('maxNoteInfo');
        const pourcentageDisplay = document.getElementById('pourcentageDisplay');

        let currentMax = {{ $resteAEvaluer ?? 0 }};
        let currentNoteRef = {{ $critereSelectionne?->note_reference_critere_evaluation ?? 0 }};

        function updateMaxAndPourcentage() {
            const resultat = parseFloat(resultatInput.value) || 0;

            // Validation du max
            if (resultat > currentMax) {
                resultatInput.value = currentMax;
            }

            // Calcul du pourcentage
            if (currentNoteRef > 0) {
                const pourcentage = (Math.min(resultat, currentMax) / currentNoteRef * 100).toFixed(1);
                pourcentageDisplay.textContent = `(${pourcentage}% du critère)`;

                // Couleur selon pourcentage
                if (pourcentage >= 70) {
                    pourcentageDisplay.className = 'text-lg font-semibold text-green-600';
                } else if (pourcentage >= 50) {
                    pourcentageDisplay.className = 'text-lg font-semibold text-yellow-600';
                } else {
                    pourcentageDisplay.className = 'text-lg font-semibold text-red-600';
                }
            }
        }

        // Écouter les changements de critère
        critereRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                currentMax = parseFloat(this.dataset.reste) || 0;
                currentNoteRef = parseFloat(this.dataset.noteRef) || 0;

                resultatInput.max = currentMax;
                maxNoteInfo.textContent = `(max: ${currentMax.toFixed(2)})`;

                // Réinitialiser la valeur si elle dépasse le nouveau max
                if (parseFloat(resultatInput.value) > currentMax) {
                    resultatInput.value = currentMax;
                }

                updateMaxAndPourcentage();
            });
        });

        // Écouter les changements de résultat
        resultatInput.addEventListener('input', updateMaxAndPourcentage);
        resultatInput.addEventListener('change', updateMaxAndPourcentage);

        // Calcul initial
        updateMaxAndPourcentage();
    });
</script>
@endpush
