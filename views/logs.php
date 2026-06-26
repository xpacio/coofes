<?php require __DIR__ . '/header.php'; ?>
<style>main{max-width:100%;}</style>
<h2>Historial de cargas</h2>

<?php if (isset($_GET['restaurado'])): ?>
    <div class="success">Archivo restaurado correctamente. Listo para restaurar otra ruta.</div>
<?php endif; ?>
<?php if (isset($_GET['error_restaurar'])): ?>
    <div class="error"><?= htmlspecialchars($_GET['error_restaurar']) ?></div>
<?php endif; ?>

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
            <th>Restaurar</th>
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
                <td>
                    <?php
                    if ($log['estado'] === 'restaurado'):
                        echo htmlspecialchars($log['detalle'] ?? '-');
                    else:
                        $rutas_detalle = json_decode($log['detalle'], true);
                        if (is_array($rutas_detalle)):
                            foreach ($rutas_detalle as $rd):
                                if (!empty($rd['exito'])):
                    ?>
                            <form method="POST" action="?action=logs" class="restore-form" style="white-space:nowrap">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="restaurar" value="<?= (int)$log['id'] ?>">
                                <input type="hidden" name="ruta" value="<?= htmlspecialchars($rd['ruta']) ?>">
                                <label>
                                    <input type="checkbox" class="restore-check" onchange="this.form.querySelector('.restore-btn').disabled=!this.checked">
                                    <?= htmlspecialchars($rd['ruta']) ?>
                                </label>
                                <button type="submit" class="restore-btn" disabled>Restaurar</button>
                            </form>
                    <?php
                                endif;
                            endforeach;
                        else:
                            echo '-';
                        endif;
                    endif;
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
            <tr><td colspan="<?= obtener_usuario_actual()['es_admin'] ? 11 : 10 ?>">No hay registros</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>
