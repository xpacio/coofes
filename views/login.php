<?php require __DIR__ . '/header.php'; ?>
<h2>Iniciar sesión</h2>
<?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="POST">
    <label>Nickname: <input type="text" name="nickname" required></label>
    <label>Contraseña: <input type="password" name="password" required></label>
    <button type="submit">Ingresar</button>
</form>
<?php require __DIR__ . '/footer.php'; ?>
