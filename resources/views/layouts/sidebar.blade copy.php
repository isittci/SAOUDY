<!-- Sidebar avec design moderne et responsive -->
<aside id="sidebar" class="w-64 bg-gradient-to-b from-green-700 to-green-800 text-white flex-shrink-0 overflow-y-auto fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out shadow-2xl">

    <!-- Logo/Header avec gradient -->
    <div
        class="p-4 lg:p-5 bg-gradient-to-r from-gray-900 to-gray-800 flex items-center justify-between sticky top-0 z-10 shadow-lg">
        <div class="flex items-center space-x-3">
            <div
                class="w-9 h-9 lg:w-10 lg:h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md transform hover:scale-110 transition-transform duration-300">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-white font-bold text-sm lg:text-base">MarketPlace AO</span>
                <span class="text-gray-300 text-xs">Gestion d'appels d'offres</span>
            </div>
        </div>
        <!-- Close button for mobile -->
        <button onclick="toggleMobileMenu()"
            class="lg:hidden text-white hover:bg-gray-700 p-2 rounded-lg transition-all duration-200 active:scale-95">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Navigation Menu avec scroll personnalisé -->
    <nav class="py-4 px-2 space-y-1">
        <!-- Menu Header -->
        <div class="px-3 mb-3 flex items-center space-x-2 text-gray-300">
            <i class="fas fa-grip-horizontal text-xs"></i>
            <span class="font-semibold text-xs tracking-wider uppercase">Navigation</span>
        </div>

        <!-- Tableau de bord -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center space-x-3 px-3 py-3 bg-green-600 hover:bg-green-500 rounded-lg transition-all duration-200 group shadow-md hover:shadow-lg transform hover:translate-x-1">
            <i class="fas fa-th text-base group-hover:scale-110 transition-transform"></i>
            <span class="font-medium text-sm">Tableau de bord</span>
        </a>

        <!-- SECTION : APPELS D'OFFRES -->
        <div class="mt-6 space-y-1">
            <div class="px-3 mb-3 flex items-center space-x-2 text-gray-300">
                <i class="fas fa-bullhorn text-xs"></i>
                <span class="font-semibold text-xs tracking-wider uppercase">Appels d'offres</span>
            </div>



            <!-- Consultations -->
            <a href="{{ route('types-appels-offres.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-search text-sm group-hover:scale-110 transition-transform"></i>
                <span class="font-medium text-sm">Types d'appels d'offres</span>
            </a>

            <!-- Menu Appels d'offres -->
            <div>
                <button onclick="toggleSubmenu('aoMenu','aoIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-bullhorn text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Appels d'offres</span>
                    </div>
                    <i id="aoIcon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <div id="aoMenu" class="hidden ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3">
                    <a href="{{ route('appels-offres.create') }}"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-plus-circle text-xs group-hover:rotate-90 transition-transform"></i>
                        <span>Créer un appel</span>
                    </a>
                    <a href="{{ route('appels-offres.index') }}"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-list text-xs"></i>
                        <span>Liste des appels</span>
                    </a>
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-history text-xs"></i>
                        <span>Historique</span>
                    </a>
                </div>
            </div>

            <!-- Prestataires -->
            <div>
                <button onclick="toggleSubmenu('prestataireMenu','prestataireIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user-tie text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Prestataires</span>
                    </div>
                    <i id="prestataireIcon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <div id="prestataireMenu" class="hidden ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3">
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-user-plus text-xs group-hover:rotate-12 transition-transform"></i>
                        <span>Ajouter</span>
                    </a>
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-users text-xs"></i>
                        <span>Liste</span>
                    </a>
                </div>
            </div>

            <!-- Attribution -->
            <div>
                <button onclick="toggleSubmenu('attribMenu','attribIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-signature text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Attribution d'appel</span>
                    </div>
                    <i id="attribIcon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <div id="attribMenu" class="hidden ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3">
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-plus text-xs"></i>
                        <span>Nouvelle attribution</span>
                    </a>
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-list text-xs"></i>
                        <span>Liste</span>
                    </a>
                </div>
            </div>

            <!-- Évaluation des lots -->
            <a href="#"
                class="flex items-center space-x-3 px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-clipboard-check text-sm group-hover:scale-110 transition-transform"></i>
                <span class="font-medium text-sm">Évaluation des lots</span>
            </a>

            <!-- Paiements -->
            <a href="#"
                class="flex items-center space-x-3 px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-money-check-alt text-sm group-hover:scale-110 transition-transform"></i>
                <span class="font-medium text-sm">Paiements</span>
            </a>

            <!-- Consultations -->
            <a href="#"
                class="flex items-center space-x-3 px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-search text-sm group-hover:scale-110 transition-transform"></i>
                <span class="font-medium text-sm">Consultations</span>
            </a>
        </div>

        <!-- SECTION ADMINISTRATION -->
        <div class="mt-6 space-y-1">
            <div class="px-3 mb-3 flex items-center space-x-2 text-gray-300">
                <i class="fas fa-cogs text-xs"></i>
                <span class="font-semibold text-xs tracking-wider uppercase">Administration</span>
            </div>

            <!-- Roles -->
            <div>
                <button onclick="toggleSubmenu('roleMenu','roleIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user-tag text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Rôles</span>
                    </div>
                    <i id="roleIcon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <div id="roleMenu" class="hidden ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3">
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-plus-circle text-xs"></i>
                        <span>Créer un rôle</span>
                    </a>
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-list text-xs"></i>
                        <span>Liste des rôles</span>
                    </a>
                </div>
            </div>

            <!-- Permissions -->
            <div>
                <button onclick="toggleSubmenu('permMenu','permIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shield-alt text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Permissions</span>
                    </div>
                    <i id="permIcon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <div id="permMenu" class="hidden ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3">
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-plus-circle text-xs"></i>
                        <span>Créer permission</span>
                    </a>
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-list text-xs"></i>
                        <span>Liste permissions</span>
                    </a>
                </div>
            </div>

            <!-- Personnels -->
            <div>
                <button onclick="toggleSubmenu('staffMenu','staffIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Personnels</span>
                    </div>
                    <i id="staffIcon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <div id="staffMenu" class="hidden ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3">
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-user-plus text-xs"></i>
                        <span>Ajouter</span>
                    </a>
                    <a href="#"
                        class="flex items-center space-x-3 px-3 py-2 hover:bg-green-600 rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-list text-xs"></i>
                        <span>Liste</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Sidebar -->
        <div class="mt-8 px-3 pb-4">
            <div class="bg-green-900/50 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-xs text-gray-300 text-center">Version 1.0.0</p>
                <p class="text-xs text-gray-400 text-center mt-1">© 2025 MarketPlace AO</p>
            </div>
        </div>
    </nav>
</aside>

<script>
    function toggleSubmenu(menuId, iconId) {
        const menu = document.getElementById(menuId);
        const icon = document.getElementById(iconId);

        // Animation fluide
        if (menu.classList.contains("hidden")) {
            menu.classList.remove("hidden");
            menu.style.maxHeight = "0px";
            menu.style.overflow = "hidden";

            setTimeout(() => {
                menu.style.maxHeight = menu.scrollHeight + "px";
                menu.style.transition = "max-height 0.3s ease-out";
            }, 10);
        } else {
            menu.style.maxHeight = "0px";
            setTimeout(() => {
                menu.classList.add("hidden");
            }, 300);
        }

        icon.classList.toggle("rotate-180");
    }
</script>
