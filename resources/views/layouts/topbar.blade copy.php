<!-- Top Navigation Bar avec design moderne -->
<header class="bg-gradient-to-r from-orange-500 via-orange-600 to-orange-700 shadow-xl sticky top-0 z-30">
    <div class="flex items-center justify-between px-3 sm:px-4 lg:px-6 py-3">
        <!-- Mobile Menu Button -->
        <button onclick="toggleMobileMenu()"
            class="lg:hidden p-2.5 hover:bg-orange-600/80 rounded-xl mr-2 transition-all duration-200 active:scale-95 shadow-md">
            <i class="fas fa-bars text-xl text-white"></i>
        </button>

        <!-- Search Bar avec animation -->
        <div class="flex-1 max-w-3xl">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i
                        class="fas fa-search text-gray-400 text-sm group-focus-within:text-orange-500 transition-colors"></i>
                </div>
                <input type="text" placeholder="Rechercher un appel d'offres, prestataire..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-white/95 backdrop-blur-sm border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-white/50 shadow-lg hover:shadow-xl transition-all duration-200 placeholder:text-gray-400" />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <kbd
                        class="hidden sm:inline-block px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded">
                        Ctrl+K
                    </kbd>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 ml-2 sm:ml-4">
            <!-- Notifications -->
            <div class="relative">
                <button
                    class="text-white hover:bg-orange-600/80 p-2.5 rounded-xl transition-all duration-200 relative group active:scale-95 shadow-md">
                    <i class="far fa-bell text-lg lg:text-xl group-hover:animate-pulse"></i>
                    <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 shadow-lg"></span>
                    </span>
                </button>
                <!-- Notification Badge avec compteur -->
                <span
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-lg">
                    3
                </span>
            </div>

            <!-- Divider -->
            <div class="hidden sm:block h-8 w-px bg-white/30"></div>

            <!-- User Profile -->
            <div class="relative">
                <button onclick="toggleUserMenu()"
                    class="flex items-center space-x-2 sm:space-x-3 hover:bg-orange-600/80 px-2 sm:px-3 py-2 rounded-xl transition-all duration-200 group active:scale-95 shadow-md">
                    <div class="relative">
                        <div
                            class="w-9 h-9 lg:w-10 lg:h-10 bg-gradient-to-br from-white to-gray-100 rounded-full flex items-center justify-center flex-shrink-0 shadow-md ring-2 ring-white/50 group-hover:ring-white transition-all">
                            <i class="fas fa-user text-orange-500 text-sm lg:text-base"></i>
                        </div>
                        <!-- Status indicator -->
                        <span
                            class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white shadow-sm"></span>
                    </div>
                    <div class="hidden sm:flex flex-col items-start">
                        <span
                            class="font-semibold text-white text-sm leading-tight">{{ auth()->user()->nom_complet }}</span>
                        <span class="text-xs text-orange-100">{{ auth()->user()->role->name }}</span>
                    </div>
                    <i id="userMenuIcon"
                        class="hidden sm:block fas fa-chevron-down text-white text-xs transition-transform duration-300 group-hover:text-orange-100"></i>
                </button>

                <!-- User Dropdown Menu avec animation -->
                <div id="userMenu"
                    class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl py-2 z-50 border border-gray-100 overflow-hidden">
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-md">
                                <i class="fas fa-user text-white text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">
                                    {{ auth()->user()->nom_complet }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="#"
                            class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent transition-all duration-200 group">
                            <div
                                class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                                <i class="fas fa-user-circle text-orange-600"></i>
                            </div>
                            <span class="font-medium">Mon profil</span>
                        </a>

                        <a href="#"
                            class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent transition-all duration-200 group">
                            <div
                                class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-cog text-blue-600"></i>
                            </div>
                            <span class="font-medium">Paramètres</span>
                        </a>

                        <a href="#"
                            class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent transition-all duration-200 group">
                            <div
                                class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-question-circle text-purple-600"></i>
                            </div>
                            <span class="font-medium">Aide & Support</span>
                        </a>
                    </div>

                    <hr class="my-2 border-gray-200" />

                    {{-- <!-- Logout {{ route('auth.logout') }} -->
                    <a href="#" class="flex items-center space-x-3 px-4 py-3 text-sm text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent transition-all duration-200 group">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                            <i class="fas fa-sign-out-alt text-red-600"></i>
                        </div>
                        <span class="font-medium">Déconnexion</span>
                    </a> --}}

                    {{-- <form action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center space-x-3 px-4 py-3 text-sm text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent transition-all duration-200 group">

                            <div
                                class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                <i class="fas fa-sign-out-alt text-red-600"></i>
                            </div>

                            <span class="font-medium">Déconnexion</span>
                        </button>
                    </form> --}}

                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="flex items-center space-x-3 px-4 py-3 text-sm text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent transition-all duration-200 group">

                        <div
                            class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                            <i class="fas fa-sign-out-alt text-red-600"></i>
                        </div>

                        <span class="font-medium">Déconnexion</span>
                    </a>

                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>


                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb (Optional) -->
    <div class="hidden lg:block px-6 pb-3">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-white transition-colors flex items-center space-x-1">
                <i class="fas fa-home text-xs"></i>
                <span>Accueil</span>
            </a>
            <i class="fas fa-chevron-right text-white/50 text-xs"></i>
            <span class="text-white font-medium">@yield('breadcrumb', 'Tableau de bord')</span>
        </nav>
    </div>
</header>
