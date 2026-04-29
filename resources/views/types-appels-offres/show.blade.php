@extends('layouts.main')
@section('title', 'Détails Type AO - ' . $typeAO->code_type_appel_offre)
@section('breadcrumb')
    <a href="{{ route('types-appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Types AO</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium"
        title="{{ $typeAO->libelle_type_appel_offre }}">{{ \Illuminate\Support\Str::limit($typeAO->libelle_type_appel_offre, 50) }}</span>
@endsection

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('types-appels-offres.index') }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-3">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $typeAO->code_type_appel_offre }}</h1>
                            @if ($typeAO->actif_type_appel_offre)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Actif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inactif
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-1">{{ $typeAO->libelle_type_appel_offre }}</p>
                    </div>
                </div>

                @canany(['type_appels_offres.update', 'type_appels_offres.toggle-status', 'type_appels_offres.delete'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        @can('type_appels_offres.update')
                            @if ($typeAO->actif_type_appel_offre)
                                <button onclick='openEditModal(@json($typeAO))'
                                    class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-edit text-sm"></i>
                                    <span class="text-sm font-medium">Modifier</span>
                                </button>
                            @endif
                        @endcan

                        @can('type_appels_offres.toggle-status')
                            <button
                                onclick="toggleStatus('{{ $typeAO->id_type_appel_offre }}', {{ $typeAO->actif_type_appel_offre ? 'true' : 'false' }})"
                                class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-power-off text-sm"></i>
                                <span
                                    class="text-sm font-medium">{{ $typeAO->actif_type_appel_offre ? 'Désactiver' : 'Activer' }}</span>
                            </button>
                        @endcan

                        @can('type_appels_offres.delete')
                            <button
                                onclick="confirmDelete('{{ $typeAO->id_type_appel_offre }}', '{{ $typeAO->libelle_type_appel_offre }}', {{ $typeAO->appel_offres_count }})"
                                class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-trash text-sm"></i>
                                <span class="text-sm font-medium">Supprimer</span>
                            </button>
                        @endcan
                    </div>
                @endcanany
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informations principales -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                            Informations générales
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Code et Libellé -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Code</label>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                        {{ $typeAO->code_type_appel_offre }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Statut</label>
                                @if ($typeAO->actif_type_appel_offre)
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i> Type actif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-800">
                                        <i class="fas fa-times-circle mr-2"></i> Type inactif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Libellé -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Libellé</label>
                            <p class="text-gray-900 font-medium">{{ $typeAO->libelle_type_appel_offre }}</p>
                        </div>

                        <!-- Description -->
                        @if ($typeAO->description_critere_type_appel_offre)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Description</label>
                                <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg">
                                    {{ $typeAO->description_critere_type_appel_offre }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Intervalle de valeur -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-coins text-blue-500 mr-2"></i>
                            Intervalle de valeur
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Valeur minimale -->
                            <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Valeur minimale</span>
                                    <i class="fas fa-arrow-down text-blue-500"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ number_format(floor($typeAO->valeur_minimuim_type_appel_offre), 0, ',', ' ') }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">FCFA</p>
                            </div>

                            <!-- Valeur maximale -->
                            <div class="bg-gradient-to-br from-green-50 to-white p-5 rounded-xl border border-green-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Valeur maximale</span>
                                    <i class="fas fa-arrow-up text-green-500"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ number_format(floor($typeAO->valeur_maximuim_type_appel_offre), 0, ',', ' ') }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">FCFA</p>
                            </div>
                        </div>

                        <!-- Visualisation de l'intervalle -->
                        <div class="mt-6">
                            <div class="relative pt-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-700">Plage de valeur applicable</span>
                                </div>
                                <div class="overflow-hidden h-3 text-xs flex rounded-full bg-gray-200">
                                    <div
                                        class="w-full bg-gradient-to-r from-blue-500 via-purple-500 to-green-500 rounded-full">
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-2">
                                    <span>{{ number_format(floor($typeAO->valeur_minimuim_type_appel_offre), 0, ',', ' ') }}
                                        FCFA</span>
                                    <span>{{ number_format(floor($typeAO->valeur_maximuim_type_appel_offre), 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appels d'offres associés -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-bullhorn text-purple-500 mr-2"></i>
                                Appels d'offres associés
                                <span
                                    class="ml-2 px-2.5 py-1 bg-purple-100 text-purple-800 text-sm font-semibold rounded-full">
                                    {{ $typeAO->appel_offres_count }}
                                </span>
                            </h2>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($typeAO->appelOffres->count() > 0)
                            <div class="space-y-3">
                                @foreach ($typeAO->appelOffres as $ao)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-orange-100 text-orange-700">
                                                    {{ $ao->numero_appel_offre }}
                                                </span>
                                                <p class="font-medium text-gray-900">{{ $ao->libelle_critere_appel_offre }}
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                <span><i
                                                        class="fas fa-coins mr-1"></i>{{ number_format(floor($ao->montant_global_appel_offre), 0, ',', ' ') }}
                                                    FCFA</span>
                                                <span><i
                                                        class="fas fa-calendar mr-1"></i>{{ $ao->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        @can('appels_offres.view-details')
                                            <a href="{{ route('appels-offres.show', $ao->id_appel_offre) }}"
                                                class="ml-4 p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-all">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @endcan
                                    </div>
                                @endforeach
                            </div>

                            @can('appels_offres.read')
                                @if ($typeAO->appel_offres_count > 10)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('appels-offres.index', ['type_appel_offre_id' => $typeAO->id_type_appel_offre]) }}"
                                            class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                            Voir tous les appels d'offres ({{ $typeAO->appel_offres_count }}) →
                                        </a>
                                    </div>
                                @endif
                            @endcan
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 font-medium">Aucun appel d'offres pour ce type</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Statistiques -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-orange-500 mr-2"></i>
                        Statistiques
                    </h3>

                    <div class="space-y-4">
                        <!-- Total AO -->
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-transparent rounded-lg border-l-4 border-blue-500">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Appels d'offres</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $typeAO->appel_offres_count }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bullhorn text-blue-600"></i>
                            </div>
                        </div>

                        <!-- Montant moyen -->
                        @if ($typeAO->appel_offres_count > 0)
                            <div
                                class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-transparent rounded-lg border-l-4 border-green-500">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Montant moyen</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ number_format(floor($typeAO->appelOffres->avg('montant_global_appel_offre')), 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-gray-500">FCFA</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-coins text-green-600"></i>
                                </div>
                            </div>
                        @endif

                        <!-- Plage utilisée -->
                        <div
                            class="p-4 bg-gradient-to-r from-purple-50 to-transparent rounded-lg border-l-4 border-purple-500">
                            <p class="text-sm text-gray-600 font-medium mb-2">Plage de valeur</p>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold">Min:</span>
                                    {{ number_format(floor($typeAO->valeur_minimuim_type_appel_offre), 0, ',', ' ') }} FCFA
                                </p>
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold">Max:</span>
                                    {{ number_format(floor($typeAO->valeur_maximuim_type_appel_offre), 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations système -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cog text-gray-500 mr-2"></i>
                        Informations système
                    </h3>

                    <div class="space-y-4 text-sm">
                        <!-- Enregistré par -->
                        @if ($typeAO->creator)
                            <div>
                                <p class="text-gray-600 font-medium mb-1">Enregistré par</p>
                                <p class="text-gray-900">{{ $typeAO->creator->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $typeAO->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif

                        <!-- Modifié par -->
                        @if ($typeAO->updater && $typeAO->updated_at != $typeAO->created_at)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-gray-600 font-medium mb-1">Dernière modification</p>
                                <p class="text-gray-900">{{ $typeAO->updater->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $typeAO->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        @endif


                    </div>
                </div>

                @can('appels_offres.create')
                    <!-- Actions rapides -->
                    <div class="bg-gradient-to-br from-orange-50 to-white rounded-2xl shadow-lg p-6 border border-orange-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-bolt text-orange-500 mr-2"></i>
                            Actions rapides
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('appels-offres.create') }}?type={{ $typeAO->id_type_appel_offre }}"
                                class="w-full flex items-center space-x-3 p-3 bg-white hover:bg-orange-50 border border-orange-200 rounded-lg transition-all duration-200 group">
                                <div
                                    class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                                    <i class="fas fa-plus text-orange-600"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Créer un appel d'offres de ce type</span>
                            </a>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </main>

    <!-- Modal Édition (réutiliser le même que dans index) -->
    <div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full transform transition-all">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Modifier Type d'Appel d'Offres</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Form -->
                <form id="typeForm" method="POST"
                    action="{{ route('types-appels-offres.update', $typeAO->id_type_appel_offre) }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <!-- Libellé -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Libellé <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="libelle_type_appel_offre" id="libelle" required
                                maxlength="160"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                            <div id="error_libelle" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>

                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code_type_appel_offre" id="code" required maxlength="10"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent uppercase">
                            <div id="error_code" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>

                        <!-- Valeurs -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Valeur minimale (FCFA) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="valeur_minimuim_type_appel_offre" id="valeur_min" required
                                    min="0" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                <div id="error_valeur_min" class="hidden text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Valeur maximale (FCFA) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="valeur_maximuim_type_appel_offre" id="valeur_max" required
                                    min="0" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                <div id="error_valeur_max" class="hidden text-red-500 text-sm mt-1"></div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description_critere_type_appel_offre" id="description" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"></textarea>
                        </div>

                        <!-- Statut -->
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="actif_type_appel_offre" id="actif" value="1"
                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <label for="actif" class="text-sm font-medium text-gray-700">Type actif</label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p id="deleteMessage" class="text-sm text-gray-600 mb-6"></p>

                    <div class="flex items-center justify-center space-x-3">
                        <button onclick="closeDeleteModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        <button onclick="executeDelete()"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let deleteTypeId = null;

            // Ouvrir modal édition
            function openEditModal(type) {
                document.getElementById('libelle').value = type.libelle_type_appel_offre;
                document.getElementById('code').value = type.code_type_appel_offre;
                document.getElementById('valeur_min').value = type.valeur_minimuim_type_appel_offre;
                document.getElementById('valeur_max').value = type.valeur_maximuim_type_appel_offre;
                document.getElementById('description').value = type.description_critere_type_appel_offre || '';
                document.getElementById('actif').checked = type.actif_type_appel_offre;

                clearErrors();
                document.getElementById('formModal').classList.remove('hidden');
            }

            // Fermer modal
            function closeModal() {
                document.getElementById('formModal').classList.add('hidden');
                clearErrors();
            }

            // Effacer les erreurs
            function clearErrors() {
                const errorDivs = document.querySelectorAll('[id^="error_"]');
                errorDivs.forEach(div => {
                    div.classList.add('hidden');
                    div.textContent = '';
                });
            }

            // Toggle statut
            function toggleStatus(id, isActive) {
                const action = isActive ? 'désactiver' : 'activer';
                if (confirm(`Voulez-vous vraiment ${action} ce type d'appel d'offres ?`)) {
                    fetch("{{ route('types-appels-offres.toggle-status', ':id') }}".replace(':id', id), {
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

            // Confirmer suppression
            function confirmDelete(id, libelle, nbAO) {
                deleteTypeId = id;
                let message = `Êtes-vous sûr de vouloir supprimer le type "${libelle}" ?`;

                if (nbAO > 0) {
                    message = `Impossible de supprimer ce type car il est utilisé dans ${nbAO} appel(s) d'offres.`;
                    document.getElementById('deleteMessage').innerHTML = `<strong class="text-red-600">${message}</strong>`;
                    document.querySelector('#deleteModal button[onclick="executeDelete()"]').classList.add('hidden');
                } else {
                    document.getElementById('deleteMessage').textContent = message;
                    document.querySelector('#deleteModal button[onclick="executeDelete()"]').classList.remove('hidden');
                }

                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Exécuter suppression
            function executeDelete() {
                if (!deleteTypeId) return;

                fetch(`/types-appels-offres/${deleteTypeId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('types-appels-offres.index') }}';
                        } else {
                            alert(data.message || 'Une erreur est survenue');
                            closeDeleteModal();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                        closeDeleteModal();
                    });
            }

            // Fermer modal suppression
            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
                deleteTypeId = null;
            }



            // Gestion du formulaire
            document.getElementById('typeForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';

                clearErrors();

                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    const errorDiv = document.getElementById(
                                        `error_${key.replace('_type_appel_offre', '').replace('_critere', '')}`
                                    );
                                    if (errorDiv) {
                                        errorDiv.textContent = data.errors[key][0];
                                        errorDiv.classList.remove('hidden');
                                    }
                                });
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });

            // Fermer modales avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    closeDeleteModal();
                }
            });

            // Forcer majuscules pour le code
            document.getElementById('code').addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
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
@endsection
