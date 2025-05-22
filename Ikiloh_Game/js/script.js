$(document).ready(function() {
    // Function to display games
    function displayGames(games) {
        const gamesList = $('#games-list');
        gamesList.empty();

        games.forEach(game => {
            const gameCard = `
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="${game.thumb}" class="card-img-top" alt="${game.title}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">${game.title}</h5>
                            <p class="card-text">Store: ${game.storeName}</p>
                            <h5 class="card-title text-primary">$${game.salePrice}</h5>
                            <p class="card-text"><small class="text-muted">Normal Price: $${game.normalPrice}</small></p>
                            <a href="${game.dealLink}" class="btn btn-primary" target="_blank">View Deal</a>
                        </div>
                    </div>
                </div>
            `;
            gamesList.append(gameCard);
        });
    }

    // Function to load initial deals
    function loadInitialDeals() {
        $('#search-input').val(''); // Clear search input
        $.ajax({
            url: 'https://www.cheapshark.com/api/1.0/deals',
            type: 'GET',
            dataType: 'json',
            data: {
                'limit': 12
            },
            success: function(data) {
                displayGames(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
                $('#games-list').html('<div class="col-12 text-center"><p class="text-danger">Error loading games. Please try again later.</p></div>');
            }
        });
    }

    // Search functionality
    $('#search-button').on('click', function() {
        const searchQuery = $('#search-input').val();
        
        $.ajax({
            url: 'https://www.cheapshark.com/api/1.0/deals',
            type: 'GET',
            dataType: 'json',
            data: {
                'title': searchQuery,
                'limit': 12
            },
            success: function(data) {
                displayGames(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
                $('#games-list').html('<div class="col-12 text-center"><p class="text-danger">Error loading games. Please try again later.</p></div>');
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
    $('.nav-link[href="#"]').on('click', function(e) {
        e.preventDefault();
        loadInitialDeals();
    });

    // Reset page when clicking logo
    $('.navbar-brand').on('click', function(e) {
        e.preventDefault();
        loadInitialDeals();
    });

    // Load initial deals on page load
    loadInitialDeals();
}); 