@extends('layouts.main')
@section('title', 'Corbeille - Utilisateurs')
@section('breadcrumb')
    <a @can('users.read') href="{{ route('admin.users.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Utilisateurs</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Corbeille</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-trash-restore text-red-500"></i>
                        <span>Utilisateurs supprimés</span>
                    </h1>
                </div>
                @can('users.read')
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.users.index') }}"
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-arrow-left text-sm"></i>
                            <span class="text-sm font-medium">Retour à la liste</span>
                        </a>
                    </div>
                @endcan
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

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supprimé le</th>
                            @canany(['users.restore', 'users.force-delete'])
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gray-400 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-white">{{ $user->initials }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->nom_complet }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->role)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $user->role->level_color }}-100 text-{{ $user->role->level_color }}-800">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->deleted_at->format('d/m/Y à H:i') }}
                                </td>
                                @canany(['users.restore', 'users.force-delete'])
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-1">
                                            @can('users.restore')
                                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all"
                                                        title="Restaurer">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('users.force-delete')
                                                <button
                                                    onclick="openForceDeleteModal('{{ $user->id }}', '{{ $user->nom_complet }}')"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                    title="Supprimer définitivement">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">Corbeille vide</h3>
                                        <p class="mt-1 text-sm text-gray-500">Aucun utilisateur supprimé.</p>
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

    <div id="forceDeleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeForceDeleteModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        Suppression définitive
                    </h3>
                </div>
                <form id="forceDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer définitivement <strong
                                id="forceDeleteUserName" class="text-gray-900"></strong> ?</p>
                        <p class="mt-2 text-sm text-red-600 font-semibold">Cette action est irréversible !</p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeForceDeleteModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                        @can('users.force-delete')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Supprimer définitivement</button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@can('users.view-trash')
    @push('scripts')
        <script>
            function openForceDeleteModal(id, name) {
                document.getElementById('forceDeleteForm').action = "{{ route('admin.users.force-destroy', ':id') }}".replace(':id', id);
                document.getElementById('forceDeleteUserName').textContent = name;
                document.getElementById('forceDeleteModal').classList.remove('hidden');
            }

            function closeForceDeleteModal() {
                document.getElementById('forceDeleteModal').classList.add('hidden');
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeForceDeleteModal();
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
