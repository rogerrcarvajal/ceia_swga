document.addEventListener('DOMContentLoaded', () => {
    const tablaBody = document.querySelector('#tabla-autorizaciones tbody');
    const urlParams = new URLSearchParams(window.location.search);
    const estudianteId = urlParams.get('estudiante_id');

    if (!estudianteId) {
        tablaBody.innerHTML = '<tr><td colspan="5">No se ha especificado un estudiante.</td></tr>';
        return;
    }

    fetch(`/ceia_swga/api/obtener_autorizaciones_por_estudiante.php?estudiante_id=${estudianteId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            if (data.length === 0) {
                tablaBody.innerHTML = '<tr><td colspan="5">Este estudiante no tiene autorizaciones de salida registradas.</td></tr>';
                return;
            }

            data.forEach(auth => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${new Date(auth.fecha_salida).toLocaleDateString()}</td>
                    <td>${formatTime(auth.hora_salida)}</td>
                    <td>${escapeHTML(auth.retirado_por_nombre)}</td>
                    <td>${escapeHTML(auth.motivo)}</td>
                    <td><button class="btn btn-pdf" data-id="${auth.id}">Generar PDF</button></td>
                `;
                tablaBody.appendChild(tr);
            });

            // Add event listeners to the new buttons
            document.querySelectorAll('.btn-pdf').forEach(button => {
                button.addEventListener('click', (e) => {
                    const authId = e.target.getAttribute('data-id');
                    window.open(`/ceia_swga/src/reports_generators/generar_autorizacion_pdf.php?id=${authId}`, '_blank');
                });
            });

        })
        .catch(error => {
            console.error('Error al cargar autorizaciones:', error);
            tablaBody.innerHTML = `<tr><td colspan="5">Error al cargar las autorizaciones: ${error.message}</td></tr>`;
        });

    function formatTime(timeString) {
        const [hour, minute] = timeString.split(':');
        const date = new Date();
        date.setHours(hour, minute);
        return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }

    function escapeHTML(str) {
        return str.replace(/[&<>"'/]/g, function (s) {
            const entityMap = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;'
            };
            return entityMap[s];
        });
    }
});
