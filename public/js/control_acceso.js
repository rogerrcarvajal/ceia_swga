document.addEventListener('DOMContentLoaded', () => {
    const qrForm = document.getElementById('qr-form');
    const qrInput = document.getElementById('qr-input');
    const resultDiv = document.getElementById('qr-result');
    const logDiv = document.getElementById('log-registros');

    // Foco inicial en input
    qrInput.focus();

    // --- Evento de escaneo (form submit) ---
    qrForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Extraer código puro (últimos dígitos del valor escaneado)
        const raw = qrInput.value.trim();
        const match = raw.match(/(\d{1,6})$/);  // Busca hasta 6 dígitos
        const codigo = match ? match[1] : '';

        if (codigo === '') {
            mostrarError("Código no válido. Intente nuevamente.");
            return;
        }

        try {
            const response = await fetch('/ceia_swga/api/registrar_llegada.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ estudiante_id: codigo }) // ← Es el mismo para staff/vehículo
            });

            if (!response.ok) throw new Error('Error del servidor.');

            const result = await response.json();

            if (result.status === 'error') {
                mostrarError(result.message);
            } else {
                mostrarAlerta(result);
                agregarAlLog(result);
            }

        } catch (error) {
            mostrarError(error.message);
        }

        // Reiniciar input
        qrInput.value = '';
        qrInput.focus();
    });

    // --- Mostrar mensaje de alerta según tipo ---
    function mostrarAlerta(data) {
        let clase = 'exito';
        let contenido = `
            <h4>${data.nombre_completo}</h4>
            <p>Hora de Registro: ${data.hora_llegada}</p>
        `;

        if (data.tipo === 'estudiante') {
            if (data.es_tarde) {
                if (data.conteo_tardes === 2) clase = 'advertencia';
                if (data.conteo_tardes >= 3) clase = 'error';
            }
            contenido += `<p>Grado: ${data.grado}</p>`;
            contenido += `<p><strong>${data.mensaje}</strong></p>`;
        } else if (data.tipo === 'staff') {
            clase = 'blanco';
            contenido += `<p>Posición: ${data.posicion}</p>`;
        } else if (data.tipo === 'vehiculo') {
            clase = 'blanco';
            contenido += `<p>Placa: ${data.placa}</p>`;
            contenido += `<p>Estudiante: ${data.estudiante}</p>`;
        }

        resultDiv.className = `alerta ${clase}`;
        resultDiv.innerHTML = contenido;
        resultDiv.style.display = 'block';

        setTimeout(() => { resultDiv.style.display = 'none'; }, 3000);
    }

    // --- Agregar registro al historial en pantalla ---
    function agregarAlLog(data) {
        const logEntry = document.createElement('div');
        logEntry.className = 'log-entry';
        logEntry.innerHTML = `<span>${data.hora_llegada}</span> - <span>${data.nombre_completo}</span> - <span>${data.mensaje || ''}</span>`;
        logDiv.insertBefore(logEntry, logDiv.firstChild);
    }

    // --- Mostrar errores ---
    function mostrarError(mensaje) {
        resultDiv.className = 'alerta error';
        resultDiv.innerHTML = `<h4>Error</h4><p>${mensaje}</p>`;
        resultDiv.style.display = 'block';
        setTimeout(() => { resultDiv.style.display = 'none'; }, 3000);
    }
});