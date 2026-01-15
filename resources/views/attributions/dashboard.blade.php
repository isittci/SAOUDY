@extends('layouts.main')
@section('title', 'Dashboard Attributions')
@section('breadcrumb')
    <a @can('attributions_lots.read') href="{{ route('attributions.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Dashboard</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @can('attributions_lots.read')
                        <a href="{{ route('attributions.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-orange-500 mr-3"></i>
                            Dashboard Attributions
                        </h1>
                        <p class="text-gray-600 mt-1">Vue d'ensemble des attributions de lots</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Statistiques principales -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-5 border-l-4 border-gray-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($statistiques['total']) }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full"><i class="fas fa-list text-gray-500 text-xl"></i></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-5 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">En cours</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($statistiques['en_cours']) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full"><i class="fas fa-play text-green-500 text-xl"></i></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-5 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Suspendues</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ number_format($statistiques['suspendues']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full"><i class="fas fa-pause text-yellow-500 text-xl"></i></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Terminées</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($statistiques['terminees']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full"><i class="fas fa-check-double text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-5 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">En retard</p>
                        <p class="text-3xl font-bold text-red-600">{{ number_format($statistiques['en_retard']) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full"><i
                            class="fas fa-exclamation-triangle text-red-500 text-xl"></i></div>
                </div>
            </div>
        </div>

        <!-- Montants -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Montant total engagé</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ number_format($statistiques['montant_total_engage'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="p-4 bg-white/20 rounded-full"><i class="fas fa-coins text-3xl"></i></div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Montant total payé</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ number_format($statistiques['montant_total_paye'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="p-4 bg-white/20 rounded-full"><i class="fas fa-check-circle text-3xl"></i></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Dernières attributions -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-clock text-orange-500 mr-2"></i>
                            Dernières attributions
                        </h2>
                        @can('attributions_lots.read')
                            <a href="{{ route('attributions.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                                Voir tout <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    @forelse($dernieresAttributions as $attr)
                        <a @can('attributions_lots.view-details') href="{{ route('attributions.show', $attr->id_attribution) }}" @endcan
                            class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-800">{{ $attr->numero_attribution }}</span>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $attr->statut_badge_class }}">
                                        {{ $attr->statut_label }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ Str::limit($attr->prestataire->raison_sociale_prestataire ?? '', 30) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ $attr->lot->numero ?? '' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $attr->created_at ? $attr->created_at->diffForHumans() : '' }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p>Aucune attribution récente</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Attributions en retard -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            Attributions en retard
                        </h2>
                        <span class="px-2.5 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                            {{ $attributionsEnRetard->count() }}
                        </span>
                    </div>
                </div>
                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    @forelse($attributionsEnRetard as $attr)
                        <a @can('attributions_lots.view-details') href="{{ route('attributions.show', $attr->id_attribution) }}" @endcan
                            class="flex items-center justify-between p-4 hover:bg-red-50 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-800">{{ $attr->numero_attribution }}</span>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $attr->jours_retard_actuels }}j
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ Str::limit($attr->prestataire->raison_sociale_prestataire ?? '', 30) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ $attr->lot->numero ?? '' }}</p>
                                <p class="text-xs text-red-600">Fin:
                                    {{ $attr->date_fin_prevue ? $attr->date_fin_prevue->format('d/m/Y') : '' }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                            <p>Aucune attribution en retard</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
