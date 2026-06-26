<?php require __DIR__ . '/header.php'; ?>
<h2>Subir archivo CO_OFES.DBF</h2>

<?php if (!empty($resultado) && empty($_SERVER['HTTP_X_REQUESTED_WITH'])): ?>
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

    <div id="drop-zone" class="drop-zone">
        <p>Arrastra el archivo <strong>CO_OFES.DBF</strong> aquí</p>
        <p class="drop-hint">o haz clic para seleccionar</p>
        <input type="file" name="archivo" id="archivo" accept=".dbf" required>
    </div>

    <fieldset>
        <legend>Plaza destino:</legend>
        <?php foreach ($plazas as $p): ?>
            <label><input type="radio" name="plaza" value="<?= htmlspecialchars($p) ?>" required> <?= htmlspecialchars($p) ?></label>
        <?php endforeach; ?>
        <?php if (empty($plazas)): ?>
            <p class="error">No hay plazas disponibles. Contacte al administrador.</p>
        <?php endif; ?>
    </fieldset>

    <div id="info-archivo" style="display:none">
        <p><strong>Archivo:</strong> <span id="display-ruta"></span></p>
        <p><strong>Fecha del archivo:</strong> <span id="display-fecha"></span></p>
        <p><strong>Tamaño:</strong> <span id="display-tamano"></span></p>
        <p><strong>MD5:</strong> <code id="display-hash"></code></p>
    </div>

    <div id="progreso" style="display:none">
        <div class="progress-bar"><div id="progreso-fill" class="progress-fill"></div></div>
        <span id="progreso-texto">0%</span>
    </div>

    <div id="resultados" style="display:none"></div>

    <button type="submit" id="aceptar" disabled>Aceptar</button>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/spark-md5/3.0.2/spark-md5.min.js"></script>
<script src="assets/js/app.js"></script>
<?php require __DIR__ . '/footer.php'; ?>
