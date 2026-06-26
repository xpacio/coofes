<?php

require_once __DIR__ . '/db.php';

function iniciar_sesion(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}

function login(string $nickname, string $password): array {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE nickname = :nickname');
    $stmt->execute([':nickname' => $nickname]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['exito' => false, 'error' => 'Credenciales inválidas'];
    }

    if (!$user['activo']) {
        return ['exito' => false, 'error' => 'Usuario deshabilitado'];
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'nickname' => $user['nickname'],
        'nombre' => $user['nombre'],
        'es_admin' => $user['es_admin'],
    ];

    return ['exito' => true];
}

function logout(): void {
    $_SESSION = [];
    session_destroy();
}

function require_auth(): void {
    iniciar_sesion();
    if (empty($_SESSION['user'])) {
        header('Location: ?action=login');
        exit;
    }
}

function require_admin(): void {
    require_auth();
    if (!$_SESSION['user']['es_admin']) {
        die('Acceso denegado');
    }
}

function csrf_token(): string {
    return $_SESSION['csrf_token'] ?? '';
}

function verify_csrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function obtener_usuario_actual(): ?array {
    return $_SESSION['user'] ?? null;
}
