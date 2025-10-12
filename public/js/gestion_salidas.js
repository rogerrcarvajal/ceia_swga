document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DEL DOM ---
    const form = document.getElementById('form-salida');
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
    const alertContainer = document.getElementById('alert-container');

    let nuevaAutorizacionId = null;

    // --- FUNCIONES ---

    function setInitialDateTime() {
        const now = new Date();
        const today = now.toISOString().split('T')[0];
        const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);
        fechaSalidaInput.value = today;
        horaSalidaInput.value = currentTime;
    }

    async function cargarEstudiantes() {
        if (!periodoActivoId || !estudianteSelect) return;

        try {
            const response = await fetch(`../api/obtener_estudiantes_por_periodo.php?periodo_id=${periodoActivoId}`);
            const data = await response.json();

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
        } catch (error) {
            console.error('Error al cargar estudiantes:', error);
            mostrarAlerta('danger', 'No se pudieron cargar los estudiantes.');
        }
    }

    function resetearFormularioAutorizacion() {
        [radioPadre, radioMadre, radioOtro].forEach(radio => {
            radio.checked = false;
            radio.disabled = !estudianteSelect.value;
        });
        [padreInfoDiv, madreInfoDiv, otroAutorizadoInfoDiv].forEach(div => div.style.display = 'none');
        btnGenerarPdf.disabled = true;
        salidaIdGuardadaInput.value = '';
        nuevaAutorizacionId = null;
    }

    async function manejarCambioAutorizado() {
        const studentId = estudianteSelect.value;
        if (!studentId) return;

        [padreInfoDiv, madreInfoDiv, otroAutorizadoInfoDiv].forEach(div => div.style.display = 'none');

        const valorSeleccionado = document.querySelector('input[name="autorizado_por"]:checked').value;

        try {
            if (valorSeleccionado === 'padre') {
                const response = await fetch(`../api/obtener_padre.php?estudiante_id=${studentId}`);
                const data = await response.json();
                if (data && !data.error) {
                    document.getElementById('padre_nombre').textContent = `${data.padre_nombre} ${data.padre_apellido}`;
                    document.getElementById('padre_cedula_pasaporte').textContent = data.padre_cedula_pasaporte;
                    document.getElementById('padre_id').value = data.padre_id;
                    padreInfoDiv.style.display = 'block';
                } else {
                    mostrarAlerta('danger', data.error || 'No se encontró información del padre.');
                }
            } else if (valorSeleccionado === 'madre') {
                const response = await fetch(`../api/obtener_madre.php?estudiante_id=${studentId}`);
                const data = await response.json();
                if (data && !data.error) {
                    document.getElementById('madre_nombre').textContent = `${data.madre_nombre} ${data.madre_apellido}`;
                    document.getElementById('madre_cedula_pasaporte').textContent = data.madre_cedula_pasaporte;
                    document.getElementById('madre_id').value = data.madre_id;
                    madreInfoDiv.style.display = 'block';
                } else {
                    mostrarAlerta('danger', data.error || 'No se encontró información de la madre.');
                }
            } else if (valorSeleccionado === 'otro') {
                otroAutorizadoInfoDiv.style.display = 'block';
            }
        } catch (error) {
            console.error('Error al obtener datos del autorizado:', error);
            mostrarAlerta('danger', 'Error de conexión al buscar datos del autorizado.');
        }
    }

    async function guardarAutorizacion(event) {
        event.preventDefault();
        
        const formData = new FormData(form);
        if (!formData.get('estudiante_id')) {
            mostrarAlerta('danger', 'Por favor, seleccione un estudiante.');
            return;
        }

        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';

        try {
            const response = await fetch('../api/guardar_planilla_salida.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.status === 'exito') {
                mostrarAlerta('success', data.mensaje);
                nuevaAutorizacionId = data.id;
                salidaIdGuardadaInput.value = data.id;
                btnGenerarPdf.disabled = false;
                form.reset();
                setInitialDateTime();
            } else {
                mostrarAlerta('danger', data.mensaje || 'Ocurrió un error desconocido.');
            }

        } catch (error) {
            console.error('Error en el fetch de guardado:', error);
            mostrarAlerta('danger', 'Error de conexión. No se pudo guardar la autorización.');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.textContent = 'Guardar Autorización';
        }
    }

    function mostrarAlerta(tipo, mensaje) {
        alertContainer.innerHTML = `<div class="alert alert-${tipo === 'success' ? 'success' : 'danger'}">${mensaje}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    // --- EVENT LISTENERS ---
    estudianteSelect.addEventListener('change', resetearFormularioAutorizacion);
    document.querySelectorAll('input[name="autorizado_por"]').forEach(radio => {
        radio.addEventListener('change', manejarCambioAutorizado);
    });
    form.addEventListener('submit', guardarAutorizacion);

    btnGenerarPdf.addEventListener('click', function() {
        const salidaId = salidaIdGuardadaInput.value;
        if (!salidaId) {
            mostrarAlerta('danger', 'Debe guardar la autorización antes de generar el PDF.');
            return;
        }
        const url = `../src/reports_generators/generar_autorizacion_pdf.php?id=${salidaId}`;
        window.open(url, '_blank');
    });

    // --- INICIALIZACIÓN ---
    setInitialDateTime();
    cargarEstudiantes();
});