@extends('layouts.main')
@section('title', 'Gestion des Rôles')
@section('breadcrumb', 'Rôles')

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-user-shield text-purple-500"></i>
                        <span>Gestion des Rôles</span>
                    </h1>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 sm:min-w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher un rôle..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent hover:border-purple-300 transition-all" />
                    </div>

                    <select id="typeFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent hover:border-purple-300 transition-all cursor-pointer">
                        <option value="">Tous les types</option>
                        <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>Rôles système</option>
                        <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Rôles personnalisés
                        </option>
                    </select>

                    @can('roles.create')
                        <a href="{{ route('admin.roles.create') }}"
                            class="px-4 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm font-medium">Nouveau rôle</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($roles as $role)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-200">
                    <div
                        class="px-6 py-4 bg-gradient-to-r from-{{ $role->level_color }}-50 to-white border-b border-{{ $role->level_color }}-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-{{ $role->level_color }}-100 rounded-full">
                                    <i class="fas fa-user-shield text-{{ $role->level_color }}-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">{{ $role->name }}</h3>
                                    <p class="text-xs text-gray-500">Niveau {{ $role->level }} - {{ $role->level_label }}
                                    </p>
                                </div>
                            </div>
                            @if ($role->is_system_role)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-lock text-xs mr-1"></i> Système
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $role->description ?? 'Aucune description' }}</p>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-800">{{ $role->users_count }}</div>
                                <div class="text-xs text-gray-500">Utilisateurs</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $role->permissions_count }}</div>
                                <div class="text-xs text-gray-500">Permissions</div>
                            </div>
                        </div>
                        @canany(['roles.read', 'roles.manage', 'roles.update', 'roles.delete'])
                        <div class="flex items-center space-x-2 pt-4">
                            @can('roles.read')
                                <a href="{{ route('admin.roles.show', $role) }}"
                                    class="flex-1 px-3 py-2 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i> Voir
                                </a>
                            @endcan


                                @can('roles.manage')
                                    <a href="{{ route('admin.roles.permissions', $role) }}"
                                        class="flex-1 px-3 py-2 text-center bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg transition-all text-sm font-medium">
                                        <i class="fas fa-key mr-1"></i> Permissions
                                    </a>
                                @endcan


                            @can('roles.update')
                                @if ($role->canBeEdited())
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                        class="p-2 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg transition-all">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            @endcan

                            @can('roles.delete')
                                @if ($role->canBeDeleted())
                                    <button onclick="openDeleteModal('{{ $role->id }}', '{{ $role->name }}')"
                                        class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            @endcan
                        </div>
                        @endcanany
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-shield text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">Aucun rôle trouvé</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau rôle.</p>
                </div>
            @endforelse
        </div>

        @if ($roles->hasPages())
            <div class="mt-6">
                {{ $roles->links() }}
            </div>
        @endif
    </main>

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
                        <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer le rôle <strong id="deleteRoleName"
                                class="text-gray-900"></strong> ?</p>
                        <p class="mt-2 text-sm text-gray-500">Cette action est irréversible.</p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                        @can('roles.delete')
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Supprimer</button>
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
                document.getElementById('deleteForm').action = "{{ route('admin.roles.destroy', ':role') }}".replace(':role',
                    id);
                document.getElementById('deleteRoleName').textContent = name;
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

            document.getElementById('typeFilter').addEventListener('change', applyFilters);

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const type = document.getElementById('typeFilter').value;
                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (type) params.append('type', type);
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
