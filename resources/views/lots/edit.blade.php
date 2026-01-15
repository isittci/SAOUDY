@extends('layouts.main')
@section('title', 'Modifier le Lot - ' . $lot->numero)
@section('breadcrumb')
    <a @can('lots.read') href="{{ route('lots.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Lots</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('lots.view-details') href="{{ route('lots.show', $lot->id_lot) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $lot->numero }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @can('lots.view-details')
                        <a href="{{ route('lots.show', $lot->id_lot) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-edit text-orange-500"></i>
                            <span>Modifier le Lot</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $lot->numero }} - {{ $lot->libelle }}</p>
                    </div>
                </div>

                @can('lots.view-details')
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('lots.show', $lot->id_lot) }}"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-times text-sm"></i>
                            <span class="text-sm font-medium hidden sm:inline">Annuler</span>
                        </a>
                    </div>
                @endcan
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

        <!-- Avertissement pour versioning -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="text-blue-800 font-semibold mb-1">Information importante</h3>
                    <p class="text-blue-700 text-sm">
                        La modification de ce lot créera une nouvelle version. L'ancienne version sera conservée dans
                        l'historique.
                        Le motif de modification est <strong>obligatoire</strong>.
                    </p>
                </div>
            </div>
        </div>

        @can('lots.update')
            <!-- Formulaire -->
            <form method="POST" action="{{ route('lots.update', $lot->id_lot) }}" class="max-w-5xl mx-auto">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    <!-- Informations de l'AO (lecture seule) -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-bullhorn text-orange-500 mr-2"></i>
                                Appel d'offres associé
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="p-4 bg-gradient-to-r from-orange-50 to-white border border-orange-200 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                                {{ $lot->appelOffre->numero_appel_offre }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                                {{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $lot->appelOffre->libelle_critere_appel_offre }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-coins mr-1"></i>
                                            Montant global:
                                            <strong>{{ number_format($lot->appelOffre->montant_global_appel_offre, 0, ',', ' ') }}
                                                FCFA</strong>
                                        </p>
                                    </div>
                                    <a href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}"
                                        target="_blank" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                        title="Voir l'appel d'offres">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motif de modification (obligatoire) -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-l-4 border-orange-500">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-exclamation-circle text-orange-500 mr-2"></i>
                                Motif de modification
                            </h2>
                        </div>

                        <div class="p-6">
                            <div>
                                <label for="motif_modification" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Pourquoi modifiez-vous ce lot ? <span class="text-red-500">*</span>
                                </label>
                                <textarea name="motif_modification" id="motif_modification" rows="3" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none @error('motif_modification') border-red-500 @enderror"
                                    placeholder="Ex: Modification des spécifications techniques suite à la demande du maître d'ouvrage...">{{ old('motif_modification') }}</textarea>
                                @error('motif_modification')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Ce motif sera enregistré dans l'historique des modifications
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Informations principales -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                                Informations du Lot
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            <!-- Numéro (lecture seule) -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Numéro du lot
                                </label>
                                <div class="px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                    {{ $lot->numero }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-lock mr-1"></i>
                                    Le numéro du lot ne peut pas être modifié
                                </p>
                            </div>

                            <!-- Libellé -->
                            <div>
                                <label for="libelle" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Libellé <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="libelle" id="libelle" value="{{ old('libelle', $lot->libelle) }}"
                                    required maxlength="160"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent @error('libelle') border-red-500 @enderror"
                                    placeholder="Ex: Gros œuvre, Électricité, Plomberie...">
                                @error('libelle')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description_critere" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea name="description_critere" id="description_critere" rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none @error('description_critere') border-red-500 @enderror"
                                    placeholder="Description détaillée du lot...">{{ old('description_critere', $lot->description_critere) }}</textarea>
                                @error('description_critere')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Décrivez précisément le contenu et les exigences de ce lot
                                </p>
                            </div>

                            <!-- Spécifications techniques -->
                            <div>
                                <label for="specifications_techniques" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Spécifications techniques
                                </label>
                                <textarea name="specifications_techniques" id="specifications_techniques" rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none @error('specifications_techniques') border-red-500 @enderror"
                                    placeholder="Spécifications techniques détaillées...">{{ old('specifications_techniques', $lot->specifications_techniques) }}</textarea>
                                @error('specifications_techniques')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dates et Délais -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Dates et Délais
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Date début prévue -->
                                <div>
                                    <label for="date_debut_prevue" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de début prévue <span class="text-red-500 px-1"> *</span>
                                    </label>
                                    <input type="date" required name="date_debut_prevue" id="date_debut_prevue"
                                        value="{{ old('date_debut_prevue', $lot->date_debut_prevue ? \Carbon\Carbon::parse($lot->date_debut_prevue)->format('Y-m-d') : '') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('date_debut_prevue') border-red-500 @enderror">
                                    @error('date_debut_prevue')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date fin prévue -->
                                <div>
                                    <label for="date_fin_prevue" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date de fin prévue <span class="text-red-500 px-1"> *</span>
                                    </label>
                                    <input type="date" required name="date_fin_prevue" id="date_fin_prevue"
                                        value="{{ old('date_fin_prevue', $lot->date_fin_prevue ? \Carbon\Carbon::parse($lot->date_fin_prevue)->format('Y-m-d') : '') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('date_fin_prevue') border-red-500 @enderror">
                                    @error('date_fin_prevue')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    La date de fin doit être postérieure à la date de début
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-cog text-purple-500 mr-2"></i>
                                Paramètres
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">


                            <!-- Statut -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Statut du lot <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="radio" name="statut_lot" value="1"
                                            {{ old('statut_lot', $lot->statut_lot) == 1 ? 'checked' : '' }}
                                            class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                        <span class="text-sm font-medium text-gray-700 flex items-center">
                                            <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                            Actif
                                        </span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="radio" name="statut_lot" value="0"
                                            {{ old('statut_lot', $lot->statut_lot) == 0 ? 'checked' : '' }}
                                            class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                        <span class="text-sm font-medium text-gray-700 flex items-center">
                                            <i class="fas fa-times-circle text-gray-600 mr-1"></i>
                                            Inactif
                                        </span>
                                    </label>
                                </div>
                                @error('statut_lot')
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
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                    Les champs marqués d'un <span class="text-red-500">*</span> sont obligatoires
                                </div>

                                <div class="flex items-center space-x-3 w-full sm:w-auto">
                                    <a href="{{ route('lots.show', $lot->id_lot) }}"
                                        class="flex-1 sm:flex-none px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium text-center">
                                        Annuler
                                    </a>
                                    <button type="submit"
                                        class="flex-1 sm:flex-none px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                        <i class="fas fa-save"></i>
                                        <span>Enregistrer les modifications</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endcan
    </main>
    @can('lots.update')
        @push('scripts')
            <script>
                // Validation date fin > date début
                document.getElementById('date_fin_prevue').addEventListener('change', function() {
                    const dateDebut = document.getElementById('date_debut_prevue').value;
                    const dateFin = this.value;

                    if (dateDebut && dateFin && new Date(dateFin) <= new Date(dateDebut)) {
                        alert('La date de fin doit être postérieure à la date de début');
                        this.value = '';
                    }
                });



                // Confirmation avant soumission
                document.querySelector('form').addEventListener('submit', function(e) {
                    const motif = document.getElementById('motif_modification').value.trim();

                    if (!motif) {
                        e.preventDefault();
                        alert('Le motif de modification est obligatoire');
                        document.getElementById('motif_modification').focus();
                        return false;
                    }

                    if (!confirm(
                            'Êtes-vous sûr de vouloir enregistrer ces modifications ? Une nouvelle version du lot sera créée.'
                            )) {
                        e.preventDefault();
                        return false;
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
    @endcan
@endsection
