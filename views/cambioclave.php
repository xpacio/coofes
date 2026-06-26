<?php require __DIR__ . '/header.php'; ?>
<h2>Cambiar contraseña</h2>

<p>Generá una nueva contraseña aleatoria para tu usuario <strong><?= htmlspecialchars(obtener_usuario_actual()['nickname']) ?></strong>.</p>

<?php if ($confirmado): ?>
    <div class="clave-nueva">
        <p class="exito">✓ Contraseña cambiada exitosamente.</p>
        <a href="?action=dashboard" class="btn">Ir al inicio</a>
    </div>
<?php elseif ($clave_generada): ?>
    <div class="clave-nueva">
        <p class="warning">⚠ Esta es tu <strong>única oportunidad</strong> de ver la contraseña. No se puede recuperar.</p>
        <div class="clave-box"><?= htmlspecialchars($clave_generada) ?></div>
        <p class="advertencia">Anotála o guardála antes de continuar.</p>
        <form method="POST" class="inline-form" onsubmit="return confirm('¿Confirmar cambio de contraseña? La actual dejará de funcionar.');">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="accion" value="confirmar">
            <button type="submit">Confirmar cambio de contraseña</button>
        </form>
        <a href="?action=cambioclave" class="btn btn-cancel">Cancelar</a>
    </div>
<?php else: ?>
    <form method="POST" class="inline-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="accion" value="generar">
        <p>Se generará una contraseña aleatoria segura de 14 caracteres.</p>
        <p class="advertencia">Primero se muestra la contraseña; luego podés confirmar el cambio.</p>
        <button type="submit">Generar nueva contraseña</button>
    </form>
    <?php if ($error_clave): ?>
        <div class="error"><?= htmlspecialchars($error_clave) ?></div>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
