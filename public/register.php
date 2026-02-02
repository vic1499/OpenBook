<?php
session_name('openbook_sess');
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario | OpenBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow-lg p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Crear cuenta</h3>

        <form id="registerForm">
            <input type="hidden" id="csrf" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrar</button>
        </form>

        <div id="result" class="mt-3 text-center"></div>
    </div>

    <script src="js/register.js" defer></script>

</body>

</html>