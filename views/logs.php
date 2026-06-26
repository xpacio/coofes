<?php require __DIR__ . '/header.php'; ?>
<h2>Historial de cargas</h2>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Archivo</th>
            <th>Plaza</th>
            <th>Peso</th>
            <th>MD5</th>
            <th>IP</th>
            <th>Estado</th>
            <th>Detalle</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr class="estado-<?= htmlspecialchars($log['estado']) ?>">
                <td><?= htmlspecialchars($log['fecha_hora']) ?></td>
                <td><?= htmlspecialchars($log['nickname_usuario']) ?></td>
                <td><?= htmlspecialchars($log['ruta_original'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['plaza_nombre'] ?? '-') ?></td>
                <td><?= $log['peso_bytes'] ? number_format($log['peso_bytes']) . ' B' : '-' ?></td>
                <td><code><?= htmlspecialchars($log['hash_md5'] ?? '-') ?></code></td>
                <td><?= htmlspecialchars($log['ip_origen'] ?? '-') ?></td>
                <td class="estado-label"><?= htmlspecialchars($log['estado']) ?></td>
                <td><?= htmlspecialchars(mb_substr($log['detalle'] ?? '', 0, 200)) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
            <tr><td colspan="9">No hay registros</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>
