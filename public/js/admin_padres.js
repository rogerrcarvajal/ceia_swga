document.addEventListener('DOMContentLoaded', () => {
    // Lógica para detectar si se llegó desde un enlace de estudiante
    const urlParams = new URLSearchParams(window.location.search);
    const representanteId = urlParams.get('id');
    const tipo = urlParams.get('tipo');

    if (representanteId && tipo) {
        cargarDatosRepresentante(representanteId, tipo);
    }

    // Event listener para la lista de representantes
    // ...
});

async function cargarDatosRepresentante(id, tipo) {
    // 1. Ocultar panel informativo y mostrar el de datos
    document.getElementById('panel_informativo').style.display = 'none';
    document.getElementById('panel_datos_representante').style.display = 'block';

    // 2. Cargar datos del representante (padre o madre)
    const resRep = await fetch(`/api/obtener_${tipo}.php?id=${id}`);
    const dataRep = await resRep.json();
    if(dataRep){
        // Llenar el formulario del representante
        // ...
    }

    // 3. Cargar estudiantes vinculados
    const resEstudiantes = await fetch(`/api/obtener_estudiantes_por_padre.php?id=${id}&tipo=${tipo}`);
    const dataEstudiantes = await resEstudiantes.json();
    const listaEstudiantesUl = document.getElementById('lista_estudiantes_vinculados');
    listaEstudiantesUl.innerHTML = '';
    
    if (dataEstudiantes && dataEstudiantes.length > 0) {
        dataEstudiantes.forEach(est => {
            const li = document.createElement('li');
            li.textContent = `${est.apellido_completo}, ${est.nombre_completo}`;
            listaEstudiantesUl.appendChild(li);
        });
    } else {
        listaEstudiantesUl.innerHTML = '<li>No hay estudiantes vinculados.</li>';
    }
}