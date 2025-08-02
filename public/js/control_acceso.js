// Espera a que todo el HTML esté cargado para empezar a trabajar
document.addEventListener('DOMContentLoaded', () => {
    const qrForm = document.getElementById('qr-form');
    const qrInput = document.getElementById('qr-input');
    const resultDiv = document.getElementById('qr-result');
    const logDiv = document.getElementById('log-registros');

    // Pone el foco en el campo de entrada para que el lector QR funcione de inmediato
    qrInput.focus();

    // Evento que se dispara cuando se envía el formulario (al escanear el QR)
    qrForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Evita que la página se recargue
        const estudianteId = qrInput.value.trim();

        if (estudianteId === '') {
            return; // No hacer nada si el campo está vacío
        }

        try {
            // Llama a nuestra API para registrar la llegada
            const response = await fetch('/ceia_swga/api/registrar_llegada.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ estudiante_id: estudianteId })
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor.');
            }

            const result = await response.json();
            
            // Llama a la función para mostrar la alerta visual
            mostrarAlerta(result);

            // Añade el registro al log de la pantalla
            agregarAlLog(result);

        } catch (error) {
            // Muestra un error de conexión o del servidor
            resultDiv.className = 'alerta error';
            resultDiv.innerHTML = `<h4>Error de Conexión</h4><p>${error.message}</p>`;
            resultDiv.style.display = 'block';
        }

        // Limpia el campo y lo deja listo para el siguiente escaneo
        qrInput.value = '';
        qrInput.focus();
    });

    // Función para mostrar las alertas visuales
    function mostrarAlerta(data) {
        if (data.status === 'error') {
            resultDiv.className = 'alerta error';
            resultDiv.innerHTML = `<h4>Error</h4><p>${data.message}</p>`;
        } else {
            let colorClass = 'exito'; // Verde por defecto (llegada a tiempo)
            if (data.es_tarde) {
                if (data.conteo_tardes === 2) colorClass = 'advertencia'; // Amarillo
                if (data.conteo_tardes >= 3) colorClass = 'error'; // Rojo
            }
            resultDiv.className = `alerta ${colorClass}`;
            resultDiv.innerHTML = `
                <h4>${data.nombre_completo}</h4>
                <p>Grado: ${data.grado}</p>
                <p>Hora de Registro: ${data.hora_llegada}</p>
                <p><strong>${data.mensaje}</strong></p>
            `;
        }
        resultDiv.style.display = 'block';

        // 🕒 Ocultar el mensaje después de 3 segundos
        setTimeout(() => {
            resultDiv.style.display = 'none';
        }, 3000);
    }

    // Función para añadir al historial en pantalla
    function agregarAlLog(data) {
        const logEntry = document.createElement('div');
        logEntry.className = 'log-entry';
        if (data.status === 'exito') {
            logEntry.innerHTML = `<span>${data.hora_llegada}</span> - <span>${data.nombre_completo}</span> - <span>${data.mensaje}</span>`;
            // Inserta el nuevo registro al principio del log
            logDiv.insertBefore(logEntry, logDiv.firstChild);
        }
    }
});
// Espera a que el DOM esté completamente cargado
const qrForm = document.getElementById("qr-form");
qrForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const qrInput = document.getElementById("qr-input");
  const qrContent = qrInput.value.trim();

  if (qrContent === "") return;

  let apiUrl = "";
  let bodyPayload = {};

  // --- LÓGICA PARA DIFERENCIAR QR DE ESTUDIANTE Y STAFF ---
  if (qrContent.startsWith("STAFF:")) {
    apiUrl = "/api/registrar_movimiento_staff.php";
    bodyPayload = { profesor_id: qrContent.replace("STAFF:", "") };
  } else {
    apiUrl = "/api/registrar_llegada.php";
    bodyPayload = { estudiante_id: qrContent };
  }

  try {
    const response = await fetch(apiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(bodyPayload),
    });
    const result = await response.json();
    mostrarAlerta(result); // La misma función de alerta funciona para ambos
    // ... (lógica de log)
  } catch (error) {
    // ... (manejo de error)
  }
  qrInput.value = "";
  qrInput.focus();
});