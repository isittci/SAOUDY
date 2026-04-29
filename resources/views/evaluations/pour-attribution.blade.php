@extends('layouts.main')

@section('title', 'Évaluations - Attribution ' . $attribution->numero_attribution)

@section('breadcrumb')
    <a @can('attributions_lots.read') href="{{ route('attributions.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a  @can('attributions_lots.view-details') href="{{ route('attributions.show', $attribution->id_attribution) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ $attribution->numero_attribution }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium"> Évaluations {{ $attribution->numero_attribution }} </span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('evaluations_attributions.view-details')
                        <a href="{{ route('attributions.show', $attribution->id_attribution) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Évaluations par critère</h1>
                        <p class="text-gray-600 mt-1">
                            {{ $attribution->numero_attribution }} - Lot {{ $attribution->lot->numero ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                @php
                    $noteReference = 0;
                    foreach ($criteresDisponibles as $note) {
                        $noteReference += $note['critere']->note_reference_critere_evaluation;
                    }
                @endphp

                @can('evaluations_attributions.evaluate')
                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        @if (collect($statistiquesCriteres)->sum('note_reference') < 100)
                            <a href="{{ route('evaluations.create', $attribution->id_attribution) }}"
                                class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                                <i class="fas fa-plus text-sm"></i>
                                <span class="text-sm font-medium">Nouvelle évaluation</span>
                            </a>
                        @endif
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale - Liste des critères avec évaluations -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Résumé global -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>
                            Progression des évaluations
                        </h2>
                    </div>
                    <div class="p-6">

                        @php
                            $stats = collect($statistiquesCriteres);
                            $totalNoteReference = $stats->sum(fn ($s) => (float) $s['note_reference']);
                            $totalEvalue        = $stats->sum('total_evalue');
                            $criteresComplets   = $stats->where('est_complet', true)->count();
                            $pourcentageGlobal = $totalNoteReference > 0 ? ($totalEvalue / $totalNoteReference) * 100 : 0;
                        @endphp


                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-indigo-50 rounded-xl p-4 text-center">
                                <p class="text-xs text-indigo-600 uppercase font-semibold">Critères</p>
                                <p class="text-2xl font-bold text-indigo-700">{{ count($statistiquesCriteres) }}</p>
                            </div>
                            <div class="bg-green-50 rounded-xl p-4 text-center">
                                <p class="text-xs text-green-600 uppercase font-semibold">Complets</p>
                                <p class="text-2xl font-bold text-green-700">{{ $criteresComplets }}</p>
                            </div>
                            <div class="bg-orange-50 rounded-xl p-4 text-center">
                                <p class="text-xs text-orange-600 uppercase font-semibold">Évaluations</p>
                                <p class="text-2xl font-bold text-orange-700">{{ $evaluations->count() }}</p>
                            </div>
                            <div class="bg-purple-50 rounded-xl p-4 text-center">
                                <p class="text-xs text-purple-600 uppercase font-semibold">Progression</p>
                                <p class="text-2xl font-bold text-purple-700">{{ number_format($pourcentageGlobal, 2) }}%
                                </p>
                            </div>
                        </div>

                        <!-- Barre de progression globale -->
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 bg-gray-200 rounded-full h-4">
                                <div class="h-4 rounded-full transition-all duration-500 {{ $pourcentageGlobal >= 100 ? 'bg-green-500' : ($pourcentageGlobal >= 50 ? 'bg-yellow-500' : 'bg-orange-500') }}"
                                    style="width: {{ min($pourcentageGlobal, 100) }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">
                                {{ number_format($totalEvalue, 2) }} / {{ number_format($totalNoteReference, 2) }} pts
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Liste des critères avec leurs évaluations -->
                @foreach ($statistiquesCriteres as $critereId => $stat)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <!-- En-tête du critère -->
                        <div
                            class="px-6 py-4 {{ $stat['est_complet'] ? 'bg-gradient-to-r from-green-50 to-white' : 'bg-gradient-to-r from-orange-50 to-white' }} border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="px-3 py-1 text-sm font-bold {{ $stat['est_complet'] ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }} rounded-lg">
                                        {{ $stat['critere']->numero_critere_evaluation }}
                                    </span>
                                    <h3 class="font-bold text-gray-800">{{ $stat['critere']->libelle_critere_evaluation }}
                                    </h3>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if ($stat['est_complet'])
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i> Complet
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                            <i class="fas fa-clock mr-1"></i> En cours
                                        </span>
                                    @endif
                                    <span class="text-lg font-bold text-gray-700">
                                        {{ number_format($stat['total_evalue'], 2) }} /
                                        {{ number_format($stat['note_reference'], 2) }} pts
                                    </span>
                                </div>
                            </div>

                            <!-- Barre de progression du critère -->
                            <div class="mt-3 flex items-center space-x-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all {{ $stat['est_complet'] ? 'bg-green-500' : 'bg-orange-500' }}" style="width: {{ min($stat['pourcentage_complete'], 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium {{ $stat['est_complet'] ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ number_format($stat['pourcentage_complete'], 1) }}%
                                </span>
                            </div>
                        </div>

                        <!-- Liste des évaluations pour ce critère -->
                        <div class="divide-y divide-gray-200">
                            @forelse($stat['evaluations'] as $evaluation)
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div>
                                                <a @can('evaluations_attributions.view-details') href="{{ route('evaluations.show', $evaluation->id_evaluation) }}" @endcan
                                                    class="font-semibold text-indigo-600 hover:text-indigo-800">
                                                    {{ $evaluation->numero_evaluation }}
                                                </a>
                                                <p class="text-sm text-gray-500">
                                                    {{ $evaluation->date_evaluation ? $evaluation->date_evaluation->format('d/m/Y H:i') : 'Non définie' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $evaluation->statut_badge_class }}">
                                                <i class="fas fa-{{ $evaluation->statut_icon }} mr-1"></i>
                                                {{ $evaluation->statut_label }}
                                            </span>
                                            <span
                                                class="text-lg font-bold {{ $evaluation->pourcentage_final >= 70 ? 'text-green-600' : ($evaluation->pourcentage_final >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($evaluation->resultat_evaluation, 2) }} pts
                                            </span>
                                            @can('evaluations_attributions.evaluate')
                                                <div class="flex items-center space-x-1">
                                                    <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}"
                                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                        title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>

                                    {{-- ========== DÉBUT DU BLOC À COPIER (remplace lignes 187-207) ========== --}}

                                    <!-- Responsables -->
                                    <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-500">
                                        {{-- Responsable Technique --}}
                                        @if ($evaluation->hasRespoTechnique())
                                            <div class="responsable-badge-wrapper"
                                                data-popover-id="respo-tech-{{ $evaluation->id_evaluation }}">
                                                <div
                                                    class="flex items-center px-2.5 py-1.5 bg-blue-50 rounded-lg cursor-pointer hover:bg-blue-100 transition-colors">
                                                    <i class="fas fa-user-cog text-blue-500 mr-1.5"></i>
                                                    <span
                                                        class="font-medium text-blue-700">{{ $evaluation->respo_technique_evaluation['nom_complet'] ?? '' }}</span>
                                                    @if (
                                                        !empty($evaluation->respo_technique_evaluation['email']) ||
                                                            !empty($evaluation->respo_technique_evaluation['telephone']))
                                                        <i class="fas fa-info-circle text-blue-400 ml-1.5 text-[10px]"></i>
                                                    @endif
                                                </div>

                                                @if (
                                                    !empty($evaluation->respo_technique_evaluation['email']) ||
                                                        !empty($evaluation->respo_technique_evaluation['telephone']))
                                                    <div class="responsable-popover"
                                                        id="respo-tech-{{ $evaluation->id_evaluation }}">
                                                        <div
                                                            class="popover-content bg-white rounded-lg shadow-2xl border border-gray-200 p-3 min-w-[240px]">
                                                            <div
                                                                class="flex items-center mb-2 pb-2 border-b border-gray-100">
                                                                <div
                                                                    class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                                                                    <i class="fas fa-user-cog text-blue-600 text-sm"></i>
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p class="font-semibold text-gray-800 text-sm truncate">
                                                                        {{ $evaluation->respo_technique_evaluation['nom_complet'] ?? '' }}
                                                                    </p>
                                                                    <p
                                                                        class="text-[10px] text-blue-600 uppercase font-medium">
                                                                        Responsable technique</p>
                                                                </div>
                                                            </div>
                                                            <div class="space-y-2">
                                                                @if (!empty($evaluation->respo_technique_evaluation['email']))
                                                                    <a href="mailto:{{ $evaluation->respo_technique_evaluation['email'] }}"
                                                                        class="flex items-center text-gray-600 hover:text-blue-600 transition-colors text-xs group">
                                                                        <i
                                                                            class="fas fa-envelope w-5 text-gray-400 group-hover:text-blue-500 mr-2 flex-shrink-0"></i>
                                                                        <span
                                                                            class="truncate">{{ $evaluation->respo_technique_evaluation['email'] }}</span>
                                                                    </a>
                                                                @endif
                                                                @if (!empty($evaluation->respo_technique_evaluation['telephone']))
                                                                    <a href="tel:{{ $evaluation->respo_technique_evaluation['telephone'] }}"
                                                                        class="flex items-center text-gray-600 hover:text-blue-600 transition-colors text-xs group">
                                                                        <i
                                                                            class="fas fa-phone w-5 text-gray-400 group-hover:text-blue-500 mr-2 flex-shrink-0"></i>
                                                                        <span>{{ $evaluation->respo_technique_evaluation['telephone'] }}</span>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Superviseur --}}
                                        @if ($evaluation->hasSuperviseur())
                                            <div class="responsable-badge-wrapper"
                                                data-popover-id="superviseur-{{ $evaluation->id_evaluation }}">
                                                <div
                                                    class="flex items-center px-2.5 py-1.5 bg-purple-50 rounded-lg cursor-pointer hover:bg-purple-100 transition-colors">
                                                    <i class="fas fa-user-shield text-purple-500 mr-1.5"></i>
                                                    <span
                                                        class="font-medium text-purple-700">{{ $evaluation->superviseur_evaluation['nom_complet'] ?? '' }}</span>
                                                    @if (!empty($evaluation->superviseur_evaluation['email']) || !empty($evaluation->superviseur_evaluation['telephone']))
                                                        <i
                                                            class="fas fa-info-circle text-purple-400 ml-1.5 text-[10px]"></i>
                                                    @endif
                                                </div>

                                                @if (!empty($evaluation->superviseur_evaluation['email']) || !empty($evaluation->superviseur_evaluation['telephone']))
                                                    <div class="responsable-popover"
                                                        id="superviseur-{{ $evaluation->id_evaluation }}">
                                                        <div
                                                            class="popover-content bg-white rounded-lg shadow-2xl border border-gray-200 p-3 min-w-[240px]">
                                                            <div
                                                                class="flex items-center mb-2 pb-2 border-b border-gray-100">
                                                                <div
                                                                    class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                                                                    <i
                                                                        class="fas fa-user-shield text-purple-600 text-sm"></i>
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p
                                                                        class="font-semibold text-gray-800 text-sm truncate">
                                                                        {{ $evaluation->superviseur_evaluation['nom_complet'] ?? '' }}
                                                                    </p>
                                                                    <p
                                                                        class="text-[10px] text-purple-600 uppercase font-medium">
                                                                        Superviseur</p>
                                                                </div>
                                                            </div>
                                                            <div class="space-y-2">
                                                                @if (!empty($evaluation->superviseur_evaluation['email']))
                                                                    <a href="mailto:{{ $evaluation->superviseur_evaluation['email'] }}"
                                                                        class="flex items-center text-gray-600 hover:text-purple-600 transition-colors text-xs group">
                                                                        <i
                                                                            class="fas fa-envelope w-5 text-gray-400 group-hover:text-purple-500 mr-2 flex-shrink-0"></i>
                                                                        <span
                                                                            class="truncate">{{ $evaluation->superviseur_evaluation['email'] }}</span>
                                                                    </a>
                                                                @endif
                                                                @if (!empty($evaluation->superviseur_evaluation['telephone']))
                                                                    <a href="tel:{{ $evaluation->superviseur_evaluation['telephone'] }}"
                                                                        class="flex items-center text-gray-600 hover:text-purple-600 transition-colors text-xs group">
                                                                        <i
                                                                            class="fas fa-phone w-5 text-gray-400 group-hover:text-purple-500 mr-2 flex-shrink-0"></i>
                                                                        <span>{{ $evaluation->superviseur_evaluation['telephone'] }}</span>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Responsable du suivi-évaluation --}}
                                        @if ($evaluation->hasEvaluePar())
                                            <div class="responsable-badge-wrapper"
                                                data-popover-id="evalue-par-{{ $evaluation->id_evaluation }}">
                                                <div
                                                    class="flex items-center px-2.5 py-1.5 bg-green-50 rounded-lg cursor-pointer hover:bg-green-100 transition-colors">
                                                    <i class="fas fa-user-check text-green-500 mr-1.5"></i>
                                                    <span
                                                        class="font-medium text-green-700">{{ $evaluation->evalue_par['nom_complet'] ?? '' }}</span>
                                                    @if (!empty($evaluation->evalue_par['email']) || !empty($evaluation->evalue_par['telephone']))
                                                        <i
                                                            class="fas fa-info-circle text-green-400 ml-1.5 text-[10px]"></i>
                                                    @endif
                                                </div>

                                                @if (!empty($evaluation->evalue_par['email']) || !empty($evaluation->evalue_par['telephone']))
                                                    <div class="responsable-popover"
                                                        id="evalue-par-{{ $evaluation->id_evaluation }}">
                                                        <div
                                                            class="popover-content bg-white rounded-lg shadow-2xl border border-gray-200 p-3 min-w-[240px]">
                                                            <div
                                                                class="flex items-center mb-2 pb-2 border-b border-gray-100">
                                                                <div
                                                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                                                                    <i
                                                                        class="fas fa-user-check text-green-600 text-sm"></i>
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p
                                                                        class="font-semibold text-gray-800 text-sm truncate">
                                                                        {{ $evaluation->evalue_par['nom_complet'] ?? '' }}
                                                                    </p>
                                                                    <p
                                                                        class="text-[10px] text-green-600 uppercase font-medium">
                                                                        Suivi-évaluation</p>
                                                                </div>
                                                            </div>
                                                            <div class="space-y-2">
                                                                @if (!empty($evaluation->evalue_par['email']))
                                                                    <a href="mailto:{{ $evaluation->evalue_par['email'] }}"
                                                                        class="flex items-center text-gray-600 hover:text-green-600 transition-colors text-xs group">
                                                                        <i
                                                                            class="fas fa-envelope w-5 text-gray-400 group-hover:text-green-500 mr-2 flex-shrink-0"></i>
                                                                        <span
                                                                            class="truncate">{{ $evaluation->evalue_par['email'] }}</span>
                                                                    </a>
                                                                @endif
                                                                @if (!empty($evaluation->evalue_par['telephone']))
                                                                    <a href="tel:{{ $evaluation->evalue_par['telephone'] }}"
                                                                        class="flex items-center text-gray-600 hover:text-green-600 transition-colors text-xs group">
                                                                        <i class="fas fa-phone w-5 text-gray-400 group-hover:text-green-500 mr-2 flex-shrink-0"></i>
                                                                        <span>{{ $evaluation->evalue_par['telephone'] }}</span>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>


                                    @push('scripts')
                                        <style>
                                            /* Wrapper du badge */
                                            .responsable-badge-wrapper {
                                                position: relative;
                                                display: inline-block;
                                            }

                                            /* Popover caché par défaut */
                                            .responsable-popover {
                                                position: fixed;
                                                z-index: 99999;
                                                opacity: 0;
                                                visibility: hidden;
                                                transform: translateY(5px);
                                                transition: all 0.2s ease;
                                                pointer-events: none;
                                            }

                                            /* Popover visible */
                                            .responsable-popover.active {
                                                opacity: 1;
                                                visibility: visible;
                                                transform: translateY(0);
                                                pointer-events: auto;
                                            }

                                            /* Contenu du popover */
                                            .responsable-popover .popover-content {
                                                position: relative;
                                            }

                                            /* Flèche du popover */
                                            .responsable-popover .popover-content::before {
                                                content: '';
                                                position: absolute;
                                                top: -6px;
                                                left: 20px;
                                                width: 12px;
                                                height: 12px;
                                                background: white;
                                                border-left: 1px solid #e5e7eb;
                                                border-top: 1px solid #e5e7eb;
                                                transform: rotate(45deg);
                                            }
                                        </style>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const wrappers = document.querySelectorAll('.responsable-badge-wrapper');

                                                wrappers.forEach(wrapper => {
                                                    const popover = wrapper.querySelector('.responsable-popover');
                                                    if (!popover) return;

                                                    let hideTimeout;

                                                    // Afficher le popover
                                                    function showPopover() {
                                                        clearTimeout(hideTimeout);

                                                        // Fermer tous les autres popovers
                                                        document.querySelectorAll('.responsable-popover.active').forEach(p => {
                                                            p.classList.remove('active');
                                                        });

                                                        // Calculer la position
                                                        const rect = wrapper.getBoundingClientRect();
                                                        const popoverHeight = 150; // Estimation
                                                        const popoverWidth = 260;

                                                        // Position par défaut: en dessous
                                                        let top = rect.bottom + 8;
                                                        let left = rect.left;

                                                        // Vérifier si ça dépasse en bas
                                                        if (top + popoverHeight > window.innerHeight) {
                                                            top = rect.top - popoverHeight - 8;
                                                        }

                                                        // Vérifier si ça dépasse à droite
                                                        if (left + popoverWidth > window.innerWidth - 20) {
                                                            left = window.innerWidth - popoverWidth - 20;
                                                        }

                                                        // Vérifier si ça dépasse à gauche
                                                        if (left < 20) {
                                                            left = 20;
                                                        }

                                                        popover.style.top = top + 'px';
                                                        popover.style.left = left + 'px';
                                                        popover.classList.add('active');
                                                    }

                                                    // Cacher le popover
                                                    function hidePopover() {
                                                        hideTimeout = setTimeout(() => {
                                                            popover.classList.remove('active');
                                                        }, 150);
                                                    }

                                                    // Events sur le wrapper
                                                    wrapper.addEventListener('mouseenter', showPopover);
                                                    wrapper.addEventListener('mouseleave', hidePopover);

                                                    // Garder le popover ouvert quand on le survole
                                                    popover.addEventListener('mouseenter', () => {
                                                        clearTimeout(hideTimeout);
                                                    });

                                                    popover.addEventListener('mouseleave', () => {
                                                        popover.classList.remove('active');
                                                    });
                                                });
                                            });
                                        </script>
                                    @endpush
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500">
                                    <i class="fas fa-inbox text-2xl text-gray-300 mb-2"></i>
                                    <p>Aucune évaluation pour ce critère</p>
                                </div>
                            @endforelse
                        </div>

                        @can('evaluations_attributions.evaluate')
                        <!-- Action pour ajouter une évaluation -->
                        @if ($stat['peut_ajouter_evaluation'])
                            <div class="px-6 py-4 bg-gray-50 border-t">
                                <a href="{{ route('evaluations.create', ['attribution' => $attribution->id_attribution, 'critere_id' => $critereId]) }}"
                                    class="inline-flex items-center px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 font-medium rounded-lg transition-all">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter une évaluation
                                    <span class="ml-2 text-sm">(reste: {{ number_format($stat['reste_a_evaluer'], 2) }}
                                        pts)</span>
                                </a>
                            </div>
                        @endif
                        @endcan
                    </div>
                @endforeach

            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Informations attribution -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-contract text-orange-500 mr-2"></i>
                            Attribution
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Numéro</label>
                            <a href="{{ route('attributions.show', $attribution->id_attribution) }}"
                                class="text-orange-600 hover:text-orange-800 font-medium">
                                {{ $attribution->numero_attribution }}
                            </a>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Statut</label>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $attribution->statut_badge_class }}">
                                {{ $attribution->statut_label }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Date
                                d'attribution</label>
                            <p class="text-gray-900">
                                {{ $attribution->date_attribution ? $attribution->date_attribution->format('d/m/Y') : 'Non définie' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informations lot -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-box text-indigo-500 mr-2"></i>
                            Lot
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Numéro</label>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                {{ $attribution->lot->numero ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Libellé</label>
                            <p class="text-gray-900">{{ $attribution->lot->libelle ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Appel d'offres</label>
                            <p class="text-gray-900">{{ $attribution->lot->appelOffre->numero_appel_offre ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informations prestataire -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-green-500 mr-2"></i>
                            Prestataire
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nom du prestataire</label>
                            <p class="text-gray-900 font-medium">
                                {{ $attribution->prestataire->raison_sociale_prestataire ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Contact</label>
                            <p class="text-gray-600 text-sm">{{ $attribution->prestataire->email_prestataire ?? '' }}</p>
                            <p class="text-gray-600 text-sm">
                                {{ $attribution->prestataire->telephone_principal_prestataire ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Note d'information -->
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Règle de terminaison</p>
                            <p class="text-xs">
                                Une évaluation ne peut être terminée et validée que lorsque la somme des résultats
                                pour le critère atteint sa note de référence.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection
