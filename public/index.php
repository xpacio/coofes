<?php
require_once __DIR__ . '/../src/auth.php';

iniciar_sesion();

$action = $_GET['action'] ?? 'dashboard';

$public_actions = ['login'];
$auth_actions = ['dashboard', 'logout', 'upload', 'logs', 'test', 'cambioclave', 'restaurar'];
$admin_actions = ['usuarios', 'rutas'];

if (in_array($action, $public_actions)) {
    // no auth needed
} elseif (in_array($action, $auth_actions)) {
    require_auth();
} elseif (in_array($action, $admin_actions)) {
    require_admin();
} else {
    http_response_code(404);
    die('Página no encontrada');
}

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = login($_POST['nickname'] ?? '', $_POST['password'] ?? '');
            if ($resultado['exito']) {
                header('Location: ?action=dashboard');
                exit;
            }
            $error = $resultado['error'];
        }
        require __DIR__ . '/../views/login.php';
        break;

    case 'logout':
        logout();
        header('Location: ?action=login');
        exit;

    case 'dashboard':
        require __DIR__ . '/../views/dashboard.php';
        break;

    case 'usuarios':
        require_once __DIR__ . '/../src/users.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            toggle_activo((int)$_POST['toggle_id']);
            header('Location: ?action=usuarios');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resetpass_id'])) {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            $resultado = admin_reset_password((int)$_POST['resetpass_id']);
            if ($resultado['exito']) {
                $clave_reseteada = $resultado['clave'];
                $nickname_reset = $resultado['nickname'];
            } else {
                $error_reset = $resultado['error'];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            $resultado = crear_usuario($_POST['nickname'], $_POST['nombre']);
            if ($resultado['exito']) {
                $clave_creada = $resultado['clave'];
                $nickname_creado = $_POST['nickname'];
            } else {
                $error_crear = $resultado['error'];
            }
            $error_crear = $resultado['error'];
        }

        $usuarios = listar_usuarios();
        require __DIR__ . '/../views/usuarios.php';
        break;

    case 'upload':
        require_once __DIR__ . '/../src/upload.php';
        require_once __DIR__ . '/../src/rutas.php';
        $resultado = null;
        $plazas = obtener_plazas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
                $resultado = ['exito' => false, 'error' => 'Seleccione un archivo'];
            } elseif (empty($_POST['plaza'])) {
                $resultado = ['exito' => false, 'error' => 'Seleccione una plaza'];
            } else {
                $resultado = procesar_upload(
                    $_FILES['archivo'],
                    $_POST['client_md5'] ?? '',
                    $_POST['fecha_archivo'] ?? null,
                    $_POST['ruta_original'] ?? null,
                    $_POST['plaza']
                );
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode($resultado, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
        }

        require __DIR__ . '/../views/upload.php';
        break;

    case 'logs':
        require_once __DIR__ . '/../src/logs.php';

        $logs = obtener_logs();
        require __DIR__ . '/../views/logs.php';
        break;

    case 'restaurar':
        require_once __DIR__ . '/../src/upload.php';
        require_once __DIR__ . '/../src/rutas.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restaurar_seleccionados'])) {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            $errores = [];
            $ok_count = 0;
            foreach ($_POST['restaurar_rutas'] ?? [] as $ruta_id) {
                $info = obtener_ruta_por_id((int)$ruta_id);
                if (!$info) continue;
                $res = restaurar_copia($info['ruta'], $info['plaza']);
                if ($res['exito']) {
                    $ok_count++;
                } else {
                    $errores[] = $info['plaza'] . ': ' . $res['error'];
                }
            }
            if ($ok_count > 0) {
                header('Location: ?action=restaurar&restaurado=' . $ok_count);
            } else {
                header('Location: ?action=restaurar&error_restaurar=' . urlencode(implode('; ', $errores)));
            }
            exit;
        }

        $rutas_con_bak = obtener_rutas_con_bak();
        require __DIR__ . '/../views/restaurar.php';
        break;

    case 'rutas':
        require_once __DIR__ . '/../src/rutas.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            $resultado = crear_ruta($_POST['ruta'], $_POST['plaza']);
            if ($resultado['exito']) {
                header('Location: ?action=rutas&creado=1');
                exit;
            }
            $error_ruta = $resultado['error'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_ruta'])) {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            toggle_ruta((int)$_POST['toggle_ruta']);
            header('Location: ?action=rutas');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            $resultado = editar_ruta((int)$_POST['ruta_id'], $_POST['ruta'], $_POST['plaza']);
            if ($resultado['exito']) {
                header('Location: ?action=rutas&editado=1');
                exit;
            }
            $error_ruta = $resultado['error'];
        }

        $rutas = listar_rutas();
        require __DIR__ . '/../views/rutas.php';
        break;

    case 'cambioclave':
        require_once __DIR__ . '/../src/users.php';
        $clave_generada = null;
        $error_clave = null;
        $confirmado = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            if (($_POST['accion'] ?? '') === 'confirmar') {
                $resultado = confirmar_cambio_clave(obtener_usuario_actual()['id']);
                if ($resultado['exito']) {
                    $confirmado = true;
                } else {
                    $error_clave = $resultado['error'];
                }
            } else {
                $resultado = generar_clave_temp();
                if ($resultado['exito']) {
                    $clave_generada = $resultado['clave'];
                } else {
                    $error_clave = $resultado['error'];
                }
            }
        }

        require __DIR__ . '/../views/cambioclave.php';
        break;

    default:
        http_response_code(404);
        die('Página no encontrada');
}
