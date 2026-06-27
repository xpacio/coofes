<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DBF Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <ul>
                <li><a href="?">Inicio</a></li>
                <?php if (obtener_usuario_actual()): ?>
                    <li><a href="?action=upload">Subir archivo</a></li>
                    <li><a href="?action=logs">Historial</a></li>
                    <li><a href="?action=restaurar">Restaurar</a></li>
                    <?php if (obtener_usuario_actual()['es_admin']): ?>
                        <li><a href="?action=usuarios">Usuarios</a></li>
                        <li><a href="?action=rutas">Rutas</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <?php if (obtener_usuario_actual()): ?>
            <ul>
                <li><a href="?action=cambioclave"><?= htmlspecialchars(obtener_usuario_actual()['nombre']) ?></a></li>
                <li><a href="?action=logout">Cerrar sesión</a></li>
            </ul>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
