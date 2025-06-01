$(document).ready(function() {
    // Function to show/hide loading screen
    function toggleLoadingScreen(show) {
        if (show) {
            $('#loading-screen').fadeIn(300);
        } else {
            $('#loading-screen').fadeOut(300);
        }
    }

    // Show loading screen initially
    toggleLoadingScreen(true);

    // Hide loading screen when all content is loaded
    $(window).on('load', function() {
        // Add a small delay to ensure smooth transition
        setTimeout(function() {
            toggleLoadingScreen(false);
        }, 1000);
    });

    // Show loading screen when navigating between sections
    $('.nav-link').on('click', function(e) {
        if ($(this).attr('href').startsWith('#')) {
            toggleLoadingScreen(true);
            setTimeout(function() {
                toggleLoadingScreen(false);
            }, 500);
        }
    });

    // Image Error Handler
    document.querySelectorAll('img').forEach(function(img) {
        img.onerror = function() {
            this.onerror = null; // Prevent infinite loop
            this.src = 'img/profile1.png';
        };
    });
}); 