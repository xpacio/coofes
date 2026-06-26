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

function generar_clave_aleatoria(int $longitud = 14): string {
    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $clave = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $longitud; $i++) {
        $clave .= $chars[random_int(0, $max)];
    }
    return $clave;
}

function generar_cambiar_clave(int $user_id): array {
    $db = getDB();
    $clave = generar_clave_aleatoria();
    $hash = password_hash($clave, PASSWORD_DEFAULT);

    $stmt = $db->prepare('UPDATE usuarios SET password = :hash WHERE id = :id');
    $stmt->execute([':hash' => $hash, ':id' => $user_id]);

    return ['exito' => true, 'clave' => $clave];
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
