<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/logs.php';
require_once __DIR__ . '/rutas.php';

function procesar_upload(array $archivo, string $client_md5, ?string $fecha_archivo, ?string $ruta_original, string $plaza): array {
    $nombre_original = $archivo['name'];
    $tmp_path = $archivo['tmp_name'];
    $peso_bytes = $archivo['size'];

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $idioma = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
    $nickname = $_SESSION['user']['nickname'] ?? 'desconocido';

    if ($forwarded) {
        $ip = $forwarded;
    }

    if (!preg_match('/^co_ofes\.dbf$/i', $nombre_original)) {
        insertar_log($nickname, $peso_bytes, null, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_nombre', 'Nombre inválido: ' . $nombre_original, $plaza);
        return ['exito' => false, 'error' => 'El archivo debe llamarse co_ofes.dbf'];
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        insertar_log($nickname, $peso_bytes, null, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_archivo', 'Error al recibir archivo: ' . $archivo['error'], $plaza);
        return ['exito' => false, 'error' => 'Error al recibir el archivo'];
    }

    $server_md5 = md5_file($tmp_path);

    if ($server_md5 !== $client_md5) {
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_hash', 'Hash no coincide. Cliente: ' . $client_md5 . ' / Servidor: ' . $server_md5, $plaza);
        return ['exito' => false, 'error' => 'El archivo está corrupto o no se cargó correctamente (MD5 no coincide)'];
    }

    $rutas = obtener_rutas_por_plaza($plaza);

    if (empty($rutas)) {
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_ruta', 'No hay rutas habilitadas para la plaza: ' . $plaza, $plaza);
        return ['exito' => false, 'error' => 'No hay rutas habilitadas para esta plaza'];
    }

    $resultados_rutas = [];
    $exito_en_alguna = false;

    foreach ($rutas as $r) {
        $ruta = rtrim($r['ruta'], '/') . '/';
        $destino = $ruta . 'CO_OFES.DBF';

        if (!is_dir($ruta)) {
            $resultados_rutas[] = ['ruta' => $ruta, 'exito' => false, 'error' => 'Directorio no existe'];
            continue;
        }

        if (!is_writable($ruta)) {
            $resultados_rutas[] = ['ruta' => $ruta, 'exito' => false, 'error' => 'Permisos denegados'];
            continue;
        }

        if (file_exists($destino)) {
            $bak = $destino . '.BAK';
            if (file_exists($bak)) {
                unlink($bak);
            }
            rename($destino, $bak);
        }

        if (copy($tmp_path, $destino)) {
            chmod($destino, 0774);
            $resultados_rutas[] = ['ruta' => $ruta, 'exito' => true];
            $exito_en_alguna = true;
        } else {
            $resultados_rutas[] = ['ruta' => $ruta, 'exito' => false, 'error' => 'Error al copiar'];
        }
    }

    $detalle = json_encode($resultados_rutas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if ($exito_en_alguna) {
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'exito', $detalle, $plaza);
        return ['exito' => true, 'rutas' => $resultados_rutas];
    } else {
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_ruta', $detalle, $plaza);
        return ['exito' => false, 'error' => 'No se pudo copiar el archivo a ninguna ruta', 'rutas' => $resultados_rutas];
    }
}

function restaurar_copia(int $log_id, string $ruta): array {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM logs_carga WHERE id = :id');
    $stmt->execute([':id' => $log_id]);
    $entry = $stmt->fetch();

    if (!$entry) {
        return ['exito' => false, 'error' => 'Entrada de log no encontrada'];
    }

    $ruta = rtrim($ruta, '/') . '/';
    $bak = $ruta . 'CO_OFES.DBF.BAK';
    $destino = $ruta . 'CO_OFES.DBF';

    if (!file_exists($bak)) {
        return ['exito' => false, 'error' => 'No hay respaldo en ' . $ruta];
    }

    if (!is_dir($ruta)) {
        return ['exito' => false, 'error' => 'Directorio no existe: ' . $ruta];
    }

    if (!is_writable($ruta)) {
        return ['exito' => false, 'error' => 'Permisos denegados en ' . $ruta];
    }

    if (file_exists($destino)) {
        unlink($destino);
    }

    if (copy($bak, $destino)) {
        chmod($destino, 0774);

        $nickname = $_SESSION['user']['nickname'] ?? 'desconocido';
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $idioma = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;

        insertar_log(
            $nickname,
            filesize($destino),
            md5_file($destino),
            null,
            $entry['ruta_original'],
            $ip,
            $ua,
            $idioma,
            'restaurado',
            'Restaurado desde log #' . $log_id . ' en ' . $ruta,
            $entry['plaza_nombre']
        );

        return ['exito' => true, 'ruta' => $ruta];
    }

    return ['exito' => false, 'error' => 'Error al copiar el respaldo en ' . $ruta];
}
