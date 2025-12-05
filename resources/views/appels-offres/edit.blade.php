@extends('layouts.main')
@section('title', 'Modifier AO - ' . $appelOffre->numero_appel_offre)
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">{{ $appelOffre->numero_appel_offre }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-edit text-orange-500"></i>
                        <span>Modifier l'Appel d'Offres</span>
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm">{{ $appelOffre->numero_appel_offre }} -
                        {{ $appelOffre->libelle_critere_appel_offre }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-red-700 font-medium mb-2">Erreurs de validation :</p>
                        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('appels-offres.update', $appelOffre->id_appel_offre) }}" method="POST" id="aoForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Informations générales -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                                Informations générales
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            <!-- Type d'AO (lecture seule) -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Type d'Appel d'Offres
                                </label>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-blue-100 text-blue-700">
                                        {{ $appelOffre->typeAppelOffre->code_type_appel_offre }}
                                    </span>
                                    <span
                                        class="text-sm text-gray-700">{{ $appelOffre->typeAppelOffre->libelle_type_appel_offre }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Le type ne peut pas être modifié après la création
                                </p>
                                <input type="hidden" name="type_appel_offre_id"
                                    value="{{ $appelOffre->type_appel_offre_id }}">
                            </div>

                            <!-- Numéro (lecture seule) -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Numéro
                                </label>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                        {{ $appelOffre->numero_appel_offre }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-lock mr-1"></i>
                                    Le numéro est généré automatiquement et ne peut pas être modifié
                                </p>
                            </div>

                            <!-- Libellé -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Libellé <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="libelle_critere_appel_offre" id="libelle" required
                                    maxlength="160"
                                    value="{{ old('libelle_critere_appel_offre', $appelOffre->libelle_critere_appel_offre) }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="Ex: Construction d'un immeuble administratif">
                                <div class="flex justify-between mt-1">
                                    @error('libelle_critere_appel_offre')
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 ml-auto"><span id="libelleCount">0</span>/160</p>
                                </div>
                            </div>

                            <!-- Montant Global -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Montant Global (FCFA) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="montant_global_appel_offre" id="montant_global" required
                                    min="0" step="0.01"
                                    value="{{ old('montant_global_appel_offre', $appelOffre->montant_global_appel_offre) }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="0.00">
                                @error('montant_global_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div id="montantInfo"
                                    class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <span>Intervalle du type:
                                        {{ number_format($appelOffre->typeAppelOffre->valeur_minimuim_type_appel_offre, 0, ',', ' ') }}
                                        -
                                        {{ number_format($appelOffre->typeAppelOffre->valeur_maximuim_type_appel_offre, 0, ',', ' ') }}
                                        FCFA
                                    </span>
                                </div>
                                <div id="montantWarning"
                                    class="hidden mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <span id="montantWarningText"></span>
                                </div>
                            </div>

                            <!-- Objet -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Objet de l'Appel d'Offres <span class="text-red-500">*</span>
                                </label>
                                <textarea name="objet_critere_appel_offre" id="objet" required rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Description officielle de ce qui est demandé...">{{ old('objet_critere_appel_offre', $appelOffre->objet_critere_appel_offre) }}</textarea>
                                @error('objet_critere_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description détaillée -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Description Détaillée <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description_critere_critere_appel_offre" id="description" required rows="6"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Détails des travaux, spécifications techniques...">{{ old('description_critere_critere_appel_offre', $appelOffre->description_critere_critere_appel_offre) }}</textarea>
                                @error('description_critere_critere_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dates et délais -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Dates et Délais
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Date de publication -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de Publication
                                    </label>
                                    <input type="date" name="date_publication_critere_appel_offre"
                                        id="date_publication"
                                        value="{{ old('date_publication_critere_appel_offre', $appelOffre->date_publication_critere_appel_offre?->format('Y-m-d')) }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                    @error('date_publication_critere_appel_offre')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date limite de dépôt -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date Limite de Dépôt <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_limite_depot_critere_appel_offre" id="date_limite"
                                        required
                                        value="{{ old('date_limite_depot_critere_appel_offre', $appelOffre->date_limite_depot_critere_appel_offre?->format('Y-m-d')) }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                    @error('date_limite_depot_critere_appel_offre')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date d'ouverture des plis -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date d'Ouverture des Plis
                                    </label>
                                    <input type="date" name="date_ouverture_plis_critere_appel_offre"
                                        id="date_ouverture"
                                        value="{{ old('date_ouverture_plis_critere_appel_offre', $appelOffre->date_ouverture_plis_critere_appel_offre?->format('Y-m-d')) }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                    @error('date_ouverture_plis_critere_appel_offre')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div id="dateInfo"
                                class="hidden p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span id="dateInfoText"></span>
                            </div>

                            @if ($appelOffre->isCloture())
                                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>Attention:</strong> Cet appel d'offres est clôturé. Modifier les dates pourrait
                                    le réactiver.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Critères et conditions -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-list-check text-purple-500 mr-2"></i>
                                Critères et Conditions
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            <!-- Conditions de participation -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Conditions de Participation
                                </label>
                                <textarea name="conditions_participation_critere_appel_offre" id="conditions_participation" rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Ex: Certifications requises, CA minimal, expérience...">{{ old('conditions_participation_critere_appel_offre', $appelOffre->conditions_participation_critere_appel_offre) }}</textarea>
                                @error('conditions_participation_critere_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Critères de sélection -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Critères de Sélection
                                </label>
                                <textarea name="criteres_selection_critere_appel_offre" id="criteres_selection" rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                    placeholder="Ex: Prix 60%, Technique 40%, Délais 10%...">{{ old('criteres_selection_critere_appel_offre', $appelOffre->criteres_selection_critere_appel_offre) }}</textarea>
                                @error('criteres_selection_critere_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Statut -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-toggle-on text-green-500 mr-2"></i>
                            Statut
                        </h3>

                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="statut_evaluation_critere_appel_offre" id="statut_evaluation"
                                value="1"
                                {{ old('statut_evaluation_critere_appel_offre', $appelOffre->statut_evaluation_critere_appel_offre) == 1 ? 'checked' : '' }}
                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <label for="statut_evaluation" class="text-sm font-medium text-gray-700">
                                Appel d'offres actif
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 ml-7">
                            Si décoché, l'AO sera désactivé
                        </p>
                    </div>

                    <!-- Informations -->
                    <div class="bg-blue-50 rounded-2xl shadow-lg p-6 border border-blue-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Informations
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600 font-medium mb-1">Créé le</p>
                                <p class="text-gray-900">{{ $appelOffre->created_at->format('d/m/Y à H:i') }}</p>
                                @if ($appelOffre->creator)
                                    <p class="text-xs text-gray-500">Par {{ $appelOffre->creator->nom_complet }}</p>
                                @endif
                            </div>

                            @if ($appelOffre->updated_at != $appelOffre->created_at)
                                <div class="pt-3 border-t border-blue-200">
                                    <p class="text-gray-600 font-medium mb-1">Dernière modification</p>
                                    <p class="text-gray-900">{{ $appelOffre->updated_at->format('d/m/Y à H:i') }}</p>
                                    @if ($appelOffre->updater)
                                        <p class="text-xs text-gray-500">Par {{ $appelOffre->updater->nom_complet }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="pt-3 border-t border-blue-200">
                                <p class="text-gray-600 font-medium mb-1">Nombre de lots</p>
                                <p class="text-gray-900 font-semibold">{{ $appelOffre->lots_count }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="bg-gradient-to-br from-orange-50 to-white rounded-2xl shadow-lg p-6 border border-orange-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-check-circle text-orange-500 mr-2"></i>
                            Actions
                        </h3>

                        <div class="space-y-3">
                            <button type="submit" id="submitBtn"
                                class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                <span id="submitText">Enregistrer les modifications</span>
                            </button>

                            <button type="button"
                                onclick="window.location.href='{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}'"
                                class="w-full px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </button>
                        </div>
                    </div>

                    <!-- Aide -->
                    <div class="bg-yellow-50 rounded-2xl shadow-lg p-6 border border-yellow-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                            Attention
                        </h3>

                        <div class="space-y-2 text-sm text-gray-600">
                            <p><i class="fas fa-lock text-red-500 mr-1"></i> Le type et le numéro ne peuvent pas être
                                modifiés</p>
                            <p><i class="fas fa-check text-green-500 mr-1"></i> Vérifiez que le montant reste dans
                                l'intervalle</p>
                            <p><i class="fas fa-check text-green-500 mr-1"></i> Les dates doivent être cohérentes</p>
                            @if ($appelOffre->lots_count > 0)
                                <p class="text-orange-600 font-medium mt-3">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Cet AO contient {{ $appelOffre->lots_count }} lot(s)
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    @push('scripts')
        <script>
            // Compteur de caractères
            const libelleInput = document.getElementById('libelle');
            const libelleCount = document.getElementById('libelleCount');

            libelleInput.addEventListener('input', function() {
                libelleCount.textContent = this.value.length;
            });
            libelleCount.textContent = libelleInput.value.length;

            // Validation du montant
            const montantInput = document.getElementById('montant_global');
            const montantWarning = document.getElementById('montantWarning');
            const montantWarningText = document.getElementById('montantWarningText');
            const min = {{ $appelOffre->typeAppelOffre->valeur_minimuim_type_appel_offre }};
            const max = {{ $appelOffre->typeAppelOffre->valeur_maximuim_type_appel_offre }};

            montantInput.addEventListener('input', function() {
                if (this.value) {
                    const montant = parseFloat(this.value);

                    if (montant < min || montant > max) {
                        montantWarning.classList.remove('hidden');
                        montantWarningText.textContent =
                            `Le montant doit être entre ${min.toLocaleString('fr-FR')} et ${max.toLocaleString('fr-FR')} FCFA`;
                    } else {
                        montantWarning.classList.add('hidden');
                    }
                } else {
                    montantWarning.classList.add('hidden');
                }
            });

            // Validation des dates
            const datePublication = document.getElementById('date_publication');
            const dateLimite = document.getElementById('date_limite');
            const dateOuverture = document.getElementById('date_ouverture');
            const dateInfo = document.getElementById('dateInfo');
            const dateInfoText = document.getElementById('dateInfoText');

            function validateDates() {
                const pub = datePublication.value ? new Date(datePublication.value) : new Date();
                const limite = dateLimite.value ? new Date(dateLimite.value) : null;
                const ouverture = dateOuverture.value ? new Date(dateOuverture.value) : null;

                if (limite) {
                    const diffJours = Math.ceil((limite - pub) / (1000 * 60 * 60 * 24));

                    if (diffJours > 0) {
                        dateInfo.classList.remove('hidden');
                        dateInfoText.textContent = `Délai de dépôt : ${diffJours} jour(s)`;
                    } else if (diffJours === 0) {
                        dateInfo.classList.remove('hidden');
                        dateInfo.className = 'p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700';
                        dateInfoText.textContent = `Attention : La date limite est aujourd'hui`;
                    } else {
                        dateInfo.classList.remove('hidden');
                        dateInfo.className = 'p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700';
                        dateInfoText.textContent = `Attention : La date limite est dépassée (l'AO sera clôturé)`;
                    }
                }
            }

            datePublication.addEventListener('change', validateDates);
            dateLimite.addEventListener('change', validateDates);
            dateOuverture.addEventListener('change', validateDates);

            // Soumission du formulaire
            document.getElementById('aoForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');

                submitBtn.disabled = true;
                submitText.textContent = 'Enregistrement...';
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + submitText.textContent;
            });

            // Initialiser les validations
            if (montantInput.value) {
                montantInput.dispatchEvent(new Event('input'));
            }
            validateDates();

            // Confirmation avant de quitter si modifications
            let formModified = false;
            const formElements = document.querySelectorAll('#aoForm input, #aoForm textarea, #aoForm select');

            formElements.forEach(element => {
                element.addEventListener('change', function() {
                    formModified = true;
                });
            });

            window.addEventListener('beforeunload', function(e) {
                if (formModified) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Ne pas afficher l'avertissement lors de la soumission
            document.getElementById('aoForm').addEventListener('submit', function() {
                formModified = false;
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
