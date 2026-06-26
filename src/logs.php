<?php

require_once __DIR__ . '/db.php';

function insertar_log(
    string $nickname,
    ?int $peso,
    ?string $hash,
    ?string $fecha_archivo,
    ?string $ruta_original,
    ?string $ip,
    ?string $ua,
    ?string $idioma,
    string $estado,
    ?string $detalle = null
): void {
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO logs_carga (nickname_usuario, peso_bytes, hash_md5, fecha_archivo, ruta_original, ip_origen, user_agent, idioma, estado, detalle) VALUES (:nickname, :peso, :hash, :fecha_archivo, :ruta_original, :ip, :ua, :idioma, :estado, :detalle)');

    $fecha_ts = null;
    if ($fecha_archivo) {
        $fecha_ts = date('Y-m-d H:i:s', (int)$fecha_archivo);
    }

    $stmt->execute([
        ':nickname' => $nickname,
        ':peso' => $peso,
        ':hash' => $hash,
        ':fecha_archivo' => $fecha_ts,
        ':ruta_original' => $ruta_original,
        ':ip' => $ip,
        ':ua' => $ua,
        ':idioma' => $idioma,
        ':estado' => $estado,
        ':detalle' => $detalle,
    ]);
}

function obtener_logs(int $limite = 100): array {
    $db = getDB();
    $limite = min(max($limite, 1), 500);
    return $db->query('SELECT * FROM logs_carga ORDER BY fecha_hora DESC LIMIT ' . $limite)->fetchAll();
}
