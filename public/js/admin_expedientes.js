document.addEventListener('DOMContentLoaded', () => {
    // Referencias a elementos del DOM
    const listaEstudiantes = document.getElementById('lista_estudiantes');
    const formEstudiante = document.getElementById('form_estudiante');
    const formPadre = document.getElementById('form_padre');
    const formMadre = document.getElementById('form_madre');
    const formFichaMedica = document.getElementById('form_ficha_medica');
    const mensajeActualizacion = document.getElementById('mensaje_actualizacion');

    // Función para limpiar todos los formularios
    function limpiarFormularios() {
        formEstudiante.reset();
        formPadre.reset();
        formMadre.reset();
        formFichaMedica.reset();
        document.getElementById('estudiante_id').value = '';
        document.getElementById('estudiante_id_padre').value = '';
        document.getElementById('estudiante_id_madre').value = '';
        document.getElementById('estudiante_id_medica').value = '';
        mensajeActualizacion.textContent = '';
    }

    // --- CARGADORES DE DATOS ---
    
    function cargarDatosEstudiante(id) {
        fetch(`/api/obtener_estudiante.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
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

    function cargarDatosPadre(id) {
        fetch(`/api/obtener_padre.php?padre_id=${pdre_id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Si el PHP devuelve un error conocido (ej: "No hay datos"), lo muestra.
                    console.log(data.error); 
                    formPadre.reset(); // Limpia el formulario del padre si no hay datos.
                    return;
                }
                document.getElementById('estudiante_id_padre').value = id;
                 // CORRECCIÓN: Se usaba 'formPadres' en lugar de 'formPadre'
                 for (const key in data) {
                    const field = formPadre.querySelector(`[name="${key}"]`);
                    if (field) {
                        field.value = data[key];
                    }
                }
            })
            .catch(error => {
                console.error('Error al cargar datos del padre:', error);
                // Este error (catch) se activa si hay un fallo de red o un JSON malformado.
            });
    }

    function cargarDatosMadre(id) {
        fetch(`/api/obtener_madre.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.log(data.error);
                    formMadre.reset(); // Limpia el formulario de la madre si no hay datos.
                    return;
                }
                document.getElementById('estudiante_id_madre').value = id;
                 // CORRECCIÓN: Se usaba 'formPadres' en lugar de 'formMadre'
                 for (const key in data) {
                    const field = formMadre.querySelector(`[name="${key}"]`);
                    if (field) {
                        field.value = data[key];
                    }
                }
            })
            .catch(error => console.error('Error al cargar datos de la madre:', error));
    }

    function cargarFichaMedica(id) {
        fetch(`/api/obtener_ficha_medica.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.log(data.error);
                    formFichaMedica.reset(); // Limpia la ficha si no hay datos.
                    return;
                }
                document.getElementById('estudiante_id_medica').value = id;
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
            cargarDatosEstudiante(estudianteId);
            cargarDatosPadre(estudianteId);
            cargarDatosMadre(estudianteId);
            cargarFichaMedica(estudianteId);
        }
    });

    // 2. Al enviar el formulario de actualización del estudiante
    formEstudiante.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(formEstudiante);
        fetch('/api/actualizar_estudiante.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            mensajeActualizacion.textContent = data;
            setTimeout(() => mensajeActualizacion.textContent = '', 3000);
        })
        .catch(error => {
            mensajeActualizacion.textContent = 'Error en la actualización.';
            mensajeActualizacion.style.color = 'red';
            console.error('Error:', error);
        });
    });

    // 3. Al hacer clic en el botón de actualizar padre
    document.getElementById('actualizar_padre').addEventListener('click', () => {
        const formData = new FormData(formPadre);
        fetch('/api/actualizar_padre.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
        })
        .catch(error => {
            alert('Error al actualizar datos del padre.');
            console.error('Error:', error);
        });
    });

    // 4. Al hacer clic en el botón de actualizar madre
    document.getElementById('actualizar_madre').addEventListener('click', () => {
        const formData = new FormData(formMadre);
        fetch('/api/actualizar_madre.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
        })
        .catch(error => {
            alert('Error al actualizar datos de la madre.');
            console.error('Error:', error);
        });
    });

    // 5. Al hacer clic en el botón de actualizar ficha médica
    document.getElementById('actualizar_ficha_medica').addEventListener('click', () => {
        const formData = new FormData(formFichaMedica);
        fetch('/api/actualizar_ficha_medica.php', {
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
