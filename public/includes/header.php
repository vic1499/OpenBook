<?php
// Session check is expected to be done in the main file
if (!isset($userName)) {
    $userName = $_SESSION['user_name'] ?? 'Usuario';
}
?>
<header class="topbar">
    <div class="d-flex align-items-center">
        <h4 class="mb-0">
            <?= $pageTitle ?? 'Gestión de Libros' ?>
        </h4>
    </div>
    <div class="user-info">
        <span class="text-muted">Bienvenido,</span>
        <strong>
            <?= htmlspecialchars($userName) ?>
        </strong>
    </div>
</header>