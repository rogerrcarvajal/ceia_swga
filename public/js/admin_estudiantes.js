document.addEventListener('DOMContentLoaded', () => {
    // --- Referencias a elementos principales ---
    const listaUI = document.getElementById('lista_estudiantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_estudiante');
    const filtro = document.getElementById('filtro_estudiantes');

    // --- Event Listeners ---
    if (listaUI) {
        listaUI.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                const estudianteId = e.target.dataset.id;
                if (panelInformativo) panelInformativo.style.display = 'none';
                if (panelDatos) panelDatos.style.display = 'block';
                cargarDatosCompletos(estudianteId);
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

    const formEstudiante = document.getElementById('form_estudiante');
    if (formEstudiante) {
        formEstudiante.addEventListener('submit', (e) => handleFormSubmit(e, '/api/actualizar_estudiante.php'));
    }

    const formFichaMedica = document.getElementById('form_ficha_medica');
    if (formFichaMedica) {
        formFichaMedica.addEventListener('submit', (e) => handleFormSubmit(e, '/api/actualizar_ficha_medica.php'));
    }
});

/**
 * Función genérica para manejar el envío de formularios.
 * @param {Event} event - El evento de submit.
 * @param {string} url - La URL de la API a la que se enviarán los datos.
 */
async function handleFormSubmit(event, url) {
    event.preventDefault();
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

/**
 * Rellena un formulario de forma segura con los datos de un objeto.
 * @param {HTMLFormElement} formElement - El elemento del formulario a rellenar.
 * @param {object} data - El objeto con los datos.
 */
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

/**
 * Carga todos los datos de un estudiante desde las APIs.
 * @param {number} id - El ID del estudiante.
 */
async function cargarDatosCompletos(id) {
    try {
        // --- Cargar datos del Estudiante ---
        const resEst = await fetch(`/api/obtener_estudiante.php?id=${id}`);
        const dataEst = await resEst.json();
        if (dataEst.error) throw new Error(`API Estudiante: ${dataEst.error}`);
        
        const formEstudiante = document.getElementById('form_estudiante');
        formEstudiante.reset(); // Limpiar siempre antes de rellenar
        rellenarFormulario(formEstudiante, dataEst);
        
        // --- Cargar Ficha Médica ---
        const resFicha = await fetch(`/api/obtener_ficha_medica.php?id=${id}`);
        const dataFicha = await resFicha.json();
        
        const formFicha = document.getElementById('form_ficha_medica');
        formFicha.reset(); // Limpiar siempre antes de rellenar
        if (!dataFicha.error) {
            rellenarFormulario(formFicha, dataFicha);
        }
        // Asegurarse de que el ID oculto siempre esté presente para la actualización
        const fmEstudianteIdField = document.getElementById('estudiante_id');
        if (fmEstudianteIdField) fmEstudianteIdField.value = id;

        // --- Cargar Padres Vinculados ---
        // (La lógica para padres se mantiene igual)

    } catch (error) {
        console.error("Error detallado:", error);
        mostrarMensaje('error', `Error al cargar los datos: ${error.message}`);
    }
}

/**
 * Muestra un mensaje temporal en la pantalla.
 * @param {string} status - 'exito' o 'error'.
 * @param {string} message - El mensaje a mostrar.
 */
function mostrarMensaje(status, message) {
    const divMensaje = document.getElementById('mensaje_actualizacion');
    if (divMensaje) {
        divMensaje.className = `mensaje ${status}`;
        divMensaje.textContent = message;
        divMensaje.style.display = 'block';
        setTimeout(() => { divMensaje.style.display = 'none'; }, 4000);
    }
}