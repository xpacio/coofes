<?php
require_once __DIR__ . '/../src/auth.php';

iniciar_sesion();

$action = $_GET['action'] ?? 'dashboard';

$public_actions = ['login'];
$auth_actions = ['dashboard', 'logout', 'upload', 'logs', 'test'];
$admin_actions = ['usuarios'];

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            $resultado = crear_usuario($_POST['nickname'], $_POST['password'], $_POST['nombre']);
            if ($resultado['exito']) {
                header('Location: ?action=usuarios&creado=1');
                exit;
            }
            $error_crear = $resultado['error'];
        }

        $usuarios = listar_usuarios();
        require __DIR__ . '/../views/usuarios.php';
        break;

    case 'upload':
        require_once __DIR__ . '/../src/upload.php';
        $resultado = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }
            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
                $resultado = ['exito' => false, 'error' => 'Seleccione un archivo'];
            } else {
                $resultado = procesar_upload(
                    $_FILES['archivo'],
                    $_POST['client_md5'] ?? '',
                    $_POST['fecha_archivo'] ?? null,
                    $_POST['ruta_original'] ?? null
                );
            }
        }

        require __DIR__ . '/../views/upload.php';
        break;

    case 'logs':
        require_once __DIR__ . '/../src/logs.php';
        $logs = obtener_logs();
        require __DIR__ . '/../views/logs.php';
        break;

    case 'test':
        require __DIR__ . '/../views/test.php';
        break;

    default:
        http_response_code(404);
        die('Página no encontrada');
}
