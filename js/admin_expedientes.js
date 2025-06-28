document.addEventListener('DOMContentLoaded', () => {
    // Referencias a elementos del DOM
    const listaEstudiantes = document.getElementById('lista_estudiantes');
    const formEstudiante = document.getElementById('form_estudiante');
    const formPadres = document.getElementById('form_padres_madres');
    const formFichaMedica = document.getElementById('form_ficha_medica');
    const mensajeActualizacion = document.getElementById('mensaje_actualizacion');

    // Función para limpiar todos los formularios
    function limpiarFormularios() {
        formEstudiante.reset();
        formPadres.reset();
        formFichaMedica.reset();
        document.getElementById('estudiante_id').value = '';
        document.getElementById('estudiante_id_padres').value = '';
        document.getElementById('estudiante_id_medica').value = '';
        mensajeActualizacion.textContent = '';
    }

    // --- CARGADORES DE DATOS ---
    
    function cargarDatosEstudiante(id) {
        fetch(`obtener_estudiante.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                // Rellenar todos los campos del formulario de estudiante
                for (const key in data) {
                    const field = formEstudiante.querySelector(`[name="${key}"]`);
                    if (field) {
                        if (field.type === 'checkbox') {
                            field.checked = (data[key] == 1 || data[key] === true);
                        } else {
                            field.value = data[key];
                        }
                    }
                }
            })
            .catch(error => console.error('Error al cargar datos del estudiante:', error));
    }

    function cargarDatosPadres(id) {
        fetch(`obtener_padres_madres.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                document.getElementById('estudiante_id_padres').value = id;
                 // Rellenar todos los campos del formulario de padres/madres
                 for (const key in data) {
                    const field = formPadres.querySelector(`[name="${key}"]`);
                    if (field) {
                        field.value = data[key];
                    }
                }
            })
            .catch(error => console.error('Error al cargar datos de los padres:', error));
    }

    function cargarFichaMedica(id) {
        fetch(`obtener_ficha_medica.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                document.getElementById('estudiante_id_medica').value = id;
                // Rellenar todos los campos del formulario de ficha médica
                for (const key in data) {
                    const field = formFichaMedica.querySelector(`[name="${key}"]`);
                    if (field) {
                        if (field.type === 'checkbox') {
                            field.checked = (data[key] == 1 || data[key] === true);
                        } else {
                            field.value = data[key];
                        }
                    }
                }
            })
            .catch(error => console.error('Error al cargar la ficha médica:', error));
    }


    // --- EVENT LISTENERS ---

    // 1. Cuando se selecciona un estudiante de la lista
    listaEstudiantes.addEventListener('change', () => {
        const estudianteId = listaEstudiantes.value;
        
        limpiarFormularios();

        if (estudianteId) {
            // Cargar datos para el estudiante seleccionado
            cargarDatosEstudiante(estudianteId);
            cargarDatosPadres(estudianteId);
            cargarFichaMedica(estudianteId);
        }
    });

    // 2. Al enviar el formulario de actualización del estudiante
    formEstudiante.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(formEstudiante);
        
        fetch('actualizar_estudiante.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            mensajeActualizacion.textContent = data;
            setTimeout(() => mensajeActualizacion.textContent = '', 3000); // El mensaje desaparece después de 3 segundos
        })
        .catch(error => {
            mensajeActualizacion.textContent = 'Error en la actualización.';
            mensajeActualizacion.style.color = 'red';
            console.error('Error:', error);
        });
    });

    // 3. Al hacer clic en el botón de actualizar padres/madres
    document.getElementById('actualizar_padres_madres').addEventListener('click', () => {
        const formData = new FormData(formPadres);
        
        fetch('actualizar_padres_madres.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
        })
        .catch(error => {
            alert('Error al actualizar datos de los padres.');
            console.error('Error:', error);
        });
    });

    // 4. Al hacer clic en el botón de actualizar ficha médica
    document.getElementById('actualizar_ficha_medica').addEventListener('click', () => {
        const formData = new FormData(formFichaMedica);

        fetch('actualizar_ficha_medica.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
        })
        .catch(error => {
            alert('Error al actualizar la ficha médica.');
            console.error('Error:', error);
        });
    });
});
