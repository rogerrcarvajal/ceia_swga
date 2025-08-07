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
        endpoint = "/api/registrar_llegada.php";
        payload = { estudiante_id: parseInt(codigo) };
        break;
      case "staff":
        endpoint = "/api/registrar_movimiento_staff.php";
        payload = { staff_id: parseInt(codigo) };
        break;
      case "vehiculo":
        endpoint = "/api/registrar_movimiento_vehiculo.php";
        payload = { vehiculo_id: parseInt(codigo) };
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
        mostrarAlerta(tipo, result);
        agregarAlLog(tipo, result);
      } else {
        mostrarError(result.message);
      }
    } catch (error) {
      mostrarError(error.message);
    }

    limpiarCampo();
  });

  function detectarTipoCodigo(code) {
    if (/^1\d{3,}$/.test(code)) return "estudiante";
    if (/^2\d{3,}$/.test(code)) return "staff";
    if (/^3\d{3,}$/.test(code)) return "vehiculo";
    return null;
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
        <h4>${data.nombre_completo}</h4>
        <p>Posición: ${data.posicion}</p>
        <p>Hora: ${data.hora_llegada || data.hora}</p>
        <p><strong>${data.mensaje}</strong></p>
      `;
    } else if (tipo === "vehiculo") {
      html += `
        <h4>Vehículo de: Familia ${data.apellido_familia}</h4>
        <p>Placa: ${data.placa}</p>
        <p>Modelo: ${data.modelo}</p>
        <p>Hora: ${data.hora_llegada || data.hora}</p>
        <p><strong>${data.mensaje}</strong></p>
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
    const logEntry = document.createElement("div");
    logEntry.className = "log-entry";

    let texto = "";
    if (tipo === "estudiante") {
      texto = `<span>${data.hora_llegada}</span> - <span>${data.nombre_completo}</span> - <span>${data.mensaje}</span>`;
    } else if (tipo === "staff") {
      texto = `<span>${data.hora_llegada || data.hora}</span> - <span>${
        data.nombre_completo
      }</span> - <span>${data.mensaje}</span>`;
    } else if (tipo === "vehiculo") {
      texto = `<span>${data.hora_llegada || data.hora}</span> - <span>Familia ${
        data.apellido_familia
      }</span> - <span>${data.mensaje}</span>`;
    }

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
