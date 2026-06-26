<?php

require_once __DIR__ . '/db.php';

function listar_rutas(): array {
    return getDB()->query('SELECT * FROM rutas ORDER BY plaza, id')->fetchAll();
}

function obtener_plazas(): array {
    return getDB()->query("SELECT DISTINCT plaza FROM rutas WHERE habilitado = TRUE ORDER BY plaza")->fetchAll(PDO::FETCH_COLUMN);
}

function obtener_rutas_por_plaza(string $plaza): array {
    $stmt = getDB()->prepare('SELECT * FROM rutas WHERE plaza = :plaza AND habilitado = TRUE ORDER BY id');
    $stmt->execute([':plaza' => $plaza]);
    return $stmt->fetchAll();
}

function crear_ruta(string $ruta, string $plaza): array {
    $db = getDB();
    try {
        $stmt = $db->prepare('INSERT INTO rutas (ruta, plaza) VALUES (:ruta, :plaza)');
        $stmt->execute([':ruta' => $ruta, ':plaza' => $plaza]);
        return ['exito' => true];
    } catch (PDOException $e) {
        if ($e->getCode() == 23505) {
            return ['exito' => false, 'error' => 'La ruta ya existe'];
        }
        throw $e;
    }
}

function editar_ruta(int $id, string $ruta, string $plaza): array {
    $db = getDB();
    try {
        $stmt = $db->prepare('UPDATE rutas SET ruta = :ruta, plaza = :plaza WHERE id = :id');
        $stmt->execute([':ruta' => $ruta, ':plaza' => $plaza, ':id' => $id]);
        return ['exito' => true];
    } catch (PDOException $e) {
        if ($e->getCode() == 23505) {
            return ['exito' => false, 'error' => 'La ruta ya existe'];
        }
        throw $e;
    }
}

function toggle_ruta(int $id): void {
    getDB()->prepare('UPDATE rutas SET habilitado = NOT habilitado WHERE id = :id')->execute([':id' => $id]);
}

function obtener_ruta_por_id(int $id): ?array {
    $stmt = getDB()->prepare('SELECT * FROM rutas WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function obtener_rutas_con_bak(): array {
    $rutas = listar_rutas();
    $con_bak = [];
    foreach ($rutas as $r) {
        $dir = rtrim($r['ruta'], '/') . '/';
        $bak = $dir . 'CO_OFES.DBF.BAK';
        if (!file_exists($bak)) continue;
        $dbf = $dir . 'CO_OFES.DBF';
        $r['bak_md5'] = md5_file($bak);
        $r['dbf_md5'] = file_exists($dbf) ? md5_file($dbf) : null;
        $r['restorable'] = ($r['dbf_md5'] === null || $r['bak_md5'] !== $r['dbf_md5']);
        $con_bak[] = $r;
    }
    return $con_bak;
}
