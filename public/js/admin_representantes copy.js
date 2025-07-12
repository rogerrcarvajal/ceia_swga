document.addEventListener('DOMContentLoaded', () => {
    const listaUI = document.getElementById('lista_representantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_representantes');
    const filtro = document.getElementById('filtro_representantes');

    // --- Event Listeners ---
    if (listaUI) {
        listaUI.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                const id = e.target.dataset.id;
                const tipo = e.target.dataset.tipo;
                if (panelInformativo) panelInformativo.style.display = 'none';
                if (panelDatos) panelDatos.style.display = 'block';
                cargarDatosRepresentante(id, tipo);
            }
        });
    }

    if (filtro) {
        filtro.addEventListener('keyup', () => {
            const texto = filtro.value.toLowerCase();
            document.querySelectorAll('#lista_representantes li').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }

    // Cargar datos del representante si se pasa un ID y tipo en la URL
    // Esto permite que al cargar la página se muestren los datos del representante seleccionado
    
    const params = new URLSearchParams(window.location.search);
    if (params.has('id') && params.has('tipo')) {
        cargarDatosRepresentante(params.get('id'), params.get('tipo'));
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

/** * Carga los datos de un representante (padre o madre) y los estudiantes vinculados.
 * @param {number} id - El ID del representante seleccionado.
 * 
*/
async function cargarDatosRepresentante(id, tipo) {
    try {
        const formId = `form_${tipo}`;
        const formElement = document.getElementById(formId);

        document.getElementById('form_padre').style.display = 'none';
        document.getElementById('form_madre').style.display = 'none';
        formElement.style.display = 'block';
        
        const res = await fetch(`/api/obtener_${tipo}.php?id=${id}`);
        const data = await res.json();
        if (data.error) throw new Error(`API ${tipo}: ${data.error}`);
        
        formElement.reset();
        rellenarFormulario(formElement, data);

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


/**
 * Carga los datos de los representantes (padre y madre) asociados a un estudiante.
 * @param {number} estudianteId - El ID del estudiante seleccionado.
 */
async function cargarDatosRepresentantes(estudianteId) {

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
            const resPadre = await fetch(`/api/obtener_padre.php?padre_id=${estudianteId}`);
            const dataPadre = await resPadre.json();

            const formPadre = document.getElementById('form_padre');
            formPadre.reset(); // Limpiar siempre antes de rellenar
            if (!dataPadre.error) {
                rellenarFormulario(formPadre, dataPadre);
                }

            // Asegurarse de que el ID oculto siempre esté presente para la actualización
            const PadreIdField = document.getElementById('padre_id');
            if (PadreIdField) PadreIdField.value = estudianteId;

            
        // 3. CARGAR DATOS DE LA MADRE USANDO EL dataEstudiante.madre_id
            const resMadre = await fetch(`/api/obtener_padre.php?madre_id=${estudianteId}`);
            const dataMadre = await resMadre.json();

            const formMadre = document.getElementById('form_padre');
            formMadre.reset(); // Limpiar siempre antes de rellenar
            if (!dataMadre.error) {
                rellenarFormulario(formMadre, dataMadre);
                }

            // Asegurarse de que el ID oculto siempre esté presente para la actualización
            const MadreIdField = document.getElementById('padre_id');
            if (MadreIdField) MadreIdField.value = estudianteId;
            
            
        // Asegurarse de que el ID oculto siempre esté presente para la actualización
        const fmEstudianteIdField = document.getElementById('estudiante_id');
        if (fmEstudianteIdField) fmEstudianteIdField.value = id;


    } catch (error) {
        console.error("Error al cargar los datos de los representantes:", error);
        mostrarMensaje('error', `Error al cargar los datos: ${error.message}`);

    } finally {
        // Asegurarse de que los formularios estén visibles
        if (formPadre) formPadre.style.display = 'block';
        if (formMadre) formMadre.style.display = 'block';
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
}