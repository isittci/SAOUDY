@extends('layouts.main')

@section('title', 'Modifier évaluation ' . $evaluation->numero_evaluation)

@section('breadcrumb')
    <a href="{{ route('evaluations.index') }}" class="text-white/80 hover:text-white transition-colors">Évaluations</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}" class="text-white/80 hover:text-white transition-colors">{{ $evaluation->numero_evaluation }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-gray-800">Modifier l'évaluation</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $evaluation->statut_badge_class }}">
                            {{ $evaluation->statut_label }}
                        </span>
                        @if($evaluation->version > 1)
                            <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                V{{ $evaluation->version }}
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1">{{ $evaluation->numero_evaluation }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <form action="{{ route('evaluations.update', $evaluation->id_evaluation) }}" method="POST" id="evaluationForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Notes par critère -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-list-check text-indigo-500 mr-2"></i>
                                    Notation des critères
                                </h2>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="text-gray-500">Total référence:</span>
                                    <span class="font-bold text-indigo-600">{{ $totalNotesReference }} pts</span>
                                </div>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-200">
                            @foreach($criteres as $index => $critere)
                                @php
                                    $noteCritere = $notesCriteres[$critere->id_critere_evaluation] ?? null;
                                @endphp
                                <div class="p-4 hover:bg-gray-50 transition-colors critere-row" data-note-ref="{{ $critere->note_reference_critere_evaluation }}">
                                    <input type="hidden" name="notes[{{ $index }}][critere_id]" value="{{ $critere->id_critere_evaluation }}">

                                    <div class="flex items-start gap-4">
                                        <!-- Info critère -->
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded">
                                                    {{ $critere->numero_critere_evaluation }}
                                                </span>
                                                <h4 class="font-semibold text-gray-900">
                                                    {{ $critere->libelle_critere_evaluation }}
                                                </h4>
                                            </div>
                                            @if($critere->description_critere_evaluation)
                                                <p class="text-sm text-gray-600 mb-3">{{ $critere->description_critere_evaluation }}</p>
                                            @endif

                                            <!-- Note input -->
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                                        Note obtenue *
                                                        <span class="text-gray-400">(max: {{ $critere->note_reference_critere_evaluation }})</span>
                                                    </label>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number"
                                                            name="notes[{{ $index }}][note_obtenue]"
                                                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 note-input"
                                                            min="0"
                                                            max="{{ $critere->note_reference_critere_evaluation }}"
                                                            step="0.5"
                                                            value="{{ old('notes.' . $index . '.note_obtenue', $noteCritere->note_obtenue ?? 0) }}"
                                                            required>
                                                        <span class="text-gray-500">/ {{ $critere->note_reference_critere_evaluation }}</span>
                                                        <span class="pourcentage-display text-sm font-medium text-gray-600"></span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Conformité</label>
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox"
                                                            name="notes[{{ $index }}][conforme]"
                                                            value="1"
                                                            class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                                            {{ old('notes.' . $index . '.conforme', $noteCritere->conforme ?? false) ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700">Conforme</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Observation -->
                                            <div class="mt-3">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Observation</label>
                                                <textarea name="notes[{{ $index }}][observation]" rows="2"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 text-sm"
                                                    placeholder="Observation sur ce critère...">{{ old('notes.' . $index . '.observation', $noteCritere->observation ?? '') }}</textarea>
                                            </div>

                                            <!-- Justification -->
                                            <div class="mt-3">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Justification <span class="text-red-500">*</span></label>
                                                <textarea name="notes[{{ $index }}][justification]" rows="2" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 text-sm"
                                                    placeholder="Justification de la note...">{{ old('notes.' . $index . '.justification', $noteCritere->justification ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total calculé -->
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-100 to-indigo-50 border-t">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-700">Total calculé</span>
                                <div class="text-right">
                                    <span id="totalObtenu" class="text-2xl font-bold text-indigo-600">0</span>
                                    <span class="text-gray-500">/ {{ $totalNotesReference }}</span>
                                    <span id="pourcentageTotal" class="ml-2 text-lg font-medium text-indigo-600">(0%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commentaires -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Commentaires et recommandations
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Commentaire général</label>
                                <textarea name="commentaire_general" rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400"
                                    placeholder="Commentaire général sur l'évaluation...">{{ old('commentaire_general', $evaluation->commentaire_general) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Recommandation</label>
                                <textarea name="recommandation" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400"
                                    placeholder="Recommandation pour l'attribution...">{{ old('recommandation', $evaluation->recommandation) }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">

                    <!-- Informations -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                                Informations
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Attribution</label>
                                <p class="text-gray-900 font-medium">{{ $evaluation->attribution->numero_attribution ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Lot</label>
                                <p class="text-gray-900 font-medium">{{ $evaluation->attribution->lot->numero ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Prestataire</label>
                                <p class="text-gray-900 font-medium">
                                    {{ $evaluation->attribution->prestataire->raison_sociale_prestataire ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Responsables -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-users text-green-500 mr-2"></i>
                                Responsables
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Responsable technique -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Responsable technique</label>
                                <div class="space-y-2">
                                    <input type="text" name="respo_technique[nom_complet]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Nom complet"
                                        value="{{ old('respo_technique.nom_complet', $evaluation->respo_technique_evaluation['nom_complet'] ?? '') }}">
                                    <input type="email" name="respo_technique[email]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Email"
                                        value="{{ old('respo_technique.email', $evaluation->respo_technique_evaluation['email'] ?? '') }}">
                                    <input type="text" name="respo_technique[telephone]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Téléphone"
                                        value="{{ old('respo_technique.telephone', $evaluation->respo_technique_evaluation['telephone'] ?? '') }}">
                                </div>
                            </div>

                            <!-- Superviseur -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Superviseur</label>
                                <div class="space-y-2">
                                    <input type="text" name="superviseur[nom_complet]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Nom complet"
                                        value="{{ old('superviseur.nom_complet', $evaluation->superviseur_evaluation['nom_complet'] ?? '') }}">
                                    <input type="email" name="superviseur[email]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Email"
                                        value="{{ old('superviseur.email', $evaluation->superviseur_evaluation['email'] ?? '') }}">
                                    <input type="text" name="superviseur[telephone]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Téléphone"
                                        value="{{ old('superviseur.telephone', $evaluation->superviseur_evaluation['telephone'] ?? '') }}">
                                </div>
                            </div>

                            <!-- Évalué par -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Évalué par</label>
                                <div class="space-y-2">
                                    <input type="text" name="evalue_par[nom_complet]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Nom complet"
                                        value="{{ old('evalue_par.nom_complet', $evaluation->evalue_par['nom_complet'] ?? '') }}">
                                    <input type="email" name="evalue_par[email]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Email"
                                        value="{{ old('evalue_par.email', $evaluation->evalue_par['email'] ?? '') }}">
                                    <input type="text" name="evalue_par[telephone]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                                        placeholder="Téléphone"
                                        value="{{ old('evalue_par.telephone', $evaluation->evalue_par['telephone'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 space-y-3">
                            <button type="submit"
                                class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg transition-all shadow-md">
                                <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}"
                                class="block w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all text-center">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </a>
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
        const totalRef = {{ $totalNotesReference }};
        const noteInputs = document.querySelectorAll('.note-input');

        function updateTotals() {
            let totalObtenu = 0;

            document.querySelectorAll('.critere-row').forEach(row => {
                const noteRef = parseFloat(row.dataset.noteRef);
                const input = row.querySelector('.note-input');
                const pourcentageDisplay = row.querySelector('.pourcentage-display');
                const noteValue = parseFloat(input.value) || 0;

                // Validation max
                if (noteValue > noteRef) {
                    input.value = noteRef;
                }

                totalObtenu += Math.min(noteValue, noteRef);

                // Afficher pourcentage individuel
                const pourcentage = noteRef > 0 ? (noteValue / noteRef * 100) : 0;
                pourcentageDisplay.textContent = `(${pourcentage.toFixed(1)}%)`;

                // Couleur selon pourcentage
                if (pourcentage >= 70) {
                    pourcentageDisplay.className = 'pourcentage-display text-sm font-medium text-green-600';
                } else if (pourcentage >= 50) {
                    pourcentageDisplay.className = 'pourcentage-display text-sm font-medium text-yellow-600';
                } else {
                    pourcentageDisplay.className = 'pourcentage-display text-sm font-medium text-red-600';
                }
            });

            document.getElementById('totalObtenu').textContent = totalObtenu.toFixed(1);

            const pourcentageTotal = totalRef > 0 ? (totalObtenu / totalRef * 100) : 0;
            document.getElementById('pourcentageTotal').textContent = `(${pourcentageTotal.toFixed(1)}%)`;
        }

        noteInputs.forEach(input => {
            input.addEventListener('input', updateTotals);
            input.addEventListener('change', updateTotals);
        });

        // Initial calculation
        updateTotals();
    });
</script>
@endpush
