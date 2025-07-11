document.addEventListener('DOMContentLoaded', () => {
    // --- Referencias a elementos principales ---
    const listaUI = document.getElementById('lista_estudiantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_representantes');
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
 * Carga todos los datos de un representante desde las APIs.
 * @param {number} id - El ID del estudiante.
 */
async function cargarDatosCompletos(id) {
    try {
        // --- Cargar datos del Padre ---
        const resPad = await fetch(`/api/obtener_padre.php?id=${id}`);
        const dataPad = await resPad.json();
        if (dataPad.error) throw new Error(`API Padre: ${dataPad.error}`);
        
        const formPadre = document.getElementById('form_padre');
        formPadre.reset(); // Limpiar siempre antes de rellenar
        if (!dataPad.error) {
            rellenarFormulario(formPadre, dataPad);
        }
        
        // --- Cargar datos de la Madre ---
        const resMad = await fetch(`/api/obtener_madre.php?id=${id}`);
        const dataMad = await resMad.json();

        const formMadre = document.getElementById('form_madre');
        formMadre.reset(); // Limpiar siempre antes de rellenar
        if (!dataMad.error) {
            rellenarFormulario(formMadre, dataMad);
        }

        // Asegurarse de que el ID oculto siempre esté presente para la actualización
        const MadreIdField = document.getElementById('estudiante_id');
        if (MadreIdField) MadreIdField.value = id;
    
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