@extends('layouts.main')
@section('title', 'Historique du Lot ' . $lot->numero)
@section('breadcrumb')
    <a @can('attributions_lots.read') href="{{ route('attributions.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Historique Lot {{ $lot->numero }}</span>
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
                        <div class="flex items-center space-x-3">
                            <h1 class="text-2xl font-bold text-gray-800">Historique du Lot</h1>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                {{ $lot->numero }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">{{ Str::limit($lot->libelle, 60) }}</p>
                    </div>
                </div>
                @can('attributions_lots.assign')
                @if(!$lot->attribution_lot)
                    <a href="{{ route('attributions.create',  $lot->id_lot) }}"
                        class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg shadow-md flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span class="font-medium">Attribuer ce lot</span>
                    </a>
                @endif
                @endcan
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Timeline historique -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-history text-purple-500 mr-2"></i>
                                Historique des attributions
                            </h2>
                            <span class="px-2.5 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                {{ $historique->count() }} version(s)
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        @if($historique->count() > 0)
                            <div class="relative">
                                <!-- Ligne verticale -->
                                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                                <div class="space-y-6">
                                    @foreach($historique as $attr)
                                        <div class="relative flex items-start space-x-4">
                                            <!-- Point sur la timeline -->
                                            <div class="relative z-10 flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm
                                                {{ $attr->is_active ? 'bg-orange-500' : 'bg-gray-400' }}">
                                                v{{ $attr->version_attribution }}
                                            </div>

                                            <!-- Carte attribution -->
                                            <div class="flex-1 bg-gray-50 rounded-xl p-4 border {{ $attr->is_active ? 'border-orange-200' : 'border-gray-200' }}">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <a @can('attributions_lots.view-details') href="{{ route('attributions.show', $attr->id_attribution) }}" @endcan
                                                            class="font-semibold text-gray-800 hover:text-orange-600">
                                                            {{ $attr->numero_attribution }}
                                                        </a>
                                                        @if($attr->is_active)
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                                Active
                                                            </span>
                                                        @endif
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            <i class="fas fa-building mr-1"></i>
                                                            {{ $attr->prestataire->raison_sociale_prestataire ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $attr->statut_badge_class }}">
                                                        {{ $attr->statut_label }}
                                                    </span>
                                                </div>

                                                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                                    <div>
                                                        <span class="text-gray-500">Attribution:</span>
                                                        <p class="font-medium text-gray-800">{{ $attr->date_attribution ? $attr->date_attribution->format('d/m/Y') : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Début:</span>
                                                        <p class="font-medium text-gray-800">{{ $attr->date_debut_prevue ? $attr->date_debut_prevue->format('d/m/Y') : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Fin:</span>
                                                        <p class="font-medium text-gray-800">{{ $attr->date_fin_prevue ? $attr->date_fin_prevue->format('d/m/Y') : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Avancement:</span>
                                                        <p class="font-medium text-gray-800">{{ number_format($attr->pourcentage_avancement, 0) }}%</p>
                                                    </div>
                                                </div>

                                                @if($attr->motif_retrait)
                                                    <div class="mt-3 p-2 bg-red-50 rounded-lg text-xs">
                                                        <span class="text-red-600 font-medium">Motif retrait:</span>
                                                        <p class="text-red-700 mt-1">{{ Str::limit($attr->motif_retrait, 100) }}</p>
                                                    </div>
                                                @endif

                                                <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                                    <span><i class="fas fa-user mr-1"></i>{{ $attr->createdBy->nom_complet ?? 'N/A' }}</span>
                                                    <span>{{ $attr->created_at ? $attr->created_at->format('d/m/Y H:i') : '' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500">Aucune attribution pour ce lot</p>
                                @can('attributions_lots.assign')
                                    <a href="{{ route('attributions.create', $lot->id_lot) }}"
                                        class="mt-4 inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                                        <i class="fas fa-plus mr-2"></i>Créer une attribution
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations lot -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-box text-indigo-500 mr-2"></i>
                            Informations du lot
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Numéro</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                {{ $lot->numero }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Libellé</label>
                            <p class="text-gray-800">{{ $lot->libelle }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Appel d'offres</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                {{ $lot->appelOffre->numero_appel_offre ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Statut actuel</label>
                            @if($lot->attribution_lot)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Attribué
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Non attribué
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-bar text-gray-500 mr-2"></i>
                            Statistiques
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total attributions</span>
                            <span class="font-bold text-gray-800">{{ $historique->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Terminées</span>
                            <span class="font-bold text-blue-600">{{ $historique->where('statut_attribution', 4)->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Retirées</span>
                            <span class="font-bold text-red-600">{{ $historique->where('statut_attribution', 3)->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Prestataires différents</span>
                            <span class="font-bold text-gray-800">{{ $historique->pluck('prestataire_id')->unique()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
