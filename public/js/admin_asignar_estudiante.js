document.addEventListener('DOMContentLoaded', () => {
    const listaUI = document.getElementById('lista_estudiantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_estudiante');
    const periodoSelector = document.getElementById('periodo_selector');
    const filtro = document.getElementById('filtro_estudiantes');

    // --- Event Listeners ---
    if (listaUI) {
        listaUI.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                const estudianteId = e.target.dataset.id;
                if (panelInformativo) panelInformativo.style.display = 'none';
                if (panelDatos) panelDatos.style.display = 'block';
                cargarExpedienteCompleto(estudianteId);
            }
        });
    }

    if (filtro) {
        filtro.addEventListener('keyup', () => {
            const texto = filtro.value.toLowerCase();
            document.querySelectorAll('#lista_estudiantes li').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }

    // Vinculamos el formulario a la misma función genérica de envío
    //document.getElementById('form_asignar_estudiante').addEventListener('submit', (e) => handleFormSubmit(e, '/api/asignar_estudiante.php'));

    
    const gestionarBtn = document.getElementById('gestionar_estudiantes_btn');

    // --- Event Listeners ---
    if (listaUI) {
        listaUI.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                const estudianteId = e.target.dataset.id;
                if (panelInformativo) panelInformativo.style.display = 'none';
                if (panelDatos) panelDatos.style.display = 'block';
                cargarExpedienteCompleto(estudianteId);
            }
        });
    }

    if (filtro) {
        filtro.addEventListener('keyup', () => {
            const texto = filtro.value.toLowerCase();
            document.querySelectorAll('#lista_estudiantes li').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }

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

            // Actualizar y mostrar el botón de gestionar
            gestionarBtn.href = `/ceia_swga/pages/lista_gestion_estudiantes.php?periodo_id=${periodoId}`;
            gestionarBtn.style.display = 'inline-block'; // o 'block'

        } else {
            panelInformativo.style.display = 'block';
            panelAsignacion.style.display = 'none';
            gestionarBtn.style.display = 'none';
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
            const response = await fetch('/ceia_swga/api/asignar_estudiante.php', { method: 'POST', body: formData });
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

// --- FUNCIONES ---

async function cargarEstudiantesAsignados(periodoId) {
    const listaUI = document.getElementById('lista_estudiantes_asignados');
    listaUI.innerHTML = '<li>Cargando...</li>';
    const response = await fetch(`/ceia_swga/api/obtener_estudiantes_por_periodo.php?periodo_id=${periodoId}`);
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
    const response = await fetch(`/ceia_swga/api/obtener_estudiantes_no_asignados.php?periodo_id=${periodoId}`);
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