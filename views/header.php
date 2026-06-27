<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DBF Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="?">Inicio</a>
            <?php if (obtener_usuario_actual()): ?>
                <a href="?action=upload">Subir archivo</a>
                <a href="?action=logs">Historial</a>
                <a href="?action=restaurar">Restaurar</a>
                <?php if (obtener_usuario_actual()['es_admin']): ?>
                    <a href="?action=usuarios">Usuarios</a>
                    <a href="?action=rutas">Rutas</a>
                <?php endif; ?>
                <span class="user"><?= htmlspecialchars(obtener_usuario_actual()['nombre']) ?></span>
                <a href="?action=cambioclave">Cambiar contraseña</a>
                <a href="?action=logout">Cerrar sesión</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
