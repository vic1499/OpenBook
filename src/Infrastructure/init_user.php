<?php
require __DIR__ . '/../../vendor/autoload.php';

use OpenBook\Infrastructure\Database\Connection;

$pdo = Connection::getInstance();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
");

echo "Tabla users creada.\n";
