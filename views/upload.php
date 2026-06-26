<?php require __DIR__ . '/header.php'; ?>
<h2>Subir archivo CO_OFES.DBF</h2>

<?php if ($resultado): ?>
    <div class="resultado">
        <?php if ($resultado['exito']): ?>
            <div class="success">Archivo procesado correctamente</div>
        <?php else: ?>
            <div class="error"><?= htmlspecialchars($resultado['error']) ?></div>
        <?php endif; ?>

        <?php if (isset($resultado['rutas'])): ?>
            <h4>Resultado por ruta:</h4>
            <ul>
                <?php foreach ($resultado['rutas'] as $r): ?>
                    <li class="<?= $r['exito'] ? 'success' : 'error' ?>">
                        <?= htmlspecialchars($r['ruta']) ?> —
                        <?= $r['exito'] ? '✔ Copiado' : '✘ ' . htmlspecialchars($r['error'] ?? 'Error') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="upload-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="client_md5" id="client_md5">
    <input type="hidden" name="fecha_archivo" id="fecha_archivo_input">
    <input type="hidden" name="ruta_original" id="ruta_original_input">

    <label>Archivo: <input type="file" name="archivo" id="archivo" accept=".dbf" required></label>

    <div id="info-archivo" style="display:none">
        <p><strong>Ruta original:</strong> <span id="display-ruta"></span></p>
        <p><strong>Fecha del archivo:</strong> <span id="display-fecha"></span></p>
        <p><strong>Tamaño:</strong> <span id="display-tamano"></span></p>
        <p><strong>MD5:</strong> <code id="display-hash"></code></p>
    </div>

    <button type="submit" id="aceptar" disabled>Aceptar</button>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/spark-md5/3.0.2/spark-md5.min.js"></script>
<script src="assets/js/app.js"></script>
<?php require __DIR__ . '/footer.php'; ?>
