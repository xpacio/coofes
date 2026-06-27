<?php require __DIR__ . '/header.php'; ?>
<style>main{max-width:100%;}</style>
<h2>Historial de cargas</h2>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Archivo</th>
            <th>Fecha archivo</th>
            <th>Plaza</th>
            <th>Peso</th>
            <th>MD5</th>
            <th>IP</th>
            <th>Estado</th>
            <?php if (obtener_usuario_actual()['es_admin']): ?>
            <th>Detalle</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr class="estado-<?= htmlspecialchars($log['estado']) ?>">
                <td><?= htmlspecialchars($log['fecha_hora']) ?></td>
                <td><?= htmlspecialchars($log['nickname_usuario']) ?></td>
                <td><?= htmlspecialchars($log['ruta_original'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['fecha_archivo'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['plaza_nombre'] ?? '-') ?></td>
                <td><?= $log['peso_bytes'] ? number_format($log['peso_bytes']) . ' B' : '-' ?></td>
                <td><code><?= $log['hash_md5'] ? substr(htmlspecialchars($log['hash_md5']), -4) : '-' ?></code></td>
                <td><?= htmlspecialchars($log['ip_origen'] ?? '-') ?></td>
                <td class="estado-label"><?= htmlspecialchars($log['estado']) ?></td>
                <?php if (obtener_usuario_actual()['es_admin']): ?>
                <td><?= htmlspecialchars(mb_substr($log['detalle'] ?? '', 0, 200)) ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
            <tr><td colspan="<?= obtener_usuario_actual()['es_admin'] ? 10 : 9 ?>">No hay registros</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>
