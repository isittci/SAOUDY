@extends('layouts.main')
@section('title', $role->name)
@section('breadcrumb')
    <a @can('roles.read') href="{{ route('admin.roles.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Rôles</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $role->name }}</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('roles.read')
                        <a href="{{ route('admin.roles.index') }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div class="flex items-center space-x-3">
                        <div class="p-4 bg-{{ $role->level_color }}-100 rounded-xl">
                            <i class="fas fa-user-shield text-{{ $role->level_color }}-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-3 flex-wrap gap-2">
                                <h1 class="text-2xl font-bold text-gray-800">{{ $role->name }}</h1>
                                @if ($role->is_system_role)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <i class="fas fa-lock mr-1"></i> Rôle système
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 mt-1">Niveau {{ $role->level }} - {{ $role->level_label }}</p>
                        </div>
                    </div>
                </div>

                @canany(['roles.manage', 'roles.update', 'roles.duplicate', 'roles.delete'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap gap-2">
                        @can('roles.manage')
                            <a href="{{ route('admin.roles.permissions', $role) }}"
                                class="px-4 py-2.5 bg-white border border-purple-300 text-purple-600 hover:bg-purple-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-key text-sm"></i>
                                <span class="text-sm font-medium">Gérer les permissions</span>
                            </a>
                        @endcan

                        @can('roles.update')
                            @if ($role->canBeEdited())
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                    class="px-4 py-2.5 bg-white border border-amber-300 text-amber-600 hover:bg-amber-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-edit text-sm"></i>
                                    <span class="text-sm font-medium">Modifier</span>
                                </a>
                            @endif
                        @endcan

                        {{-- @can('roles.duplicate')
                            <form action="{{ route('admin.roles.duplicate', $role) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-copy text-sm"></i>
                                    <span class="text-sm font-medium">Dupliquer</span>
                                </button>
                            </form>
                        @endcan --}}

                        @can('roles.delete')
                            @if ($role->canBeDeleted())
                                <button onclick="openDeleteModal()"
                                    class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-trash text-sm"></i>
                                    <span class="text-sm font-medium">Supprimer</span>
                                </button>
                            @endif
                        @endcan
                    </div>
                @endcanany
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

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
                <!-- Informations générales -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-info-circle text-{{ $role->level_color }}-500 mr-2"></i>
                            Informations générales
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom du rôle</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $role->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Code (Slug)</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">
                                    {{ $role->slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Niveau hiérarchique</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $role->level_color }}-100 text-{{ $role->level_color }}-800">
                                        Niveau {{ $role->level }} - {{ $role->level_label }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type de rôle</dt>
                                <dd class="mt-1">
                                    @if ($role->is_system_role)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-lock mr-1"></i> Système
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-user-edit mr-1"></i> Personnalisé
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 leading-relaxed">
                                    {{ $role->description ?? 'Aucune description' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-key text-purple-500 mr-2"></i>
                                Permissions ({{ $permissionsByModule->flatten()->count() }})
                            </h3>
                            @can('roles.manage')
                                <a href="{{ route('admin.roles.permissions', $role) }}"
                                    class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                    Gérer <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($permissionsByModule->count() > 0)
                            <div class="space-y-4">
                                @foreach ($permissionsByModule as $module => $modulePermissions)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-cube text-gray-400"></i>
                                                <span class="font-medium text-gray-700">{{ $module }}</span>
                                            </div>
                                            <span
                                                class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full font-medium">
                                                {{ $modulePermissions->count() }} permissions
                                            </span>
                                        </div>
                                        <div class="p-4 bg-white">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($modulePermissions->take(10) as $permission)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $permission->action_color }}-100 text-{{ $permission->action_color }}-800">
                                                        <i class="fas {{ $permission->action_icon }} mr-1"></i>
                                                        {{ $permission->action_label }}
                                                    </span>
                                                @endforeach
                                                @if ($modulePermissions->count() > 10)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                        +{{ $modulePermissions->count() - 10 }} autres
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-key text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">Aucune permission attribuée à ce rôle</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Utilisateurs -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-users text-blue-500 mr-2"></i>
                                Utilisateurs ({{ $role->users->count() }})
                            </h3>
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($role->users->count() > 0)
                            <div class="space-y-3">
                                @foreach ($role->users->take(5) as $user)
                                    <a @can('users.view-details') href="{{ route('admin.users.show', $user) }}" @endcan
                                        class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-all">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-{{ $role->level_color }}-500 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-white">{{ $user->initials }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->nom_complet }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                        </div>
                                        <div>
                                            @if ($user->statut === 1)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Actif
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i> Inactif
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach

                                @can('users.read')
                                    @if ($role->users->count() > 5)
                                        <a href="{{ route('admin.users.index', ['role_id' => $role->id]) }}"
                                            class="block text-center py-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            Voir tous les utilisateurs ({{ $role->users->count() }}) <i
                                                class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">Aucun utilisateur n'a ce rôle</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistiques -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                            Statistiques
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-key text-purple-500 mr-3"></i>
                                <span class="text-sm text-gray-700">Permissions</span>
                            </div>
                            <span class="text-lg font-bold text-purple-600">{{ $role->permissions->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-users text-blue-500 mr-3"></i>
                                <span class="text-sm text-gray-700">Utilisateurs</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600">{{ $role->users->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-user-check text-green-500 mr-3"></i>
                                <span class="text-sm text-gray-700">Actifs</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">{{ $role->active_users_count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Métadonnées -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-clock text-gray-500 mr-2"></i>
                            Métadonnées
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div>
                            <span class="text-gray-500">Créé le</span>
                            <p class="text-gray-900 font-medium">{{ $role->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Modifié le</span>
                            <p class="text-gray-900 font-medium">{{ $role->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Delete -->
    @if ($role->canBeDeleted())
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
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-6">
                            <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer le rôle <strong
                                    class="text-gray-900">{{ $role->name }}</strong> ?</p>
                            <p class="mt-2 text-sm text-red-600 font-semibold">Cette action est irréversible.</p>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                            <button type="button" onclick="closeDeleteModal()"
                                class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Supprimer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@can('roles.view-details')
@push('scripts')
    <script>
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });
    </script>
    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

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
    </style>
@endpush
@endcan
