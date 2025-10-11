<?php
session_name('openbook_sess');
session_start();

// Redirigir a login si no hay sesión activa
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

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <title>Bienvenido al Sistema OpenBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="header-bar">
        <div>
            📖 Bienvenido a <span style="color: #0648abff; font-weight: bold;">OpenBooks</span>,
            <strong><?= htmlspecialchars($userName) ?></strong>
        </div>
        <div>
            <a href="logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>


    <div class="container mt-4">
        <div class="search-box mb-4">
            <h2>Buscar libros</h2>
            <input type="text" class="form-control" id="searchTerm" onkeyup="searchLive()"
                placeholder="Busca por título o autor...">
        </div>

        <form id="createForm" class="mb-4">
            <h2>Introduzca los datos del libro:</h2>
            <input type="hidden" id="bookId">
            <input type="text" class="form-control mb-2" placeholder="Título" id="title" required>
            <input type="text" class="form-control mb-2" placeholder="Autor" id="author" required>
            <input type="text" class="form-control mb-2" placeholder="ISBN" id="isbn" required>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>

        <h2>Lista de libros</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Descripción</th>
                    <th>ISBN</th>
                    <th>Portada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="booksList"></tbody>
        </table>
    </div>

    <script src="js/app.js?ts=<?= time() ?>" defer></script>
</body>

</html>