<!-- Sidebar avec design moderne et responsive -->
<aside id="sidebar"
    class="w-64 bg-gradient-to-b from-green-700 to-green-800 text-white flex-shrink-0 overflow-y-auto fixed lg:relative h-full z-50 -translate-x-full lg:translate-x-0 shadow-2xl">

    <div
        class="p-4 lg:p-5 bg-gradient-to-r from-gray-900 to-gray-800 flex items-center justify-between sticky top-0 z-10 shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 lg:w-10 lg:h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md transform hover:scale-110 transition-transform duration-300 overflow-hidden">
                <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-7 h-7 lg:w-8 lg:h-8 object-contain">
            </div>
            <div class="flex flex-col">
                <span class="text-white font-bold text-sm lg:text-base">{{ env('APP_NAME') }}</span>
                <span class="text-gray-300 text-xs">Gestion des marchés</span>
            </div>
        </div>
        <!-- Close button for mobile -->
        <button onclick="closeMobileMenu()" type="button"
            class="lg:hidden text-white hover:bg-gray-700 p-2 rounded-lg transition-all duration-200 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white/50">
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

        @canany(['types_appels_offres.read', 'appels_offres.read', 'appels_offres.create', 'lots.read', 'prestataires.read', 'prestataires.create', 'attributions_lots.read', 'evaluations_attributions.read', 'factures.read', 'paiements.read', 'proformas.read'])
            <!-- SECTION : TYPES APPELS D'OFFRES -->
            <div class="mt-6 space-y-1">
                <div class="px-3 mb-3 flex items-center space-x-2 text-gray-300">
                    <i class="fas fa-bullhorn text-xs"></i>
                    <span class="font-semibold text-xs tracking-wider uppercase">Appels d'offres</span>
                </div>

                @can('types_appels_offres.read')
                    <!-- Types d'appels d'offres -->
                    <a href="{{ route('types-appels-offres.index') }}"
                        class="flex items-center space-x-3 px-3 py-2.5 {{ request()->routeIs('types-appels-offres.*') ? 'bg-green-600 shadow-lg' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                        <i class="fas fa-list-alt text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Types d'appels d'offres</span>
                    </a>
                @endcan

                @canany(['appels_offres.read', 'appels_offres.create', 'lots.read'])
                    <!-- Menu Appels d'offres -->
                    <div>
                        <button onclick="toggleSubmenu('aoMenu','aoIcon')" type="button"
                            class="w-full flex items-center justify-between px-3 py-2.5 {{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*', 'lots.*']) ? 'bg-green-600' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-bullhorn text-sm group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium text-sm">Appels d'offres</span>
                            </div>
                            <i id="aoIcon"
                                class="fas fa-chevron-down text-xs transition-transform duration-300 {{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*', 'lots.*']) ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="aoMenu"
                            class="{{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*', 'lots.*']) ? '' : 'hidden' }} ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3 overflow-hidden"
                            style="{{ request()->routeIs(['appels-offres.*', 'lots-appels-offres.*', 'lots.*']) ? 'max-height: 500px;' : 'max-height: 0;' }}">
                            @can('appels_offres.create')
                                    <a href="{{ route('appels-offres.create') }}"
                                        class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('appels-offres.create') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                                        <i class="fas fa-plus-circle text-xs group-hover:rotate-90 transition-transform"></i>
                                        <span>Créer un appel</span>
                                    </a>
                            @endcan
                            @can('appels_offres.read')
                                    <a href="{{ route('appels-offres.index') }}"
                                        class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('appels-offres.index') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                                        <i class="fas fa-list text-xs"></i>
                                        <span>Liste des appels</span>
                                    </a>
                            @endcan
                            @can('lots.read')
                                    <a href="{{ route('lots.index') }}"
                                        class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('lots-appels-offres.*', 'lots.*') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                                        <i class="fas fa-history text-xs"></i>
                                        <span>Liste des lots</span>
                                    </a>
                            @endcan
                        </div>
                    </div>
                @endcanany

                @canany(['prestataires.read', 'prestataires.create'])
                    <!-- Prestataires -->
                    <div>
                        <button onclick="toggleSubmenu('prestataireMenu','prestataireIcon')" type="button"
                            class="w-full flex items-center justify-between px-3 py-2.5 {{ request()->routeIs('prestataires.*') ? 'bg-green-600' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-user-tie text-sm group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium text-sm">Prestataires</span>
                            </div>
                            <i id="prestataireIcon" class="fas fa-chevron-down text-xs transition-transform duration-300 {{ request()->routeIs('prestataires.*') ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="prestataireMenu"
                            class="{{ request()->routeIs('prestataires.*') ? '' : 'hidden' }} ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3 overflow-hidden"
                            style="{{ request()->routeIs('prestataires.*') ? 'max-height: 500px;' : 'max-height: 0;' }}">
                        @can('prestataires.create')
                            <a href="{{ route('prestataires.create') }}"
                                class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('prestataires.create') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                                <i class="fas fa-user-plus text-xs group-hover:rotate-12 transition-transform"></i>
                                <span>Créer prestataire</span>
                            </a>
                        @endcan
                        @can('prestataires.read')
                            <a href="{{ route('prestataires.index') }}"
                                class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('prestataires.index') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                                <i class="fas fa-users text-xs"></i>
                                <span>Liste prestataires</span>
                            </a>
                        @endcan
                        </div>
                    </div>
                @endcanany

                @can('attributions_lots.read')
                    <!-- Attribution -->
                    <a href="{{ route('attributions.index') }}"
                        class="flex items-center space-x-3 px-3 py-2.5 {{ request()->routeIs('attributions.*') ? 'bg-green-600 shadow-lg' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                        <i class="fas fa-file-signature text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Attribution de lots</span>
                    </a>
                @endcan

                @can('evaluations_attributions.read')
                    <!-- Évaluations -->
                    <a href="{{ route('evaluations.index') }}"
                        class="flex items-center space-x-3 px-3 py-2.5 {{ request()->routeIs('evaluations.*') ? 'bg-green-600 shadow-lg' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                        <i class="fas fa-star text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Évaluation des lots</span>
                    </a>
                @endcan

                @canany(['factures.read', 'paiements.read'])
                    <!-- Paiements -->
                    <div>
                        <button onclick="toggleSubmenu('paiementMenu','paieIcon')" type="button"
                            class="w-full flex items-center justify-between px-3 py-2.5 {{ request()->routeIs(['factures.*', 'paiements.*']) ? 'bg-green-600' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-money-check-alt text-sm group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium text-sm">Paiements</span>
                            </div>
                            <i id="paieIcon"
                                class="fas fa-chevron-down text-xs transition-transform duration-300 {{ request()->routeIs(['factures.*', 'paiements.*']) ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="paiementMenu"
                            class="{{ request()->routeIs(['factures.*', 'paiements.*']) ? '' : 'hidden' }} ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3 overflow-hidden"
                            style="{{ request()->routeIs(['factures.*', 'paiements.*']) ? 'max-height: 500px;' : 'max-height: 0;' }}">
                            @can('factures.read')
                                <a href="{{ route('factures.index') }}"
                                    class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('factures.*') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                                    <i class="fas fa-file-invoice-dollar text-xs"></i>
                                    <span>Liste factures</span>
                                </a>
                            @endcan
                            @can('paiements.read')
                                <a href="{{ route('paiements.all') }}"
                                    class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('paiements.all') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                                    <i class="fas fa-list text-xs"></i>
                                    <span>Liste paiements</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcanany

                @can('proformas.read')
                    <!-- Proformas -->
                    <a href="{{ route('proformas.index') }}"
                        class="flex items-center space-x-3 px-3 py-2.5 {{ request()->routeIs('proformas.*') ? 'bg-green-600 shadow-lg' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                        <i class="fas fa-clipboard-check text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium text-sm">Proformas</span>
                    </a>
                @endcan
            </div>
        @endcanany


        @canany(['roles.read', 'users.read', 'role_permissions.read'])
            <!-- SECTION : ADMINISTRATION -->
            <div class="mt-6 space-y-1">
                <div class="px-3 mb-3 flex items-center space-x-2 text-gray-300">
                    <i class="fas fa-cogs text-xs"></i>
                    <span class="font-semibold text-xs tracking-wider uppercase">Administration</span>
                </div>

                <!-- Menu Administration unique -->
                <div>
                    <button onclick="toggleSubmenu('adminMenu','adminIcon')" type="button"
                        class="w-full flex items-center justify-between px-3 py-2.5 {{ request()->routeIs(['admin.roles.*', 'admin.permissions.*', 'admin.users.*']) ? 'bg-green-600' : 'hover:bg-green-600' }} rounded-lg transition-all duration-200 group">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-tools text-sm group-hover:scale-110 transition-transform"></i>
                            <span class="font-medium text-sm">Paramètres</span>
                        </div>
                        <i id="adminIcon"
                            class="fas fa-chevron-down text-xs transition-transform duration-300 {{ request()->routeIs(['admin.roles.*', 'admin.permissions.*', 'admin.users.*']) ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="adminMenu"
                        class="{{ request()->routeIs(['admin.roles.*', 'admin.permissions.*', 'admin.users.*']) ? '' : 'hidden' }} ml-8 mt-1 space-y-1 border-l-2 border-green-500 pl-3 overflow-hidden"
                        style="{{ request()->routeIs(['admin.roles.*', 'admin.permissions.*', 'admin.users.*']) ? 'max-height: 500px;' : 'max-height: 0;' }}">

                        @can('roles.read')
                        <!-- Rôles -->
                        <a href="{{ route('admin.roles.index') }}"
                            class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('admin.roles.*') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                            <i class="fas fa-user-tag text-xs"></i>
                            <span>Rôles</span>
                        </a>
                        @endcan

                        @can('role_permissions.read')
                        <!-- Permissions -->
                        <a href="{{ route('admin.permissions.index') }}"
                            class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('admin.permissions.*') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                            <i class="fas fa-shield-alt text-xs"></i>
                            <span>Permissions</span>
                        </a>
                        @endcan

                        @can('users.read')
                        <!-- Personnels -->
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center space-x-3 px-3 py-2 {{ request()->routeIs('admin.users.*') ? 'bg-green-500' : 'hover:bg-green-600' }} rounded-lg text-sm transition-all duration-200 group">
                            <i class="fas fa-users text-xs"></i>
                            <span>Personnels</span>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        @endcan


        <!-- Footer Sidebar -->
        <div class="mt-8 px-3 pb-4">
            <div class="bg-green-900/50 rounded-lg p-3 backdrop-blur-sm">
                <p class="text-xs text-gray-300 text-center">Version 1.0.0</p>
                <p class="text-xs text-gray-400 text-center mt-1">
                    © {{ date('Y') }} conçu par
                    <a href="https://{{ config('app.company_website') }}" target="_blank" title="{{ config('app.company_name') }}"
                        class="hover:text-white transition-colors duration-200">
                        {{ config('app.company_sigle') }}
                    </a>
                </p>
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

        if (!menu || !icon) return;

        const isCurrentlyOpen = !menu.classList.contains("hidden");

        // Fermer tous les autres menus ouverts
        openMenus.forEach(openMenuId => {
            if (openMenuId !== menuId) {
                const otherMenu = document.getElementById(openMenuId);
                const otherIconId = openMenuId.replace('Menu', 'Icon');
                const otherIcon = document.getElementById(otherIconId);

                if (otherMenu && !otherMenu.classList.contains("hidden")) {
                    // Animation de fermeture
                    otherMenu.style.maxHeight = "0px";
                    otherMenu.style.opacity = "0";

                    setTimeout(() => {
                        otherMenu.classList.add("hidden");
                    }, 300);

                    if (otherIcon) {
                        otherIcon.classList.remove("rotate-180");
                    }
                }
            }
        });

        // Toggle du menu actuel
        if (isCurrentlyOpen) {
            // Animation de fermeture
            menu.style.maxHeight = "0px";
            menu.style.opacity = "0";

            setTimeout(() => {
                menu.classList.add("hidden");
            }, 300);

            icon.classList.remove("rotate-180");
            openMenus = openMenus.filter(id => id !== menuId);
        } else {
            // Animation d'ouverture
            menu.classList.remove("hidden");
            menu.style.opacity = "0";
            menu.style.maxHeight = "0px";
            menu.style.transition = "max-height 0.3s ease-out, opacity 0.3s ease-out";

            // Forcer un reflow
            menu.offsetHeight;

            requestAnimationFrame(() => {
                menu.style.maxHeight = menu.scrollHeight + "px";
                menu.style.opacity = "1";
            });

            icon.classList.add("rotate-180");

            if (!openMenus.includes(menuId)) {
                openMenus.push(menuId);
            }
        }
    }

    // Initialiser les menus ouverts au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // CORRIGÉ: Ajout de 'paiementMenu' à la liste
        const allMenus = ['aoMenu', 'prestataireMenu', 'attribMenu', 'paiementMenu', 'roleMenu', 'permMenu',
            'staffMenu'
        ];

        allMenus.forEach(menuId => {
            const menu = document.getElementById(menuId);
            if (menu && !menu.classList.contains('hidden')) {
                openMenus.push(menuId);
                menu.style.maxHeight = menu.scrollHeight + "px";
                menu.style.opacity = "1";
            }
        });
    });
</script>
