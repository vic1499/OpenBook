<?php
namespace OpenBook\Application;

function validateCsrf(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    $body = json_decode(file_get_contents('php://input'), true);
    $bodyToken = is_array($body) && isset($body['_csrf']) ? $body['_csrf'] : null;

    $token = $headerToken ?? $bodyToken ?? null;
    if (!$token || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }
}
