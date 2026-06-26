(function () {
    'use strict';

    var dropZone = document.getElementById('drop-zone');
    var input = document.getElementById('archivo');
    var form = document.getElementById('upload-form');
    var infoDiv = document.getElementById('info-archivo');
    var displayRuta = document.getElementById('display-ruta');
    var displayFecha = document.getElementById('display-fecha');
    var displayTamano = document.getElementById('display-tamano');
    var displayHash = document.getElementById('display-hash');
    var acceptBtn = document.getElementById('aceptar');
    var clientMd5 = document.getElementById('client_md5');
    var fechaInput = document.getElementById('fecha_archivo_input');
    var rutaInput = document.getElementById('ruta_original_input');
    var progresoDiv = document.getElementById('progreso');
    var progresoFill = document.getElementById('progreso-fill');
    var progresoTexto = document.getElementById('progreso-texto');
    var resultadosDiv = document.getElementById('resultados');

    var NAME_PATTERN = /^co_ofes\.dbf$/i;
    var currentFile = null;

    if (!input || !dropZone) return;

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    function handleFile(file) {
        if (!file) {
            infoDiv.style.display = 'none';
            acceptBtn.disabled = true;
            return;
        }

        if (!NAME_PATTERN.test(file.name)) {
            alert('El archivo debe llamarse exactamente "co_ofes.dbf"');
            input.value = '';
            infoDiv.style.display = 'none';
            acceptBtn.disabled = true;
            currentFile = null;
            return;
        }

        acceptBtn.disabled = true;
        currentFile = file;

        displayRuta.textContent = file.name;
        rutaInput.value = file.name;

        var d = new Date(file.lastModified);
        displayFecha.textContent = d.toLocaleString('es-ES');
        fechaInput.value = Math.floor(file.lastModified / 1000);

        displayTamano.textContent = formatBytes(file.size);
        infoDiv.style.display = 'block';
        displayHash.textContent = 'Calculando...';

        var spark = new SparkMD5.ArrayBuffer();
        var reader = new FileReader();
        var chunkSize = 2 * 1024 * 1024;
        var currentPos = 0;

        function readNextChunk() {
            var slice = file.slice(currentPos, Math.min(currentPos + chunkSize, file.size));
            reader.readAsArrayBuffer(slice);
        }

        reader.onload = function (evt) {
            if (evt.target.result) {
                spark.append(evt.target.result);
            }
            currentPos += chunkSize;
            if (currentPos < file.size) {
                readNextChunk();
            } else {
                var hash = spark.end();
                displayHash.textContent = hash;
                clientMd5.value = hash;
                acceptBtn.disabled = false;
            }
        };

        reader.onerror = function () {
            displayHash.textContent = 'Error al leer archivo';
        };

        readNextChunk();
    }

    input.addEventListener('change', function (e) {
        handleFile(e.target.files[0]);
    });

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        var file = e.dataTransfer.files[0];
        input.files = e.dataTransfer.files;
        handleFile(file);
    });

    dropZone.addEventListener('click', function () {
        input.click();
    });

    form.addEventListener('submit', function (e) {
        if (!currentFile || acceptBtn.disabled) {
            e.preventDefault();
            return;
        }

        var plaza = form.querySelector('input[name="plaza"]:checked');
        if (!plaza) {
            alert('Seleccione una plaza destino');
            e.preventDefault();
            return;
        }

        e.preventDefault();

        acceptBtn.disabled = true;
        aceptar.style.display = 'none';
        progresoDiv.style.display = 'block';
        resultadosDiv.style.display = 'none';
        resultadosDiv.innerHTML = '';
        progresoFill.style.width = '0%';
        progresoTexto.textContent = '0%';

        var xhr = new XMLHttpRequest();
        var formData = new FormData(form);

        xhr.upload.onprogress = function (evt) {
            if (evt.lengthComputable) {
                var pct = Math.round(evt.loaded * 100 / evt.total);
                progresoFill.style.width = pct + '%';
                progresoTexto.textContent = pct + '%';
            }
        };

        xhr.onload = function () {
            progresoDiv.style.display = 'none';
            aceptar.style.display = 'inline';

            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    mostrarResultados(res);
                } catch (_) {
                    resultadosDiv.innerHTML = '<div class="error">Error al procesar respuesta del servidor</div>';
                    resultadosDiv.style.display = 'block';
                }
            } else {
                resultadosDiv.innerHTML = '<div class="error">Error de conexión: ' + xhr.status + '</div>';
                resultadosDiv.style.display = 'block';
            }
        };

        xhr.onerror = function () {
            progresoDiv.style.display = 'none';
            aceptar.style.display = 'inline';
            resultadosDiv.innerHTML = '<div class="error">Error de red al enviar el archivo</div>';
            resultadosDiv.style.display = 'block';
        };

        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    });

    function mostrarResultados(res) {
        var html = '';
        if (res.exito) {
            html += '<div class="success">Archivo procesado correctamente</div>';
        } else {
            html += '<div class="error">' + escapeHtml(res.error) + '</div>';
        }

        if (res.rutas && res.rutas.length) {
            html += '<h4>Resultado por ruta:</h4><ul>';
            for (var i = 0; i < res.rutas.length; i++) {
                var r = res.rutas[i];
                var cls = r.exito ? 'success' : 'error';
                var msg = r.exito ? '✔ Copiado' : '✘ ' + escapeHtml(r.error || 'Error');
                html += '<li class="' + cls + '">' + escapeHtml(r.ruta) + ' — ' + msg + '</li>';
            }
            html += '</ul>';
        }

        resultadosDiv.innerHTML = html;
        resultadosDiv.style.display = 'block';
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
})();
