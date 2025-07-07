document.addEventListener('DOMContentLoaded', function() {

    const periodoSelect = document.getElementById('periodo-select');
    const tbody = document.getElementById('tabla-profesores-body');
    const statusMessage = document.getElementById('status-message');
    const btnMostrarForm = document.getElementById('btn-mostrar-form-asignar');
    const formAsignarContainer = document.getElementById('form-asignar');
    const formAsignar = document.getElementById('form-asignacion-profesor');
    const btnCancelarAsignacion = document.getElementById('btn-cancelar-asignacion');

    let originalValue = '';

    // --- CARGA INICIAL Y EVENTOS ---
    if (periodoSelect) {
        cargarProfesores(periodoSelect.value);

        periodoSelect.addEventListener('change', () => {
            formAsignarContainer.style.display = 'none';
            cargarProfesores(periodoSelect.value);
        });
    }

    if (btnMostrarForm) {
        btnMostrarForm.addEventListener('click', () => {
            formAsignarContainer.style.display = 'block';
            cargarProfesoresNoAsignados();
            cargarOpcionesHomeroom();
        });
    }
    
    if (btnCancelarAsignacion) {
        btnCancelarAsignacion.addEventListener('click', () => {
            formAsignarContainer.style.display = 'none';
        });
    }
    
    if (formAsignar) {
        formAsignar.addEventListener('submit', guardarNuevaAsignacion);
    }

    // --- FUNCIONES ---
    function cargarProfesores(periodo_id) {
        fetch(`/api/obtener_profesores.php?periodo_id=${periodo_id}`)
            .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
            .then(data => {
                if (!tbody) return;
                tbody.innerHTML = '';
                data.forEach(p => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${p.nombre_completo}</td>
                        <td>${p.cedula}</td>
                        <td data-id="${p.asignacion_id}" data-field="posicion">${p.posicion}</td>
                        <td data-id="${p.asignacion_id}" data-field="homeroom_teacher">${p.homeroom_teacher || 'N/A'}</td>
                        <td>${p.telefono || ''}</td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => mostrarMensaje('Error al cargar los profesores.', 'error'));
    }
    
    // --- CORRECCIÓN CLAVE AQUÍ ---
    function guardarNuevaAsignacion(e) {
        e.preventDefault();
        const formData = new FormData(formAsignar);
        formData.append('periodo_id', periodoSelect.value);

        // La ruta ahora es absoluta, apuntando a la carpeta /api/
        fetch('/api/asignar_profesor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            mostrarMensaje(data.message, data.status);
            if (data.status === 'success') {
                formAsignarContainer.style.display = 'none';
                formAsignar.reset();
                cargarProfesores(periodoSelect.value); // Recargar la tabla para mostrar el nuevo registro
            }
        })
        .catch(error => mostrarMensaje('Error de conexión al guardar.', 'error'));
    }

    function cargarProfesoresNoAsignados() {
        const select = document.getElementById('profesor-a-asignar');
        fetch(`/api/obtener_profesores_no_asignados.php?periodo_id=${periodoSelect.value}`)
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">Seleccione un profesor...</option>';
                data.forEach(p => {
                    select.innerHTML += `<option value="${p.id}">${p.nombre_completo}</option>`;
                });
            });
    }

    function cargarOpcionesHomeroom() {
        const select = document.getElementById('homeroom-asignar');
        select.innerHTML = '';
        const opciones = ["N/A", "Daycare, Pk-3", "Pk-4, Kindergarten", "Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10", "Grade 11", "Grade 12"];
        opciones.forEach(op => {
            const option = document.createElement('option');
            option.value = op;
            option.textContent = op;
            select.appendChild(option);
        });
    }
    
    function mostrarMensaje(mensaje, tipo) {
        if (!statusMessage) return;
        statusMessage.textContent = mensaje;
        statusMessage.className = `status-${tipo}`;
        statusMessage.style.display = 'block';
        setTimeout(() => {
            statusMessage.style.display = 'none';
        }, 4000);
    }

    // El resto de funciones de edición en tabla, si las tienes, van aquí...
});