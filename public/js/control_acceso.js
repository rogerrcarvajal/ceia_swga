document.addEventListener("DOMContentLoaded", () => {
  const qrInput = document.getElementById("qr-input");
  const qrResult = document.getElementById("qr-result");
  let procesando = false;

  function mostrarMensaje(texto, tipo) {
    qrResult.textContent = texto;
    qrResult.className = `alerta ${tipo}`;
    qrResult.style.display = "block";

    clearTimeout(window.timeoutMensaje);
    window.timeoutMensaje = setTimeout(() => {
      qrResult.style.display = "none";
      qrInput.value = "";
      procesando = false;
      qrInput.focus();
    }, 3500);
  }

  function procesarCodigo(codigo) {
    if (!codigo || procesando) return;

    procesando = true;
    
    const codigoNormalizado = codigo.trim().replace("/", "-").toUpperCase();
    
    console.log("Código Original Recibido:", codigo.trim());
    console.log("Código Normalizado para Procesar:", codigoNormalizado);

    let urlApi = "";

    if (codigoNormalizado.startsWith("EST-")) {
      urlApi = "/ceia_swga/api/registrar_llegada.php";
    } else if (codigoNormalizado.startsWith("STF-")) {
      urlApi = "/ceia_swga/api/registrar_movimiento_staff.php";
    } else if (codigoNormalizado.startsWith("VEH-")) {
      urlApi = "/ceia_swga/api/registrar_movimiento_vehiculo.php";
    } else {
      mostrarMensaje("QR no reconocido o inválido.", "error");
      procesando = false;
      return;
    }

    const formData = new FormData();
    formData.append("codigo", codigoNormalizado);
    formData.append("timestamp", new Date().toISOString());

    fetch(urlApi, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Error del servidor: ${response.statusText}`);
        }
        return response.json();
      })
      .then((data) => {
        if (data && data.success) {
          mostrarMensaje(data.message || "Operación exitosa.", "exito");
        } else {
          mostrarMensaje(data.message || "Error al procesar la solicitud.", "error");
        }
      })
      .catch((error) => {
        console.error("Error en la función fetch:", error);
        mostrarMensaje(`Error de comunicación: ${error.message}`, "error");
      });
  }

  qrInput.addEventListener("change", (e) => {
    procesarCodigo(e.target.value);
  });

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
});