document.addEventListener("DOMContentLoaded", () => {
  const qrInput = document.getElementById("qr-input");
  const qrResult = document.getElementById("qr-result");
  let ultimoCodigo = null;
  let timeoutMensaje = null;

  function mostrarMensaje(texto, tipo = "info") {
    qrResult.textContent = texto;
    qrResult.className = `alerta ${tipo}`; // success, error, info
    qrResult.style.display = "block";

    // Ocultar después de 3 segundos
    clearTimeout(timeoutMensaje);
    timeoutMensaje = setTimeout(() => {
      qrResult.style.display = "none";
      qrInput.value = "";
      ultimoCodigo = null;
      qrInput.focus();
    }, 3000);
  }

  function procesarCodigo(codigo) {
    if (!codigo) return;

    // Evita duplicados inmediatos
    if (codigo === ultimoCodigo) return;
    ultimoCodigo = codigo;

    // Limpia y normaliza el código
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

    // Enviar como FormData para que PHP lo lea con $_POST
    const formData = new FormData();
    formData.append("qr_code", codigo); // 🔹 Clave que espera la API

    fetch(urlApi, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          mostrarMensaje(data.message || "Registro exitoso", "exito");
        } else {
          mostrarMensaje(data.message || "Error en el registro", "error");
        }
      })
      .catch((err) => {
        console.error(err);
        mostrarMensaje("Error de conexión con el servidor", "error");
      });
  }

  // Detectar cambio de valor (lector de códigos normalmente lo hace así)
  qrInput.addEventListener("change", (e) => {
    procesarCodigo(e.target.value);
  });

  // Si el lector manda Enter
  qrInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      procesarCodigo(qrInput.value);
    }
  });

  // Mantener el foco en el input siempre
  setInterval(() => {
    if (document.activeElement !== qrInput) {
      qrInput.focus();
    }
  }, 500);
});
