@extends('layouts.main')
@section('title', 'Modifier Attribution')
@section('breadcrumb')
    <a @can('attributions_lots.read') href="{{ route('attributions.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('attributions_lots.view-details') href="{{ route('attributions.show', $attribution->id_attribution) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ $attribution->numero_attribution }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                @can('attributions_lots.view-details')
                <a href="{{ route('attributions.show', $attribution->id_attribution) }}" class="p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                @endcan
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Modifier l'attribution</h1>
                    <p class="text-gray-600 mt-1">{{ $attribution->numero_attribution }}</p>
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

        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg mb-6">
            <p class="text-yellow-800 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                Seules les informations non critiques peuvent être modifiées. Pour changer le prestataire ou la proforma, utilisez la fonction de réattribution.
            </p>
        </div>

        @can('attributions_lots.assign')
        <form action="{{ route('attributions.update', $attribution->id_attribution) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">

                    <!-- Dates -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Planification</h2>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date début prévue</label>
                                <input type="date" name="date_debut_prevue"
                                    value="{{ old('date_debut_prevue', $attribution->date_debut_prevue ? $attribution->date_debut_prevue->format('Y-m-d') : '') }}"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date fin prévue</label>
                                <input type="date" name="date_fin_prevue"
                                    value="{{ old('date_fin_prevue', $attribution->date_fin_prevue ? $attribution->date_fin_prevue->format('Y-m-d') : '') }}"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>


                    <!-- Observations -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-comment-alt text-gray-500 mr-2"></i>Notes</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Observations</label>
                                <textarea name="observations" rows="3"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-gray-400"
                                    placeholder="Notes...">{{ old('observations', $attribution->observations) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Conditions particulières</label>
                                <textarea name="conditions_particulieres" rows="4"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-gray-400"
                                    placeholder="Conditions...">{{ old('conditions_particulieres', $attribution->conditions_particulieres) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Résumé -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-info-circle text-orange-500 mr-2"></i>Résumé</h2>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">N° Attribution:</span>
                                <span class="font-medium text-gray-800">{{ $attribution->numero_attribution }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lot:</span>
                                <span class="font-medium text-gray-800">{{ $attribution->lot->numero ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Prestataire:</span>
                                <span class="font-medium text-gray-800 truncate max-w-[150px]">{{ $attribution->prestataire->raison_sociale_prestataire ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Proforma:</span>
                                <span class="font-medium text-gray-800">{{ $attribution->proforma->numero_proforma ?? 'N/A' }}</span>
                            </div>
                            <hr class="border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Statut:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $attribution->statut_badge_class }}">
                                    {{ $attribution->statut_label }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Avancement:</span>
                                <span class="font-medium text-gray-800">{{ number_format($attribution->pourcentage_avancement, 2) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 space-y-3">
                        <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg shadow-md flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('attributions.show', $attribution->id_attribution) }}" class="w-full px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
        @endcan
    </main>
@endsection
