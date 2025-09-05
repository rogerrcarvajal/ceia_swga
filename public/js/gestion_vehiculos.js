document.addEventListener("DOMContentLoaded", () => {
  const semanaInput = document.getElementById("filtro_semana");
  const vehiculoSelect = document.getElementById("filtro_vehiculo");
  const tabla = document.getElementById("tabla_resultados_vehiculos");
  const botonPDF = document.getElementById("btnGenerarPDF");

  semanaInput.addEventListener("change", consultarMovimientos);
  vehiculoSelect.addEventListener("change", consultarMovimientos);
  botonPDF.addEventListener("click", generarPDF);

  async function consultarMovimientos() {
    const semana = semanaInput.value;
    const vehiculoId = vehiculoSelect.value;

    if (!semana || vehiculoId === "") {
      tabla.innerHTML = `<tr><td colspan="6" style="text-align:center;">Seleccione una semana y un vehículo.</td></tr>`;
      return;
    }

    tabla.innerHTML = `<tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>`;

    try {
      let url = `/ceia_swga/api/consultar_movimiento_vehiculos.php?semana=${semana}`;
      if (vehiculoId !== "todos") {
        url += `&vehiculo_id=${vehiculoId}`;
      }

      const response = await fetch(url);
      const data = await response.json();

      tabla.innerHTML = ""; // Limpiar tabla

      if (data.status === 'ok' && data.data && data.data.length > 0) {
          data.data.forEach((mov) => {
              const fila = document.createElement("tr");
              fila.innerHTML = `
                  <td>${mov.placa} - ${mov.modelo} (${mov.nombre_completo} ${mov.apellido_completo})</td>
                  <td>${mov.fecha}</td>
                  <td>${mov.hora_entrada || "-"}</td>
                  <td>${mov.hora_salida || "-"}</td>
                  <td>${mov.registrado_por || "-"}</td>
                  <td>${mov.observaciones || "-"}</td>
              `;
              tabla.appendChild(fila);
          });
      } else {
          tabla.innerHTML = `<tr><td colspan="6" style="text-align:center;">${data.message || 'No se encontraron registros para la selección.'}</td></tr>`;
      }
    } catch (error) {
      console.error("Error al consultar movimientos:", error);
      tabla.innerHTML = `<tr><td colspan="6" style="text-align:center;">Error de conexión o en la respuesta del servidor.</td></tr>`;
    }
  }

  function generarPDF() {
    const semana = semanaInput.value;
    const vehiculoId = vehiculoSelect.value;

    if (!semana || vehiculoId === "") {
      alert("Por favor, seleccione una semana y un vehículo.");
      return;
    }

<<<<<<< HEAD
    let url = `/ceia_swga/src/reports_generators/generar_movimiento_vehiculos_pdf.php?semana=${semana}`;
=======
    let url = `/ceia_swga/src/reports_generators/pdf_movimientos_vehiculos.php?semana=${semana}`;
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec
    if (vehiculoId !== "todos") {
      url += `&vehiculo_id=${vehiculoId}`;
    }

    window.open(url, "_blank");
  }

  // --- Lógica para establecer la semana actual al cargar ---
  function getWeekNumber(d) {
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    var weekNo = Math.ceil(((d - yearStart) / 86400000 + 1) / 7);
    return [d.getUTCFullYear(), weekNo];
  }

  // Establecer la semana actual por defecto y cargar datos
  const [anio, semana] = getWeekNumber(new Date());
  semanaInput.value = `${anio}-W${semana.toString().padStart(2, "0")}`;
  consultarMovimientos(); // Carga inicial de datos
});
