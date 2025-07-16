document.addEventListener('DOMContentLoaded', () => {
    const filtroSemana = document.getElementById('filtro_semana');
    const filtroGrado = document.getElementById('filtro_grado');
    const tablaResultados = document.getElementById('tabla_resultados_latepass');

    // Función para cargar los datos
    async function cargarDatos() {
        // '2024-W29' -> extrae '29'
        const semana = filtroSemana.value ? filtroSemana.value.split('-W')[1] : null;
        const grado = filtroGrado.value;

        // No hacer nada si no se ha seleccionado una semana
        if (!semana) {
            tablaResultados.innerHTML = '<tr><td colspan="5" style="text-align:center;">Por favor, seleccione una semana.</td></tr>';
            return;
        }
        
        tablaResultados.innerHTML = '<tr><td colspan="5" style="text-align:center;">Cargando...</td></tr>';

        try {
            const response = await fetch(`/api/consultar_latepass.php?semana=${semana}&grado=${grado}`);
            const data = await response.json();

            tablaResultados.innerHTML = ''; // Limpiar la tabla
            if (data.status === 'exito' && data.registros.length > 0) {
                data.registros.forEach(reg => {
                    const tr = document.createElement('tr');
                    
                    // Colorear la fila según el número de strikes
                    if(reg.conteo_tardes == 2) tr.style.backgroundColor = 'rgba(255, 255, 0, 0.2)'; // Amarillo
                    if(reg.conteo_tardes >= 3) tr.style.backgroundColor = 'rgba(255, 0, 0, 0.2)'; // Rojo

                    tr.innerHTML = `
                        <td>${reg.nombre_completo} ${reg.apellido_completo}</td>
                        <td>${reg.grado_cursado}</td>
                        <td>${reg.fecha_registro}</td>
                        <td>${reg.hora_llegada}</td>
                        <td style="text-align:center;">${reg.conteo_tardes}</td>
                    `;
                    tablaResultados.appendChild(tr);
                });
            } else {
                tablaResultados.innerHTML = `<tr><td colspan="5" style="text-align:center;">${data.message || 'No se encontraron registros para los filtros seleccionados.'}</td></tr>`;
            }
        } catch (error) {
            tablaResultados.innerHTML = `<tr><td colspan="5" style="text-align:center;">Error de conexión.</td></tr>`;
        }
    }

    // Añadir listeners para que se actualice al cambiar los filtros
    filtroSemana.addEventListener('change', cargarDatos);
    filtroGrado.addEventListener('change', cargarDatos);

    // Opcional: Establecer la semana actual por defecto
    const hoy = new Date();
    const anio = hoy.getFullYear();
    const semana = Math.ceil((((hoy - new Date(anio, 0, 1)) / 86400000) + new Date(anio, 0, 1).getDay() + 1) / 7);
    filtroSemana.value = `${anio}-W${semana.toString().padStart(2, '0')}`;
});