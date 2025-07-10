document.addEventListener('DOMContentLoaded', () => {
    const listaEstudiantes = document.getElementById('lista_estudiantes');
    
    listaEstudiantes.addEventListener('click', (e) => {
        if (e.target && e.target.nodeName === "LI") {
            const estudianteId = e.target.dataset.id;
            cargarDatosCompletos(estudianteId);
        }
    });

    // Lógica para actualizar estudiante y ficha médica
    // ...
});

async function cargarDatosCompletos(id) {
    // 1. Ocultar panel informativo y mostrar el de datos
    document.getElementById('panel_informativo').style.display = 'none';
    document.getElementById('panel_datos_estudiante').style.display = 'block';

    // 2. Cargar datos del estudiante
    const resEstudiante = await fetch(`/api/obtener_estudiante.php?id=${id}`);
    const dataEstudiante = await resEstudiante.json();
    if (dataEstudiante) {
        // Llenar formulario de estudiante
        // ...
    }

    // 3. Cargar datos de la ficha médica
    const resFicha = await fetch(`/api/obtener_ficha_medica.php?estudiante_id=${id}`);
    const dataFicha = await resFicha.json();
    if (dataFicha) {
        // Llenar formulario de ficha médica
        // ...
    }

    // 4. Cargar padres vinculados y crear enlaces
    const padreId = dataEstudiante.padre_id;
    const madreId = dataEstudiante.madre_id;
    const listaPadresUl = document.getElementById('lista_padres_vinculados');
    listaPadresUl.innerHTML = ''; // Limpiar

    if (padreId) {
        const resPadre = await fetch(`/api/obtener_padre.php?id=${padreId}`);
        const dataPadre = await resPadre.json();
        const li = document.createElement('li');
        li.innerHTML = `${dataPadre.padre_nombre} ${dataPadre.padre_apellido} (Padre) 
                        <a href="/pages/administrar_planilla_padres.php?id=${padreId}&tipo=padre">Gestionar</a>`;
        listaPadresUl.appendChild(li);
    }
    // Repetir lógica para la madre...
    if (madreId) {
        const resMadre = await fetch(`/api/obtener_madre.php?id=${madreId}`);
        const dataMadre = await resMadre.json();
        const li = document.createElement('li');
        li.innerHTML = `${dataMadre.madre_nombre} ${dataMadre.madre_apellido} (Madre) adre.madre_nombre} ${dataMadre.madre_apellido} (Madre) 
                        <a href="/pages/administrar_planilla_padres.php?id=${padreId}&tipo=madre">Gestionar</a>`;
        listaMadresUl.appendChild(li);
    }
}
// ...