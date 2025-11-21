// La función se mueve al ámbito global para ser accesible desde el atributo onclick
function reimprimirAutorizacion(id) {
    if (!id) {
        console.error('ID de autorización no válido para reimprimir.');
        return;
    }
    const url = `/ceia_swga/pages/reimprimir_permiso_staff.php?id=${id}`;
    window.open(url, '_blank');
}

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
            // Si la categoría es "todas", no cargamos staff específico
            return;
        }

        try {
            const response = await fetch(`/ceia_swga/api/obtener_staff_por_categoria.php?categoria=${encodeURIComponent(categoria)}`);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();

            if (data.status === 'exito') {
                data.staff.forEach(miembro => {
                    const option = document.createElement('option');
                    option.value = miembro.id;
                    option.textContent = miembro.nombre_completo;
                    filtroStaff.appendChild(option);
                });
            } else {
                console.error('Error del API al cargar staff:', data.mensaje);
            }
        } catch (error) {
            console.error('Error de red o de parsing al cargar personal:', error);
        }
    }

    async function cargarResultados() {
        const semana = filtroSemana.value;
        const categoria = filtroCategoria.value;
        const staffId = filtroStaff.value;

        if (!semana) {
            tablaBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Seleccione una semana para ver los registros.</td></tr>';
            return;
        }

        tablaBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Cargando...</td></tr>';

        try {
            const url = new URL('/ceia_swga/api/consultar_salidas_staff.php', window.location.origin);
            url.searchParams.append('semana', semana);
            url.searchParams.append('categoria', categoria);
            url.searchParams.append('staff_id', staffId);
            
            const response = await fetch(url);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();

            tablaBody.innerHTML = '';
            if (data.status === 'exito' && data.registros.length > 0) {
                data.registros.forEach(reg => {
                    // Se añade la columna de acciones con el botón de reimprimir
                    const fila = `
                        <tr>
                            <td>${reg.fecha_permiso || 'N/A'}</td>
                            <td>${reg.hora_salida ? new Date('1970-01-01T' + reg.hora_salida).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : 'N/A'}</td>
                            <td>${reg.duracion_horas || 'N/A'}</td>
                            <td>${reg.nombre_completo || 'N/A'}</td>
                            <td>${reg.categoria || 'N/A'}</td>
                            <td>${reg.motivo || ''}</td>
                            <td>
                                <button onclick="reimprimirAutorizacion(${reg.id})" class="btn" style="margin: 0; padding: 8px 12px; font-size: 0.9em; background-color: #6c757d;">Reimprimir</button>
                            </td>
                        </tr>`;
                    tablaBody.innerHTML += fila;
                });
            } else {
                tablaBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No se encontraron registros para los filtros seleccionados.</td></tr>';
            }
        } catch (error) {
            console.error('Error al cargar resultados:', error);
            tablaBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color: red;">Error al cargar los datos. ${error.message}</td></tr>`;
        }
    }

    function setInitialWeek() {
        const now = new Date();
        const year = now.getFullYear();
        // Lógica para obtener el número de semana ISO
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
    cargarStaff().then(cargarResultados); // Cargar staff y luego resultados al inicio
});