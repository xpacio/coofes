<?php require __DIR__ . '/header.php'; ?>
<h2>Gestión de rutas por plaza</h2>

<?php if (isset($_GET['creado'])): ?>
    <div class="success">Ruta creada correctamente</div>
<?php endif; ?>
<?php if (isset($_GET['editado'])): ?>
    <div class="success">Ruta actualizada correctamente</div>
<?php endif; ?>
<?php if (isset($error_ruta)): ?>
    <div class="error"><?= htmlspecialchars($error_ruta) ?></div>
<?php endif; ?>

<h3>Nueva ruta</h3>
<form method="POST" class="inline-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="crear_ruta" value="1">
    <label>Ruta: <input type="text" name="ruta" required placeholder="/var/smb/almar/ALMAR/"></label>
    <label>Plaza: <input type="text" name="plaza" required placeholder="ALMAR"></label>
    <button type="submit">Crear</button>
</form>

<h3>Rutas existentes</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ruta</th>
            <th>Plaza</th>
            <th>Habilitado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rutas as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['ruta']) ?></td>
                <td><?= htmlspecialchars($r['plaza']) ?></td>
                <td><?= $r['habilitado'] ? 'Sí' : 'No' ?></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="toggle_ruta" value="<?= $r['id'] ?>">
                        <button type="submit"><?= $r['habilitado'] ? 'Deshabilitar' : 'Habilitar' ?></button>
                    </form>
                    <button onclick="editarRuta(<?= $r['id'] ?>,'<?= htmlspecialchars($r['ruta'], ENT_QUOTES) ?>','<?= htmlspecialchars($r['plaza'], ENT_QUOTES) ?>')" class="btn-edit">Editar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="edit-form" style="display:none; margin-top:1em; border:1px solid #ccc; padding:1em;">
    <h3>Editar ruta</h3>
    <form method="POST" class="inline-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="editar_ruta" value="1">
        <input type="hidden" name="ruta_id" id="edit-id">
        <label>Ruta: <input type="text" name="ruta" id="edit-ruta" required></label>
        <label>Plaza: <input type="text" name="plaza" id="edit-plaza" required></label>
        <button type="submit">Guardar</button>
        <button type="button" onclick="document.getElementById('edit-form').style.display='none'" class="btn-cancel">Cancelar</button>
    </form>
</div>

<script>
function editarRuta(id, ruta, plaza) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-ruta').value = ruta;
    document.getElementById('edit-plaza').value = plaza;
    document.getElementById('edit-form').style.display = 'block';
}
</script>
<?php require __DIR__ . '/footer.php'; ?>
