@extends('layouts.main')
@section('title', 'Modifier ' . $user->nom_complet)
@section('breadcrumb')
    <a @can('users.read') href="{{ route('admin.users.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Utilisateurs</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('users.view-details') href="{{ route('admin.users.show', $user) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $user->nom_complet }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Modifier</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.show', $user) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-user-edit text-amber-500 mr-2"></i>
                        Modifier {{ $user->nom_complet }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-red-700 font-medium mb-2">Erreurs de validation :</p>
                        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @can('users.update')
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="max -w-4xl">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-user text-orange-500 mr-2"></i>
                            Informations personnelles
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nom_complet" value="{{ old('nom_complet', $user->nom_complet) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Adresse email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone principal</label>
                                <input type="text" name="telephone_principal"
                                    value="{{ old('telephone_principal', $user->telephone_principal) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone secondaire</label>
                                <input type="text" name="telephone_secondaire"
                                    value="{{ old('telephone_secondaire', $user->telephone_secondaire) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-lock text-blue-500 mr-2"></i>
                            Mot de passe (laisser vide pour ne pas modifier)
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe</label>
                                <input type="password" name="password" minlength="8"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-user-shield text-purple-500 mr-2"></i>
                            Rôle et permissions
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Rôle <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }} (Niveau {{ $role->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="statut" value="1"
                                        {{ old('statut', $user->statut) == '1' ? 'checked' : '' }} required
                                        class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700">Actif</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="statut" value="0"
                                        {{ old('statut', $user->statut) == '0' ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                    <span class="ml-2 text-sm text-gray-700">Inactif</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="email_verified" value="1"
                                    {{ $user->email_verified_at ? 'checked' : '' }}
                                    class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <span class="ml-2 text-sm text-gray-700">Email vérifié</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.users.show', $user) }}"
                        class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-times text-sm"></i>
                        <span class="font-medium">Annuler</span>
                    </a>

                    <button type="submit"
                        class="px-6 py-3 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md"
                        style="background: linear-gradient(to right, #f59e0b, #d97706);">
                        <i class="fas fa-save text-sm"></i>
                        <span class="font-medium">Enregistrer les modifications</span>
                    </button>
                </div>
            </form>
        @endcan
    </main>
@endsection
