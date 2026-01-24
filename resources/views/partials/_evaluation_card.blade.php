{{--
    Partial à intégrer dans la vue attributions/show.blade.php
    Pour ajouter le module d'évaluation
--}}

{{-- ====================================================================== --}}
{{-- BOUTON ÉVALUATION - À AJOUTER DANS LE HEADER DES ACTIONS --}}
{{-- ====================================================================== --}}

@if($attribution->statut_attribution === \App\Models\AttributionLotPrestataire::STATUT_ATTRIBUE)
    @php
        $evaluationExistante = \App\Models\Evaluation::pourAttribution($attribution->id_attribution)
            ->current()
            ->first();
    @endphp

    @canany(['evaluations_attributions.read', 'evaluations_attributions.evaluate'])
    @if($evaluationExistante)
    @can('evaluations_attributions.read')
        <a href="{{ route('evaluations.show', $evaluationExistante->id_evaluation) }}"
            class="px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
            <i class="fas fa-clipboard-check text-sm"></i>
            <span class="text-sm font-medium">Voir l'évaluation</span>
        </a>
        @endcan
    @else
    @can('evaluations_attributions.evaluate')
        <a href="{{ route('evaluations.create', $attribution->id_attribution) }}"
            class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
            <i class="fas fa-clipboard-list text-sm"></i>
            <span class="text-sm font-medium">Évaluer</span>
        </a>
        @endcan
    @endif
    @endcanany
@endif

{{-- ====================================================================== --}}
{{-- CARD ÉVALUATION - À AJOUTER DANS LA COLONNE LATÉRALE --}}
{{-- ====================================================================== --}}

@php
    $evaluation = \App\Models\Evaluation::pourAttribution($attribution->id_attribution)
        ->current()
        ->with(['notesCriteres', 'validateur'])
        ->first();
@endphp

@if($evaluation)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-clipboard-check text-indigo-500 mr-2"></i>
                    Évaluation
                </h2>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $evaluation->statut_badge_class }}">
                    {{ $evaluation->statut_label }}
                </span>
            </div>
        </div>
        <div class="p-6 space-y-4">
            {{-- Numéro --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Numéro</label>
                <a @can('evaluations_attributions.view-details') href="{{ route('evaluations.show', $evaluation->id_evaluation) }}" @endcan
                    class="text-indigo-600 hover:text-indigo-800 font-medium">
                    {{ $evaluation->numero_evaluation }}
                </a>
            </div>

            {{-- Score --}}
            <div class="bg-gradient-to-r from-indigo-50 to-white rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-700">Score obtenu</span>
                    <span class="text-2xl font-bold {{ $evaluation->pourcentage_final >= 70 ? 'text-green-600' : ($evaluation->pourcentage_final >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($evaluation->pourcentage_final, 1) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full {{ $evaluation->pourcentage_final >= 70 ? 'bg-green-500' : ($evaluation->pourcentage_final >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                         style="width: {{ min($evaluation->pourcentage_final, 100) }}%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>{{ number_format($evaluation->resultat_evaluation, 1) }} pts</span>
                    <span>/ {{ number_format($evaluation->note_maximale, 1) }} pts</span>
                </div>
            </div>

            {{-- Rang si validé --}}
            @if($evaluation->rang && $evaluation->isValidee())
                <div class="flex items-center justify-center p-3 bg-{{ $evaluation->rang === 1 ? 'yellow' : 'gray' }}-50 rounded-lg">
                    <i class="fas fa-trophy text-{{ $evaluation->rang === 1 ? 'yellow' : 'gray' }}-500 mr-2"></i>
                    <span class="font-bold text-{{ $evaluation->rang === 1 ? 'yellow' : 'gray' }}-700">
                        Rang {{ $evaluation->rang }}{{ $evaluation->rang === 1 ? 'er' : 'ème' }}
                    </span>
                </div>
            @endif

            {{-- Date --}}
            @if($evaluation->date_evaluation)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Date d'évaluation</label>
                    <p class="text-gray-900">{{ $evaluation->date_evaluation->format('d/m/Y H:i') }}</p>
                </div>
            @endif

            @can('evaluations_attributions.view-details')
            {{-- Lien vers détails --}}
            <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}"
                class="block w-full text-center px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium rounded-lg transition-all">
                <i class="fas fa-eye mr-2"></i>Voir les détails
            </a>
            @endcan
        </div>
    </div>
@else
    {{-- Pas encore d'évaluation --}}
    @if($attribution->statut_attribution === \App\Models\AttributionLotPrestataire::STATUT_ATTRIBUE)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-clipboard-check text-indigo-500 mr-2"></i>
                    Évaluation
                </h2>
            </div>
            <div class="p-6 text-center">
                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-600 mb-4">Aucune évaluation n'a été effectuée.</p>
                @can('evaluations_attributions.evaluate')
                <a href="{{ route('evaluations.create', $attribution->id_attribution) }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-medium rounded-lg transition-all shadow-md">
                    <i class="fas fa-plus mr-2"></i>Créer une évaluation
                </a>
                @endcan
            </div>
        </div>
    @endif
@endif
