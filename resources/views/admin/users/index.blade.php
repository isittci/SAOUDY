@extends('layouts.main')
@section('title', 'Gestion des Utilisateurs')
@section('breadcrumb', 'Utilisateurs')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-users text-orange-500"></i>
                        <span>Gestion des Utilisateurs</span>
                    </h1>
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher par nom, email, téléphone..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>

                    <!-- Filtre rôle -->
                    <select id="roleFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les rôles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}</option>
                        @endforeach
                    </select>

                    <!-- Filtre statut -->
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actifs</option>
                        <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactifs</option>
                    </select>

                    @can('users.view-trash')
                        <a href="{{ route('admin.users.trash') }}"
                            class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-trash text-sm"></i>
                            <span class="text-sm font-medium">Corbeille</span>
                        </a>
                    @endcan

                    @can('users.create')
                        <a href="{{ route('admin.users.create') }}"
                            class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm font-medium">Nouvel utilisateur</span>
                        </a>
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
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-gray-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $users->total() }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="fas fa-users text-gray-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Actifs</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ \App\Models\User::where('statut', 'actif')->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-user-check text-green-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Inactifs</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ \App\Models\User::where('statut', 'inactif')->count() }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-user-times text-red-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Vérifiés</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-envelope-circle-check text-blue-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Liste des utilisateurs</h3>
                    <span class="text-sm text-gray-500">{{ $users->total() }} utilisateur(s)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Utilisateur</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Rôle</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Email vérifié</th>
                            @canany(['users.view-details', 'users.update', 'users.toggle-status', 'users.delete'])
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-orange-50/50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-{{ $user->role->level_color ?? 'gray' }}-500 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ $user->initials }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <a @can('users.view-details') href="{{ route('admin.users.show', $user) }}" @endcanany
                                                class="text-sm font-semibold text-gray-900 hover:text-orange-600 transition-colors">
                                                {{ $user->nom_complet }}
                                            </a>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->role)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $user->role->level_color }}-100 text-{{ $user->role->level_color }}-800">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Aucun rôle</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($user->statut === \App\Models\User::STATUT_ACTIF)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Actif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($user->email_verified_at)
                                        <span class="text-green-500"
                                            title="Vérifié le {{ $user->email_verified_at->format('d/m/Y') }}">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @else
                                        <span class="text-gray-400" title="Non vérifié">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                    @endif
                                </td>
                                @canany(['users.view-details', 'users.update', 'users.toggle-status', 'users.delete'])
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-1">
                                            @can('users.view-details')
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                    class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                    title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @canany(['users.update', 'users.toggle-status', 'users.delete'])
                                                @if (auth()->user()->canManageUser($user))
                                                    @can('users.update')
                                                        <a href="{{ route('admin.users.edit', $user) }}"
                                                            class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                                                            title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('users.toggle-status')
                                                        @if ($user->id !== auth()->id())
                                                            <form action="{{ route('admin.users.toggle-status', $user) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="p-2 text-gray-400 hover:text-{{ $user->statut === 'actif' ? 'red' : 'green' }}-600 hover:bg-{{ $user->statut === 'actif' ? 'red' : 'green' }}-50 rounded-lg transition-all"
                                                                    title="{{ $user->statut === 'actif' ? 'Désactiver' : 'Activer' }}">
                                                                    <i
                                                                        class="fas fa-{{ $user->statut === 'actif' ? 'toggle-on text-green-500' : 'toggle-off' }}"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                    @can('users.delete')
                                                        @if ($user->canBeDeleted() && $user->id !== auth()->id())
                                                            <button
                                                                onclick="openDeleteModal('{{ $user->id }}', '{{ $user->nom_complet }}')"
                                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                                title="Supprimer">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                @endif
                                            @endcanany
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">Aucun utilisateur trouvé</h3>
                                        <p class="mt-1 text-sm text-gray-500">Aucun utilisateur ne correspond à vos critères.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Supprimer -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        Confirmer la suppression
                    </h3>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer l'utilisateur <strong
                                id="deleteUserName" class="text-gray-900"></strong> ?</p>
                        <p class="mt-2 text-sm text-gray-500">L'utilisateur sera déplacé dans la corbeille.</p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                        @can('users.delete')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Supprimer</button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@can('users.read')
@push('scripts')
    <script>
        function openDeleteModal(id, name) {
            document.getElementById('deleteForm').action = `/users/${id}`;
            document.getElementById('deleteUserName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => applyFilters(), 500);
        });

        document.getElementById('roleFilter').addEventListener('change', applyFilters);
        document.getElementById('statutFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const role = document.getElementById('roleFilter').value;
            const statut = document.getElementById('statutFilter').value;
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (role) params.append('role_id', role);
            if (statut) params.append('statut', statut);
            window.location.href = `?${params.toString()}`;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
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
@endcan
