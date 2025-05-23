$(document).ready(function() {
    // Function to display games in a table
    function displayGames(games) {
        const gamesListBody = $('#games-list-body');
        gamesListBody.empty();

        if (games.length === 0) {
            gamesListBody.html('<tr><td colspan="8" class="text-center">No games found.</td></tr>');
            return;
        }

        games.forEach(game => {
            const gameRow = `
                <tr>
                    <td>${game.storeName}</td>
                    <td>${Math.round(parseFloat(game.savings))}%</td>
                    <td>$${parseFloat(game.salePrice).toFixed(2)} <small class="text-muted"><s>$${parseFloat(game.normalPrice).toFixed(2)}</s></small></td>
                    <td><img src="${game.thumb}" style="width: 80px; height: auto;"></td>
                    <td><a href="https://www.cheapshark.com/redirect?dealID=${game.dealID}" target="_blank">${game.title}</a></td>
                    <td>${game.dealRating}</td>
                    <td>${game.releaseDate > 0 ? new Date(game.releaseDate * 1000).toLocaleDateString() : '?'}</td>
                    <td>${game.metacriticScore !== '0' ? game.metacriticScore : '?'}</td>
                    <td>${timeSince(game.lastChange * 1000)} ago</td>
                </tr>
            `;
            gamesListBody.append(gameRow);
        });
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

    // Function to load initial deals and store info
    function loadInitialDeals() {
        $('#search-input').val(''); // Clear search input
        $.ajax({
            url: 'https://www.cheapshark.com/api/1.0/deals',
            type: 'GET',
            dataType: 'json',
            data: {
                'limit': 60 // show deals initially
            },
            success: function(data) {
                 // Fetch store information
                 $.ajax({
                    url: 'https://www.cheapshark.com/api/1.0/stores',
                    type: 'GET',
                    dataType: 'json',
                    success: function(storesData) {
                        // Create a map of store IDs to store names
                        const storeMap = {};
                        storesData.forEach(store => {
                             // Only include active stores
                            if (store.isActive) {
                                storeMap[store.storeID] = { name: store.storeName };
                            }
                        });

                        // Update games data with store name
                        data.forEach(game => {
                            if (storeMap[game.storeID]) {
                                game.storeName = storeMap[game.storeID].name;
                            } else {
                                game.storeName = 'Unknown Store';
                            }
                        });

                        displayGames(data); // Display games with store info
                    },
                    error: function(xhr, status, error) {
                         console.error('Error fetching stores:', error);
                         // Display games without store names if fetching stores fails
                         data.forEach(game => { game.storeName = `Store ID: ${game.storeID}`; });
                         displayGames(data);
                    }
                 });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
                $('#games-list-body').html('<tr><td colspan="8" class="text-center"><p class="text-danger">Error loading games. Please try again later.</p></td></tr>');
            }
        });
    }

    // Search functionality
    $('#search-button').on('click', function() {
        const searchQuery = $('#search-input').val();
        
        if (!searchQuery) {
             loadInitialDeals(); // Load initial deals if search is empty
             return;
        }

        $.ajax({
            url: 'https://www.cheapshark.com/api/1.0/deals',
            type: 'GET',
            dataType: 'json',
            data: {
                'title': searchQuery,
                'limit': 60 // search results
            },
            success: function(data) {
                // Fetch store information
                 $.ajax({
                    url: 'https://www.cheapshark.com/api/1.0/stores',
                    type: 'GET',
                    dataType: 'json',
                    success: function(storesData) {
                        // Create a map of store IDs to store names
                        const storeMap = {};
                        storesData.forEach(store => {
                             // Only include active stores
                            if (store.isActive) {
                                storeMap[store.storeID] = { name: store.storeName };
                            }
                        });

                        // Update games data with store name
                        data.forEach(game => {
                            if (storeMap[game.storeID]) {
                                game.storeName = storeMap[game.storeID].name;
                            } else {
                                game.storeName = 'Unknown Store';
                            }
                        });

                        displayGames(data); // Display games with store info
                    },
                    error: function(xhr, status, error) {
                         console.error('Error fetching stores:', error);
                         // Display games without store names if fetching stores fails
                         data.forEach(game => { game.storeName = `Store ID: ${game.storeID}`; });
                         displayGames(data);
                    }
                 });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
                $('#games-list-body').html('<tr><td colspan="8" class="text-center"><p class="text-danger">Error loading games. Please try again later.</p></td></tr>');
            }
        });
    });

    // Search on Enter key press
    $('#search-input').on('keypress', function(e) {
        if (e.key === 'Enter') {
            $('#search-button').click();
        }
    });

    // Reset page when clicking Games navbar link (now just logo)
    $('.navbar-brand').on('click', function(e) {
        e.preventDefault();
        loadInitialDeals();
    });

    // Load initial deals on page load
    loadInitialDeals();
}); 