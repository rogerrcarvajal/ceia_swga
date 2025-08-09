document.addEventListener("DOMContentLoaded", () => {
  const qrForm = document.getElementById("qr-form");
  const qrInput = document.getElementById("qr-input");
  const resultDiv = document.getElementById("qr-result");
  const logDiv = document.getElementById("log-registros");

  qrInput.focus();
  iniciarRelojDigital();

  qrForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const codigo = qrInput.value.trim();
    if (!codigo) return;

    const tipo = detectarTipoCodigo(codigo);
    if (!tipo) {
      mostrarError("Código no reconocido. Verifique el QR.");
      limpiarCampo();
      return;
    }

    let endpoint = "";
    let payload = {};

    // Seleccionar endpoint y payload por tipo de QR
    switch (tipo) {
      case "estudiante":
        endpoint = "/ceia_swga/api/registrar_llegada.php";
        payload = { estudiante_id: parseInt(codigo) };
        break;
      case "staff":
        endpoint = "/ceia_swga/api/registrar_movimiento_staff.php";
        payload = { qr_id: parseInt(codigo) };
        break;
      case "vehiculo":
        endpoint = "/ceia_swga/api/registrar_movimiento_vehiculo.php";
        payload = { qr_id: parseInt(codigo) };
        break;
    }

    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      if (!response.ok) throw new Error("Error del servidor.");
      const result = await response.json();

      if (result.status === "exito") {
        // Si el backend devuelve 'registros', usar el primero
        const data = result.registros ? result.registros[0] : result;
        mostrarAlerta(tipo, data);
        agregarAlLog(tipo, data);
      } else {
        mostrarError(result.message || "Error inesperado.");
      }
    } catch (error) {
      mostrarError(error.message);
    }

    limpiarCampo();
  });

  function detectarTipoCodigo(codigo) {
    const id = parseInt(codigo, 10);
    if (isNaN(id)) {
      return null; // El código no es numérico
    }

    if (id >= 1 && id <= 9999) {
      return "estudiante";
    }
    if (id >= 10000 && id <= 19999) {
      return "staff";
    }
    if (id >= 20000) {
      return "vehiculo";
    }
    return null; // El código está fuera de los rangos definidos
  }

  function mostrarAlerta(tipo, data) {
    let html = `<div class="reloj-digital" id="reloj"></div>`;
    let colorClass = "exito";
    if (tipo === "estudiante") {
      if (data.es_tarde) {
        if (data.conteo_tardes === 2) colorClass = "advertencia";
        if (data.conteo_tardes >= 3) colorClass = "error";
      }
      html += `
        <h4>${data.nombre_completo}</h4>
        <p>Grado: ${data.grado}</p>
        <p>Hora de Registro: ${data.hora_llegada}</p>
        <p><strong>${data.mensaje}</strong></p>
      `;
    } else if (tipo === "staff") {
      html += `
        <h4>${data.nombre || data.nombre_completo || ""}</h4>
        <p>Posición: ${data.posicion || "No asignada"}</p>
        <p>Hora Entrada: ${data.hora || data.hora_entrada || ""}</p>
        <p><strong>${data.mensaje || "Movimiento registrado."}</strong></p>
      `;
    } else if (tipo === "vehiculo") {
      html += `
        <h4>${data.descripcion || "Vehículo"}</h4>
        <p>Fecha: ${data.fecha || ""}</p>
        <p>Hora Entrada: ${data.hora_entrada || data.hora || ""}</p>
        <p>Hora Salida: ${data.hora_salida || "-"}</p>
        <p><strong>${
          data.mensaje || "Movimiento de vehículo registrado."
        }</strong></p>
      `;
    }
    resultDiv.className = `alerta ${colorClass}`;
    resultDiv.innerHTML = html;
    resultDiv.style.display = "block";
    setTimeout(() => {
      resultDiv.style.display = "none";
    }, 6000);
  }

  function mostrarError(msg) {
    resultDiv.className = "alerta error";
    resultDiv.innerHTML = `<div class="reloj-digital" id="reloj"></div><h4>Error</h4><p>${msg}</p>`;
    resultDiv.style.display = "block";

    setTimeout(() => {
      resultDiv.style.display = "none";
    }, 6000);
  }

  function agregarAlLog(tipo, data) {
    if (tipo !== "estudiante") return; // Solo mostrar en el log los estudiantes
    const logEntry = document.createElement("div");
    logEntry.className = "log-entry";
    let texto = `<span>${
      data.hora_llegada || data.hora_entrada || ""
    }</span> - <span>${data.nombre_completo || ""}</span> - <span>${
      data.mensaje || data.observacion || ""
    }</span>`;
    logEntry.innerHTML = texto;
    logDiv.insertBefore(logEntry, logDiv.firstChild);
  }

  function limpiarCampo() {
    qrInput.value = "";
    qrInput.focus();
  }

  function iniciarRelojDigital() {
    setInterval(() => {
      const now = new Date();
      const hora = now.toLocaleTimeString("es-VE", { hour12: false });
      const reloj = document.getElementById("reloj");
      if (reloj) reloj.textContent = hora;
    }, 1000);
  }
});
