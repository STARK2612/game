// Add your JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    // Logout user after 10 minutes of inactivity
    let logoutTimer = setTimeout(function() {
        window.location.href = 'backend/logout.php';
    }, 600000);

    // Reset the timer on user interaction
    document.addEventListener('mousemove', resetLogoutTimer);
    document.addEventListener('keydown', resetLogoutTimer);

    function resetLogoutTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(function() {
            window.location.href = 'backend/logout.php';
        }, 600000);
    }
});
