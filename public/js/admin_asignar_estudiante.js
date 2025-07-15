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
            cargarEstudiantesAsignados(periodoId);
            cargarEstudiantesNoAsignados(periodoId);
        } else {
            panelInformativo.style.display = 'block';
            panelAsignacion.style.display = 'none';
        }
    });

    document.getElementById('form_asignar_estudiante').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const periodoId = formData.get('periodo_id');
        if (!formData.get('estudiante_id')) {
            alert('Por favor, seleccione un estudiante.');
            return;
        }

        try {
            const response = await fetch('/api/asignar_estudiante.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.status === 'exito') {
                cargarEstudiantesAsignados(periodoId);
                cargarEstudiantesNoAsignados(periodoId);
                mostrarMensaje(result.status, result.message);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            mostrarMensaje('error', `Error: ${error.message}`);
        }
    });
});

async function cargarEstudiantesAsignados(periodoId) {
    const listaUI = document.getElementById('lista_estudiantes_asignados');
    listaUI.innerHTML = '<li>Cargando...</li>';
    const response = await fetch(`/api/obtener_estudiantes_por_periodo.php?periodo_id=${periodoId}`);
    const estudiantes = await response.json();
    listaUI.innerHTML = '';
    if (estudiantes.length > 0) {
        estudiantes.forEach(est => {
            const li = document.createElement('li');
            li.textContent = `${est.apellido_completo}, ${est.nombre_completo} (${est.grado_cursado})`;
            listaUI.appendChild(li);
        });
    } else {
        listaUI.innerHTML = '<li>No hay estudiantes asignados.</li>';
    }
}

async function cargarEstudiantesNoAsignados(periodoId) {
    const selectUI = document.getElementById('estudiante_id');
    selectUI.innerHTML = '<option>Cargando...</option>';
    const response = await fetch(`/api/obtener_estudiantes_no_asignados.php?periodo_id=${periodoId}`);
    const estudiantes = await response.json();
    selectUI.innerHTML = '<option value="">-- Elija un estudiante para asignar --</option>';
    if (estudiantes.length > 0) {
        estudiantes.forEach(est => {
            const option = document.createElement('option');
            option.value = est.id;
            option.textContent = `${est.apellido_completo}, ${est.nombre_completo}`;
            selectUI.appendChild(option);
        });
    } else {
        selectUI.innerHTML = '<option value="">No hay estudiantes disponibles.</option>';
    }
}

function mostrarMensaje(status, message) {
    const div = document.getElementById('mensaje_asignacion');
    div.className = `mensaje ${status}`;
    div.textContent = message;
    div.style.display = 'block';
    setTimeout(() => { div.style.display = 'none'; }, 4000);
}