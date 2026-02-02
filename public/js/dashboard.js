/**
 * dashboard.js
 * Handles UI interactions for the Velzon-style dashboard
 */

document.addEventListener('DOMContentLoaded', function () {
    const hamburgerBtn = document.getElementById('topnav-hamburger-menu');
    const overlay = document.getElementById('vertical-overlay');
    const body = document.body;

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function () {
            body.classList.toggle('vertical-sidebar-enable');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            body.classList.remove('vertical-sidebar-enable');
        });
    }

    // Close sidebar when clicking on a menu link (on mobile)
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 992) {
                body.classList.remove('vertical-sidebar-enable');
            }
        });
    });
});
