<?php require __DIR__ . '/header.php'; ?>
<h2>Restaurar respaldo</h2>

<?php if (isset($_GET['restaurado'])): ?>
    <div class="success"><?= (int)$_GET['restaurado'] ?> ruta(s) restaurada(s) correctamente.</div>
<?php endif; ?>
<?php if (isset($_GET['error_restaurar'])): ?>
    <div class="error"><?= htmlspecialchars($_GET['error_restaurar']) ?></div>
<?php endif; ?>

<?php if (!empty($rutas_con_bak)): ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <table>
        <thead>
            <tr>
                <th>Plaza</th>
                <th>Ruta</th>
                <th>BAK</th>
                <th>DBF</th>
                <th>Restaurar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rutas_con_bak as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['plaza']) ?></td>
                <td><?= htmlspecialchars($r['ruta']) ?></td>
                <td><code><?= substr($r['bak_md5'], -4) ?></code></td>
                <td><code><?= $r['dbf_md5'] ? substr($r['dbf_md5'], -4) : '—' ?></code></td>
                <td>
                    <?php if ($r['restorable']): ?>
                        <input type="checkbox" name="restaurar_rutas[]" value="<?= (int)$r['id'] ?>">
                    <?php else: ?>
                        <span class="md5-info">Idéntico</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" name="restaurar_seleccionados" value="1">Restaurar seleccionados</button>
</form>
<?php else: ?>
    <p>No hay respaldos disponibles para restaurar.</p>
<?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
