@extends('layouts.main')

@section('title', 'Modifier l\'évaluation - ' . $evaluation->numero_evaluation)

@section('breadcrumb')
    <a @can('evaluations_attributions.read') href="{{ route('evaluations.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Évaluations</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('evaluations_attributions.view-details') href="{{ route('evaluations.show', $evaluation->id_evaluation) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ $evaluation->numero_evaluation }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                @can('evaluations_attributions.view-details')
                <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                @endcan
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-gray-800">Modifier l'évaluation</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $evaluation->statut_badge_class }}">
                            <i class="fas fa-{{ $evaluation->statut_icon }} mr-1"></i>
                            {{ $evaluation->statut_label }}
                        </span>
                        @if($evaluation->version > 1)
                            <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                V{{ $evaluation->version }}
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1">
                        {{ $evaluation->numero_evaluation }} - Lot {{ $attribution->lot->numero }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')
        @can('evaluations_attributions.evaluate')
            <form action="{{ route('evaluations.update', $evaluation->id_evaluation) }}" method="POST" id="evaluationEditForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Colonne principale -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Critère évalué (lecture seule) -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-list-check text-indigo-500 mr-2"></i>
                                    Critère d'évaluation
                                    <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full">
                                        <i class="fas fa-lock text-xs mr-1"></i>Non modifiable
                                    </span>
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <span class="px-3 py-1 text-sm font-bold bg-indigo-100 text-indigo-700 rounded-lg">
                                                {{ $evaluation->critereEvaluation->numero_critere_evaluation }}
                                            </span>
                                            <h4 class="font-semibold text-gray-900">{{ $evaluation->critereEvaluation->libelle_critere_evaluation }}</h4>
                                        </div>
                                        <span class="text-lg font-bold text-indigo-600">
                                            {{ number_format($noteReferenceCritere, 2) }} pts
                                        </span>
                                    </div>
                                    @if($evaluation->critereEvaluation->description_critere_evaluation)
                                        <p class="text-sm text-gray-600 mb-3">{{ $evaluation->critereEvaluation->description_critere_evaluation }}</p>
                                    @endif
                                    <div class="grid grid-cols-3 gap-4 pt-3 border-t border-indigo-200 text-sm">
                                        <div>
                                            <span class="text-indigo-600">Autres évaluations:</span>
                                            <p class="font-bold text-indigo-800">{{ number_format($totalAutresEvaluations, 2) }} pts</p>
                                        </div>
                                        <div>
                                            <span class="text-indigo-600">Valeur actuelle:</span>
                                            <p class="font-bold text-indigo-800">{{ number_format($evaluation->resultat_evaluation, 2) }} pts</p>
                                        </div>
                                        <div>
                                            <span class="text-green-600">Max modifiable:</span>
                                            <p class="font-bold text-green-700">{{ number_format($maxModifiable, 2) }} pts</p>
                                        </div>
                                    </div>
                                </div>
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
                                            (max: {{ number_format($maxModifiable, 2) }})
                                        </span>
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        <input type="number" required
                                            name="resultat_evaluation"
                                            id="resultatEvaluation"
                                            class="w-40 px-4 py-3 text-xl font-bold border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-400 focus:border-green-400 @error('resultat_evaluation') border-red-500 @enderror"
                                            min="0"
                                            step="0.01"
                                            max="{{ $maxModifiable }}"
                                            value="{{ old('resultat_evaluation', $evaluation->resultat_evaluation) }}"
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
                                        placeholder="Observations sur cette évaluation...">{{ old('observation', $noteCritere?->observation) }}</textarea>
                                </div>

                                <!-- Justification -->
                                <div class="mt-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Justification</label>
                                    <textarea name="justification" rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400"
                                        placeholder="Justification de la note attribuée...">{{ old('justification', $noteCritere?->justification) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Commentaire général et recommandation -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                    Commentaires
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Commentaire général</label>
                                    <textarea name="commentaire_general" rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400"
                                        placeholder="Commentaire général sur l'évaluation...">{{ old('commentaire_general', $evaluation->commentaire_general) }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Recommandation</label>
                                    <textarea name="recommandation" rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400"
                                        placeholder="Recommandations...">{{ old('recommandation', $evaluation->recommandation) }}</textarea>
                                </div>
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
                                            value="{{ old('respo_technique.nom_complet', $evaluation->respo_technique_evaluation['nom_complet'] ?? '') }}"
                                            required>
                                        @error('respo_technique.nom_complet')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                        <input type="email" name="respo_technique[email]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                                            placeholder="Email"
                                            value="{{ old('respo_technique.email', $evaluation->respo_technique_evaluation['email'] ?? '') }}">
                                        <input type="text" name="respo_technique[telephone]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                                            placeholder="Téléphone"
                                            value="{{ old('respo_technique.telephone', $evaluation->respo_technique_evaluation['telephone'] ?? '') }}">
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
                                            value="{{ old('superviseur.nom_complet', $evaluation->superviseur_evaluation['nom_complet'] ?? '') }}"
                                            required>
                                        @error('superviseur.nom_complet')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                        <input type="email" name="superviseur[email]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400"
                                            placeholder="Email"
                                            value="{{ old('superviseur.email', $evaluation->superviseur_evaluation['email'] ?? '') }}">
                                        <input type="text" name="superviseur[telephone]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400"
                                            placeholder="Téléphone"
                                            value="{{ old('superviseur.telephone', $evaluation->superviseur_evaluation['telephone'] ?? '') }}">
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
                                            value="{{ old('evalue_par.nom_complet', $evaluation->evalue_par['nom_complet'] ?? '') }}"
                                            required>
                                        @error('evalue_par.nom_complet')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
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

                        @canany(['evaluations_attributions.evaluate', 'evaluations_attributions.view-details'])
                        <!-- Actions -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="p-6 space-y-3">
                                @can('evaluations_attributions.evaluate')
                                <button type="submit"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg transition-all shadow-md">
                                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                                </button>
                                @endcan
                                @can('evaluations_attributions.view-details')
                                <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}"
                                    class="block w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all text-center">
                                    <i class="fas fa-times mr-2"></i>Annuler
                                </a>
                                @endcan
                            </div>
                        </div>
                        @endcanany

                        <!-- Note d'information -->
                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-1 mr-3"></i>
                                <div class="text-sm text-amber-800">
                                    <p class="font-semibold mb-1">Important</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Le critère ne peut pas être modifié</li>
                                        <li>La note ne peut pas dépasser <strong>{{ number_format($maxModifiable, 2) }} pts</strong></li>
                                        <li>Les 3 responsables restent <strong>obligatoires</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Informations audit -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                                    Informations
                                </h2>
                            </div>
                            <div class="p-6 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Créé le</span>
                                    <span class="text-gray-900">{{ $evaluation->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($evaluation->creator)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Par</span>
                                        <span class="text-gray-900">{{ $evaluation->creator->name ?? 'N/A' }}</span>
                                    </div>
                                @endif
                                @if($evaluation->updated_at != $evaluation->created_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Modifié le</span>
                                        <span class="text-gray-900">{{ $evaluation->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        @endcan
    </main>
@endsection

@can('evaluations_attributions.evaluate')
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resultatInput = document.getElementById('resultatEvaluation');
            const pourcentageDisplay = document.getElementById('pourcentageDisplay');

            const maxModifiable = {{ $maxModifiable }};
            const noteReference = {{ $noteReferenceCritere }};

            function updatePourcentage() {
                const resultat = parseFloat(resultatInput.value) || 0;

                // Validation du max
                if (resultat > maxModifiable) {
                    resultatInput.value = maxModifiable;
                }

                // Calcul du pourcentage
                if (noteReference > 0) {
                    const pourcentage = (Math.min(resultat, maxModifiable) / noteReference * 100).toFixed(1);
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

            // Écouter les changements de résultat
            resultatInput.addEventListener('input', updatePourcentage);
            resultatInput.addEventListener('change', updatePourcentage);

            // Calcul initial
            updatePourcentage();

            // Validation avant soumission
            document.getElementById('evaluationEditForm').addEventListener('submit', function(e) {
                const resultat = parseFloat(resultatInput.value) || 0;

                if (resultat > maxModifiable) {
                    e.preventDefault();
                    alert(`La note ne peut pas dépasser ${maxModifiable.toFixed(2)} points`);
                    resultatInput.focus();
                    return false;
                }

                if (resultat < 0) {
                    e.preventDefault();
                    alert('La note ne peut pas être négative');
                    resultatInput.focus();
                    return false;
                }
            });
        });
    </script>
    @endpush
@endcan
