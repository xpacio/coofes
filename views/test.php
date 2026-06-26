<?php require __DIR__ . '/header.php'; ?>
<h2>Prueba de loader</h2>
<p>Barrita indeterminada para mostrar progreso.</p>

<button id="btn-toggle" onclick="toggleLoader()">Mostrar loader</button>

<div id="loader-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:999; display:none; align-items:center; justify-content:center; flex-direction:column;">
    <div class="progress-bar"><div class="progress-indeterminate"></div></div>
    <p style="color:#fff; margin-top:12px;">Procesando...</p>
</div>

<script>
function toggleLoader() {
    var el = document.getElementById('loader-overlay');
    var btn = document.getElementById('btn-toggle');
    if (el.style.display === 'flex') {
        el.style.display = 'none';
        btn.textContent = 'Mostrar loader';
    } else {
        el.style.display = 'flex';
        btn.textContent = 'Ocultar loader';
    }
}
</script>
<?php require __DIR__ . '/footer.php'; ?>
