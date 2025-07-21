document.addEventListener('DOMContentLoaded', () => {
    const filtroSemana = document.getElementById('filtro_semana');
    const filtroGrado = document.getElementById('filtro_grado');
    const tablaResultados = document.getElementById('tabla_resultados_latepass');

    // Función para cargar los datos
    async function cargarDatos() {
        // '2024-W29' -> extrae '29'
        const semana = filtroSemana.value ? filtroSemana.value.split('-W')[1] : null;
        const grado = filtroGrado.value;

        if (!semana) {
            tablaResultados.innerHTML = '<tr><td colspan="6" style="text-align:center;">Por favor, seleccione una semana.</td></tr>';
            return;
        }
        
        tablaResultados.innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>';

        try {
            const response = await fetch(`/api/consultar_latepass.php?semana=${semana}&grado=${grado}`);
            const data = await response.json();

            tablaResultados.innerHTML = ''; // Limpiar la tabla
            if (data.status === 'exito' && data.registros.length > 0) {
                data.registros.forEach(reg => {
                    const tr = document.createElement('tr');
                    
                    // Lógica de colores para la fila
                    if(reg.conteo_tardes == 2) tr.style.backgroundColor = 'rgba(255, 255, 0, 0.2)'; // Amarillo
                    if(reg.conteo_tardes >= 3) tr.style.backgroundColor = 'rgba(255, 0, 0, 0.2)'; // Rojo

                    // Se corrige el colspan a 6 en el mensaje de "no encontrados"
                    tr.innerHTML = `
                        <td>${reg.nombre_completo} ${reg.apellido_completo}</td>
                        <td>${reg.grado_cursado}</td>
                        <td>${reg.fecha_registro}</td>
                        <td>${reg.hora_llegada}</td>
                        <td style="text-align:center;">${reg.conteo_tardes}</td>
                        <td>${reg.ultimo_mensaje || ''}</td>
                    `;
                    tablaResultados.appendChild(tr);
                });
            } else {
                tablaResultados.innerHTML = `<tr><td colspan="6" style="text-align:center;">${data.message || 'No se encontraron registros para los filtros seleccionados.'}</td></tr>`;
            }
        } catch (error) {
            tablaResultados.innerHTML = `<tr><td colspan="6" style="text-align:center;">Error de conexión o en la respuesta del servidor.</td></tr>`;
        }
    }

    // Añadir listeners para que se actualice al cambiar los filtros
    filtroSemana.addEventListener('change', cargarDatos);
    filtroGrado.addEventListener('change', cargarDatos);

    // Función para calcular la semana ISO actual
    function getWeekNumber(d) {
        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
        var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
        return [d.getUTCFullYear(), weekNo];
    }

    // Establecer la semana actual por defecto
    const [anio, semana] = getWeekNumber(new Date());
    filtroSemana.value = `${anio}-W${semana.toString().padStart(2, '0')}`;
    
    // --- ¡CORRECCIÓN CLAVE! ---
    // Llamar a cargarDatos() una vez que la página ha cargado y la semana ha sido establecida.
    cargarDatos();


// Botón Generar PDF
document.getElementById('btnGenerarPDF').addEventListener('click', () => {
    const semanaRaw = filtroSemana.value;
    const match = semanaRaw.match(/W(\d{1,2})$/);
    const semana = match ? parseInt(match[1], 10) : null;
    const grado = filtroGrado.value;

    if (!semana) {
        alert('Seleccione una semana válida.');
        return;
    }

    window.open(`/src/reports_generators/generar_latepass_pdf.php?semana=${semana}&grado=${encodeURIComponent(grado)}`, '_blank');
});

});
// --- FIN DEL CÓDIGO ---
// Este código JavaScript se ejecuta cuando el DOM está completamente cargado.