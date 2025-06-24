document.addEventListener('DOMContentLoaded', () => {
    const inputBuscar = document.getElementById('buscar_padre_madre');
    const resultados = document.getElementById('resultados_padre_madre');

    inputBuscar.addEventListener('input', () => {
        const query = inputBuscar.value.trim();

        if (query.length < 2) {
            resultados.innerHTML = '';
            return;
        }

        fetch(`buscar_padres_madres.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultados.innerHTML = '';
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item.nombre;
                    resultados.appendChild(li);
                });
            });
    });
});