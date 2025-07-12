document.addEventListener('DOMContentLoaded', () => {
    const listaUI = document.getElementById('lista_representantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_representantes');
    const filtro = document.getElementById('filtro_representantes');

    // Clic en un representante de la lista
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

    // Filtro de búsqueda
    if (filtro) {
        filtro.addEventListener('keyup', () => {
            const texto = filtro.value.toLowerCase();
            document.querySelectorAll('#lista_representantes li').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }

    // Cargar datos si se llega a la página con parámetros en la URL
    const params = new URLSearchParams(window.location.search);
    if (params.has('id') && params.has('tipo')) {
        if (panelInformativo) panelInformativo.style.display = 'none';
        if (panelDatos) panelDatos.style.display = 'block';
        cargarDatosRepresentante(params.get('id'), params.get('tipo'));
    }

    // Vincular formularios a la función de envío
    const formPadre = document.getElementById('form_padre');
    if (formPadre) {
        formPadre.addEventListener('submit', (e) => handleFormSubmit(e, '/api/actualizar_padre.php'));
    }

    const formMadre = document.getElementById('form_madre');
    if (formMadre) {
        formMadre.addEventListener('submit', (e) => handleFormSubmit(e, '/api/actualizar_madre.php'));
    }
});

async function cargarDatosRepresentante(id, tipo) {
    try {
        const formId = `form_${tipo}`;
        const formElement = document.getElementById(formId);

        document.getElementById('form_padre').style.display = 'none';
        document.getElementById('form_madre').style.display = 'none';
        formElement.style.display = 'grid'; // 'grid' para que se vea en dos columnas
        
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
            field.value = data[key] || '';
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