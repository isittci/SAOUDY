<!-- Top Navigation Bar avec design moderne -->
<header class="bg-gradient-to-r from-orange-500 via-orange-600 to-orange-700 shadow-xl sticky top-0 z-30">
    <div class="flex items-center justify-between px-3 sm:px-4 lg:px-6 py-3">
        <!-- Mobile Menu Button -->
        <button onclick="toggleMobileMenu()"
            class="lg:hidden p-2.5 hover:bg-orange-600/80 rounded-xl mr-2 transition-all duration-200 active:scale-95 shadow-md">
            <i class="fas fa-bars text-xl text-white"></i>
        </button>

        <!-- Nom de l'Application -->
        <div class="flex-1 flex items-center justify-center lg:justify-start">
            <div class="flex items-center space-x-3">
                <div class="hidden sm:flex items-center justify-center w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl shadow-lg overflow-hidden">
                    <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                </div>
                <div class="flex flex-col">
                    <h1 class="text-white font-bold text-base sm:text-lg lg:text-xl leading-tight tracking-wide">
                        {{env('APP_NAME', 'SAODY')}}
                    </h1>
                    <span class="hidden sm:block text-orange-100 text-xs font-medium">
                        Système de gestion des Appels d'Offres du District de Yamoussoukro
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 ml-2 sm:ml-4">
           

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
                        <a href="{{ route('profile.index') }}"
                            class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent transition-all duration-200 group">
                            <div
                                class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                                <i class="fas fa-user-circle text-orange-600"></i>
                            </div>
                            <span class="font-medium">Mon profil</span>
                        </a>

                        {{-- <a href="#"
                            class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent transition-all duration-200 group">
                            <div
                                class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-cog text-blue-600"></i>
                            </div>
                            <span class="font-medium">Paramètres</span>
                        </a> --}}

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
