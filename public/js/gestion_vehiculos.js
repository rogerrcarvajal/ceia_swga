document.addEventListener("DOMContentLoaded", () => {
  const semanaInput = document.getElementById("filtro_semana");
  const vehiculoSelect = document.getElementById("filtro_vehiculo");
  const tabla = document.getElementById("tabla_resultados_vehiculos");
  const botonPDF = document.getElementById("btnGenerarPDF");

  semanaInput.addEventListener("change", consultarMovimientos);
  vehiculoSelect.addEventListener("change", consultarMovimientos);
  botonPDF.addEventListener("click", generarPDF);

  function consultarMovimientos() {
    const semana = semanaInput.value;
    const vehiculo = vehiculoSelect.value;

    if (!semana || vehiculo === "") return;

    fetch(
      `/api/consultar_movimiento_vehiculos.php?semana=${semana}&vehiculo_id=${vehiculo}`
        `/api/consultar_movimiento_vehiculos.php?semana=${semana}${vehiculo !== "todos" ? `&vehiculo_id=${vehiculo}` : ""}`
      )
        .then((res) => res.json())
        .then((data) => {
          tabla.innerHTML = "";
          if (data.status !== "ok" || !data.data || data.data.length === 0) {
            tabla.innerHTML = `<tr><td colspan="6" style="text-align:center;">Sin registros para la selección.</td></tr>`;
            return;
          }
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
        });

  function generarPDF() {
    const semana = semanaInput.value;
    const vehiculo = vehiculoSelect.value;

    if (!semana || vehiculo === "") {
      alert("Seleccione una semana y un vehículo.");
      return;
    }

    window.open(
      `/reportes/pdf_movimiento_vehiculos.php?semana=${semana}&vehiculo_id=${vehiculo}`,
      "_blank"
      );
  }

// --- FIN DEL CÓDIGO ---
// Este código JavaScript se ejecuta cuando el DOM está completamente cargado.  