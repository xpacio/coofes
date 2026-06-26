(function () {
    'use strict';

    var input = document.getElementById('archivo');
    if (!input) return;

    var infoDiv = document.getElementById('info-archivo');
    var displayRuta = document.getElementById('display-ruta');
    var displayFecha = document.getElementById('display-fecha');
    var displayTamano = document.getElementById('display-tamano');
    var displayHash = document.getElementById('display-hash');
    var acceptBtn = document.getElementById('aceptar');
    var clientMd5 = document.getElementById('client_md5');
    var fechaInput = document.getElementById('fecha_archivo_input');
    var rutaInput = document.getElementById('ruta_original_input');

    var NAME_PATTERN = /^co_ofes\.dbf$/i;

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    input.addEventListener('change', function (e) {
        var file = e.target.files[0];

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
            return;
        }

        acceptBtn.disabled = true;

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
    });
})();
