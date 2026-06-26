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

<h3 id="form-title">Nueva ruta</h3>
<form method="POST" class="inline-form" id="ruta-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="accion" id="ruta-accion" value="crear">
    <input type="hidden" name="ruta_id" id="edit-id" value="">
    <label>Ruta: <input type="text" name="ruta" id="edit-ruta" required placeholder="/var/smb/almar/ALMAR/"></label>
    <label>Plaza: <input type="text" name="plaza" id="edit-plaza" required placeholder="ALMAR"></label>
    <button type="submit" id="form-submit">Crear</button>
    <button type="button" id="form-cancel" style="display:none" class="btn-cancel">Cancelar</button>
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
                    <button type="button" class="btn-edit" data-id="<?= $r['id'] ?>" data-ruta="<?= htmlspecialchars($r['ruta'], ENT_QUOTES) ?>" data-plaza="<?= htmlspecialchars($r['plaza'], ENT_QUOTES) ?>">Editar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
(function() {
    var title = document.getElementById('form-title');
    var form = document.getElementById('ruta-form');
    var accion = document.getElementById('ruta-accion');
    var idInput = document.getElementById('edit-id');
    var rutaInput = document.getElementById('edit-ruta');
    var plazaInput = document.getElementById('edit-plaza');
    var submitBtn = document.getElementById('form-submit');
    var cancelBtn = document.getElementById('form-cancel');

    var btns = document.querySelectorAll('[data-id][data-ruta][data-plaza].btn-edit');
    for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener('click', function() {
            idInput.value = this.getAttribute('data-id');
            rutaInput.value = this.getAttribute('data-ruta');
            plazaInput.value = this.getAttribute('data-plaza');
            accion.value = 'editar';
            submitBtn.textContent = 'Guardar';
            title.textContent = 'Editar ruta';
            cancelBtn.style.display = 'inline';
        });
    }

    cancelBtn.addEventListener('click', function() {
        idInput.value = '';
        rutaInput.value = '';
        plazaInput.value = '';
        accion.value = 'crear';
        submitBtn.textContent = 'Crear';
        title.textContent = 'Nueva ruta';
        cancelBtn.style.display = 'none';
    });
})();
</script>
<?php require __DIR__ . '/footer.php'; ?>
