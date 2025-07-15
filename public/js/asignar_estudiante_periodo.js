document.addEventListener('DOMContentLoaded', () => {
    const periodoSelector = document.getElementById('periodo_selector');

    periodoSelector.addEventListener('change', () => {
        const periodoId = periodoSelector.value;
        const panelInformativo = document.getElementById('panel_informativo');
        const panelAsignacion = document.getElementById('panel_asignacion');
        
        if (periodoId) {
            panelInformativo.style.display = 'none';
            panelAsignacion.style.display = 'block';
            document.getElementById('periodo_id_hidden').value = periodoId;

            // Cargar ambas listas
            cargarEstudiantesAsignados(periodoId);
            cargarEstudiantesNoAsignados();
        } else {
            panelInformativo.style.display = 'block';
            panelAsignacion.style.display = 'none';
        }
    });

    document.getElementById('form_asignar_estudiante').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const periodoId = formData.get('periodo_id');
        
        // Validar que se seleccionó un estudiante
        if (!formData.get('estudiante_id')) {
            alert('Por favor, seleccione un estudiante de la lista.');
            return;
        }

        try {
            const response = await fetch('/api/asignar_estudiante_periodo.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'exito') {
                // Recargar listas para mostrar el cambio al instante
                cargarEstudiantesAsignados(periodoId);
                cargarEstudiantesNoAsignados();
            } else {
                alert('Error al asignar: ' + result.message);
            }
        } catch (error) {
            alert('Error de conexión al asignar el estudiante.');
            console.error(error);
        }
    });
});

async function cargarEstudiantesAsignados(periodoId) {
    const listaUI = document.getElementById('lista_estudiantes_asignados');
    listaUI.innerHTML = '<li>Cargando...</li>'; // Feedback para el usuario

    try {
        const response = await fetch(`/api/obtener_estudiantes_asignados.php?periodo_id=${periodoId}`);
        const estudiantes = await response.json();

        listaUI.innerHTML = ''; // Limpiar la lista
        if (estudiantes.length > 0) {
            estudiantes.forEach(est => {
                const li = document.createElement('li');
                li.textContent = `${est.apellido_completo}, ${est.nombre_completo} (${est.grado_ingreso})`;
                listaUI.appendChild(li);
            });
        } else {
            listaUI.innerHTML = '<li>No hay estudiantes asignados a este período.</li>';
        }
    } catch (error) {
        listaUI.innerHTML = '<li>Error al cargar los estudiantes.</li>';
    }
}

async function cargarEstudiantesNoAsignados() {
    const selectUI = document.getElementById('estudiante_id');
    selectUI.innerHTML = '<option>Cargando...</option>';

    try {
        const response = await fetch(`/api/obtener_estudiantes_no_asignados.php`);
        const estudiantes = await response.json();
        
        selectUI.innerHTML = '<option value="">-- Elija un estudiante para asignar --</option>'; // Opción por defecto
        if (estudiantes.length > 0) {
            estudiantes.forEach(est => {
                const option = document.createElement('option');
                option.value = est.id;
                option.textContent = `${est.apellido_completo}, ${est.nombre_completo}`;
                selectUI.appendChild(option);
            });
        } else {
            selectUI.innerHTML = '<option value="">No hay estudiantes disponibles para asignar.</option>';
        }
    } catch (error) {
        selectUI.innerHTML = '<option>Error al cargar la lista.</option>';
    }
}