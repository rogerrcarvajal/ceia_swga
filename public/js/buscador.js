document.addEventListener('DOMContentLoaded', () => {
    const inputBuscar = document.getElementById('buscar_estudiante');
    const resultados = document.getElementById('resultados_estudiantes');

    inputBuscar.addEventListener('input', () => {
        const query = inputBuscar.value.trim();

        if (query.length < 2) {
            resultados.innerHTML = '';
            return;
        }

        fetch(`/api/buscar_estudiantes.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultados.innerHTML = '';
                data.forEach(estudiante => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a href="administrar_planilla_inscripcion.php?estudiante_id=${estudiante.id}">${estudiante.nombre_completo}</a>`;
                    resultados.appendChild(li);
                });
            });
    });
});