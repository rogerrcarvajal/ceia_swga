document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-salida');
    const mensajeContainer = document.querySelector('.container');
    const studentSearchInput = document.getElementById('estudiante_search');
    const studentIdInput = document.getElementById('estudiante_id');
    const studentDatalist = document.getElementById('estudiantes_datalist');
    const fechaInput = document.getElementById('fecha_salida');
    const horaInput = document.getElementById('hora_salida');

    // --- Autocompletar fecha y hora ---
    if (fechaInput && horaInput) {
        const now = new Date();

        // Formato YYYY-MM-DD
        const year = now.getFullYear();
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const day = now.getDate().toString().padStart(2, '0');
        fechaInput.value = `${year}-${month}-${day}`;

        // Formato HH:MM
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        horaInput.value = `${hours}:${minutes}`;
    }

    // --- Manejo de búsqueda de estudiantes ---
    if (studentSearchInput) {
        studentSearchInput.addEventListener('input', function(e) {
            const inputValue = e.target.value;
            const options = studentDatalist.options;
            studentIdInput.value = ''; // Reset hidden input

            for (let i = 0; i < options.length; i++) {
                if (options[i].value === inputValue) {
                    studentIdInput.value = options[i].getAttribute('data-value');
                    break;
                }
            }
        });
    }

    // --- Manejo de envío de formulario ---
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validar que se ha seleccionado un estudiante válido
            if (!studentIdInput.value) {
                 displayMessage('Por favor, seleccione un estudiante válido de la lista.', 'error');
                 return;
            }

            const existingAlert = mensajeContainer.querySelector('.alerta');
            if (existingAlert) {
                existingAlert.remove();
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            // Asegurarse de que el id del estudiante se envíe correctamente
            data.estudiante_id = studentIdInput.value;


            try {
                const response = await fetch('/ceia_swga/api/guardar_planilla_salida.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = `/ceia_swga/reports/generar_planilla_salida_pdf.php?id=${result.salida_id}`;
                } else {
                    displayMessage('Error: ' + (result.message || 'No se pudo procesar la solicitud.'), 'error');
                }
            } catch (error) {
                console.error('Error en la solicitud:', error);
                displayMessage('Ocurrió un error de comunicación. Por favor, intente de nuevo.', 'error');
            }
        });
    }

    function displayMessage(message, type = 'error') {
        const existingAlert = mensajeContainer.querySelector('.alerta');
        if (existingAlert) {
            existingAlert.remove();
        }
        const alertDiv = document.createElement('p');
        alertDiv.className = `alerta ${type}`;
        alertDiv.textContent = message;
        mensajeContainer.insertBefore(alertDiv, form);
        window.scrollTo(0, 0);
    }
});