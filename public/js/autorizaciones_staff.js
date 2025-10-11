document.addEventListener('DOMContentLoaded', () => {
    const tablaBody = document.querySelector('#tabla-autorizaciones tbody');
    const urlParams = new URLSearchParams(window.location.search);
    const staffId = urlParams.get('staff_id');

    if (!staffId) {
        tablaBody.innerHTML = '<tr><td colspan="5">No se ha especificado un miembro del staff.</td></tr>';
        return;
    }

    fetch(`/ceia_swga/api/obtener_autorizaciones_por_staff.php?staff_id=${staffId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            if (data.length === 0) {
                tablaBody.innerHTML = '<tr><td colspan="5">Este miembro del staff no tiene autorizaciones de salida registradas.</td></tr>';
                return;
            }

            data.forEach(auth => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${new Date(auth.fecha_permiso).toLocaleDateString()}</td>
                    <td>${formatTime(auth.hora_salida)}</td>
                    <td>${escapeHTML(auth.duracion_horas)}</td>
                    <td>${escapeHTML(auth.motivo)}</td>
                    <td><button class="btn btn-pdf" data-id="${auth.id}">Generar PDF</button></td>
                `;
                tablaBody.appendChild(tr);
            });

            // Add event listeners to the new buttons
            document.querySelectorAll('.btn-pdf').forEach(button => {
                button.addEventListener('click', (e) => {
                    const authId = e.target.getAttribute('data-id');
                    window.open(`/ceia_swga/src/reports_generators/generar_permiso_staff_pdf.php?id=${authId}`, '_blank');
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
        if (str === null || str === undefined) {
            return '';
        }
        return str.toString().replace(/[&<>"'/]/g, function (s) {
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
