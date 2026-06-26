<?php

require_once __DIR__ . '/db.php';

function listar_usuarios(): array {
    $db = getDB();
    return $db->query('SELECT id, nickname, nombre, activo, es_admin FROM usuarios ORDER BY id')->fetchAll();
}

function toggle_activo(int $id): void {
    $db = getDB();
    $stmt = $db->prepare('UPDATE usuarios SET activo = NOT activo WHERE id = :id AND es_admin = FALSE');
    $stmt->execute([':id' => $id]);
}

function crear_usuario(string $nickname, string $password, string $nombre): array {
    $db = getDB();
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare('INSERT INTO usuarios (nickname, password, nombre) VALUES (:nickname, :password, :nombre)');
        $stmt->execute([
            ':nickname' => $nickname,
            ':password' => $hash,
            ':nombre' => $nombre,
        ]);
        return ['exito' => true];
    } catch (PDOException $e) {
        if ($e->getCode() == 23505) {
            return ['exito' => false, 'error' => 'El nickname ya existe'];
        }
        throw $e;
    }
}
