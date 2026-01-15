@extends('layouts.main')

@section('title', 'Capacités Techniques - ' . $prestataire->raison_sociale_prestataire)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .capacite-card {
        transition: all 0.3s ease;
    }
    .capacite-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .badge-certification {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        margin: 0.125rem;
    }
    .badge-agrement {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        margin: 0.125rem;
    }
    .badge-expertise {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        margin: 0.125rem;
    }
    .score-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
    }
    .score-excellent { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .score-bon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .score-moyen { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
    .score-faible { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }
</style>
@endpush

@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Prestataires</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 30) }}</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Capacités Techniques</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    @can('prestataires.read')
                    <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                       class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-cogs text-orange-500 mr-2"></i>
                            Capacités Techniques
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                    </div>
                </div>
                @can('capacites_techniques.manage')
                <div class="flex items-center gap-2">
                    <a href="{{ route('prestataires.capacites-techniques.create', $prestataire->id_prestataire) }}"
                       class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i>Ajouter
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages Flash -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Fiches</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Effectif Total</span>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['effectif_total'] ?? 0, 0, ',', ' ') }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Certifications</span>
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-certificate text-purple-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['avec_certifications'] }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Agréments</span>
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-award text-orange-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['avec_agrements'] }}</p>
            </div>
        </div>

        <!-- Liste des capacités techniques -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list text-blue-500 mr-2"></i>
                    Fiches de Capacités Techniques
                    <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ $capacites->total() }}</span>
                </h2>
            </div>

            @if($capacites->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($capacites as $capacite)
                        @php
                            $niveau = $capacite->getNiveau();
                            $score = $capacite->calculerScore();
                            $scoreClass = match(true) {
                                $score >= 80 => 'score-excellent',
                                $score >= 60 => 'score-bon',
                                $score >= 40 => 'score-moyen',
                                default => 'score-faible',
                            };
                        @endphp
                        <div class="capacite-card p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                                <!-- Score -->
                                <div class="flex-shrink-0 flex flex-col items-center">
                                    <div class="score-circle {{ $scoreClass }}">
                                        {{ $score }}
                                    </div>
                                    <span class="mt-2 text-sm font-medium text-{{ $niveau['classe'] }}-600">{{ $niveau['niveau'] }}</span>
                                </div>

                                <!-- Contenu principal -->
                                <div class="flex-1 min-w-0">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <!-- Effectifs -->
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-users text-blue-500 mr-2"></i>Effectifs
                                            </h4>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-500">Permanents</p>
                                                    <p class="text-lg font-bold text-gray-800">{{ $capacite->effectif_permanent_capacite_technique ?? 0 }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Temporaires</p>
                                                    <p class="text-lg font-bold text-gray-800">{{ $capacite->effectif_temporaire_capacite_technique ?? 0 }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Références & Compétences -->
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3">
                                                <i class="fas fa-star text-yellow-500 mr-2"></i>Références
                                            </h4>
                                            <p class="text-sm text-gray-600">{{ $capacite->references_capacite_technique ?: 'Non renseigné' }}</p>
                                            @if($capacite->competences_cles_capacite_technique)
                                                <p class="text-xs text-gray-500 mt-2">
                                                    <strong>Compétences:</strong> {{ $capacite->competences_cles_capacite_technique }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Certifications -->
                                    @if($capacite->certifications_capacite_technique)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 mb-2">
                                                <i class="fas fa-certificate text-purple-500 mr-1"></i>Certifications
                                            </p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($capacite->certifications_array as $certification)
                                                    <span class="badge-certification">{{ $certification }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Agréments -->
                                    @if($capacite->agrements_capacite_technique)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 mb-2">
                                                <i class="fas fa-award text-green-500 mr-1"></i>Agréments
                                            </p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($capacite->agrements_array as $agrement)
                                                    <span class="badge-agrement">{{ $agrement }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Domaines d'expertise -->
                                    @if($capacite->domaines_expertise_capacite_technique)
                                        <div>
                                            <p class="text-xs text-gray-500 mb-2">
                                                <i class="fas fa-briefcase text-orange-500 mr-1"></i>Domaines d'expertise
                                            </p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($capacite->domaines_expertise_array as $domaine)
                                                    <span class="badge-expertise">{{ $domaine }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>


                                @canany(['capacites_techniques.read', 'capacites_techniques.manage'])
                                    <!-- Actions -->
                                    <div class="flex-shrink-0 flex lg:flex-col gap-2">
                                        @can('capacites_techniques.read')
                                        <a href="{{ route('prestataires.capacites-techniques.show', [$prestataire->id_prestataire, $capacite->id_capacite_technique]) }}"
                                        class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg transition-colors" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('capacites_techniques.manage')
                                        <a href="{{ route('prestataires.capacites-techniques.edit', [$prestataire->id_prestataire, $capacite->id_capacite_technique]) }}"
                                        class="p-2 bg-orange-100 hover:bg-orange-200 text-orange-600 rounded-lg transition-colors" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('prestataires.capacites-techniques.destroy', [$prestataire->id_prestataire, $capacite->id_capacite_technique]) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette fiche ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                @endcanany

                            </div>

                            <!-- Date de création -->
                            <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400">
                                <i class="fas fa-clock mr-1"></i>
                                Créée le {{ $capacite->created_at->format('d/m/Y à H:i') }}
                                @if($capacite->updated_at != $capacite->created_at)
                                    • Modifiée le {{ $capacite->updated_at->format('d/m/Y à H:i') }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($capacites->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $capacites->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-cogs text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">Aucune capacité technique enregistrée</p>
                    @can('capacites_techniques.manage')
                        <p class="text-gray-400 text-sm mb-4">Commencez par ajouter les capacités techniques de ce prestataire</p>
                        <a href="{{ route('prestataires.capacites-techniques.create', $prestataire->id_prestataire) }}"
                        class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Ajouter une fiche
                        </a>
                    @endcan
                </div>
            @endif
        </div>

    </main>
@endsection
