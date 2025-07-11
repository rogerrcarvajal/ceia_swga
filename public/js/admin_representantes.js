document.addEventListener('DOMContentLoaded', () => {
    // --- Referencias a elementos principales ---
    const listaEstudiantes = document.getElementById('lista_estudiantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_representantes');
    const filtro = document.getElementById('filtro_estudiantes');

    // --- Event Listeners ---
    if (listaEstudiantes) {
        listaEstudiantes.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                const estudianteId = e.target.dataset.id;
                if (panelInformativo) panelInformativo.style.display = 'none';
                if (panelDatos) panelDatos.style.display = 'block';
                // La función ahora carga los datos de los representantes USANDO el ID del estudiante
                cargarDatosRepresentantes(estudianteId);
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

    // --- Vinculación de formularios para actualización ---
    const formPadre = document.getElementById('form_padre');
    if (formPadre) {
        formPadre.addEventListener('submit', (e) => handleFormSubmit(e, '/api/actualizar_padre.php'));
    }

    const formMadre = document.getElementById('form_madre');
    if (formMadre) {
        formMadre.addEventListener('submit', (e) => handleFormSubmit(e, '/api/actualizar_madre.php'));
    }
});

/**
 * Carga los datos de los representantes (padre y madre) asociados a un estudiante.
 * @param {number} estudianteId - El ID del estudiante seleccionado.
 */
async function cargarDatosRepresentantes(estudianteId) {
    const formPadre = document.getElementById('form_padre');
    const formMadre = document.getElementById('form_madre');

    // Limpiar formularios al inicio
    if (formPadre) formPadre.reset();
    if (formMadre) formMadre.reset();

    try {
        // 1. OBTENER LOS DATOS DEL ESTUDIANTE PARA ENCONTRAR padre_id y madre_id
        const resEstudiante = await fetch(`/api/obtener_estudiante.php?id=${estudianteId}`);
        const dataEstudiante = await resEstudiante.json();

        if (dataEstudiante.error) {
            throw new Error(`No se pudo encontrar al estudiante: ${dataEstudiante.error}`);
        }

        // 2. CARGAR DATOS DEL PADRE USANDO EL dataEstudiante.padre_id
        if (dataEstudiante.padre_id && formPadre) {
            const resPadre = await fetch(`/api/obtener_padre.php?id=${dataEstudiante.padre_id}`);
            const dataPadre = await resPadre.json();
            if (!dataPadre.error) {
                rellenarFormulario(formPadre, dataPadre);
            }
        }

        // 3. CARGAR DATOS DE LA MADRE USANDO EL dataEstudiante.madre_id
        if (dataEstudiante.madre_id && formMadre) {
            const resMadre = await fetch(`/api/obtener_madre.php?id=${dataEstudiante.madre_id}`);
            const dataMadre = await resMadre.json();
            if (!dataMadre.error) {
                rellenarFormulario(formMadre, dataMadre);
            }
        }

    } catch (error) {
        console.error("Error detallado al cargar representantes:", error);
        mostrarMensaje('error', `Error al cargar los datos: ${error.message}`);
    }
}

async function handleFormSubmit(event, url) { event.preventDefault();
    const formData = new FormData(event.target);
    try {
        const response = await fetch(url, { method: 'POST', body: formData });
        if (!response.ok) throw new Error(`Error de red: ${response.statusText}`);
        const result = await response.json();
        mostrarMensaje(result.status, result.message);
    } catch (error) {
        console.error('Error al enviar formulario:', error);
        mostrarMensaje('error', 'Error de comunicación al guardar los datos.');
    }
}

function mostrarMensaje(status, message) { 
    const divMensaje = document.getElementById('mensaje_actualizacion');
    if (divMensaje) {
        divMensaje.className = `mensaje ${status}`;
        divMensaje.textContent = message;
        divMensaje.style.display = 'block';
        setTimeout(() => { divMensaje.style.display = 'none'; }, 4000);
    }
}

function rellenarFormulario(formElement, data) { 
    if (!formElement || !data) return;
    // Recorrer todos los campos del formulario
    for (const key in data) {
        // Buscar el campo por su atributo 'name'
        const field = formElement.querySelector(`[name="${key}"]`);
        // **¡COMPROBACIÓN DE SEGURIDAD!** Si el campo existe, rellenarlo.
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = (data[key] == 1 || data[key] === true);
            } else {
                field.value = data[key] || ''; // Usar '' si el valor es null
            }
        }
    }
}
