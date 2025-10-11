<?php
session_name('openbook_sess');
session_start();

require __DIR__ . '/../vendor/autoload.php';
use OpenBook\Application\AuthService;
use OpenBook\Infrastructure\UserRepository;

$authService = new AuthService(new UserRepository());

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $result = $authService->login($email, $password);

        if (!empty($result['success']) && isset($result['user'])) {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_name'] = $result['user']['name'];
            header('Location: index.php');
            exit;
        } else {
            $message = $result['error'] ?? 'Credenciales incorrectas.';
        }

    } else {
        $message = 'Por favor completa todos los campos.';
    }
}

// Redirigir si ya hay sesión activa
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OpenBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h3 class="text-center mb-4">📚 OpenBook Login</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" name="email" id="email" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">¿No tienes cuenta? <a href="register.php">Regístrate</a></small>
        </div>
    </div>
</body>

</html>