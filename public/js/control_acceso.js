document.addEventListener("DOMContentLoaded", () => {
  const qrInput = document.getElementById("qr-input");
  const mensaje = document.getElementById("mensaje");
  let ultimoCodigo = null;
  let timeoutMensaje = null;

  function mostrarMensaje(texto, tipo = "info") {
    mensaje.textContent = texto;
    mensaje.className = tipo; // success, error, info
    mensaje.style.display = "block";

    // Ocultar después de 3 segundos y limpiar estado
    clearTimeout(timeoutMensaje);
    timeoutMensaje = setTimeout(() => {
      mensaje.style.display = "none";
      qrInput.value = "";
      ultimoCodigo = null; // 🔹 Limpia el código previo
    }, 3000);
  }

  function procesarCodigo(codigo) {
    if (!codigo) return;

    // Evita duplicados inmediatos
    if (codigo === ultimoCodigo) return;
    ultimoCodigo = codigo;

    // Limpia y normaliza el código leído
    codigo = codigo.trim().toUpperCase();

    console.log("Código leído:", codigo);

    let urlApi = "";
    if (codigo.startsWith("STE-")) {
      urlApi = "/ceia_swga/api/registrar_llegada.php";
    } else if (codigo.startsWith("STF-")) {
      urlApi = "/ceia_swga/api/registrar_movimiento_staff.php";
    } else if (codigo.startsWith("VHI-")) {
      urlApi = "/ceia_swga/api/registrar_movimiento_vehiculo.php";
    } else {
      mostrarMensaje("QR no reconocido", "error");
      return;
    }

    // Llamar a la API correspondiente
    fetch(urlApi, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ codigo }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          mostrarMensaje(data.message || "Registro exitoso", "success");
        } else {
          mostrarMensaje(data.message || "Error en el registro", "error");
        }
      })
      .catch((err) => {
        console.error(err);
        mostrarMensaje("Error de conexión con el servidor", "error");
      });
  }

  // Detectar cuando se escanee un código
  qrInput.addEventListener("change", (e) => {
    const codigoLeido = e.target.value;
    procesarCodigo(codigoLeido);
  });

  // Si el lector manda Enter en vez de change
  qrInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      procesarCodigo(qrInput.value);
    }
  });

  // Mantener el foco en el input para el próximo escaneo
  setInterval(() => {
    if (document.activeElement !== qrInput) {
      qrInput.focus();
    }
  }, 500);
});
