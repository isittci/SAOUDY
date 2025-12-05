<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard | '.config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

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
            transition: background 0.3s;
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

        /* Amélioration des transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-duration: 200ms;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
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
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Menu Overlay avec blur -->
        <div id="mobileMenuOverlay"
             class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden transition-all duration-300"
             onclick="toggleMobileMenu()">
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
        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileMenuOverlay");

            // S'assurer que le menu est fermé sur mobile au chargement
            if (window.innerWidth < 1024) {
                sidebar.classList.add("-translate-x-full");
                overlay.classList.add("hidden");
                document.body.style.overflow = "";
            } else {
                // S'assurer que le menu est ouvert sur desktop
                sidebar.classList.remove("-translate-x-full");
                overlay.classList.add("hidden");
            }
        });

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

            if (!userProfile && !userMenu.contains(event.target)) {
                userMenu.classList.add("hidden");
                userMenuIcon.classList.remove("rotate-180");
            }
        });

        // Toggle Mobile Menu avec animations
        function toggleMobileMenu() {

            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileMenuOverlay");

            sidebar.classList.toggle("-translate-x-full");
            overlay.classList.toggle("hidden");

            // Prevent body scroll when menu is open
            document.body.style.overflow = sidebar.classList.contains("-translate-x-full") ? "" : "hidden";
        }

        // Toggle Filters
        function toggleFilters() {
            const filtersContainer = document.getElementById("filtersContainer");
            const toggleIcon = document.getElementById("filterToggleIcon");

            filtersContainer.classList.toggle("hidden");
            toggleIcon.classList.toggle("rotate-180");
        }

        // Auto-close mobile menu on resize (seulement si déjà ouvert)
        let resizeTimer;
        let lastWidth = window.innerWidth;

        window.addEventListener("resize", function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const currentWidth = window.innerWidth;

                // Ne fermer que si on passe de mobile à desktop ET que le menu est ouvert
                if (currentWidth >= 1024 && lastWidth < 1024) {
                    const sidebar = document.getElementById("sidebar");
                    const overlay = document.getElementById("mobileMenuOverlay");

                    // Vérifier si le menu est actuellement ouvert
                    if (!sidebar.classList.contains("-translate-x-full")) {
                        sidebar.classList.add("-translate-x-full");
                        overlay.classList.add("hidden");
                        document.body.style.overflow = "";
                    }
                }

                // Auto-show filters on desktop uniquement si on passe de mobile à desktop
                if (currentWidth >= 768 && lastWidth < 768) {
                    const filtersContainer = document.getElementById("filtersContainer");
                    const toggleIcon = document.getElementById("filterToggleIcon");

                    filtersContainer.classList.remove("hidden");
                    toggleIcon.classList.remove("rotate-180");
                }

                lastWidth = currentWidth;
            }, 250);
        });

        // Escape key to close menus
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                // Close mobile menu
                const sidebar = document.getElementById("sidebar");
                const overlay = document.getElementById("mobileMenuOverlay");
                if (!sidebar.classList.contains("-translate-x-full")) {
                    toggleMobileMenu();
                }

                // Close user menu
                const userMenu = document.getElementById("userMenu");
                if (!userMenu.classList.contains("hidden")) {
                    toggleUserMenu();
                }
            }
        });

        // Add loading state to buttons
        document.querySelectorAll('button[type="submit"], a[data-loading]').forEach(element => {
            element.addEventListener('click', function(e) {

                if (!this.disabled) {
                    const originalContent = this.innerHTML;
                    // this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Chargement...';

                    // Re-enable after 3 seconds (fallback)
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = originalContent;
                    }, 3000);
                }
            });
        });
    </script>
</body>

</html>
