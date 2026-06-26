<?php require __DIR__ . '/header.php'; ?>
<h2>Gestión de usuarios</h2>

<?php if (isset($_GET['creado'])): ?>
    <div class="success">Usuario creado correctamente</div>
<?php endif; ?>
<?php if (isset($error_crear)): ?>
    <div class="error"><?= htmlspecialchars($error_crear) ?></div>
<?php endif; ?>

<h3>Nuevo usuario</h3>
<form method="POST" class="inline-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="crear" value="1">
    <label>Nickname: <input type="text" name="nickname" required></label>
    <label>Nombre: <input type="text" name="nombre" required></label>
    <label>Contraseña: <input type="password" name="password" required></label>
    <button type="submit">Crear</button>
</form>

<h3>Usuarios existentes</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nickname</th>
            <th>Nombre</th>
            <th>Activo</th>
            <th>Admin</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nickname']) ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= $u['activo'] ? 'Sí' : 'No' ?></td>
                <td><?= $u['es_admin'] ? 'Sí' : 'No' ?></td>
                <td>
                    <?php if (!$u['es_admin']): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="toggle_id" value="<?= $u['id'] ?>">
                            <button type="submit"><?= $u['activo'] ? 'Deshabilitar' : 'Habilitar' ?></button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>
