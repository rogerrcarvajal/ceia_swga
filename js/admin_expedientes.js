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

    // --- CARGADORES DE DATOS (VERSIÓN EXPLÍCITA Y ROBUSTA) ---
    
    function cargarDatosEstudiante(id) {
        fetch(`obtener_estudiante.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    console.error('No se recibieron datos del estudiante o hubo un error:', data ? data.error : 'Sin datos');
                    return;
                }
                // Asignación explícita campo por campo
                document.getElementById('estudiante_id').value = data.id || '';
                document.getElementById('nombre_completo').value = data.nombre_completo || '';
                document.getElementById('apellido_completo').value = data.apellido_completo || '';
                document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento || '';
                document.getElementById('lugar_nacimiento').value = data.lugar_nacimiento || '';
                document.getElementById('nacionalidad').value = data.nacionalidad || '';
                document.getElementById('idioma').value = data.idioma || '';
                document.getElementById('direccion').value = data.direccion || '';
                document.getElementById('telefono_casa').value = data.telefono_casa || '';
                document.getElementById('telefono_movil').value = data.telefono_movil || '';
                document.getElementById('telefono_emergencia').value = data.telefono_emergencia || '';
                document.getElementById('grado_ingreso').value = data.grado_ingreso || '';
                document.getElementById('fecha_inscripcion').value = data.fecha_inscripcion || '';
                document.getElementById('recomendado_por').value = data.recomendado_por || '';
                document.getElementById('edad_estudiante').value = data.edad_estudiante || '';
                document.getElementById('activo').checked = (data.activo == 1 || data.activo === true);
            })
            .catch(error => console.error('Error fatal al cargar datos del estudiante:', error));
    }

    function cargarDatosPadres(id) {
        fetch(`obtener_padres_madres.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    console.error('No se recibieron datos de los padres o hubo un error:', data ? data.error : 'Sin datos');
                    return;
                }
                document.getElementById('estudiante_id_padres').value = id;
                
                // Asignación explícita para el Padre
                document.getElementById('padre_id').value = data.padre_id || '';
                document.getElementById('padre_nombre').value = data.padre_nombre || '';
                document.getElementById('padre_apellido').value = data.padre_apellido || '';
                document.getElementById('padre_fecha_nacimiento').value = data.padre_fecha_nacimiento || '';
                document.getElementById('padre_cedula_pasaporte').value = data.padre_cedula_pasaporte || '';
                document.getElementById('padre_nacionalidad').value = data.padre_nacionalidad || '';
                document.getElementById('padre_idioma').value = data.padre_idioma || '';
                document.getElementById('padre_profesion').value = data.padre_profesion || '';
                document.getElementById('padre_empresa').value = data.padre_empresa || '';
                document.getElementById('padre_telefono_trabajo').value = data.padre_telefono_trabajo || '';
                document.getElementById('padre_celular').value = data.padre_celular || '';
                document.getElementById('padre_email').value = data.padre_email || '';

                // Asignación explícita para la Madre
                document.getElementById('madre_id').value = data.madre_id || '';
                document.getElementById('madre_nombre').value = data.madre_nombre || '';
                document.getElementById('madre_apellido').value = data.madre_apellido || '';
                document.getElementById('madre_fecha_nacimiento').value = data.madre_fecha_nacimiento || '';
                document.getElementById('madre_cedula_pasaporte').value = data.madre_cedula_pasaporte || '';
                document.getElementById('madre_nacionalidad').value = data.madre_nacionalidad || '';
                document.getElementById('madre_idioma').value = data.madre_idioma || '';
                document.getElementById('madre_profesion').value = data.madre_profesion || '';
                document.getElementById('madre_empresa').value = data.madre_empresa || '';
                document.getElementById('madre_telefono_trabajo').value = data.madre_telefono_trabajo || '';
                document.getElementById('madre_celular').value = data.madre_celular || '';
                document.getElementById('madre_email').value = data.madre_email || '';
            })
            .catch(error => console.error('Error fatal al cargar datos de los padres:', error));
    }

    function cargarFichaMedica(id) {
        fetch(`obtener_ficha_medica.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    console.error('No se recibieron datos de la ficha médica o hubo un error:', data ? data.error : 'Sin datos');
                    return;
                }
                document.getElementById('estudiante_id_medica').value = id;
                // Asignación explícita campo por campo
                document.getElementById('completado_por').value = data.completado_por || '';
                document.getElementById('fecha_salud').value = data.fecha_salud || '';
                document.getElementById('contacto_emergencia').value = data.contacto_emergencia || '';
                document.getElementById('relacion_emergencia').value = data.relacion_emergencia || '';
                document.getElementById('telefono1').value = data.telefono1 || '';
                document.getElementById('telefono2').value = data.telefono2 || '';
                document.getElementById('observaciones').value = data.observaciones || '';
                document.getElementById('info_adicional').value = data.info_adicional || '';
                document.getElementById('problemas_oido_vista').value = data.problemas_oido_vista || '';
                document.getElementById('fecha_examen').value = data.fecha_examen || '';
                document.getElementById('medicamentos_actuales').value = data.medicamentos_actuales || '';
                
                document.getElementById('dislexia').checked = (data.dislexia == 1 || data.dislexia === true);
                document.getElementById('atencion').checked = (data.atencion == 1 || data.atencion === true);
                document.getElementById('otros').checked = (data.otros == 1 || data.otros === true);
                document.getElementById('autorizo_medicamentos').checked = (data.autorizo_medicamentos == 1 || data.autorizo_medicamentos === true);
                document.getElementById('autorizo_emergencia').checked = (data.autorizo_emergencia == 1 || data.autorizo_emergencia === true);
            })
            .catch(error => console.error('Error fatal al cargar la ficha médica:', error));
    }

    // --- EVENT LISTENERS (SIN CAMBIOS) ---

    listaEstudiantes.addEventListener('change', () => {
        const estudianteId = listaEstudiantes.value;
        limpiarFormularios();
        if (estudianteId) {
            cargarDatosEstudiante(estudianteId);
            cargarDatosPadres(estudianteId);
            cargarFichaMedica(estudianteId);
        }
    });

    formEstudiante.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(formEstudiante);
        fetch('actualizar_estudiante.php', { method: 'POST', body: formData })
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

    document.getElementById('actualizar_padres_madres').addEventListener('click', () => {
        const formData = new FormData(formPadres);
        fetch('actualizar_padres_madres.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => { alert(data); })
            .catch(error => {
                alert('Error al actualizar datos de los padres.');
                console.error('Error:', error);
            });
    });

    document.getElementById('actualizar_ficha_medica').addEventListener('click', () => {
        const formData = new FormData(formFichaMedica);
        fetch('actualizar_ficha_medica.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => { alert(data); })
            .catch(error => {
                alert('Error al actualizar la ficha médica.');
                console.error('Error:', error);
            });
    });
});
