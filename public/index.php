<?php
session_name('openbook_sess');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use OpenBook\Application\BookService;
use OpenBook\Infrastructure\BookRepository;
use OpenBook\Infrastructure\BookApiClient;

$repository = new BookRepository();
$apiClient = new BookApiClient();
$service = new BookService($repository, $apiClient);

$userName = $_SESSION['user_name'] ?? 'Usuario';
$pageTitle = "Bienvenido, " . htmlspecialchars($userName);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard - OpenBook</title>
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/header.php'; ?>

            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Buscador Inteligente</h4>
                                </div>
                                <div class="card-body">
                                    <div class="search-box">
                                        <input type="text" class="form-control search" id="searchTerm"
                                            onkeyup="searchLive()" placeholder="Busca por título, autor o ISBN...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12" id="booksTableSection">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Colección de Libros</h4>
                                    <div class="flex-shrink-0">
                                        <button type="button" class="btn btn-soft-primary btn-sm"
                                            onclick="window.location.href='create.php'">
                                            <i class="bi bi-plus-lg align-middle"></i> Nuevo Libro
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table
                                            class="table table-striped table-striped-columns table-hover align-middle table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Portada</th>
                                                    <th>Título y Autor</th>
                                                    <th>ISBN</th>
                                                    <th>Descripción</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="booksList"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/app.js?ts=<?= time() ?>" defer></script>
</body>

</html>