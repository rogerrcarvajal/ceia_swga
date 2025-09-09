document.addEventListener("DOMContentLoaded", () => {
  console.log("Control de Acceso JS Cargado");
  const qrInput = document.getElementById("qr-input");
  const qrResult = document.getElementById("qr-result");
  let procesando = false;

  function mostrarMensaje(tipo, data) {
    console.log("Mostrando mensaje:", tipo, data);
    let html = '';

    try {
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
                  let strikeInfo = '';
                  // Determinar color y mensaje basado en el nivel de strike
                  switch (data.strike_level) {
                      case 0: // A tiempo
                          qrResult.style.backgroundColor = 'rgba(42, 74, 109, 0.9)'; // Azul neutro
                          break;
                      case 1: // 1er Strike
                          qrResult.style.backgroundColor = 'rgba(46, 204, 113, 0.9)'; // Verde
                          strikeInfo = `<p style="margin:5px 0; font-size:1.1em; font-weight:bold;">STRIKE SEMANAL: ${data.strike_count}</p>`;
                          break;
                      case 2: // 2do Strike
                          qrResult.style.backgroundColor = 'rgba(241, 196, 15, 0.9)'; // Amarillo
                          qrResult.style.color = '#111';
                          qrResult.style.textShadow = 'none';
                          strikeInfo = `<p style="margin:5px 0; font-size:1.1em; font-weight:bold;">STRIKE SEMANAL: ${data.strike_count}</p>`;
                          break;
                      default: // 3er Strike o más
                          qrResult.style.backgroundColor = 'rgba(231, 76, 60, 0.9)'; // Rojo
                          strikeInfo = `<p style="margin:5px 0; font-size:1.1em; font-weight:bold;">STRIKE SEMANAL: ${data.strike_count}</p>`;
                          break;
                  }

                  // Construir el HTML
                  html = `
                      <h4 style="margin:0 0 10px 0; padding:0; font-size:1.2em; text-transform:uppercase;">${data.strike_level > 0 ? 'LLEGADA TARDE' : 'LLEGADA REGISTRADA'}</h4>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Estudiante:</strong> ${data.nombre_completo}</p>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Grado:</strong> ${data.grado}</p>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Hora:</strong> ${data.hora_registrada}</p>
                      ${strikeInfo}
                  `;

                  // Añadir mensaje especial si existe
                  if (data.mensaje_especial) {
                      html += `<p style="margin-top:15px; padding:10px; background-color:rgba(0,0,0,0.2); border-radius:5px; font-weight:bold;">${data.mensaje_especial}</p>`;
                  }
                  break;
              case 'STF':
                  qrResult.style.backgroundColor = 'rgba(109, 74, 42, 0.9)'; // Marrón para Staff
                  html = `
                      <h4 style="margin:0 0 10px 0; padding:0; font-size:1.2em; text-transform:uppercase;">${data.tipo_movimiento}</h4>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Staff:</strong> ${data.nombre_completo}</p>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Posición:</strong> ${data.posicion}</p>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Hora:</strong> ${data.hora_registrada}</p>
                  `;
                  break;
              case 'VEH':
                  qrResult.style.backgroundColor = 'rgba(74, 109, 42, 0.9)'; // Verde para Vehículos
                  html = `
                      <h4 style="margin:0 0 10px 0; padding:0; font-size:1.2em; text-transform:uppercase;">${data.tipo_movimiento}</h4>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Vehículo:</strong> ${data.placa} - ${data.modelo}</p>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Estudiante:</strong> ${data.nombre_completo} (${data.grado})</p>
                      <p style="margin:5px 0; font-size:1.1em;"><strong>Hora:</strong> ${data.hora_registrada}</p>
                  `;
                  break;
          }
      } else {
          qrResult.style.backgroundColor = 'rgba(231, 76, 60, 0.9)';
          qrResult.style.textAlign = 'center';
          html = `<p style="font-weight:bold;">${data}</p>`;
      }
    } catch (error) {
        console.error("Error al mostrar mensaje:", error);
        qrResult.style.backgroundColor = 'rgba(231, 76, 60, 0.9)';
        qrResult.style.textAlign = 'center';
        html = `<p style="font-weight:bold;">Error al procesar la alerta: ${error.message}</p>`;
    }

    qrResult.innerHTML = html;
    qrResult.className = 'alerta';
    qrResult.style.display = "block";

    clearTimeout(window.timeoutMensaje);
    window.timeoutMensaje = setTimeout(() => {
      qrResult.style.display = "none";
    }, 5000);
  }

  function procesarCodigo(codigo) {
    if (!codigo || procesando) return;

    procesando = true;
    qrInput.value = ""; 
    
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
      qrInput.focus();
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
      })
      .finally(() => {
        procesando = false;
        qrInput.focus();
        console.log("Proceso finalizado. Listo para el siguiente escaneo.");
      });
  }

  qrInput.addEventListener("change", (e) => procesarCodigo(e.target.value));
  qrInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      procesarCodigo(qrInput.value);
    }
  });

  setInterval(() => {
    if (!procesando && document.activeElement !== qrInput) {
      qrInput.focus();
    }
  }, 500);
  qrInput.focus();
});