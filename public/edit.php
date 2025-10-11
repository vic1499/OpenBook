<?php
session_name('openbook_sess');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">
    <title>Editar libro 📘</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/edit.css?ts=<?= time() ?>">
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

    <div class="container">
        <h2>Editar libro</h2>
        <div class="edit-layout">
            <div class="cover-preview" id="coverContainer">
                <!-- Aquí se insertará la imagen del libro -->
                <img src="" alt="Portada del libro" id="coverImage">
            </div>
            <form id="editForm" class="edit-form">
                <input type="hidden" id="id">
                <label>Título</label>
                <input type="text" id="title">
                <label>Autor</label>
                <input type="text" id="author">
                <label>ISBN</label>
                <input type="text" id="isbn">
                <label>Descripción</label>
                <textarea id="description" rows="7"></textarea>
                <div class="buttons">
                    <button type="submit">Guardar cambios</button>
                    <button type="button" onclick="window.location.href='index.php'">Volver</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/edit.js" defer></script>
</body>

</html>