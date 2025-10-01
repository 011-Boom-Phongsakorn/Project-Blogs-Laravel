// Search functionality with auto-complete and live search
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = document.querySelectorAll('[data-search-input]');

    searchInputs.forEach(input => {
        let searchTimeout;
        let resultsContainer;

        // Create results container if it doesn't exist
        if (input.dataset.liveSearch === 'true') {
            resultsContainer = document.createElement('div');
            resultsContainer.className = 'absolute z-50 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-b-lg shadow-lg max-h-96 overflow-y-auto hidden';
            resultsContainer.style.top = '100%';

            // Position the results container
            const wrapper = input.closest('.relative') || input.parentNode;
            if (!input.closest('.relative')) {
                wrapper.classList.add('relative');
            }
            wrapper.appendChild(resultsContainer);
        }

        // Live search functionality
        input.addEventListener('input', function(e) {
            const query = e.target.value.trim();

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (!resultsContainer) return;

            if (query.length < 2) {
                resultsContainer.classList.add('hidden');
                resultsContainer.innerHTML = '';
                return;
            }

            // Debounce search requests
            searchTimeout = setTimeout(() => {
                performLiveSearch(query, resultsContainer);
            }, 300);
        });

        // Handle keyboard navigation
        input.addEventListener('keydown', function(e) {
            if (!resultsContainer) return;

            const activeItem = resultsContainer.querySelector('.search-result-item.active');
            const items = resultsContainer.querySelectorAll('.search-result-item');

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    if (activeItem) {
                        activeItem.classList.remove('active');
                        const next = activeItem.nextElementSibling;
                        if (next && next.classList.contains('search-result-item')) {
                            next.classList.add('active');
                        } else if (items.length > 0) {
                            items[0].classList.add('active');
                        }
                    } else if (items.length > 0) {
                        items[0].classList.add('active');
                    }
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    if (activeItem) {
                        activeItem.classList.remove('active');
                        const prev = activeItem.previousElementSibling;
                        if (prev && prev.classList.contains('search-result-item')) {
                            prev.classList.add('active');
                        } else if (items.length > 0) {
                            items[items.length - 1].classList.add('active');
                        }
                    } else if (items.length > 0) {
                        items[items.length - 1].classList.add('active');
                    }
                    break;

                case 'Enter':
                    e.preventDefault();
                    if (activeItem) {
                        const link = activeItem.querySelector('a');
                        if (link) {
                            window.location.href = link.href;
                        }
                    } else {
                        // Submit the search form
                        const form = input.closest('form');
                        if (form) {
                            form.submit();
                        }
                    }
                    break;

                case 'Escape':
                    resultsContainer.classList.add('hidden');
                    resultsContainer.innerHTML = '';
                    input.blur();
                    break;
            }
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (resultsContainer && !input.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('hidden');
            }
        });
    });

    // Perform live search
    function performLiveSearch(query, resultsContainer) {
        const loadingHTML = `
            <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                <div class="animate-spin inline-block w-4 h-4 border-2 border-gray-300 dark:border-gray-600 border-t-blue-500 rounded-full mr-2"></div>
                Searching...
            </div>
        `;

        resultsContainer.innerHTML = loadingHTML;
        resultsContainer.classList.remove('hidden');

        // Make search request to our suggestions endpoint
        fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                const resultsHTML = data.map(post => `
                    <div class="search-result-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 cursor-pointer">
                        <a href="${post.url}" class="block">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">${escapeHtml(post.title)}</h4>
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>${escapeHtml(post.author)}</span>
                            </div>
                        </a>
                    </div>
                `).join('');

                const viewAllHTML = `
                    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <a href="/search?q=${encodeURIComponent(query)}"
                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                            View all results →
                        </a>
                    </div>
                `;

                resultsContainer.innerHTML = resultsHTML + viewAllHTML;
            } else {
                resultsContainer.innerHTML = `
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        <p class="text-sm">No posts found for "${escapeHtml(query)}"</p>
                        <a href="/search?q=${encodeURIComponent(query)}"
                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">
                            Try advanced search →
                        </a>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-red-500 dark:text-red-400">
                    <p class="text-sm">Search failed. Please try again.</p>
                </div>
            `;
        });
    }

    // Search suggestions functionality
    const searchSuggestions = [
        'javascript', 'php', 'laravel', 'vue.js', 'react', 'tailwind',
        'tutorial', 'guide', 'tips', 'best practices', 'development',
        'web design', 'css', 'html', 'database', 'api'
    ];

    // Add search suggestions on focus
    searchInputs.forEach(input => {
        if (input.dataset.showSuggestions === 'true') {
            input.addEventListener('focus', function() {
                if (this.value.trim() === '') {
                    showSearchSuggestions(this, searchSuggestions.slice(0, 6));
                }
            });
        }
    });

    function showSearchSuggestions(input, suggestions) {
        const wrapper = input.closest('.relative') || input.parentNode;
        let suggestionsContainer = wrapper.querySelector('.search-suggestions');

        if (!suggestionsContainer) {
            suggestionsContainer = document.createElement('div');
            suggestionsContainer.className = 'search-suggestions absolute z-40 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-b-lg shadow-lg';
            suggestionsContainer.style.top = '100%';
            wrapper.appendChild(suggestionsContainer);
        }

        const suggestionsHTML = `
            <div class="p-2">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Popular topics:</p>
                <div class="flex flex-wrap gap-1">
                    ${suggestions.map(suggestion => `
                        <button type="button"
                                data-suggestion="${escapeHtml(suggestion)}"
                                class="suggestion-tag px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
                            ${escapeHtml(suggestion)}
                        </button>
                    `).join('')}
                </div>
            </div>
        `;

        suggestionsContainer.innerHTML = suggestionsHTML;
        suggestionsContainer.classList.remove('hidden');

        // Handle suggestion clicks
        suggestionsContainer.addEventListener('click', function(e) {
            const suggestionButton = e.target.closest('[data-suggestion]');
            if (suggestionButton) {
                input.value = suggestionButton.dataset.suggestion;
                suggestionsContainer.classList.add('hidden');

                // Trigger search form submission
                const form = input.closest('form');
                if (form) {
                    form.submit();
                }
            }
        });

        // Hide suggestions when clicking outside
        setTimeout(() => {
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    suggestionsContainer.classList.add('hidden');
                }
            }, { once: true });
        }, 100);
    }
});

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}