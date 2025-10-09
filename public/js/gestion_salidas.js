document.addEventListener('DOMContentLoaded', function() {
    const periodoActivoId = document.getElementById('periodo_activo_id')?.value;
    const estudianteSelect = document.getElementById('estudiante_id');
    const fechaSalidaInput = document.getElementById('fecha_salida');
    const horaSalidaInput = document.getElementById('hora_salida');

    const radioPadre = document.getElementById('radio_padre');
    const radioMadre = document.getElementById('radio_madre');
    const radioOtro = document.getElementById('radio_otro');

    const padreInfoDiv = document.getElementById('padre_info');
    const madreInfoDiv = document.getElementById('madre_info');
    const otroAutorizadoInfoDiv = document.getElementById('otro_autorizado_info');

    const btnGuardar = document.getElementById('btn-guardar');
    const btnGenerarPdf = document.getElementById('btn-generar-pdf');
    const salidaIdGuardadaInput = document.getElementById('salida_id_guardada');

    // Set current date and time
    const now = new Date();
    const today = now.toISOString().split('T')[0];
    const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);
    fechaSalidaInput.value = today;
    horaSalidaInput.value = currentTime;

    // Fetch students if a period is active
    if (periodoActivoId && estudianteSelect) {
        fetch(`../api/obtener_estudiantes_por_periodo.php?periodo_id=${periodoActivoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(estudiante => {
                        const option = document.createElement('option');
                        option.value = estudiante.id;
                        option.textContent = `${estudiante.apellido_completo}, ${estudiante.nombre_completo}`;
                        estudianteSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.textContent = 'No hay estudiantes asignados a este período';
                    option.disabled = true;
                    estudianteSelect.appendChild(option);
                }
            })
            .catch(error => console.error('Error al cargar estudiantes:', error));
    }

    // Event listener for student selection
    estudianteSelect.addEventListener('change', function() {
        const studentId = this.value;
        // Reset and disable radios and hide info divs
        [radioPadre, radioMadre, radioOtro].forEach(radio => {
            radio.checked = false;
            radio.disabled = !studentId;
        });
        [padreInfoDiv, madreInfoDiv, otroAutorizadoInfoDiv].forEach(div => div.style.display = 'none');
        // Disable PDF button when student changes
        btnGenerarPdf.disabled = true;
        salidaIdGuardadaInput.value = '';
    });

    // Event listener for authorized person radio buttons
    document.querySelectorAll('input[name="autorizado_por"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const studentId = estudianteSelect.value;
            if (!studentId) return;

            [padreInfoDiv, madreInfoDiv, otroAutorizadoInfoDiv].forEach(div => div.style.display = 'none');

            if (this.value === 'padre') {
                fetch(`../api/obtener_padre.php?estudiante_id=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && !data.error) {
                            document.getElementById('padre_nombre').textContent = `${data.padre_nombre} ${data.padre_apellido}`;
                            document.getElementById('padre_cedula_pasaporte').textContent = data.padre_cedula_pasaporte;
                            document.getElementById('padre_id').value = data.padre_id;
                            padreInfoDiv.style.display = 'block';
                        } else {
                            alert('No se encontró información del padre para este estudiante.');
                        }
                    });
            } else if (this.value === 'madre') {
                fetch(`../api/obtener_madre.php?estudiante_id=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && !data.error) {
                            document.getElementById('madre_nombre').textContent = `${data.madre_nombre} ${data.madre_apellido}`;
                            document.getElementById('madre_cedula_pasaporte').textContent = data.madre_cedula_pasaporte;
                            document.getElementById('madre_id').value = data.madre_id;
                            madreInfoDiv.style.display = 'block';
                        } else {
                            alert('No se encontró información de la madre para este estudiante.');
                        }
                    });
            } else if (this.value === 'otro') {
                otroAutorizadoInfoDiv.style.display = 'block';
            }
        });
    });
    
    // Form submission for saving authorization
    document.getElementById('form-salida').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const estudianteId = formData.get('estudiante_id');
        if (!estudianteId) {
            alert('Por favor, seleccione un estudiante.');
            return;
        }

        fetch('../api/guardar_planilla_salida.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success && data.salida_id) {
                salidaIdGuardadaInput.value = data.salida_id;
                btnGenerarPdf.disabled = false;
                alert('Autorización guardada exitosamente. Ahora puede generar el PDF.');
            } else {
                alert('Error al guardar la autorización: ' + (data.message || 'Error desconocido.'));
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            alert('Ocurrió un error de comunicación con el servidor.');
        });
    });

    // PDF Generation
    btnGenerarPdf.addEventListener('click', function() {
        const salidaId = salidaIdGuardadaInput.value;
        if (!salidaId) {
            alert('Debe guardar la autorización antes de generar el PDF.');
            return;
        }
        // Abre el PDF en una nueva pestaña.
        // El script PHP se encarga de generar el PDF y mostrarlo.
        const url = `../src/reports_generators/generar_autorizacion_pdf.php?id=${salidaId}`;
        window.open(url, '_blank');
    });
});