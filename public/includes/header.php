<?php
// Session check is expected to be done in the main file
if (!isset($userName)) {
    $userName = $_SESSION['user_name'] ?? 'Usuario';
}
?>
<header class="topbar">
    <div class="d-flex align-items-center">
        <button type="button" class="hamburger-button" id="topnav-hamburger-menu">
            <i class="bi bi-list"></i>
        </button>
        <h4 class="mb-0">
            <?= $pageTitle ?? 'Gestión de Libros' ?>
        </h4>
    </div>
    <div class="user-info">
        <span class="text-muted d-none d-sm-inline">Bienvenido,</span>
        <strong>
            <?= htmlspecialchars($userName) ?>
        </strong>
    </div>
</header>
<!-- Overlay for mobile sidebar -->
<div class="vertical-overlay" id="vertical-overlay"></div>