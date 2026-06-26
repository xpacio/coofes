<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/logs.php';

function procesar_upload(array $archivo, string $client_md5, ?string $fecha_archivo, ?string $ruta_original): array {
    global $rutas_destino;

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
        insertar_log($nickname, $peso_bytes, null, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_nombre', 'Nombre inválido: ' . $nombre_original);
        return ['exito' => false, 'error' => 'El archivo debe llamarse co_ofes.dbf'];
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        insertar_log($nickname, $peso_bytes, null, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_archivo', 'Error al recibir archivo: ' . $archivo['error']);
        return ['exito' => false, 'error' => 'Error al recibir el archivo'];
    }

    $server_md5 = md5_file($tmp_path);

    if ($server_md5 !== $client_md5) {
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_hash', 'Hash no coincide. Cliente: ' . $client_md5 . ' / Servidor: ' . $server_md5);
        return ['exito' => false, 'error' => 'El archivo está corrupto o no se cargó correctamente (MD5 no coincide)'];
    }

    if (empty($rutas_destino)) {
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_ruta', 'No hay rutas destino configuradas');
        return ['exito' => false, 'error' => 'No hay rutas destino configuradas'];
    }

    $resultados_rutas = [];
    $exito_en_alguna = false;

    $nombre_destino = 'CO_OFES.DBF.DEMO';  // ← quitar .DEMO para pasar a produccion

    foreach ($rutas_destino as $ruta) {
        $ruta = rtrim($ruta, '/') . '/';
        $destino = $ruta . $nombre_destino;

        if (!is_dir($ruta)) {
            $resultados_rutas[] = ['ruta' => $ruta, 'exito' => false, 'error' => 'Directorio no existe'];
            continue;
        }

        if (!is_writable($ruta)) {
            $resultados_rutas[] = ['ruta' => $ruta, 'exito' => false, 'error' => 'Permisos denegados'];
            continue;
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
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'exito', $detalle);
        return ['exito' => true, 'rutas' => $resultados_rutas];
    } else {
        insertar_log($nickname, $peso_bytes, $server_md5, $fecha_archivo, $ruta_original, $ip, $ua, $idioma, 'error_ruta', $detalle);
        return ['exito' => false, 'error' => 'No se pudo copiar el archivo a ninguna ruta', 'rutas' => $resultados_rutas];
    }
}
