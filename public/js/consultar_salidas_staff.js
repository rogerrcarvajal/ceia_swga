document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DEL DOM ---
    const filtroSemana = document.getElementById('filtro_semana');
    const filtroCategoria = document.getElementById('filtro_categoria');
    const filtroStaff = document.getElementById('filtro_staff');
    const tablaBody = document.getElementById('tabla_resultados');

    // --- EVENT LISTENERS ---
    filtroSemana.addEventListener('change', cargarResultados);
    filtroCategoria.addEventListener('change', () => {
        cargarStaff().then(cargarResultados);
    });
    filtroStaff.addEventListener('change', cargarResultados);

    // --- FUNCIONES ---

    async function cargarStaff() {
        const categoria = filtroCategoria.value;
        filtroStaff.innerHTML = '<option value="todos">Todos</option>'; // Reset

        if (categoria === 'todas') {
            return;
        }

        try {
            const response = await fetch(`/ceia_swga/api/obtener_staff_por_categoria.php?categoria=${categoria}`);
            const data = await response.json();
            if (data.status === 'exito') {
                data.staff.forEach(miembro => {
                    const option = document.createElement('option');
                    option.value = miembro.id;
                    option.textContent = miembro.nombre_completo;
                    filtroStaff.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar personal:', error);
        }
    }

    async function cargarResultados() {
        const semana = filtroSemana.value;
        const categoria = filtroCategoria.value;
        const staffId = filtroStaff.value;

        if (!semana) {
            tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Seleccione una semana para ver los registros.</td></tr>';
            return;
        }

        tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>';

        try {
            const url = `/ceia_swga/api/consultar_salidas_staff.php?semana=${semana}&categoria=${categoria}&staff_id=${staffId}`;
            const response = await fetch(url);
            const data = await response.json();

            tablaBody.innerHTML = '';
            if (data.status === 'exito' && data.registros.length > 0) {
                data.registros.forEach(reg => {
                    const fila = `
                        <tr>
                            <td>${reg.fecha_permiso || ''}</td>
                            <td>${reg.hora_salida || ''}</td>
                            <td>${reg.duracion_horas || ''}</td>
                            <td>${reg.nombre_completo || ''}</td>
                            <td>${reg.categoria || ''}</td>
                            <td>${reg.motivo || ''}</td>
                        </tr>`;
                    tablaBody.innerHTML += fila;
                });
            } else {
                tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No se encontraron registros para los filtros seleccionados.</td></tr>';
            }
        } catch (error) {
            console.error('Error al cargar resultados:', error);
            tablaBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color: red;">Error al cargar los datos.</td></tr>';
        }
    }

    function setInitialWeek() {
        const now = new Date();
        const year = now.getFullYear();
        const getISOWeek = (date) => {
            const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            return weekNo;
        };
        const week = getISOWeek(now);
        filtroSemana.value = `${year}-W${String(week).padStart(2, '0')}`;
    }

    // --- INICIALIZACIÓN ---
    setInitialWeek();
    cargarResultados();
});