document.addEventListener("DOMContentLoaded", () => {
  console.log("Control de Acceso JS Cargado");
  const qrInput = document.getElementById("qr-input");
  const qrResult = document.getElementById("qr-result");
  let procesando = false;

  function mostrarMensaje(tipo, data) {
    console.log("Mostrando mensaje:", tipo, data);
    let html = '';

    // Estilos base para la alerta
    qrResult.style.padding = '15px';
    qrResult.style.marginBottom = '20px';
    qrResult.style.borderRadius = '8px';
    qrResult.style.textAlign = 'left';
    qrResult.style.color = 'white';
    qrResult.style.textShadow = '1px 1px 2px rgba(0,0,0,0.5)';
    qrResult.style.border = '1px solid rgba(255, 255, 255, 0.3)';

    if (tipo === 'exito' && data && data.tipo) {
        switch (data.tipo) {
            case 'EST':
                qrResult.style.backgroundColor = 'rgba(42, 74, 109, 0.9)';
                let strikeClass = '';
                let strikeMsg = '';
                if (data.strike_count > 0) {
                    let color = '#28a745'; // Verde para strike 1
                    let textColor = 'white';
                    if (data.strike_count === 2) { color = '#ffc107'; textColor = '#212529'; } // Amarillo
                    if (data.strike_count >= 3) color = '#dc3545'; // Rojo
                    strikeMsg = `<div style="display:inline-block; padding: 5px 12px; border-radius:15px; font-weight:bold; color:${textColor}; background-color:${color}; margin-bottom:10px; font-size:1em; text-transform:uppercase;">STRIKE ${data.strike_count}</div>`;
                }
                html = `
                    ${strikeMsg}
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Estudiante:</strong> ${data.nombre_completo}</p>
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Grado:</strong> ${data.grado}</p>
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Hora:</strong> ${data.hora_registrada}</p>
                    ${data.strike_count >= 3 ? '<p style="font-weight:bold; color:#f8d7da; margin-top:10px; padding:8px; background-color:rgba(220, 53, 69, 0.3); border-radius:4px;">Pierde la primera hora de clases. Debe comunicarse con su representante.</p>' : ''}
                `;
                break;
            case 'STF':
                qrResult.style.backgroundColor = 'rgba(109, 74, 42, 0.9)';
                html = `
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Staff:</strong> ${data.nombre_completo}</p>
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Posición:</strong> ${data.posicion}</p>
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Hora de ${data.tipo_movimiento}:</strong> ${data.hora_registrada}</p>
                `;
                break;
            case 'VEH':
                qrResult.style.backgroundColor = 'rgba(74, 109, 42, 0.9)';
                html = `
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Vehículo:</strong> ${data.placa} - ${data.modelo}</p>
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Estudiante:</strong> ${data.nombre_completo} (${data.grado})</p>
                    <p style="margin:5px 0; font-size:1.1em;"><strong>Hora de ${data.tipo_movimiento}:</strong> ${data.hora_registrada}</p>
                `;
                break;
        }
    } else {
        // Mensaje de error
        qrResult.style.backgroundColor = 'rgba(231, 76, 60, 0.9)';
        qrResult.style.textAlign = 'center';
        html = `<p style="font-weight:bold;">${data}</p>`;
    }

    qrResult.innerHTML = html;
    qrResult.className = 'alerta'; // Quitar otras clases para evitar conflictos
    qrResult.style.display = "block";

    clearTimeout(window.timeoutMensaje);
    window.timeoutMensaje = setTimeout(() => {
      qrResult.style.display = "none";
      qrInput.value = "";
      procesando = false;
      qrInput.focus();
    }, 5000);
  }

  function procesarCodigo(codigo) {
    if (!codigo || procesando) return;

    procesando = true;
    console.log(`Procesando código: ${codigo}`);
    
    const codigoNormalizado = codigo.trim().replace("/", "-").toUpperCase();
    let urlApi = "";

    if (codigoNormalizado.startsWith("EST-")) {
      urlApi = "/ceia_swga/api/registrar_llegada.php";
    } else if (codigoNormalizado.startsWith("STF-")) {
      urlApi = "/ceia_swga/api/registrar_movimiento_staff.php";
    } else if (codigoNormalizado.startsWith("VEH-")) {
      urlApi = "/ceia_swga/api/registrar_movimiento_vehiculo.php";
    } else {
      mostrarMensaje("error", "QR no reconocido o inválido.");
      procesando = false;
      return;
    }

    console.log(`URL de la API: ${urlApi}`);
    const formData = new FormData();
    formData.append("codigo", codigoNormalizado);
    formData.append("timestamp", new Date().toISOString());

    fetch(urlApi, {
      method: "POST",
      body: formData,
    })
      .then(response => {
        console.log("Respuesta recibida de la API");
        if (!response.ok) {
            console.error("Error en la respuesta del servidor:", response.statusText);
            throw new Error(`Error del servidor: ${response.statusText}`);
        }
        return response.json();
      })
      .then(res => {
        console.log("Datos JSON de la API:", res);
        if (res && res.success) {
          mostrarMensaje("exito", res.data);
        } else {
          mostrarMensaje("error", res.message || "Error al procesar la solicitud.");
        }
      })
      .catch(error => {
        console.error("Error en la función fetch:", error);
        mostrarMensaje("error", `Error de comunicación: ${error.message}`);
      });
  }

  qrInput.addEventListener("change", (e) => procesarCodigo(e.target.value));
  qrInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      procesarCodigo(qrInput.value);
    }
  });

  // Asegurarse de que el input tenga el foco
  setInterval(() => {
    if (!procesando && document.activeElement !== qrInput) {
      qrInput.focus();
    }
  }, 500);
  qrInput.focus(); // Foco inicial
});