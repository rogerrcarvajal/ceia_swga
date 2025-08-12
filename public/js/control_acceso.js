document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("qr-form");
  const input = document.getElementById("qr-input");
  const resultDiv = document.getElementById("qr-result");

  // Mapeo prefijo → API
  const apiMap = {
    STE: "/ceia_swga/api/registrar_llegada.php",
    STF: "/ceia_swga/api/registrar_movimiento_staff.php",
    VHI: "/ceia_swga/api/registrar_movimiento_vehiculo.php",
  };

  let mensajeTimeout = null;

  // Función para mostrar mensajes y ocultarlos después de 3 segundos
  function mostrarMensaje(tipo, mensaje) {
    clearTimeout(mensajeTimeout);
    resultDiv.className = "alerta " + (tipo === "exito" ? "exito" : "error");
    resultDiv.textContent = mensaje;

    // Forzar reflow para reiniciar animación
    void resultDiv.offsetWidth;
    resultDiv.classList.add("mostrar");

    mensajeTimeout = setTimeout(() => {
      resultDiv.classList.remove("mostrar");
      setTimeout(() => {
        resultDiv.textContent = "";
        resultDiv.className = "alerta";
      }, 400); // Tiempo para que termine la animación de salida
    }, 3000);
  }

  // Evento de envío del formulario
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    let codigo = input.value.trim().toUpperCase();
    if (!codigo) {
      mostrarMensaje("error", "Debe escanear o ingresar un código QR.");
      reiniciarFormulario();
      return;
    }

    let prefijo = codigo.substring(0, 3);
    let apiURL = apiMap[prefijo];

    if (!apiURL) {
      mostrarMensaje("error", "Código QR inválido o desconocido.");
      reiniciarFormulario();
      return;
    }

    try {
      let resp = await fetch(apiURL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ codigo }),
      });

      let data = await resp.json();

      if (data.status === "exito") {
        let mensaje = "";
        if (data.tipo === "estudiante") {
          mensaje = `${data.mensaje} - ${data.nombre_completo} (${data.hora})`;
        } else if (data.tipo === "staff") {
          mensaje = `${data.mensaje} - ${data.nombre_completo} (${data.hora})`;
        } else if (data.tipo === "vehiculo") {
          mensaje = `${data.mensaje} - Placa: ${data.placa} (${data.hora})`;
        }
        mostrarMensaje("exito", mensaje);
      } else {
        mostrarMensaje("error", data.message || "Error desconocido.");
      }
    } catch (error) {
      mostrarMensaje("error", "Error en la conexión con el servidor.");
    }

    reiniciarFormulario();
  });

  // Reiniciar y enfocar campo
  function reiniciarFormulario() {
    input.value = "";
    input.focus();
  }

  // Mantener el foco en el input
  input.focus();
});
