<?php require __DIR__ . '/header.php'; ?>
<h2>Bienvenido, <?= htmlspecialchars(obtener_usuario_actual()['nombre']) ?></h2>
<ul>
    <li><a href="?action=upload">Subir archivo CO_OFES.DBF</a></li>
    <li><a href="?action=logs">Ver historial de cargas</a></li>
    <?php if (obtener_usuario_actual()['es_admin']): ?>
        <li><a href="?action=usuarios">Gestionar usuarios</a></li>
    <?php endif; ?>
</ul>
<?php require __DIR__ . '/footer.php'; ?>
