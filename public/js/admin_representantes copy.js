document.addEventListener('DOMContentLoaded', () => {
    // --- Referencias a elementos principales ---
    const listaUI = document.getElementById('lista_representantes'); // CORRECCIÓN: El ID correcto de la lista.
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_representantes');
    const filtro = document.getElementById('filtro_representantes'); // CORRECCIÓN: El ID correcto del filtro.

    // --- Event Listeners ---
    if (listaUI) {
        listaUI.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                const id = e.target.dataset.id;
                const tipo = e.target.dataset.tipo; // 'padre' o 'madre'
                if (panelInformativo) panelInformativo.style.display = 'none';
                if (panelDatos) panelDatos.style.display = 'block';
                cargarDatosRepresentante(id, tipo);
            }
        });
    }

    // Detectar si venimos con un ID en la URL (desde la página de estudiantes)
    const params = new URLSearchParams(window.location.search);
    if (params.has('id') && params.has('tipo')) {
        const id = params.get('id');
        const tipo = params.get('tipo');
        if (panelInformativo) panelInformativo.style.display = 'none';
        if (panelDatos) panelDatos.style.display = 'block';
        cargarDatosRepresentante(id, tipo);
    }
    
    // El resto de la lógica (filtros, envío de formularios) se mantiene igual a como la diseñamos.
    // ...
});

async function cargarDatosRepresentante(id, tipo) {
    try {
        const formId = `form_${tipo}`; // 'form_padre' o 'form_madre'
        const formElement = document.getElementById(formId);

        // Ocultar ambos formularios y luego mostrar el correcto
        document.getElementById('form_padre').style.display = 'none';
        document.getElementById('form_madre').style.display = 'none';
        formElement.style.display = 'block';
        
        const res = await fetch(`/api/obtener_${tipo}.php?id=${id}`);
        const data = await res.json();
        if (data.error) throw new Error(`API ${tipo}: ${data.error}`);
        
        formElement.reset();
        rellenarFormulario(formElement, data);

        // Cargar los estudiantes vinculados a este representante
        const listaEstudiantesUl = document.getElementById('estudiantes_vinculados_lista');
        if (listaEstudiantesUl) {
            listaEstudiantesUl.innerHTML = '<li>Cargando...</li>';
            const resEst = await fetch(`/api/obtener_estudiantes_por_padre.php?id=${id}&tipo=${tipo}`);
            const dataEst = await resEst.json();
            let html = '';
            if (dataEst && !dataEst.error && dataEst.length > 0) {
                dataEst.forEach(est => {
                    html += `<li>${est.apellido_completo}, ${est.nombre_completo}</li>`;
                });
            } else {
                html = '<li>No hay estudiantes vinculados.</li>';
            }
            listaEstudiantesUl.innerHTML = html;
        }

    } catch (error) {
        console.error("Error detallado:", error);
        mostrarMensaje('error', `Error al cargar los datos: ${error.message}`);
    }
}
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
            const resPadre = await fetch(`/api/obtener_padre.php?padre_id=${dataEstudiante.padre_id}`);
            const dataPadre = await resPadre.json();
            if (!dataPadre.error) {
                rellenarFormulario(formPadre, dataPadre);
            }
        }

        // 3. CARGAR DATOS DE LA MADRE USANDO EL dataEstudiante.madre_id
        if (dataEstudiante.madre_id && formMadre) {
            const resMadre = await fetch(`/api/obtener_madre.php?madre_id=${dataEstudiante.madre_id}`);
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