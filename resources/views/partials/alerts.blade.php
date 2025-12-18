{{--
    Partial pour afficher les messages d'alerte (success, error, warning, info)
    À placer dans resources/views/partials/alerts.blade.php
--}}

{{-- Message de succès --}}
@if (session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fadeIn" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

{{-- Message d'erreur --}}
@if (session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

{{-- Message d'avertissement --}}
@if (session('warning'))
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm animate-fadeIn" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-yellow-700 font-medium">{{ session('warning') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-yellow-500 hover:text-yellow-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

{{-- Message d'information --}}
@if (session('info'))
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm animate-fadeIn" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-blue-700 font-medium">{{ session('info') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-blue-500 hover:text-blue-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

{{-- Erreurs de validation --}}
@if ($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-red-800 font-semibold mb-2">Erreurs de validation</h3>
                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

{{-- Style pour l'animation --}}
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
