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

    // Vinculamos los 4 formularios a la misma función genérica de envío
    document.getElementById('form_estudiante').addEventListener('submit', (e) => handleFormSubmit(e, '/ceia_swga/api/actualizar_estudiante.php'));
    document.getElementById('form_padre').addEventListener('submit', (e) => handleFormSubmit(e, '/ceia_swga/api/actualizar_padre.php'));
    document.getElementById('form_madre').addEventListener('submit', (e) => handleFormSubmit(e, '/ceia_swga/api/actualizar_madre.php'));
    document.getElementById('form_ficha_medica').addEventListener('submit', (e) => handleFormSubmit(e, '/ceia_swga/api/actualizar_ficha_medica.php'));
});

/**
 * Carga TODA la información vinculada a un estudiante.
 * @param {string} estudianteId - El ID del estudiante seleccionado.
 */
async function cargarExpedienteCompleto(estudianteId) {
    // Referencias a los formularios
    const formEstudiante = document.getElementById('form_estudiante');
    const formPadre = document.getElementById('form_padre');
    const formMadre = document.getElementById('form_madre');
    const formFicha = document.getElementById('form_ficha_medica');

    // Resetear todos los formularios para evitar datos de selecciones anteriores
    [formEstudiante, formPadre, formMadre, formFicha].forEach(form => form.reset());

    try {
        // 1. OBTENER DATOS DEL ESTUDIANTE (el punto de partida)
        const resEst = await fetch(`/ceia_swga/api/obtener_estudiante.php?id=${estudianteId}`);
        const dataEst = await resEst.json();
        if (dataEst.error) throw new Error(`API Estudiante: ${dataEst.error}`);
        rellenarFormulario(formEstudiante, dataEst);

        // 2. OBTENER DATOS DEL PADRE (SOLO SI EL ESTUDIANTE TIENE UN padre_id ASOCIADO)
        if (dataEst.padre_id) {
            const resPadre = await fetch(`/ceia_swga/api/obtener_padre.php?id=${dataEst.padre_id}`);
            const dataPadre = await resPadre.json();
            if (!dataPadre.error) {
                rellenarFormulario(formPadre, dataPadre);
            }
        }
        
        // 3. OBTENER DATOS DE LA MADRE (SOLO SI EL ESTUDIANTE TIENE UN madre_id ASOCIADO)
        if (dataEst.madre_id) {
            const resMadre = await fetch(`/ceia_swga/api/obtener_madre.php?id=${dataEst.madre_id}`);
            const dataMadre = await resMadre.json();
            if (!dataMadre.error) {
                rellenarFormulario(formMadre, dataMadre);
            }
        }

        
        // 4. OBTENER FICHA MÉDICA
        // CORRECCIÓN: El parámetro en la URL debe llamarse 'estudiante_id' para que coincida con la API.
        const resFicha = await fetch(`/ceia_swga/api/obtener_ficha_medica.php?estudiante_id=${estudianteId}`);
        const dataFicha = await resFicha.json();
        if (!dataFicha.error) {
            rellenarFormulario(formFicha, dataFicha);
        }
        // Siempre nos aseguramos de que el ID del estudiante esté en el campo oculto de la ficha
        formFicha.querySelector('[name="estudiante_id"]').value = estudianteId;;

    } catch (error) {
        mostrarMensaje('error', `Error al cargar el expediente: ${error.message}`);
        console.error("Error detallado:", error);
    }
}

// --- FUNCIONES AUXILIARES (Genéricas y Reutilizables) ---

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

function rellenarFormulario(formElement, data) {
    if (!formElement || !data) return;
    for (const key in data) {
        const field = formElement.querySelector(`[name="${key}"]`);
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = (data[key] == 1 || data[key] === true);
            } else {
                field.value = data[key] || '';
            }
        }
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