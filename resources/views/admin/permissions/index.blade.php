@extends('layouts.main')
@section('title', 'Gestion des Permissions')
@section('breadcrumb', 'Permissions')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-key text-orange-500"></i>
                        <span>Gestion des Permissions</span>
                    </h1>
                    @can('role_permissions.create')
                    <button onclick="openCreateModal()"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau</span>
                    </button>
                    @endcan
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>

                    <!-- Filtre catégorie -->
                    <select id="categoryFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Toutes catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filtre action -->
                    <select id="actionFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Toutes actions</option>
                        @foreach($ACTIONS as $key => $action)
                            <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>

                    @can('role_permissions.create')
                    <!-- Bouton générer CRUD -->
                    <button onclick="openGenerateCrudModal()"
                        class="px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm font-medium">
                        <i class="fas fa-magic text-sm"></i>
                        <span class="text-sm hidden sm:inline">CRUD</span>
                    </button>

                    <!-- Bouton créer (desktop) -->
                    <button onclick="openCreateModal()"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Créer</span>
                    </button>
                    @endcan
                </div>
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

        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $permissions->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-key text-orange-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Actives</p>
                        <p class="text-2xl font-bold text-green-600">{{ $activeCount ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Système</p>
                        <p class="text-2xl font-bold text-red-600">{{ $systemCount ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-red-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Catégories</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $categoryCount ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-folder text-purple-500 text-xl"></i>
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
                        Liste des permissions
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
                                Permission</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ressource / Action</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Catégorie</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Rôles</th>
                            @canany(['role_permissions.update', 'role_permissions.delete'])
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($permissions as $permission)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                            @switch($permission->action)
                                                @case('create') bg-green-100 @break
                                                @case('read') bg-blue-100 @break
                                                @case('update') bg-orange-100 @break
                                                @case('delete') bg-red-100 @break
                                                @default bg-purple-100
                                            @endswitch">
                                            <i class="fas
                                                @switch($permission->action)
                                                    @case('create') fa-plus text-green-500 @break
                                                    @case('read') fa-eye text-blue-500 @break
                                                    @case('update') fa-edit text-orange-500 @break
                                                    @case('delete') fa-trash text-red-500 @break
                                                    @case('export') fa-file-export text-purple-500 @break
                                                    @case('import') fa-file-import text-indigo-500 @break
                                                    @case('validate') fa-check-circle text-green-600 @break
                                                    @case('reject') fa-times-circle text-red-600 @break
                                                    @case('restore') fa-undo text-teal-500 @break
                                                    @case('manage') fa-cog text-gray-600 @break
                                                    @default fa-key text-purple-500
                                                @endswitch"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $permission->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                        {{ $permission->resource ?? '-' }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                                        @switch($permission->action)
                                            @case('create') bg-green-100 text-green-800 @break
                                            @case('read') bg-blue-100 text-blue-800 @break
                                            @case('update') bg-orange-100 text-orange-800 @break
                                            @case('delete') bg-red-100 text-red-800 @break
                                            @default bg-purple-100 text-purple-800
                                        @endswitch">
                                        {{ $permission->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        <i class="fas fa-folder mr-1"></i>
                                        {{ $permission->category ?? 'Non catégorisé' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($permission->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                        <i class="fas fa-user-tag mr-1"></i>
                                        {{ $permission->roles_count ?? $permission->roles->count() }}
                                    </span>
                                </td>
                                @canany(['role_permissions.update', 'role_permissions.delete'])
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            @can('role_permissions.view-details')
                                            <button onclick="openViewModal('{{ $permission->id }}')"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                                title="Voir">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>
                                            @endcan

                                            @can('role_permissions.update')
                                            @if(!$permission->is_system || auth()->user()->isSuperAdmin())
                                                <button onclick="openEditModal('{{ $permission->id }}')"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                                    title="Modifier">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>
                                            @endif
                                            @endcan
                                            @can('role_permissions.update')
                                            @if($permission->is_active)
                                                <form action="#" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200"
                                                        title="Désactiver">
                                                        <i class="fas fa-toggle-on text-sm"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.permissions.activate', $permission) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-all duration-200"
                                                        title="Activer">
                                                        <i class="fas fa-toggle-off text-sm"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @endcan
                                            @can('role_permissions.delete')
                                            @if(!$permission->is_system)
                                                <button onclick="confirmDelete('{{ $permission->id }}', '{{ $permission->name }}')"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-key text-gray-300 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg">Aucune permission trouvée</p>
                                        @can('role_permissions.create')
                                            <button onclick="openCreateModal()"
                                                class="mt-4 px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium">
                                                <i class="fas fa-plus mr-2"></i>Créer une permission
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($permissions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $permissions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Création/Modification -->
    <div id="permissionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePermissionModal()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl transform transition-all sm:max-w-lg sm:w-full mx-auto">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white" id="modalTitle">Nouvelle Permission</h3>
                        <button type="button" onclick="closePermissionModal()" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="permissionForm" method="POST" action="{{ route('admin.permissions.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="px-6 py-4 space-y-4 max-h-96 overflow-y-auto">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                placeholder="Ex: Créer utilisateurs">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Ressource <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="resource" id="resource" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="Ex: users">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Action <span class="text-red-500">*</span>
                                </label>
                                <select name="action" id="action" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                                    <option value="">Sélectionner</option>
                                    @foreach($ACTIONS as $key => $action)
                                        <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                placeholder="Description de la permission..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <input type="text" name="category" id="category"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                placeholder="Ex: Administration">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Permission active</label>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" onclick="closePermissionModal()"
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        @can('role_permissions.create')
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md">
                            <i class="fas fa-save mr-2"></i>
                            <span id="submitBtnText">Créer</span>
                        </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Visualisation -->
    <div id="viewModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeViewModal()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl transform transition-all sm:max-w-lg sm:w-full mx-auto">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Détails de la permission</h3>
                        <button type="button" onclick="closeViewModal()" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-key text-orange-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xl font-semibold text-gray-800" id="viewName"></h4>
                            <p class="text-gray-500" id="viewSlug"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between py-3 border-b border-gray-100">
                            <span class="text-gray-600">Ressource</span>
                            <span class="font-medium text-gray-800" id="viewResource">-</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-100">
                            <span class="text-gray-600">Action</span>
                            <span class="font-medium text-gray-800" id="viewAction">-</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-100">
                            <span class="text-gray-600">Catégorie</span>
                            <span class="font-medium text-gray-800" id="viewCategory">-</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-100">
                            <span class="text-gray-600">Statut</span>
                            <span id="viewStatut"></span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-gray-600">Type</span>
                            <span id="viewType"></span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex justify-end">
                    <button type="button" onclick="closeViewModal()"
                        class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Générer CRUD -->
    <div id="generateCrudModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeGenerateCrudModal()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl transform transition-all sm:max-w-md sm:w-full mx-auto">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-purple-500 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            <i class="fas fa-magic mr-2"></i>Générer permissions CRUD
                        </h3>
                        <button type="button" onclick="closeGenerateCrudModal()" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('admin.permissions.generate-crud') }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 space-y-4">
                        <p class="text-sm text-gray-600">
                            Cette action va générer automatiquement toutes les permissions CRUD pour une ressource donnée.
                        </p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nom de la ressource <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="resource" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                                placeholder="Ex: products, orders, invoices...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <input type="text" name="category"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                                placeholder="Ex: Gestion Produits">
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" onclick="closeGenerateCrudModal()"
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200 font-medium">
                            <i class="fas fa-magic mr-2"></i>Générer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl transform transition-all sm:max-w-md sm:w-full mx-auto">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Confirmer la suppression</h3>
                    <p class="text-gray-600 mb-6" id="deleteMessage"></p>
                    <div class="flex justify-center space-x-3">
                        <button onclick="closeDeleteModal()"
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        <button onclick="executeDelete()"
                            class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 font-medium">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let deletePermissionId = null;
            let editPermissionId = null;
            const permissionsData = @json($permissions->items());

            function openCreateModal() {
                editPermissionId = null;
                document.getElementById('modalTitle').textContent = 'Nouvelle Permission';
                document.getElementById('submitBtnText').textContent = 'Créer';
                document.getElementById('permissionForm').action = '{{ route("admin.permissions.store") }}';
                document.getElementById('formMethod').value = 'POST';
                resetForm();
                document.getElementById('permissionModal').classList.remove('hidden');
            }

            function openEditModal(permissionId) {
                const permission = permissionsData.find(p => p.id === permissionId);
                if (!permission) return;

                editPermissionId = permissionId;
                document.getElementById('modalTitle').textContent = 'Modifier la Permission';
                document.getElementById('submitBtnText').textContent = 'Mettre à jour';
                document.getElementById('permissionForm').action = "{{ route('admin.permissions.update', ':permission') }}".replace(':permission', permission.id);
                document.getElementById('formMethod').value = 'PUT';

                document.getElementById('name').value = permission.name || '';
                document.getElementById('resource').value = permission.resource || '';
                document.getElementById('action').value = permission.action || '';
                document.getElementById('description').value = permission.description || '';
                document.getElementById('category').value = permission.category || '';
                document.getElementById('is_active').checked = permission.is_active;

                document.getElementById('permissionModal').classList.remove('hidden');
            }

            function openViewModal(permissionId) {
                const permission = permissionsData.find(p => p.id === permissionId);
                if (!permission) return;

                document.getElementById('viewName').textContent = permission.name || '-';
                document.getElementById('viewSlug').textContent = permission.slug || '-';
                document.getElementById('viewResource').textContent = permission.resource || '-';
                document.getElementById('viewAction').textContent = permission.action || '-';
                document.getElementById('viewCategory').textContent = permission.category || 'Non catégorisé';
                document.getElementById('viewStatut').innerHTML = permission.is_active ?
                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Active</span>' :
                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"><i class="fas fa-times-circle mr-1"></i>Inactive</span>';
                document.getElementById('viewType').innerHTML = permission.is_system ?
                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800"><i class="fas fa-lock mr-1"></i>Système</span>' :
                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"><i class="fas fa-edit mr-1"></i>Personnalisé</span>';

                document.getElementById('viewModal').classList.remove('hidden');
            }

            function openGenerateCrudModal() { document.getElementById('generateCrudModal').classList.remove('hidden'); }
            function closePermissionModal() { document.getElementById('permissionModal').classList.add('hidden'); resetForm(); }
            function closeViewModal() { document.getElementById('viewModal').classList.add('hidden'); }
            function closeGenerateCrudModal() { document.getElementById('generateCrudModal').classList.add('hidden'); }
            function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); deletePermissionId = null; }
            function resetForm() { document.getElementById('permissionForm').reset(); document.getElementById('is_active').checked = true; }

            function confirmDelete(permissionId, permissionName) {
                deletePermissionId = permissionId;
                document.getElementById('deleteMessage').textContent = `Êtes-vous sûr de vouloir supprimer la permission "${permissionName}" ?`;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function executeDelete() {
                if (!deletePermissionId) return;
                const permission = permissionsData.find(p => p.id === deletePermissionId);
                fetch("{{ route('admin.permissions.destroy', ':permission') }}".replace(':permission', permission.id), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => { if (data.success) location.reload(); else { alert(data.message || 'Une erreur est survenue'); closeDeleteModal(); } })
                .catch(error => { console.error('Erreur:', error); alert('Une erreur est survenue'); closeDeleteModal(); });
            }

            function refreshTable() { location.reload(); }

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => applyFilters(), 500);
            });

            document.getElementById('categoryFilter').addEventListener('change', applyFilters);
            document.getElementById('actionFilter').addEventListener('change', applyFilters);

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const category = document.getElementById('categoryFilter').value;
                const action = document.getElementById('actionFilter').value;
                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (category) params.append('category', category);
                if (action) params.append('action', action);
                window.location.href = `?${params.toString()}`;
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') { closePermissionModal(); closeViewModal(); closeGenerateCrudModal(); closeDeleteModal(); }
            });
        </script>

        <style>
            @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
        </style>
    @endpush
@endsection
