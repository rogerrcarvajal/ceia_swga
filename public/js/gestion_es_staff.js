document.addEventListener("DOMContentLoaded", () => {
  const semanaInput = document.getElementById("filtro_semana");
  const staffSelect = document.getElementById("filtro_staff");
  const tabla = document.getElementById("tabla_resultados_staff");
  const botonPDF = document.getElementById("btnGenerarPDF");

  semanaInput.addEventListener("change", consultarMovimientos);
  staffSelect.addEventListener("change", consultarMovimientos);
  botonPDF.addEventListener("click", generarPDF);

  function consultarMovimientos() {
    const semana = semanaInput.value;
    const staff = staffSelect.value;

    if (!semana || staff === "") return;

    fetch(
      `/api/consultar_movimiento_staff.php?semana=${semana}&staff_id=${staff}`
    )
      .then((res) => res.json())
      .then((data) => {
        tabla.innerHTML = "";

        if (!data.length) {
          tabla.innerHTML = `<tr><td colspan="5" style="text-align:center;">Sin registros para la selección.</td></tr>`;
          return;
        }

        data.forEach((reg) => {
          const fila = document.createElement("tr");
          fila.innerHTML = `
            <td>${reg.nombre}</td>
            <td>${reg.fecha}</td>
            <td>${reg.hora_entrada || "-"}</td>
            <td>${reg.hora_salida || "-"}</td>
            <td>${reg.ausente ? "✔️" : "No"}</td>
          `;
          tabla.appendChild(fila);
        });
      });
  }

  function generarPDF() {
    const semana = semanaInput.value;
    const staff = staffSelect.value;

    if (!semana || staff === "") {
      alert("Seleccione una semana y un miembro del personal.");
      return;
    }

    window.open(
      `/reportes/pdf_movimiento_staff.php?semana=${semana}&staff_id=${staff}`,
      "_blank"
    );
  }
});
