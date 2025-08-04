document.addEventListener('DOMContentLoaded', () => {
    const semanaInput = document.getElementById('filtro_semana');
    const vehiculoSelect = document.getElementById('filtro_vehiculo');
    const tablaBody = document.getElementById('tabla_resultados_vehiculos');

    function cargarDatos() {
        const semana = semanaInput.value;
        const vehiculo = vehiculoSelect.value;

        if (!semana) {
            tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Seleccione una semana para ver los registros.</td></tr>';
            return;
        }

        fetch(`/ceia_swga/api/consultar_movimiento_vehiculos.php?semana=${semana}&vehiculo_id=${vehiculo}`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) {
                    tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hay registros para los filtros seleccionados.</td></tr>';
                    return;
                }

                tablaBody.innerHTML = '';
                data.forEach(r => {
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${r.descripcion}</td>
                        <td>${r.fecha}</td>
                        <td>${r.hora_entrada || '-'}</td>
                        <td>${r.hora_salida || '-'}</td>
                        <td>${r.registrado_por || '-'}</td>
                        <td>${r.observaciones || '-'}</td>
                    `;
                    tablaBody.appendChild(fila);
                });
            })
            .catch(err => {
                console.error('Error al cargar los datos:', err);
                tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color: red;">Error al consultar los datos.</td></tr>';
            });
    }

    semanaInput.addEventListener('change', cargarDatos);
    vehiculoSelect.addEventListener('change', cargarDatos);
});