document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DEL DOM ---
    const form = document.getElementById('form-salida-staff');
    const categoriaSelect = document.getElementById('categoria');
    const staffSelect = document.getElementById('profesor_id');
    const posicionInput = document.getElementById('posicion');
    const cedulaInput = document.getElementById('cedula');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnGenerarPDF = document.getElementById('btnGenerarPDF');
    const alertContainer = document.getElementById('alert-container');

    let nuevaAutorizacionId = null;

    // --- EVENT LISTENERS ---

    // Cargar personal al cambiar la categoría
    categoriaSelect.addEventListener('change', cargarPersonalPorCategoria);

    // Actualizar campos al cambiar el personal
    staffSelect.addEventListener('change', actualizarCamposPersonal);

    // Manejar el envío del formulario
    form.addEventListener('submit', guardarAutorizacion);

    // Generar PDF
    btnGenerarPDF.addEventListener('click', function() {
        if (nuevaAutorizacionId) {
            // Se necesitará un nuevo script para generar este PDF
            window.open(`/ceia_swga/src/reports_generators/generar_permiso_staff_pdf.php?id=${nuevaAutorizacionId}`, '_blank');
        }
    });

    // --- FUNCIONES ---

    async function cargarPersonalPorCategoria() {
        const categoria = categoriaSelect.value;
        staffSelect.innerHTML = '<option value="">Cargando...</option>';
        posicionInput.value = '';
        cedulaInput.value = '';

        if (!categoria) {
            staffSelect.innerHTML = '<option value="">Seleccione una categoría primero...</option>';
            return;
        }

        try {
            const response = await fetch(`/ceia_swga/api/obtener_staff_por_categoria.php?categoria=${categoria}`);
            const data = await response.json();

            if (data.status === 'exito') {
                staffSelect.innerHTML = '<option value="">Seleccione un miembro del personal...</option>';
                data.staff.forEach(miembro => {
                    const option = document.createElement('option');
                    option.value = miembro.id;
                    option.textContent = miembro.nombre_completo;
                    option.dataset.posicion = miembro.posicion || '';
                    option.dataset.cedula = miembro.cedula || '';
                    staffSelect.appendChild(option);
                });
            } else {
                staffSelect.innerHTML = `<option value="">Error: ${data.mensaje}</option>`;
            }
        } catch (error) {
            staffSelect.innerHTML = '<option value="">Error al cargar datos.</option>';
            console.error('Error en fetch:', error);
        }
    }

    function actualizarCamposPersonal() {
        const selectedOption = staffSelect.options[staffSelect.selectedIndex];
        posicionInput.value = selectedOption.dataset.posicion || 'No disponible';
        cedulaInput.value = selectedOption.dataset.cedula || 'No disponible';
    }

    async function guardarAutorizacion(event) {
        event.preventDefault(); // Evitar el envío tradicional
        
        const formData = new FormData(form);
        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';

        try {
            const response = await fetch('../api/guardar_autorizacion_staff.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.status === 'exito') {
                mostrarAlerta('success', data.mensaje);
                nuevaAutorizacionId = data.id; // Guardar el ID devuelto por la API
                btnGenerarPDF.disabled = false; // Habilitar el botón de PDF
                form.reset(); // Limpiar el formulario
                setInitialDate(); // Restablecer la fecha a hoy
                // Opcional: redirigir después de un tiempo
                setTimeout(() => {
                    if(data.redirect) {
                       // window.location.href = data.redirect;
                    }
                }, 3000);
            } else {
                mostrarAlerta('danger', data.mensaje || 'Ocurrió un error desconocido.');
            }

        } catch (error) {
            mostrarAlerta('danger', 'Error de conexión. No se pudo guardar la autorización.');
            console.error('Error en el fetch de guardado:', error);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.textContent = 'Guardar Autorización';
        }
    }

    function mostrarAlerta(tipo, mensaje) {
        alertContainer.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    function setInitialDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fecha_permiso').value = today;
    }

    // --- INICIALIZACIÓN ---
    setInitialDate();
});
