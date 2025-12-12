@extends('layouts.main')
@section('title', 'Réattribuer le lot')
@section('breadcrumb')
    <a href="{{ route('attributions.index') }}" class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Réattribution</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('attributions.show', $attribution->id_attribution) }}" class="p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Réattribuer le Lot</h1>
                    <p class="text-gray-600 mt-1">Lot {{ $attribution->lot->numero ?? 'N/A' }} - Ancienne: {{ $attribution->numero_attribution }}</p>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
            <p class="text-yellow-800 font-medium"><i class="fas fa-info-circle mr-2"></i>Réattribution en cours</p>
            <p class="text-sm text-yellow-700 mt-1">Le lot <strong>{{ $attribution->lot->numero ?? 'N/A' }}</strong> sera réattribué. L'ancienne attribution devient historique.</p>
        </div>

        <form action="{{ route('attributions.reattribuer', $attribution->id_attribution) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Motif -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-exclamation-circle text-red-500 mr-2"></i>Motif</h2></div>
                        <div class="p-6">
                            <textarea name="motif_reattribution" rows="3" required minlength="10" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-400" placeholder="Pourquoi réattribuez-vous ce lot ?">{{ old('motif_reattribution') }}</textarea>
                        </div>
                    </div>

                    <!-- Nouvelle attribution -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-user-plus text-green-500 mr-2"></i>Nouvelle attribution</h2></div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau prestataire *</label>
                                <select name="prestataire_id" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400">
                                    <option value="">Sélectionnez...</option>
                                    @foreach($prestataires as $p)
                                        <option value="{{ $p->id_prestataire }}" {{ old('prestataire_id') == $p->id_prestataire ? 'selected' : '' }}>
                                            {{ $p->raison_sociale_prestataire }} @if($p->id_prestataire == $attribution->prestataire_id)(Ancien)@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nouvelle proforma *</label>
                                <select name="proforma_id" id="proforma_id" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400">
                                    <option value="">Sélectionnez...</option>
                                    @foreach($proformas as $pf)
                                        <option value="{{ $pf->id_proforma }}" data-montant="{{ $pf->montant_ttc ?? 0 }}" {{ old('proforma_id') == $pf->id_proforma ? 'selected' : '' }}>
                                            {{ $pf->numero_proforma }} - {{ number_format($pf->montant_ttc ?? 0, 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Planification</h2></div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date attribution *</label>
                                <input type="date" name="date_attribution" value="{{ old('date_attribution', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date début *</label>
                                <input type="date" name="date_debut_prevue" value="{{ old('date_debut_prevue', date('Y-m-d')) }}" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date fin *</label>
                                <input type="date" name="date_fin_prevue" value="{{ old('date_fin_prevue') }}" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-comment-alt text-gray-500 mr-2"></i>Observations</h2></div>
                        <div class="p-6">
                            <textarea name="observations" rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-gray-400" placeholder="Notes...">{{ old('observations') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Lot -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-box text-indigo-500 mr-2"></i>Lot</h2></div>
                        <div class="p-6 space-y-3">
                            <div><span class="text-sm text-gray-600">Numéro:</span> <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">{{ $attribution->lot->numero ?? 'N/A' }}</span></div>
                            <div><span class="text-sm text-gray-600">Libellé:</span> <p class="text-gray-800 font-medium">{{ $attribution->lot->libelle ?? 'N/A' }}</p></div>
                        </div>
                    </div>

                    <!-- Ancienne -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-100 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-history text-gray-500 mr-2"></i>Ancienne</h2></div>
                        <div class="p-6 space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">N°:</span><span class="font-medium">{{ $attribution->numero_attribution }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Version:</span><span class="font-medium">v{{ $attribution->version_attribution }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Prestataire:</span><span class="font-medium truncate max-w-[120px]">{{ $attribution->prestataire->raison_sociale_prestataire ?? 'N/A' }}</span></div>
                        </div>
                    </div>

                    <!-- Finances -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-coins text-green-500 mr-2"></i>Finances</h2></div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Montant engagé</label>
                                <input type="number" name="montant_engage" id="montant_engage" value="{{ old('montant_engage', 0) }}" min="0" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Taux pénalités (%)</label>
                                <input type="number" name="taux_penalites" value="{{ old('taux_penalites', $attribution->taux_penalites) }}" min="0" max="100" step="0.01" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400">
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 space-y-3">
                        <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg shadow-md flex items-center justify-center">
                            <i class="fas fa-redo mr-2"></i>Réattribuer
                        </button>
                        <a href="{{ route('attributions.show', $attribution->id_attribution) }}" class="w-full px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </main>
@endsection

@push('scripts')
<script>
document.getElementById('proforma_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt.dataset.montant) document.getElementById('montant_engage').value = parseFloat(opt.dataset.montant);
});
</script>
@endpush
