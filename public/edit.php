<?php
session_name('openbook_sess');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';
$pageTitle = 'Editar Libro';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar Libro - OpenBook</title>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="css/edit.css?ts=<?= time() ?>">
</head>

<body>
    <div id="layout-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/header.php'; ?>

            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Formulario de Edición</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="bg-light p-3 rounded text-center mb-3">
                                                <div id="coverContainer">
                                                    <img src="" alt="Portada" id="coverImage"
                                                        class="img-fluid rounded shadow" style="max-height: 350px;">
                                                </div>
                                                <div class="mt-3">
                                                    <span class="badge bg-info-subtle text-info">Vista Previa
                                                        Automática</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <form id="editForm" action="javascript:void(0);">
                                                <input type="hidden" id="id">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="title" class="form-label">Título del Libro</label>
                                                        <input type="text" id="title" class="form-control"
                                                            placeholder="Título">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="author" class="form-label">Autor</label>
                                                        <input type="text" id="author" class="form-control"
                                                            placeholder="Autor">
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="isbn" class="form-label">Código ISBN</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="bi bi-hash"></i></span>
                                                            <input type="text" id="isbn" class="form-control"
                                                                placeholder="ISBN">
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="description" class="form-label">Descripción /
                                                            Sinopsis</label>
                                                        <textarea id="description" rows="8" class="form-control"
                                                            placeholder="Escribe aquí la descripción del libro..."></textarea>
                                                        <div class="form-text text-muted">Este campo se actualiza
                                                            automáticamente si se encuentra información en Open Library.
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <div class="hstack gap-2 justify-content-end">
                                                            <button type="button" class="btn btn-soft-secondary"
                                                                onclick="window.location.href='index.php'">
                                                                <i class="bi bi-x-lg"></i> Cancelar
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-save"></i> Guardar Cambios
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/edit.js?ts=<?= time() ?>" defer></script>
</body>

</html>