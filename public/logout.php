<?php
// logout.php
session_name('openbook_sess');
session_start();

// Limpiar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: login.php');
exit;
