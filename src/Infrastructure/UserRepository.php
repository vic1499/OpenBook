<?php
namespace OpenBook\Infrastructure;

use OpenBook\Domain\User;
use OpenBook\Infrastructure\Database\Connection;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
        $this->init(); // Crear tabla si no existe
    }

    /**
     * Crea la tabla users si no existe
     */
    private function init(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new User($row['id'], $row['name'], $row['email'], $row['password_hash']);
    }

    public function create(User $user): User
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
        );
        $stmt->execute([
            'name' => $user->name,
            'email' => $user->email,
            'password_hash' => $user->passwordHash
        ]);

        $user->id = (int) $this->db->lastInsertId();
        return $user;
    }
}
