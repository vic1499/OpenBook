<?php
namespace OpenBook\Application;

use OpenBook\Infrastructure\UserRepository;
use OpenBook\Domain\User;

class AuthService
{
    private UserRepository $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function register(string $name, string $email, string $password): array
    {
        $existing = $this->repo->findByEmail($email);
        if ($existing) {
            return ['error' => 'El correo ya está registrado'];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $user = new User(null, $name, $email, $hash);
        $this->repo->create($user);

        return ['success' => true, 'message' => 'Usuario registrado correctamente'];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->repo->findByEmail($email); // <- aquí estaba el error

        if (!$user || !password_verify($password, $user->passwordHash)) {
            return ['success' => false, 'error' => 'Credenciales incorrectas'];
        }

        // Login correcto, devolver info del usuario
        return [
            'success' => true,
            'message' => 'Inicio de sesión correcto',
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ];
    }



    public function logout(): void
    {
        session_destroy();
    }

    public function getCurrentUser(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email']
        ];
    }
}
