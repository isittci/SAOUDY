@extends('layouts.main')
@section('title', 'Attributions de Lots')
@section('breadcrumb', 'Attributions')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-handshake text-orange-500"></i>
                        <span>Attributions de Lots</span>
                    </h1>
                    <button onclick="window.location.href='{{ route('attributions.create') }}'"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau</span>
                    </button>
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher par numéro, lot, prestataire..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>

                    <!-- Filtre statut -->
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="0" {{ request('statut') === '0' ? 'selected' : '' }}>En attente</option>
                        <option value="1" {{ request('statut') === '1' ? 'selected' : '' }}>Attribué</option>
                        <option value="2" {{ request('statut') === '2' ? 'selected' : '' }}>Suspendu</option>
                        <option value="3" {{ request('statut') === '3' ? 'selected' : '' }}>Retiré</option>
                        <option value="4" {{ request('statut') === '4' ? 'selected' : '' }}>Terminé</option>
                        <option value="5" {{ request('statut') === '5' ? 'selected' : '' }}>Annulé</option>
                    </select>

                    <!-- Filtre état actif -->
                    <select id="etatFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Actifs & Historique</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Actives uniquement</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Historique</option>
                    </select>

                    <!-- Filtre prestataire -->
                    <select id="prestataireFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les prestataires</option>
                        @foreach($prestataires as $prestataire)
                            <option value="{{ $prestataire->id_prestataire }}" {{ request('prestataire_id') == $prestataire->id_prestataire ? 'selected' : '' }}>
                                {{ Str::limit($prestataire->raison_sociale_prestataire, 30) }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Bouton Dashboard -->
                    <button onclick="window.location.href='{{ route('attributions.dashboard') }}'"
                        class="hidden md:flex px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 items-center space-x-2 shadow-sm">
                        <i class="fas fa-chart-pie text-sm"></i>
                        <span class="text-sm font-medium">Dashboard</span>
                    </button>

                    <!-- Bouton créer (desktop) -->
                    <button onclick="window.location.href='{{ route('attributions.create') }}'"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Attribuer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages de succès/erreur -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
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

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-gray-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($statistiques['total']) }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="fas fa-list text-gray-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">En cours</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($statistiques['en_cours']) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-play text-green-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Suspendues</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($statistiques['suspendues']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-pause text-yellow-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Terminées</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($statistiques['terminees']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-check-double text-blue-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">En retard</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($statistiques['en_retard']) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Actives</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($statistiques['actives']) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-bolt text-purple-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- En-tête du tableau -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Liste des attributions (<span id="totalCount">{{ $attributions->total() }}</span>)
                    </h2>
                    <div class="flex items-center space-x-2">
                        <button onclick="refreshTable()"
                            class="px-3 py-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all duration-200">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table responsive -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                N° Attribution</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Lot</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Prestataire</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Version</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Avancement</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Dates</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($attributions as $attribution)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 {{ !$attribution->is_active ? 'bg-gray-50 opacity-75' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        @if(!$attribution->is_active)
                                            <i class="fas fa-history text-gray-400 text-xs" title="Historique"></i>
                                        @endif
                                        <a href="{{ route('attributions.show', $attribution->id_attribution) }}"
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors">
                                            {{ $attribution->numero_attribution }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $attribution->lot->numero ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-1">{{ Str::limit($attribution->lot->libelle ?? '', 35) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ Str::limit($attribution->prestataire->raison_sociale_prestataire ?? 'N/A', 25) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $attribution->prestataire->ville_prestataire ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center space-y-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $attribution->statut_badge_class }}">
                                            @switch($attribution->statut_attribution)
                                                @case(0)
                                                    <i class="fas fa-clock mr-1"></i>
                                                    @break
                                                @case(1)
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    @break
                                                @case(2)
                                                    <i class="fas fa-pause-circle mr-1"></i>
                                                    @break
                                                @case(3)
                                                    <i class="fas fa-ban mr-1"></i>
                                                    @break
                                                @case(4)
                                                    <i class="fas fa-check-double mr-1"></i>
                                                    @break
                                                @case(5)
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                    @break
                                            @endswitch
                                            {{ $attribution->statut_label }}
                                        </span>
                                        @if($attribution->estEnRetard() && $attribution->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $attribution->jours_retard_actuels }}j retard
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        v{{ $attribution->version_attribution }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 w-20">
                                            <div class="h-2 rounded-full {{ $attribution->pourcentage_avancement >= 100 ? 'bg-green-500' : 'bg-orange-500' }}"
                                                 style="width: {{ min($attribution->pourcentage_avancement, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">{{ number_format($attribution->pourcentage_avancement, 0) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-xs text-gray-700 space-y-1">
                                        @if($attribution->date_debut_prevue)
                                            <div><span class="font-medium">Début:</span> {{ $attribution->date_debut_prevue->format('d/m/Y') }}</div>
                                        @endif
                                        @if($attribution->date_fin_prevue)
                                            <div class="{{ $attribution->estEnRetard() ? 'text-red-600 font-semibold' : '' }}">
                                                <span class="font-medium">Fin:</span> {{ $attribution->date_fin_prevue->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Voir détails -->
                                        <button onclick="window.location.href='{{ route('attributions.show', $attribution->id_attribution) }}'"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                            title="Voir détails">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>

                                        @if($attribution->is_active)
                                            <!-- Actions selon le statut -->
                                            @if($attribution->peutEtreSuspendue())
                                                <button onclick="openSuspendreModal('{{ $attribution->id_attribution }}')"
                                                    class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200"
                                                    title="Suspendre">
                                                    <i class="fas fa-pause text-sm"></i>
                                                </button>
                                            @endif

                                            @if($attribution->peutEtreReprise())
                                                <button onclick="reprendre('{{ $attribution->id_attribution }}')"
                                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200"
                                                    title="Reprendre">
                                                    <i class="fas fa-play text-sm"></i>
                                                </button>
                                            @endif

                                            @if($attribution->peutEtreRetiree())
                                                <button onclick="openRetirerModal('{{ $attribution->id_attribution }}')"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                                                    title="Retirer">
                                                    <i class="fas fa-ban text-sm"></i>
                                                </button>
                                            @endif

                                            @if($attribution->statut_attribution === 1)
                                                <button onclick="openAvancementModal('{{ $attribution->id_attribution }}', {{ $attribution->pourcentage_avancement }})"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                                    title="Avancement">
                                                    <i class="fas fa-tasks text-sm"></i>
                                                </button>
                                            @endif
                                        @else
                                            <!-- Réattribuer depuis l'historique -->
                                            <button onclick="window.location.href='{{ route('attributions.reattribuer.form', $attribution->id_attribution) }}'"
                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                                title="Réattribuer ce lot">
                                                <i class="fas fa-redo text-sm"></i>
                                            </button>
                                        @endif

                                        <!-- Menu Actions -->
                                        <div class="relative">
                                            <button onclick="toggleMenu('{{ $attribution->id_attribution }}')"
                                                class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-all duration-200"
                                                title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v text-sm"></i>
                                            </button>
                                            <div id="menu-{{ $attribution->id_attribution }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                <div class="py-1">
                                                    <a href="{{ route('attributions.historique.lot', $attribution->lot_id) }}"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-history text-purple-500 mr-2"></i>
                                                        Historique du lot
                                                    </a>
                                                    <a href="{{ route('attributions.historique.prestataire', $attribution->prestataire_id) }}"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-user-clock text-blue-500 mr-2"></i>
                                                        Historique prestataire
                                                    </a>
                                                    @if($attribution->is_active && $attribution->peutEtreTerminee())
                                                        <button onclick="terminer('{{ $attribution->id_attribution }}')"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                            <i class="fas fa-check-double text-green-500 mr-2"></i>
                                                            Terminer
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <i class="fas fa-inbox text-gray-300 text-5xl"></i>
                                        <p class="text-gray-500 font-medium">Aucune attribution trouvée</p>
                                        <button onclick="window.location.href='{{ route('attributions.create') }}'"
                                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all duration-200">
                                            Créer une attribution
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($attributions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $attributions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Suspendre -->
    <div id="suspendreModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeSuspendreModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-pause-circle text-yellow-500 mr-2"></i>
                        Suspendre l'attribution
                    </h3>
                </div>
                <form id="suspendreForm" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif de suspension *</label>
                            <textarea name="motif_suspension" rows="3" required minlength="10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                placeholder="Décrivez la raison de la suspension..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de reprise prévue</label>
                            <input type="date" name="date_reprise_prevue" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeSuspendreModal()"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-md">
                            Suspendre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Retirer -->
    <div id="retirerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRetirerModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-ban text-red-500 mr-2"></i>
                        Retirer l'attribution
                    </h3>
                </div>
                <form id="retirerForm" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Type de retrait *</label>
                            <select name="type_retrait" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent">
                                <option value="">Sélectionnez...</option>
                                <option value="volontaire">Retrait volontaire</option>
                                <option value="force">Retrait forcé</option>
                                <option value="resiliation">Résiliation</option>
                                <option value="abandon">Abandon</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif de retrait *</label>
                            <textarea name="motif_retrait" rows="3" required minlength="10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent"
                                placeholder="Décrivez la raison du retrait..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeRetirerModal()"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all shadow-md">
                            Retirer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Avancement -->
    <div id="avancementModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAvancementModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tasks text-orange-500 mr-2"></i>
                        Mettre à jour l'avancement
                    </h3>
                </div>
                <form id="avancementForm" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pourcentage d'avancement *</label>
                            <div class="flex items-center space-x-4">
                                <input type="range" name="pourcentage_avancement" id="avancementRange" min="0" max="100" value="0"
                                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                    oninput="document.getElementById('avancementValue').value = this.value">
                                <input type="number" id="avancementValue" name="pourcentage_avancement_display" min="0" max="100" value="0"
                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center font-semibold"
                                    oninput="document.getElementById('avancementRange').value = this.value">
                                <span class="text-gray-500 font-medium">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Observations</label>
                            <textarea name="observations" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                placeholder="Notes sur l'avancement..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeAvancementModal()"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all shadow-md">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Toggle menu dropdown
        function toggleMenu(id) {
            const menu = document.getElementById('menu-' + id);
            document.querySelectorAll('[id^="menu-"]').forEach(m => {
                if (m.id !== 'menu-' + id) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
        }

        // Fermer menus au clic ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[id^="menu-"]') && !e.target.closest('button')) {
                document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
            }
        });

        // Modal Suspendre
        function openSuspendreModal(id) {
            document.getElementById('suspendreForm').action = `/attributions/${id}/suspendre`;
            document.getElementById('suspendreModal').classList.remove('hidden');
        }

        function closeSuspendreModal() {
            document.getElementById('suspendreModal').classList.add('hidden');
            document.getElementById('suspendreForm').reset();
        }

        // Modal Retirer
        function openRetirerModal(id) {
            document.getElementById('retirerForm').action = `/attributions/${id}/retirer`;
            document.getElementById('retirerModal').classList.remove('hidden');
        }

        function closeRetirerModal() {
            document.getElementById('retirerModal').classList.add('hidden');
            document.getElementById('retirerForm').reset();
        }

        // Modal Avancement
        function openAvancementModal(id, currentValue) {
            document.getElementById('avancementForm').action = `/attributions/${id}/avancement`;
            document.getElementById('avancementRange').value = currentValue;
            document.getElementById('avancementValue').value = currentValue;
            document.getElementById('avancementModal').classList.remove('hidden');
        }

        function closeAvancementModal() {
            document.getElementById('avancementModal').classList.add('hidden');
            document.getElementById('avancementForm').reset();
        }

        // Reprendre
        function reprendre(id) {
            if (confirm('Confirmer la reprise de cette attribution ?')) {
                fetch(`/attributions/${id}/reprendre`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                });
            }
        }

        // Terminer
        function terminer(id) {
            if (confirm('Confirmer la terminaison de cette attribution ?')) {
                fetch(`/attributions/${id}/terminer`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                });
            }
        }

        // Rafraîchir le tableau
        function refreshTable() {
            location.reload();
        }

        // Recherche en temps réel
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });

        // Filtres
        document.getElementById('statutFilter').addEventListener('change', applyFilters);
        document.getElementById('etatFilter').addEventListener('change', applyFilters);
        document.getElementById('prestataireFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const statut = document.getElementById('statutFilter').value;
            const etat = document.getElementById('etatFilter').value;
            const prestataire = document.getElementById('prestataireFilter').value;

            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (statut !== '') params.append('statut', statut);
            if (etat !== '') params.append('is_active', etat);
            if (prestataire) params.append('prestataire_id', prestataire);

            window.location.href = `?${params.toString()}`;
        }

        // Fermer modals avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuspendreModal();
                closeRetirerModal();
                closeAvancementModal();
                document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
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

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Custom range slider */
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f97316;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f97316;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>
@endpush
