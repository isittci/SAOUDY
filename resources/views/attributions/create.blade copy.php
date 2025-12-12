@extends('layouts.main')
@section('title', 'Nouvelle Attribution')
@section('breadcrumb')
    <a href="{{ route('attributions.index') }}" class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle attribution</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('attributions.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-all">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Nouvelle Attribution de Lot</h1>
                    <p class="text-gray-600 mt-1">Attribuez un lot à un prestataire</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-red-700 font-medium">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('attributions.store') }}" method="POST" id="attributionForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Sélection lot, prestataire, proforma -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-link text-orange-500 mr-2"></i>
                                Informations principales
                            </h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <!-- Lot -->
                            <div>
                                <label for="lot_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Lot à attribuer <span class="text-red-500">*</span>
                                </label>
                                <select name="lot_id" id="lot_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('lot_id') border-red-500 @enderror">
                                    <option value="">Sélectionnez un lot...</option>
                                    @foreach($lots as $lot)
                                        <option value="{{ $lot->id_lot }}"
                                            {{ old('lot_id', $lotPreselectionne->id_lot ?? '') == $lot->id_lot ? 'selected' : '' }}
                                            data-ao="{{ $lot->appelOffre->numero_appel_offre ?? '' }}">
                                            {{ $lot->numero }} - {{ Str::limit($lot->libelle, 50) }}
                                            ({{ $lot->appelOffre->numero_appel_offre ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('lot_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prestataire -->
                            <div>
                                <label for="prestataire_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Prestataire <span class="text-red-500">*</span>
                                </label>
                                <select name="prestataire_id" id="prestataire_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('prestataire_id') border-red-500 @enderror">
                                    <option value="">Sélectionnez un prestataire...</option>
                                    @foreach($prestataires as $prestataire)
                                        <option value="{{ $prestataire->id_prestataire }}"
                                            {{ old('prestataire_id') == $prestataire->id_prestataire ? 'selected' : '' }}>
                                            {{ $prestataire->raison_sociale_prestataire }}
                                            @if($prestataire->ville_prestataire) ({{ $prestataire->ville_prestataire }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('prestataire_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Proforma -->
                            <div>
                                <label for="proforma_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Proforma <span class="text-red-500">*</span>
                                </label>
                                <select name="proforma_id" id="proforma_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('proforma_id') border-red-500 @enderror">
                                    <option value="">Sélectionnez une proforma...</option>
                                    @foreach($proformas as $proforma)
                                        <option value="{{ $proforma->id_proforma }}"
                                            {{ old('proforma_id') == $proforma->id_proforma ? 'selected' : '' }}
                                            data-montant="{{ $proforma->montant_ttc ?? 0 }}">
                                            {{ $proforma->numero_proforma }} -
                                            {{ number_format($proforma->montant_ttc ?? 0, 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                                @error('proforma_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Planification
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label for="date_attribution" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date d'attribution <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_attribution" id="date_attribution" required
                                        value="{{ old('date_attribution', date('Y-m-d')) }}"
                                        max="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 @error('date_attribution') border-red-500 @enderror">
                                    @error('date_attribution')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_debut_prevue" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date début prévue <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_debut_prevue" id="date_debut_prevue" required
                                        value="{{ old('date_debut_prevue') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 @error('date_debut_prevue') border-red-500 @enderror">
                                    @error('date_debut_prevue')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_fin_prevue" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date fin prévue <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date_fin_prevue" id="date_fin_prevue" required
                                        value="{{ old('date_fin_prevue') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 @error('date_fin_prevue') border-red-500 @enderror">
                                    @error('date_fin_prevue')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                                <span class="text-sm text-gray-600">Durée prévue:</span>
                                <span id="dureeCalculee" class="font-semibold text-gray-800">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Informations complémentaires
                            </h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label for="observations" class="block text-sm font-semibold text-gray-700 mb-2">Observations</label>
                                <textarea name="observations" id="observations" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
                                    placeholder="Notes ou commentaires...">{{ old('observations') }}</textarea>
                            </div>

                            <div>
                                <label for="conditions_particulieres" class="block text-sm font-semibold text-gray-700 mb-2">Conditions particulières</label>
                                <textarea name="conditions_particulieres" id="conditions_particulieres" rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400"
                                    placeholder="Conditions spécifiques à cette attribution...">{{ old('conditions_particulieres') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">

                    <!-- Paramètres financiers -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-coins text-green-500 mr-2"></i>
                                Paramètres financiers
                            </h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label for="montant_engage" class="block text-sm font-semibold text-gray-700 mb-2">Montant engagé (FCFA)</label>
                                <input type="number" name="montant_engage" id="montant_engage"
                                    value="{{ old('montant_engage', 0) }}"
                                    min="0" step="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                                @error('montant_engage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="taux_penalites" class="block text-sm font-semibold text-gray-700 mb-2">Taux de pénalités (%)</label>
                                <input type="number" name="taux_penalites" id="taux_penalites"
                                    value="{{ old('taux_penalites', 0) }}"
                                    min="0" max="100" step="0.01"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                                <p class="mt-1 text-xs text-gray-500">Pénalité par jour de retard</p>
                                @error('taux_penalites')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Récapitulatif -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-orange-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-clipboard-check text-orange-500 mr-2"></i>
                                Récapitulatif
                            </h2>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lot:</span>
                                <span id="recap_lot" class="font-medium text-gray-800 text-right max-w-[150px] truncate">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Prestataire:</span>
                                <span id="recap_prestataire" class="font-medium text-gray-800 text-right max-w-[150px] truncate">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Proforma:</span>
                                <span id="recap_proforma" class="font-medium text-gray-800">-</span>
                            </div>
                            <hr class="border-orange-200">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Durée:</span>
                                <span id="recap_duree" class="font-medium text-gray-800">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant:</span>
                                <span id="recap_montant" class="font-medium text-gray-800">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-6 space-y-3">
                        <button type="submit"
                            class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center">
                            <i class="fas fa-check mr-2"></i>
                            Enregistrer l'attribution
                        </button>

                        <a href="{{ route('attributions.index') }}"
                            class="w-full px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-all flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lotSelect = document.getElementById('lot_id');
    const prestataireSelect = document.getElementById('prestataire_id');
    const proformaSelect = document.getElementById('proforma_id');
    const dateDebut = document.getElementById('date_debut_prevue');
    const dateFin = document.getElementById('date_fin_prevue');
    const montantInput = document.getElementById('montant_engage');

    function updateRecap() {
        // Lot
        const lotOption = lotSelect.options[lotSelect.selectedIndex];
        document.getElementById('recap_lot').textContent = lotOption.value ? lotOption.text.split(' - ')[0] : '-';

        // Prestataire
        const prestataireOption = prestataireSelect.options[prestataireSelect.selectedIndex];
        let prestataireText = prestataireOption.value ? prestataireOption.text.split(' (')[0] : '-';
        document.getElementById('recap_prestataire').textContent = prestataireText.substring(0, 25) + (prestataireText.length > 25 ? '...' : '');

        // Proforma
        const proformaOption = proformaSelect.options[proformaSelect.selectedIndex];
        document.getElementById('recap_proforma').textContent = proformaOption.value ? proformaOption.text.split(' - ')[0] : '-';

        // Durée
        updateDuree();

        // Montant
        const montant = parseFloat(montantInput.value) || 0;
        document.getElementById('recap_montant').textContent = montant > 0
            ? new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA' : '-';
    }

    function updateDuree() {
        const debut = new Date(dateDebut.value);
        const fin = new Date(dateFin.value);

        if (dateDebut.value && dateFin.value && fin > debut) {
            const diffDays = Math.ceil(Math.abs(fin - debut) / (1000 * 60 * 60 * 24));
            const dureeText = diffDays + ' jour' + (diffDays > 1 ? 's' : '');
            document.getElementById('dureeCalculee').textContent = dureeText;
            document.getElementById('recap_duree').textContent = dureeText;
        } else {
            document.getElementById('dureeCalculee').textContent = '-';
            document.getElementById('recap_duree').textContent = '-';
        }
    }

    // Auto-remplir montant depuis proforma
    proformaSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.dataset.montant) {
            montantInput.value = parseFloat(option.dataset.montant);
        }
        updateRecap();
    });

    // Contraintes de dates
    document.getElementById('date_attribution').addEventListener('change', function() {
        dateDebut.min = this.value;
        if (dateDebut.value && dateDebut.value < this.value) {
            dateDebut.value = this.value;
        }
        updateRecap();
    });

    dateDebut.addEventListener('change', function() {
        dateFin.min = this.value;
        if (dateFin.value && dateFin.value <= this.value) {
            const debut = new Date(this.value);
            debut.setDate(debut.getDate() + 30);
            dateFin.value = debut.toISOString().split('T')[0];
        }
        updateRecap();
    });

    // Event listeners
    [lotSelect, prestataireSelect, dateFin, montantInput].forEach(el => {
        el.addEventListener('change', updateRecap);
        el.addEventListener('input', updateRecap);
    });

    // Init
    updateRecap();
});
</script>
@endpush
