document.addEventListener('DOMContentLoaded', () => {
    const listaUI = document.getElementById('lista_estudiantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_estudiante');
    const filtro = document.getElementById('filtro_estudiantes');

    // Clic en un estudiante de la lista
    listaUI.addEventListener('click', (e) => {
        if (e.target && e.target.nodeName === "LI") {
            const estudianteId = e.target.dataset.id;
            panelInformativo.style.display = 'none';
            panelDatos.style.display = 'block';
            cargarDatosCompletos(estudianteId);
        }
    });

    // Lógica del filtro de búsqueda
    filtro.addEventListener('keyup', () => {
        const texto = filtro.value.toLowerCase();
        document.querySelectorAll('#lista_estudiantes li').forEach(item => {
            const nombreItem = item.textContent.toLowerCase();
            item.style.display = nombreItem.includes(texto) ? '' : 'none';
        });
    });

    // Enviar formulario de ACTUALIZAR ESTUDIANTE
    document.getElementById('form_estudiante').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const response = await fetch('/api/actualizar_estudiante.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        mostrarMensaje(result.status, result.message);
    });

    // Enviar formulario de ACTUALIZAR FICHA MÉDICA
    document.getElementById('form_ficha_medica').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const response = await fetch('/api/actualizar_ficha_medica.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        mostrarMensaje(result.status, result.message);
    });
});

// Función para rellenar un formulario con datos de un objeto
function rellenarFormulario(formElement, data) {
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

// Carga TODOS los datos relacionados con un estudiante
async function cargarDatosCompletos(id) {
    try {
        // Cargar y rellenar datos del estudiante
        const resEst = await fetch(`/api/obtener_estudiante.php?id=${id}`);
        const dataEst = await resEst.json();
        if (dataEst.error) throw new Error(dataEst.error);
        
        rellenarFormulario(document.getElementById('form_estudiante'), dataEst);

        // Cargar y rellenar ficha médica
        const resFicha = await fetch(`/api/obtener_ficha_medica.php?id=${id}`);
        const dataFicha = await resFicha.json();
        document.getElementById('form_ficha_medica').reset(); // Limpiar por si no hay ficha
        if (!dataFicha.error) {
            rellenarFormulario(document.getElementById('form_ficha_medica'), dataFicha);
        }
        // Siempre poner el ID del estudiante en el campo oculto de la ficha
        document.getElementById('fm_estudiante_id').value = id;

        // Cargar padres vinculados
        const listaPadresUl = document.getElementById('lista_padres_vinculados');
        listaPadresUl.innerHTML = '<li>Cargando...</li>';
        let htmlPadres = '';
        if (dataEst.padre_id) {
            const resPadre = await fetch(`/api/obtener_padre.php?id=${dataEst.padre_id}`);
            const dataPadre = await resPadre.json();
            if (!dataPadre.error) {
                htmlPadres += `<li>${dataPadre.padre_nombre} ${dataPadre.padre_apellido} (Padre) <a href="/pages/administrar_planilla_padres.php?id=${dataPadre.id}&tipo=padre">Gestionar</a></li>`;
            }
        }
        if (dataEst.madre_id) {
            const resMadre = await fetch(`/api/obtener_madre.php?id=${dataEst.madre_id}`);
            const dataMadre = await resMadre.json();
             if (!dataMadre.error) {
                htmlPadres += `<li>${dataMadre.madre_nombre} ${dataMadre.madre_apellido} (Madre) <a href="/pages/administrar_planilla_padres.php?id=${dataMadre.id}&tipo=madre">Gestionar</a></li>`;
            }
        }
        listaPadresUl.innerHTML = htmlPadres || '<li>No hay padres vinculados.</li>';

    } catch (error) {
        mostrarMensaje('error', `Error al cargar los datos: ${error.message}`);
    }
}

function mostrarMensaje(status, message) {
    const divMensaje = document.getElementById('mensaje_actualizacion');
    divMensaje.className = `mensaje ${status}`;
    divMensaje.textContent = message;
    divMensaje.style.display = 'block';
    setTimeout(() => { divMensaje.style.display = 'none'; }, 4000);
}