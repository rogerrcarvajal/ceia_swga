document.addEventListener("DOMContentLoaded", () => {
  const semanaInput = document.getElementById("filtro_semana");
  const staffSelect = document.getElementById("filtro_staff");
  const tabla = document.getElementById("tabla_resultados_staff");
  const botonPDF = document.getElementById("btnGenerarPDF");

  semanaInput.addEventListener("change", consultarMovimientos);
  staffSelect.addEventListener("change", consultarMovimientos);
  botonPDF.addEventListener("click", generarPDF);

  async function consultarMovimientos() {
    const semana = semanaInput.value;
    const staffId = staffSelect.value;

    if (!semana || staffId === "") {
      tabla.innerHTML = `<tr><td colspan="5" style="text-align:center;">Seleccione una semana y un miembro del personal.</td></tr>`;
      return;
    }

    tabla.innerHTML = `<tr><td colspan="5" style="text-align:center;">Cargando...</td></tr>`;

    try {
      let url = `/ceia_swga/api/consultar_movimiento_staff.php?semana=${semana}`;
      if (staffId !== "todos") {
        url += `&staff_id=${staffId}`;
      }

      const response = await fetch(url);
      const data = await response.json();

      tabla.innerHTML = ""; // Limpiar tabla

      if (data.status === "ok" && data.data && data.data.length > 0) {
        data.data.forEach((reg) => {
          const fila = document.createElement("tr");
          fila.innerHTML = `
              <td>${reg.nombre_completo}</td>
              <td>${reg.fecha}</td>
              <td>${reg.hora_entrada || "-"}</td>
              <td>${reg.hora_salida || "-"}</td>
              <td>${reg.ausente ? "✔️" : "No"}</td>
            `;
          tabla.appendChild(fila);
        });
      } else {
        tabla.innerHTML = `<tr><td colspan="5" style="text-align:center;">${
          data.message || "No se encontraron registros para la selección."
        }</td></tr>`;
      }
    } catch (error) {
      console.error("Error al consultar movimientos del staff:", error);
      tabla.innerHTML = `<tr><td colspan="5" style="text-align:center;">Error de conexión o en la respuesta del servidor.</td></tr>`;
    }
  }

  function generarPDF() {
    const semana = semanaInput.value;
    const staffId = staffSelect.value;

    if (!semana || staffId === "") {
      alert("Por favor, seleccione una semana y un miembro del personal.");
      return;
    }

    let url = `/ceia_swga/src/reports_generators/generar_movimiento_staff_pdf.php?semana=${semana}`;
    if (staffId !== "todos") {
      url += `&staff_id=${staffId}`;
    }

    window.open(url, "_blank");
  }
});
