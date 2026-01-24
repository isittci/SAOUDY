{{--
    ============================================================================
    SCRIPT: Auto-complétion des responsables (Version partagée)
    ============================================================================

    Tous les responsables sont partagés entre les 3 rôles.
    Un responsable technique peut être suggéré comme superviseur ou évaluateur.

    À inclure dans les vues create.blade.php et edit.blade.php:
    @push('scripts')
        @include('evaluations.partials.responsables-autocomplete-script')
    @endpush
--}}

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ========================================
    // Configuration
    // ========================================
    const CONFIG = {
        minChars: 1,
        debounceDelay: 150,
        allUrl: '{{ route("evaluations.responsables.all") }}',
    };

    // Cache des responsables existants (liste unique partagée)
    let responsablesCache = [];
    let cacheLoaded = false;

    // Navigation clavier
    let activeSuggestionIndex = -1;
    let currentSuggestions = [];
    let currentContainer = null;

    // ========================================
    // Chargement initial des responsables
    // ========================================
    async function loadAllResponsables() {
        try {
            const response = await fetch(CONFIG.allUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data) {
                    // Tous les types ont la même liste, on prend le premier
                    responsablesCache = result.data.respo_technique || [];
                    cacheLoaded = true;
                    console.log('Responsables chargés:', responsablesCache.length, 'entrées');
                }
            }
        } catch (error) {
            console.error('Erreur chargement responsables:', error);
        }
    }

    // Charger au démarrage
    loadAllResponsables();

    // ========================================
    // Recherche de responsables (liste unique)
    // ========================================
    function searchResponsables(query) {
        if (!cacheLoaded || responsablesCache.length === 0) {
            return [];
        }

        const queryLower = query.toLowerCase().trim();

        if (queryLower.length < CONFIG.minChars) {
            return [];
        }

        return responsablesCache.filter(r => {
            const nom = (r.nom_complet || '').toLowerCase();
            const email = (r.email || '').toLowerCase();
            const tel = (r.telephone || '').toLowerCase();

            return nom.includes(queryLower) ||
                   email.includes(queryLower) ||
                   tel.includes(queryLower);
        }).slice(0, 8);
    }

    // ========================================
    // Affichage des suggestions
    // ========================================
    function showSuggestions(container, suggestions, type) {
        const suggestionsDiv = container.querySelector('.responsable-suggestions');

        if (!suggestionsDiv || suggestions.length === 0) {
            hideSuggestions(container);
            return;
        }

        currentSuggestions = suggestions;
        currentContainer = container;
        activeSuggestionIndex = -1;

        let html = '';

        // Option pour nouvelle saisie
        html += `
            <div class="responsable-suggestion-item new-entry" data-index="-1">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-plus-circle text-green-500"></i>
                    <span class="text-sm text-green-700 font-medium">Nouvelle saisie</span>
                </div>
                <div class="suggestion-details text-xs text-gray-500">Continuer avec les informations saisies</div>
            </div>
        `;

        // Suggestions existantes
        suggestions.forEach((resp, index) => {
            const details = [];
            if (resp.email) details.push(`<i class="fas fa-envelope mr-1"></i>${escapeHtml(resp.email)}`);
            if (resp.telephone) details.push(`<i class="fas fa-phone mr-1"></i>${escapeHtml(resp.telephone)}`);

            html += `
                <div class="responsable-suggestion-item"
                     data-index="${index}"
                     data-nom="${escapeHtml(resp.nom_complet || '')}"
                     data-email="${escapeHtml(resp.email || '')}"
                     data-telephone="${escapeHtml(resp.telephone || '')}">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user text-blue-500"></i>
                        <span class="suggestion-name font-semibold text-gray-800">${escapeHtml(resp.nom_complet)}</span>
                    </div>
                    ${details.length > 0 ? `<div class="suggestion-details text-xs text-gray-500 mt-1">${details.join(' • ')}</div>` : ''}
                </div>
            `;
        });

        suggestionsDiv.innerHTML = html;
        suggestionsDiv.classList.remove('hidden');
        suggestionsDiv.classList.add('show');

        // Événements de clic
        suggestionsDiv.querySelectorAll('.responsable-suggestion-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (this.classList.contains('new-entry')) {
                    hideSuggestions(container);
                    return;
                }

                selectSuggestion(container, type, {
                    nom_complet: this.dataset.nom,
                    email: this.dataset.email,
                    telephone: this.dataset.telephone
                });
            });
        });
    }

    function hideSuggestions(container) {
        const suggestionsDiv = container?.querySelector('.responsable-suggestions');
        if (suggestionsDiv) {
            suggestionsDiv.classList.add('hidden');
            suggestionsDiv.classList.remove('show');
            suggestionsDiv.innerHTML = '';
        }
        currentSuggestions = [];
        activeSuggestionIndex = -1;
        currentContainer = null;
    }

    function hideAllSuggestions() {
        document.querySelectorAll('.responsable-suggestions').forEach(div => {
            div.classList.add('hidden');
            div.classList.remove('show');
        });
        currentSuggestions = [];
        activeSuggestionIndex = -1;
        currentContainer = null;
    }

    // ========================================
    // Sélection d'une suggestion
    // ========================================
    function selectSuggestion(container, type, data) {
        const nomInput = document.getElementById(`${type}_nom_complet`);
        const emailInput = document.getElementById(`${type}_email`);
        const telInput = document.getElementById(`${type}_telephone`);

        if (nomInput) nomInput.value = data.nom_complet || '';
        if (emailInput) emailInput.value = data.email || '';
        if (telInput) telInput.value = data.telephone || '';

        hideSuggestions(container);

        // Animation de confirmation
        const parentDiv = container.closest('.bg-gray-50');
        if (parentDiv) {
            parentDiv.classList.add('ring-2', 'ring-green-400', 'ring-opacity-50');
            setTimeout(() => {
                parentDiv.classList.remove('ring-2', 'ring-green-400', 'ring-opacity-50');
            }, 500);
        }

        // Focus sur le champ suivant vide
        if (emailInput && !data.email) {
            emailInput.focus();
        } else if (telInput && !data.telephone) {
            telInput.focus();
        }
    }

    // ========================================
    // Navigation clavier
    // ========================================
    function navigateSuggestions(direction) {
        if (currentSuggestions.length === 0 || !currentContainer) return;

        const suggestionsDiv = currentContainer.querySelector('.responsable-suggestions');
        const items = suggestionsDiv.querySelectorAll('.responsable-suggestion-item');

        // Désactiver l'élément précédent
        items.forEach(item => item.classList.remove('active'));

        // Calculer le nouvel index (-1 = nouvelle saisie, 0+ = suggestions)
        if (direction === 'down') {
            activeSuggestionIndex = Math.min(activeSuggestionIndex + 1, currentSuggestions.length - 1);
        } else {
            activeSuggestionIndex = Math.max(activeSuggestionIndex - 1, -1);
        }

        // Activer le nouvel élément (index + 1 car "nouvelle saisie" est en premier)
        const activeItem = items[activeSuggestionIndex + 1];
        if (activeItem) {
            activeItem.classList.add('active');
            activeItem.scrollIntoView({ block: 'nearest' });
        }
    }

    function selectActiveSuggestion(container, type) {
        if (activeSuggestionIndex === -1) {
            // "Nouvelle saisie" sélectionnée
            hideSuggestions(container);
            return;
        }

        if (activeSuggestionIndex >= 0 && activeSuggestionIndex < currentSuggestions.length) {
            selectSuggestion(container, type, currentSuggestions[activeSuggestionIndex]);
        }
    }

    // ========================================
    // Initialisation des champs
    // ========================================
    const autocompleteContainers = document.querySelectorAll('[data-responsable-autocomplete]');

    autocompleteContainers.forEach(container => {
        const type = container.dataset.responsableAutocomplete;
        const input = container.querySelector('.responsable-autocomplete-input');

        if (!input) return;

        let debounceTimer;

        // Événement de saisie
        input.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);

            const query = this.value;

            debounceTimer = setTimeout(() => {
                if (query.length >= CONFIG.minChars) {
                    // Recherche dans la liste unique (pas par type)
                    const suggestions = searchResponsables(query);
                    showSuggestions(container, suggestions, type);
                } else {
                    hideSuggestions(container);
                }
            }, CONFIG.debounceDelay);
        });

        // Focus sur le champ - afficher toutes les suggestions si vide
        input.addEventListener('focus', function() {
            const query = this.value;
            if (query.length >= CONFIG.minChars) {
                const suggestions = searchResponsables(query);
                showSuggestions(container, suggestions, type);
            } else if (query.length === 0 && responsablesCache.length > 0) {
                // Afficher les premiers responsables si le champ est vide
                showSuggestions(container, responsablesCache.slice(0, 5), type);
            }
        });

        // Navigation clavier
        input.addEventListener('keydown', function(e) {
            const suggestionsDiv = container.querySelector('.responsable-suggestions');
            const isVisible = suggestionsDiv && !suggestionsDiv.classList.contains('hidden');

            if (!isVisible) return;

            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    navigateSuggestions('down');
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    navigateSuggestions('up');
                    break;

                case 'Enter':
                    if (currentSuggestions.length > 0 || activeSuggestionIndex === -1) {
                        e.preventDefault();
                        selectActiveSuggestion(container, type);
                    }
                    break;

                case 'Escape':
                    e.preventDefault();
                    hideSuggestions(container);
                    break;

                case 'Tab':
                    hideSuggestions(container);
                    break;
            }
        });
    });

    // Fermer au clic extérieur
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-responsable-autocomplete]')) {
            hideAllSuggestions();
        }
    });

    // ========================================
    // Utilitaires
    // ========================================
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

});
</script>
