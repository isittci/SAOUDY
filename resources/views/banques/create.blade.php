@extends('layouts.main')
@section('title', 'Nouvelle Banque - ' . $prestataire->raison_sociale_prestataire)
@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 30) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('banques_prestataires.read') href="{{ route('banques.index', $prestataire->id_prestataire) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Banques</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                @can('banques_prestataires.read')
                    <a href="{{ route('banques.index', $prestataire->id_prestataire) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                @endcan
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-plus-circle text-orange-500"></i>
                        <span>Nouvelle banque</span>
                    </h1>
                    <p class="text-gray-600 mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-red-700 font-medium mb-2">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="list-disc list-inside text-red-600 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @can('banques_prestataires.create')
            <form action="{{ route('banques.store', $prestataire->id_prestataire) }}" method="POST" id="banqueForm">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-university text-orange-500 mr-2"></i>
                                    Informations générales
                                </h2>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <label for="nom_banque" class="block text-sm font-semibold text-gray-700 mb-2">Nom de la
                                        banque <span class="text-red-500">*</span></label>
                                    <input type="text" name="nom_banque" id="nom_banque" required
                                        value="{{ old('nom_banque') }}" placeholder="Ex: Bank of Africa, Ecobank..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('nom_banque') border-red-500 @enderror">
                                    @error('nom_banque')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="code_banque" class="block text-sm font-semibold text-gray-700 mb-2">Code
                                            banque</label>
                                        <input type="text" name="code_banque" id="code_banque"
                                            value="{{ old('code_banque') }}" placeholder="Ex: CI008"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('code_banque') border-red-500 @enderror">
                                        @error('code_banque')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="titulaire_compte_banque"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Titulaire du compte</label>
                                        <input type="text" name="titulaire_compte_banque" id="titulaire_compte_banque"
                                            value="{{ old('titulaire_compte_banque') }}" placeholder="Nom du titulaire"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent @error('titulaire_compte_banque') border-red-500 @enderror">
                                        @error('titulaire_compte_banque')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                                    Coordonnées bancaires (RIB)
                                </h2>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="numero_compte_banque"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Numéro de compte</label>
                                        <input type="text" name="numero_compte_banque" id="numero_compte_banque"
                                            value="{{ old('numero_compte_banque') }}" placeholder="Ex: 01041308549"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent font-mono @error('numero_compte_banque') border-red-500 @enderror">
                                        @error('numero_compte_banque')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="code_guichet_banque"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Code guichet</label>
                                        <input type="text" name="code_guichet_banque" id="code_guichet_banque"
                                            value="{{ old('code_guichet_banque') }}" placeholder="Ex: 01001"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent font-mono @error('code_guichet_banque') border-red-500 @enderror">
                                        @error('code_guichet_banque')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="cle_rib_banque" class="block text-sm font-semibold text-gray-700 mb-2">Clé
                                        RIB</label>
                                    <input type="text" name="cle_rib_banque" id="cle_rib_banque"
                                        value="{{ old('cle_rib_banque') }}" placeholder="Ex: 85"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent font-mono @error('cle_rib_banque') border-red-500 @enderror">
                                    @error('cle_rib_banque')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div id="ribPreview" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-blue-700">Aperçu RIB</span>
                                        <span id="ribValue" class="font-mono text-blue-800"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-globe text-green-500 mr-2"></i>
                                    Informations internationales
                                </h2>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <label for="iban_banque"
                                        class="block text-sm font-semibold text-gray-700 mb-2">IBAN</label>
                                    <input type="text" name="iban_banque" id="iban_banque"
                                        value="{{ old('iban_banque') }}" placeholder="Ex: CI93CI0080104130854900185"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent font-mono uppercase @error('iban_banque') border-red-500 @enderror">
                                    @error('iban_banque')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-1">Format: 2 lettres + 2 chiffres + jusqu'à 30
                                        caractères</p>
                                </div>
                                <div>
                                    <label for="swift_bic_banque" class="block text-sm font-semibold text-gray-700 mb-2">Code
                                        SWIFT/BIC</label>
                                    <input type="text" name="swift_bic_banque" id="swift_bic_banque"
                                        value="{{ old('swift_bic_banque') }}" placeholder="Ex: SGBFCIAB"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent font-mono uppercase @error('swift_bic_banque') border-red-500 @enderror">
                                    @error('swift_bic_banque')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-1">Format: 8 ou 11 caractères</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-toggle-on text-purple-500 mr-2"></i>
                                    Statut
                                </h2>
                            </div>
                            <div class="p-6">
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="actif_banque" value="1"
                                            {{ old('actif_banque', true) ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500">
                                        </div>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Banque active</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-2">Une banque inactive ne pourra pas être utilisée pour les
                                    paiements</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                    Aide
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-700"><i class="fas fa-info-circle mr-1"></i>Le
                                        <strong>RIB</strong> est composé du code banque, code guichet, numéro de compte et clé.
                                    </p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <p class="text-sm text-green-700"><i class="fas fa-globe mr-1"></i>L'<strong>IBAN</strong>
                                        et le <strong>SWIFT</strong> sont nécessaires pour les virements internationaux.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="p-6 space-y-3">
                                <button type="submit"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg font-medium">
                                    <i class="fas fa-save"></i>
                                    <span>Enregistrer</span>
                                </button>
                                <button type="button"
                                    onclick="window.location.href='{{ route('banques.index', $prestataire->id_prestataire) }}'"
                                    class="w-full px-6 py-3 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 font-medium">
                                    <i class="fas fa-times"></i>
                                    <span>Annuler</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endcan
    </main>

    @can('banques_prestataires.create')
        @push('scripts')
            <script>
                ['code_banque', 'code_guichet_banque', 'iban_banque', 'swift_bic_banque'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('input', function() {
                        this.value = this.value.toUpperCase();
                    });
                });

                ['code_banque', 'code_guichet_banque', 'numero_compte_banque', 'cle_rib_banque'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('input', updateRibPreview);
                });

                function updateRibPreview() {
                    const parts = ['code_banque', 'code_guichet_banque', 'numero_compte_banque', 'cle_rib_banque'].map(id =>
                        document.getElementById(id).value).filter(v => v);
                    const preview = document.getElementById('ribPreview');
                    if (parts.length) {
                        document.getElementById('ribValue').textContent = parts.join(' ');
                        preview.classList.remove('hidden');
                    } else {
                        preview.classList.add('hidden');
                    }
                }
                updateRibPreview();
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
@endsection
