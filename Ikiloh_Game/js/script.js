$(document).ready(function() {
    // Function to display error message
    function displayError(message, isFatal = false) {
        const errorHtml = `
            <div class="no-games-message">
                <p>${message === 'No games found. Please try a different search term.' ? 'No games found :(' : message}</p>
                ${!isFatal ? '<button class="btn btn-navy mt-3" onclick="location.reload()">Back</button>' : ''}
            </div>
        `;
        $('#games-list-body').html(`
            <tr>
                <td colspan="9" class="text-center">
                    ${errorHtml}
                </td>
            </tr>
        `);
    }

    // Function to display games in a table
    function displayGames(games) {
        const gamesListBody = $('#games-list-body');
        gamesListBody.empty();

        try {
            games.forEach(game => {
                const gameRow = `
                    <tr>
                        <td>${game.storeName || 'Unknown Store'}</td>
                        <td>${Math.round(parseFloat(game.savings || 0))}%</td>
                        <td>$${parseFloat(game.salePrice || 0).toFixed(2)} <small class="price-original">$${parseFloat(game.normalPrice || 0).toFixed(2)}</small></td>
                        <td><img src="${game.thumb || 'asset/placeholder.jpg'}" alt="${game.title || 'Game'}" class="game-thumb" onerror="this.src='asset/placeholder.jpg'"></td>
                        <td><a href="https://www.cheapshark.com/redirect?dealID=${game.dealID || ''}" target="_blank" class="game-title">${game.title || 'Unknown Title'}</a></td>
                        <td>${game.dealRating || 'N/A'}</td>
                        <td>${game.releaseDate > 0 ? new Date(game.releaseDate * 1000).toLocaleDateString() : '?'}</td>
                        <td>${game.metacriticScore !== '0' ? game.metacriticScore : '?'}</td>
                        <td>${timeSince(game.lastChange * 1000)} ago</td>
                    </tr>
                `;
                gamesListBody.append(gameRow);
            });
        } catch (error) {
            console.error('Error displaying games:', error);
            displayError('Error displaying games. Please try again.');
        }
    }

    // Function to calculate time since
    function timeSince(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes";
        return Math.floor(seconds) + " seconds";
    }

    // Function to process store data
    function processStoreData(data, storesData) {
        const storeMap = {};
        storesData.forEach(store => {
            if (store.isActive) {
                storeMap[store.storeID] = { name: store.storeName };
            }
        });

        data.forEach(game => {
            if (storeMap[game.storeID]) {
                game.storeName = storeMap[game.storeID].name;
            } else {
                game.storeName = 'Unknown Store';
            }
        });

        displayGames(data);
    }

    // Function to handle store data error
    function handleStoreError(data, error) {
        console.error('Error fetching stores:', error);
        data.forEach(game => { game.storeName = `Store ID: ${game.storeID}`; });
        displayGames(data);
    }

    // Function to handle API error
    function handleApiError(xhr, status, error, context) {
        console.error(`Error ${context}:`, error);
        let errorMessage = `Error ${context}. `;
        
        if (status === 'timeout') {
            errorMessage += 'Request timed out. Please check your internet connection.';
        } else if (status === 'error') {
            errorMessage += 'Server error. Please try again later.';
        } else if (xhr.status === 404) {
            errorMessage += context === 'searching games' ? 'No results found for your search.' : 'API endpoint not found.';
        } else if (xhr.status === 403) {
            errorMessage += 'Access denied. Please try again later.';
        } else {
            errorMessage += 'Please try again later.';
        }
        
        displayError(errorMessage, false);
    }

    // Function to fetch store data
    function fetchStoreData(data) {
        $.ajax({
            url: 'https://www.cheapshark.com/api/1.0/stores',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(storesData) {
                processStoreData(data, storesData);
            },
            error: function(xhr, status, error) {
                handleStoreError(data, error);
            }
        });
    }

    // Function to load initial deals
    function loadInitialDeals() {
        $('#search-input').val('');
        $.ajax({
            url: 'https://www.cheapshark.com/api/1.0/deals',
            type: 'GET',
            dataType: 'json',
            data: { 'limit': 60 },
            timeout: 10000,
            success: function(data) {
                fetchStoreData(data);
            },
            error: function(xhr, status, error) {
                handleApiError(xhr, status, error, 'loading games');
            }
        });
    }

    // Search functionality
    $('#search-button').on('click', function() {
        const searchQuery = $('#search-input').val();
        
        if (!searchQuery) {
            loadInitialDeals();
            return;
        }

        $.ajax({
            url: 'https://www.cheapshark.com/api/1.0/deals',
            type: 'GET',
            dataType: 'json',
            data: {
                'title': searchQuery,
                'limit': 60
            },
            timeout: 10000,
            success: function(data) {
                if (!data || data.length === 0) {
                    displayError('No games found. Please try a different search term.');
                    return;
                }
                fetchStoreData(data);
            },
            error: function(xhr, status, error) {
                handleApiError(xhr, status, error, 'searching games');
            }
        });
    });

    // Search on Enter key press
    $('#search-input').on('keypress', function(e) {
        if (e.key === 'Enter') {
            $('#search-button').click();
        }
    });

    // Reset page when clicking Games navbar link
    $('.navbar-brand').on('click', function(e) {
        e.preventDefault();
        loadInitialDeals();
    });

    // Load initial deals on page load
    loadInitialDeals();
}); 