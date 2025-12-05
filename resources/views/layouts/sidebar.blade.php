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
            class="flex items-center space-x-3 px-3 py-3 {{ request()->routeIs('dashboard') ? 'bg-green-600 shadow-lg' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group shadow-md hover:shadow-lg transform hover:translate-x-1">
            <i class="fas fa-th text-base group-hover:scale-110 transition-transform"></i>
            <span class="font-medium text-sm">Tableau de bord</span>
        </a>

        <!-- SECTION : APPELS D'OFFRES -->
        <div class="mt-6 space-y-1">
            <div class="px-3 mb-3 flex items-center space-x-2 text-gray-300">
                <i class="fas fa-bullhorn text-xs"></i>
                <span class="font-semibold text-xs tracking-wider uppercase">Appels d'offres</span>
            </div>

            <!-- Types d'appels d'offres -->
            <a href="{{ route('types-appels-offres.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 {{ request()->routeIs('types-appels-offres.*') ? 'bg-green-600 shadow-lg' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                <i class="fas fa-list-alt text-sm group-hover:scale-110 transition-transform"></i>
                <span class="font-medium text-sm">Types d'appels d'offres</span>
            </a>

            <!-- Menu Appels d'offres -->
            <div>
                <button onclick="toggleSubmenu('aoMenu','aoIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 {{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*']) ? 'bg-green-600' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-bullhorn text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Appels d'offres</span>
                    </div>
                    <i id="aoIcon" class="fas fa-chevron-down text-xs transition-transform duration-300 {{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*']) ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="aoMenu" class="{{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*']) ? '' : 'hidden' }} ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3" style="{{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*']) ? 'max-height: 500px;' : '' }}">
                    <a href="{{ route('appels-offres.create') }}"
                        class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('appels-offres.create') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-plus-circle text-xs group-hover:rotate-90 transition-transform"></i>
                        <span>Créer un appel</span>
                    </a>
                    <a href="{{ route('appels-offres.index') }}"
                        class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('appels-offres.index') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-list text-xs"></i>
                        <span>Liste des appels</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('lots-appels-offres.*') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-history text-xs"></i>
                        <span>Historique</span>
                    </a>
                </div>
            </div>

            <!-- Prestataires -->
            <div>
                <button onclick="toggleSubmenu('prestataireMenu','prestataireIcon')"
                    class="w-full flex items-center justify-between px-3 py-2.5 {{ request()->routeIs('prestataires.*') ? 'bg-green-600' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user-tie text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Prestataires</span>
                    </div>
                    <i id="prestataireIcon" class="fas fa-chevron-down text-xs transition-transform duration-300 {{ request()->routeIs('prestataires.*') ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="prestataireMenu" class="{{ request()->routeIs( 'prestataires.*') ? '' : 'hidden' }} ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3" style="{{ request()->routeIs('prestataires.*') ? 'max-height: 500px;' : '' }}">
                    <a href="{{ route('prestataires.create') }}"
                        class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('prestataires.create') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-user-plus text-xs group-hover:rotate-12 transition-transform"></i>
                        <span>Ajouter</span>
                    </a>
                    <a href="{{ route('prestataires.index') }}"
                        class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('prestataires.index') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                        <i class="fas fa-users text-xs"></i>
                        <span>Liste</span>
                    </a>
                </div>
            </div>

            <a href="{{ route('proformas.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 hover:bg-green-600 rounded-lg transition-all duration-200 group">
                <i class="fas fa-clipboard-check text-sm group-hover:scale-110 transition-transform"></i>
                <span class="font-medium text-sm">Proformas</span>
            </a>

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
    // Tableau pour garder une trace de tous les menus ouverts
    let openMenus = [];

    function toggleSubmenu(menuId, iconId) {
        const menu = document.getElementById(menuId);
        const icon = document.getElementById(iconId);
        const isCurrentlyOpen = !menu.classList.contains("hidden");

        // Fermer tous les autres menus ouverts
        openMenus.forEach(openMenuId => {
            if (openMenuId !== menuId) {
                const otherMenu = document.getElementById(openMenuId);
                const otherIconId = openMenuId.replace('Menu', 'Icon');
                const otherIcon = document.getElementById(otherIconId);

                if (otherMenu && !otherMenu.classList.contains("hidden")) {
                    // Fermer le menu
                    otherMenu.style.maxHeight = "0px";
                    setTimeout(() => {
                        otherMenu.classList.add("hidden");
                    }, 300);

                    // Réinitialiser l'icône
                    if (otherIcon) {
                        otherIcon.classList.remove("rotate-180");
                    }
                }
            }
        });

        // Toggle du menu actuel
        if (isCurrentlyOpen) {
            // Fermer le menu
            menu.style.maxHeight = "0px";
            setTimeout(() => {
                menu.classList.add("hidden");
            }, 300);
            icon.classList.remove("rotate-180");

            // Retirer de la liste des menus ouverts
            openMenus = openMenus.filter(id => id !== menuId);
        } else {
            // Ouvrir le menu
            menu.classList.remove("hidden");
            menu.style.maxHeight = "0px";
            menu.style.overflow = "hidden";

            setTimeout(() => {
                menu.style.maxHeight = menu.scrollHeight + "px";
                menu.style.transition = "max-height 0.3s ease-out";
            }, 10);

            icon.classList.add("rotate-180");

            // Ajouter à la liste des menus ouverts
            if (!openMenus.includes(menuId)) {
                openMenus.push(menuId);
            }
        }
    }

    // Initialiser les menus ouverts au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier quels menus sont déjà ouverts (basé sur les classes Laravel)
        const allMenus = ['aoMenu', 'prestataireMenu', 'attribMenu', 'roleMenu', 'permMenu', 'staffMenu'];

        allMenus.forEach(menuId => {
            const menu = document.getElementById(menuId);
            if (menu && !menu.classList.contains('hidden')) {
                openMenus.push(menuId);
                menu.style.maxHeight = menu.scrollHeight + "px";
            }
        });
    });
</script>
