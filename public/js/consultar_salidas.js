document.addEventListener('DOMContentLoaded', function() {
    const filtroSemana = document.getElementById('filtro_semana');
    const filtroEstudiante = document.getElementById('filtro_estudiante');
    const tablaBody = document.getElementById('tabla_resultados');

    // --- Event Listeners ---
    if (filtroSemana) {
        filtroSemana.addEventListener('change', cargarResultados);
    }
    if (filtroEstudiante) {
        filtroEstudiante.addEventListener('change', cargarResultados);
    }

    // --- Funciones ---
    async function cargarResultados() {
        const semana = filtroSemana.value;
        const estudianteId = filtroEstudiante.value;

        if (!semana) {
            tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Seleccione una semana para ver los registros.</td></tr>';
            return;
        }

        tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>';

        try {
            const response = await fetch(`/ceia_swga/api/consultar_salidas.php?semana=${semana}&estudiante_id=${estudianteId}&_=${new Date().getTime()}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            tablaBody.innerHTML = '';

            if (data.status === 'exito' && data.registros.length > 0) {
                data.registros.forEach(reg => {
                    const fila = `
                        <tr>
                            <td>${reg.fecha_salida || ''}</td>
                            <td>${reg.hora_salida || ''}</td>
                            <td>${reg.nombre_estudiante || ''}</td>
                            <td>${reg.retirado_por_nombre || ''}</td>
                            <td>${reg.retirado_por_parentesco || ''}</td>
                            <td>${reg.motivo || ''}</td>
                        </tr>`;
                    tablaBody.innerHTML += fila;
                });
            } else {
                tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No se encontraron registros para los filtros seleccionados.</td></tr>';
            }
        } catch (error) {
            console.error('Error al cargar los resultados:', error);
            tablaBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color: red;">Error al cargar los datos. ${error.message}</td></tr>`;
        }
    }

    function setInitialWeek() {
        const now = new Date();
        const year = now.getFullYear();
        
        // Función para obtener el número de semana ISO 8601
        const getISOWeek = (date) => {
            const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            return weekNo;
        };

        const week = getISOWeek(now);
        
        if (filtroSemana) {
            filtroSemana.value = `${year}-W${week.toString().padStart(2, '0')}`;
            cargarResultados(); // Cargar resultados para la semana actual
        }
    }

    // --- Inicialización ---
    setInitialWeek();
});