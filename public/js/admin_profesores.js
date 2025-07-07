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


    // --- HELPERS PARA CREAR SELECTS DINÁMICOS ---
    function crearSelectPosicion(selectedValue) {
        const select = document.createElement('select');
        const opciones = [
            "Director", "Bussiness Manager", "Administrative Assistant", "IT Manager", "Psychology",
            "DC-Grade 12 Music", "Daycare, Pk-3", "Pk-4, Kindergarten", "Grade 1", "Grade 2", "Grade 3",
            "Grade 4", "Grade 5", "Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10", "Grade 11",
            "Grade 12", "Spanish teacher - Grade 1-6", "Spanish teacher - Grade 7-12",
            "Social Studies - Grade 6-12", "IT Teacher - Grade Pk-3-12", "Science Teacher - Grade 6-12",
            "ESL - Elementary", "ESL - Secondary", "PE - Grade Pk3-12", "Language Arts - Grade 6-9",
            "Math teacher - Grade 6-9", "Math teacher - Grade 10-12", "Librarian"
        ];
        opciones.forEach(op => {
            const option = document.createElement('option');
            option.value = op;
            option.textContent = op;
            if (op === selectedValue) option.selected = true;
            select.appendChild(option);
        });
        return select;
    }

    // --- FUNCIONES ---
    function cargarProfesores(periodo_id) {
        fetch(`../api/obtener_profesores.php?periodo_id=${periodo_id}`)
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

    // --- CARGA DE PROFESORES NO ASIGNADOS ---
    function cargarProfesoresNoAsignados() {
        const select = document.getElementById('profesor-a-asignar');
        fetch(`../api/obtener_profesores_no_asignados.php?periodo_id=${periodoSelect.value}`)
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">Seleccione un profesor...</option>';
                data.forEach(p => {
                    select.innerHTML += `<option value="${p.id}">${p.nombre_completo}</option>`;
                });
            });
    }
    // --- CARGA DE OPCIONES PARA HOMEROOM --
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

    // --- CORRECCIÓN CLAVE AQUÍ ---
    function guardarNuevaAsignacion(e) {
        e.preventDefault();
        const formData = new FormData(formAsignar);
        formData.append('periodo_id', periodoSelect.value);

        // La ruta ahora es absoluta, apuntando a la carpeta /api/
        fetch('../api/asignar_profesor.php', {
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
    
    function mostrarMensaje(mensaje, tipo) {
        statusMessage.textContent = mensaje;
        statusMessage.className = `status-${tipo}`;
        statusMessage.style.display = 'block';
        setTimeout(() => statusMessage.style.display = 'none', 4000);
    }

    // El resto de funciones de edición en tabla, si las tienes, van aquí...

        // --- LÓGICA DE EDICIÓN EN LA TABLA ---
    tbody.addEventListener('click', function(e) {
        const cell = e.target.closest('td[data-field]');
        if (!cell || cell.querySelector('input, select')) return;

        originalValue = cell.textContent.trim();
        const field = cell.dataset.field;
        let editor;

        // ----- CORRECCIÓN APLICADA AQUÍ -----
        if (field === 'homeroom_teacher') {
            editor = crearSelectHomeroom(originalValue);
        } else if (field === 'posicion') { // Se añade la condición para el campo 'posicion'
            editor = crearSelectPosicion(originalValue);
        } else { // Esto ya no debería ocurrir para los campos editables
            editor = document.createElement('input');
            editor.type = 'text';
            editor.value = originalValue;
        }

        cell.innerHTML = '';
        cell.appendChild(editor);
        editor.focus();

        editor.addEventListener('blur', () => guardarCambio(cell, editor));
        editor.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') editor.blur();
            if (e.key === 'Escape') cell.innerHTML = originalValue;
        });
    });

    // --- FUNCIÓN PARA GUARDAR CAMBIOS DE UNA CELDA EDITADA ---
    function guardarCambio(cell, editor) {
        const newValue = editor.value;
        if (newValue.trim() === originalValue) {
            cell.innerHTML = originalValue;
            return;
        }

        const formData = new FormData();
        formData.append('id', cell.dataset.id);
        formData.append('field', cell.dataset.field);
        formData.append('value', newValue);

        fetch('actualizar_profesores.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cell.innerHTML = newValue || (cell.dataset.field === 'homeroom_teacher' ? 'N/A' : '');
                    mostrarMensaje(data.message, 'success');
                } else {
                    cell.innerHTML = originalValue;
                    mostrarMensaje(data.message || 'Error desconocido.', 'error');
                }
            }).catch(() => mostrarMensaje('Error de conexión.', 'error'));
    }
    
});
// --- FIN DEL CÓDIGO ---
// Este código JavaScript se ejecuta cuando el DOM está completamente cargado.