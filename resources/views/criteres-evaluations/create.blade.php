@extends('layouts.main')
@section('title', 'Nouveau Critère d\'Évaluation')
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors" title="{{ $lot->appelOffre->libelle_critere_appel_offre }}">{{ \Illuminate\Support\Str::limit($lot->appelOffre->libelle_critere_appel_offre, 15) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>


    <a href="{{ route('lots-appels-offres.index', [$lot->appelOffre->id_appel_offre]) }}" class="text-white/80 hover:text-white transition-colors" title="Liste de lots - {{ $lot->appelOffre->libelle_critere_appel_offre }}">Lots</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}" class="text-white/80 hover:text-white transition-colors" title="{{ $lot->libelle }}">{{ \Illuminate\Support\Str::limit($lot->libelle, 15) }}</a>


    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('criteres-evaluations.index', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}" class="text-white/80 hover:text-white transition-colors" title="Liste des critères d'évaluation - {{ $lot->libelle }}">Critères d'évaluation</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouveau</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('criteres-evaluations.index', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-plus-circle text-green-500"></i>
                            <span>Nouveau Critère d'Évaluation</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $lot->numero }} - {{ $lot->libelle }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('criteres-evaluations.index', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-times text-sm"></i>
                        <span class="text-sm font-medium hidden sm:inline">Annuler</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="text-red-800 font-semibold mb-2">Erreurs de validation</h3>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Formulaire -->
        <form method="POST" action="{{ route('criteres-evaluations.store', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}" class="max-w-4xl mx-auto">
            @csrf

            <div class="space-y-6">

                <!-- Informations du Lot (lecture seule) -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-box text-indigo-500 mr-2"></i>
                            Lot associé
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="p-4 bg-gradient-to-r from-indigo-50 to-white border border-indigo-200 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                            {{ $lot->numero }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ $lot->libelle }}
                                    </h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-star mr-1"></i>
                                            Notes utilisées: <strong>{{ number_format($totalNotesExistantes, 0) }}/100</strong>
                                        </span>
                                        <span class="text-green-600 font-semibold">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Disponible: {{ number_format($noteRestante, 0) }} points
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations du critère -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                            Informations du Critère
                        </h2>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Numéro (lecture seule, généré auto) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Numéro <span class="text-gray-400 text-xs">(généré automatiquement)</span>
                            </label>
                            <input type="text"
                                   value="{{ $prochainNumero }}"
                                   disabled
                                   class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                        </div>

                        <!-- Ordre d'exécution -->
                        <div>
                            <label for="ordre_execution_critere_evaluation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Ordre d'exécution <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="ordre_execution_critere_evaluation"
                                   name="ordre_execution_critere_evaluation"
                                   value="{{ old('ordre_execution_critere_evaluation', $prochainOrdre) }}"
                                   min="1"
                                   required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('ordre_execution_critere_evaluation') border-red-500 @enderror">
                            @error('ordre_execution_critere_evaluation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Libellé -->
                        <div class="md:col-span-2">
                            <label for="libelle_critere_evaluation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Libellé du critère <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="libelle_critere_evaluation"
                                   name="libelle_critere_evaluation"
                                   value="{{ old('libelle_critere_evaluation') }}"
                                   maxlength="160"
                                   required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('libelle_critere_evaluation') border-red-500 @enderror"
                                   placeholder="Ex: Qualité technique de l'offre">
                            @error('libelle_critere_evaluation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description_critere_evaluation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                id="description_critere_evaluation"
                                name="description_critere_evaluation"
                                rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('description_critere_evaluation') border-red-500 @enderror"
                                placeholder="Décrivez les détails de ce critère d'évaluation...">{{ old('description_critere_evaluation') }}</textarea>
                            @error('description_critere_evaluation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Note et Statut -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-star text-purple-500 mr-2"></i>
                            Note et Statut
                        </h2>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Note de référence -->
                        <div>
                            <label for="note_reference_critere_evaluation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Note de référence (sur 100) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number"
                                       id="note_reference_critere_evaluation"
                                       name="note_reference_critere_evaluation"
                                       value="{{ old('note_reference_critere_evaluation') }}"
                                       min="0"
                                       max="{{ $noteRestante }}"
                                       step="0.01"
                                       required
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('note_reference_critere_evaluation') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 text-sm">/ {{ $noteRestante }}</span>
                                </div>
                            </div>
                            @error('note_reference_critere_evaluation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Maximum disponible: {{ number_format($noteRestante, 2) }} points
                            </p>
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="statut_critere_evaluation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select id="statut_critere_evaluation"
                                    name="statut_critere_evaluation"
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('statut_critere_evaluation') border-red-500 @enderror">
                                <option value="1" {{ old('statut_critere_evaluation', '1') == '1' ? 'selected' : '' }}>Actif</option>
                                <option value="0" {{ old('statut_critere_evaluation') == '0' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('statut_critere_evaluation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                Les champs marqués d'une étoile sont obligatoires
                            </div>

                            <div class="flex items-center space-x-3 w-full sm:w-auto">
                                <a href="{{ route('criteres-evaluations.index', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                    class="flex-1 sm:flex-none px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium text-center">
                                    Annuler
                                </a>
                                <button type="submit"
                                    class="flex-1 sm:flex-none px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                    <i class="fas fa-save"></i>
                                    <span>Créer le critère</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    @push('scripts')
        <script>
            // Validation en temps réel de la note
            document.getElementById('note_reference_critere_evaluation')?.addEventListener('input', function() {
                const maxNote = {{ $noteRestante }};
                const value = parseFloat(this.value);

                if (value > maxNote) {
                    this.value = maxNote;
                    alert(`La note ne peut pas dépasser ${maxNote} points (note restante disponible)`);
                }

                if (value < 0) {
                    this.value = 0;
                }
            });
        </script>

        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
            }
        </style>
    @endpush
@endsection
