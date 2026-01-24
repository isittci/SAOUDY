<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard | ' . config('app.name'))</title>

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ asset('build/assets/app-Cnh10ZxB.css') }}">
    <script src="{{ asset('build/assets/app-CAiCLEjY.js') }}" defer></script>

    @stack('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <!-- Dans votre <head> ou avant </body> -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <!-- Dans le <head> -->

        <!-- Tom Select CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <link rel="shortcut icon" href="{{asset('favicon.png')}}" type="image/x-icon">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <style>
        /* Custom scrollbar - Plus moderne */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #64748b, #475569);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #475569, #334155);
        }

        /* Animation pour le sidebar */
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Animation pour le menu dropdown */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-enter {
            animation: fadeInDown 0.3s ease-out;
        }

        /* Focus visible pour l'accessibilité */
        *:focus-visible {
            outline: 2px solid #f97316;
            outline-offset: 2px;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Sidebar transitions */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        /* Overlay transitions */
        #mobileMenuOverlay {
            transition: opacity 0.3s ease-in-out;
        }

        #mobileMenuOverlay.opacity-0 {
            pointer-events: none;
        }

        /* Desktop sidebar always visible */
        @media (min-width: 1024px) {
            #sidebar {
                transform: translateX(0) !important;
            }

            #mobileMenuOverlay {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Menu Overlay avec blur -->
        <div id="mobileMenuOverlay"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden opacity-0 pointer-events-none"
            onclick="closeMobileMenu()">
        </div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden w-full">
            <!-- Top Navigation Bar -->
            @include('layouts.topbar')

            <!-- Content Area -->
            @yield('content')
        </div>
    </div>

    @stack('scripts')

    <script>
        // État du menu mobile
        let isMobileMenuOpen = false;
        const MOBILE_BREAKPOINT = 1024;

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebar();
            setupResizeHandler();
        });

        // Initialiser l'état du sidebar
        function initializeSidebar() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileMenuOverlay");

            if (window.innerWidth < MOBILE_BREAKPOINT) {
                // Mode mobile : menu fermé par défaut
                sidebar.classList.add("-translate-x-full");
                overlay.classList.add("opacity-0", "pointer-events-none");
                isMobileMenuOpen = false;
            } else {
                // Mode desktop : menu toujours visible
                sidebar.classList.remove("-translate-x-full");
            }

            document.body.style.overflow = "";
        }

        // Ouvrir le menu mobile
        function openMobileMenu() {
            if (window.innerWidth >= MOBILE_BREAKPOINT) return;

            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileMenuOverlay");

            sidebar.classList.remove("-translate-x-full");
            overlay.classList.remove("opacity-0", "pointer-events-none");

            document.body.style.overflow = "hidden";
            isMobileMenuOpen = true;
        }

        // Fermer le menu mobile
        function closeMobileMenu() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileMenuOverlay");

            sidebar.classList.add("-translate-x-full");
            overlay.classList.add("opacity-0", "pointer-events-none");

            document.body.style.overflow = "";
            isMobileMenuOpen = false;
        }

        // Toggle du menu mobile
        function toggleMobileMenu() {
            if (isMobileMenuOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        }

        // Gestion du redimensionnement
        function setupResizeHandler() {
            let resizeTimeout;
            let previousWidth = window.innerWidth;

            window.addEventListener("resize", function() {
                clearTimeout(resizeTimeout);

                resizeTimeout = setTimeout(function() {
                    const currentWidth = window.innerWidth;
                    const sidebar = document.getElementById("sidebar");
                    const overlay = document.getElementById("mobileMenuOverlay");

                    // Passage de mobile à desktop
                    if (currentWidth >= MOBILE_BREAKPOINT && previousWidth < MOBILE_BREAKPOINT) {
                        sidebar.classList.remove("-translate-x-full");
                        overlay.classList.add("opacity-0", "pointer-events-none");
                        document.body.style.overflow = "";
                        isMobileMenuOpen = false;
                    }

                    // Passage de desktop à mobile
                    if (currentWidth < MOBILE_BREAKPOINT && previousWidth >= MOBILE_BREAKPOINT) {
                        sidebar.classList.add("-translate-x-full");
                        overlay.classList.add("opacity-0", "pointer-events-none");
                        document.body.style.overflow = "";
                        isMobileMenuOpen = false;
                    }

                    previousWidth = currentWidth;
                }, 150);
            });
        }

        // Toggle User Menu
        function toggleUserMenu() {
            const userMenu = document.getElementById("userMenu");
            const userMenuIcon = document.getElementById("userMenuIcon");

            if (userMenu.classList.contains("hidden")) {
                userMenu.classList.remove("hidden");
                userMenu.classList.add("dropdown-enter");
                userMenuIcon.classList.add("rotate-180");
            } else {
                userMenu.classList.add("hidden");
                userMenu.classList.remove("dropdown-enter");
                userMenuIcon.classList.remove("rotate-180");
            }
        }

        // Close user menu when clicking outside
        document.addEventListener("click", function(event) {
            const userMenu = document.getElementById("userMenu");
            const userMenuIcon = document.getElementById("userMenuIcon");
            const userProfile = event.target.closest('button[onclick*="toggleUserMenu"]');

            if (!userProfile && userMenu && !userMenu.contains(event.target)) {
                userMenu.classList.add("hidden");
                if (userMenuIcon) {
                    userMenuIcon.classList.remove("rotate-180");
                }
            }
        });

        // Toggle Filters (si utilisé dans les pages)
        function toggleFilters() {
            const filtersContainer = document.getElementById("filtersContainer");
            const toggleIcon = document.getElementById("filterToggleIcon");

            if (filtersContainer) {
                filtersContainer.classList.toggle("hidden");
            }
            if (toggleIcon) {
                toggleIcon.classList.toggle("rotate-180");
            }
        }

        // Escape key to close menus
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                // Fermer le menu mobile s'il est ouvert
                if (isMobileMenuOpen) {
                    closeMobileMenu();
                }

                // Fermer le menu utilisateur
                const userMenu = document.getElementById("userMenu");
                if (userMenu && !userMenu.classList.contains("hidden")) {
                    toggleUserMenu();
                }
            }
        });

        // Fermer le menu mobile lors d'un clic sur un lien (optionnel)
        document.addEventListener('click', function(event) {
            if (window.innerWidth < MOBILE_BREAKPOINT && isMobileMenuOpen) {
                const clickedLink = event.target.closest('#sidebar a[href]:not([href="#"])');
                if (clickedLink) {
                    // Petite temporisation pour laisser le temps au lien de s'activer
                    setTimeout(closeMobileMenu, 150);
                }
            }
        });
    </script>
</body>

</html>
