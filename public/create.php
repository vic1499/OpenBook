<?php
session_name('openbook_sess');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';
$pageTitle = 'Añadir Nuevo Libro';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Añadir Libro - OpenBook</title>
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/header.php'; ?>

            <div class="page-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Información del Libro</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Completa los campos para registrar un nuevo libro. El sistema
                                        intentará obtener detalles adicionales automáticamente usando el ISBN.</p>
                                    <div class="live-preview">
                                        <form id="createForm" action="javascript:void(0);">
                                            <input type="hidden" id="bookId">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div>
                                                        <label for="title" class="form-label">Título <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="title"
                                                            placeholder="Ej: Cien años de soledad" required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div>
                                                        <label for="author" class="form-label">Autor <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="author"
                                                            placeholder="Ej: Gabriel García Márquez" required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div>
                                                        <label for="isbn" class="form-label">ISBN <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="isbn"
                                                            placeholder="Ej: 9788437604947" required>
                                                        <div class="form-text">Usa el formato de 10 o 13 dígitos.</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-4">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="button" class="btn btn-light"
                                                            onclick="window.location.href='index.php'">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Registrar
                                                            Libro</button>
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

    <!-- Script reuse for creation logic -->
    <script src="js/app.js?ts=<?= time() ?>" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Set active menu item
            document.getElementById('nav-add-book').classList.add('active');
        });
    </script>
</body>

</html>