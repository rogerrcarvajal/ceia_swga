document.addEventListener('DOMContentLoaded', function () {
    // --- Elementos del DOM ---
    const form = document.getElementById('form-salida');
    const studentSearchInput = document.getElementById('estudiante_search');
    const studentIdInput = document.getElementById('estudiante_id');
    const datalist = document.getElementById('estudiantes_datalist');
    const submitButton = form.querySelector('button[type="submit"]');

    // Radios y contenedores de información
    const radioPadre = document.getElementById('radio_padre');
    const radioMadre = document.getElementById('radio_madre');
    const radioOtro = document.getElementById('radio_otro');
    const radios = [radioPadre, radioMadre, radioOtro];

    const padreInfo = document.getElementById('padre_info');
    const madreInfo = document.getElementById('madre_info');
    const otroInfo = document.getElementById('otro_autorizado_info');

    const padreNombreSpan = document.getElementById('padre_nombre');
    const madreNombreSpan = document.getElementById('madre_nombre');
    const padreIdInput = document.getElementById('padre_id');
    const madreIdInput = document.getElementById('madre_id');

    const fechaSalidaInput = document.getElementById('fecha_salida');
    const horaSalidaInput = document.getElementById('hora_salida');

    // --- 1. Lógica de selección de estudiante ---
    studentSearchInput.addEventListener('input', function() {
        // Limpiar todo al empezar a escribir
        resetAutorizadoFields();
        studentIdInput.value = '';

        const selectedOption = Array.from(datalist.options).find(option => option.value === studentSearchInput.value);
        if (selectedOption) {
            const studentId = selectedOption.getAttribute('data-value');
            studentIdInput.value = studentId;
            fetchRepresentantes(studentId);
        } 
    });

    function resetAutorizadoFields() {
        radios.forEach(radio => {
            radio.checked = false;
            if(radio.id !== 'radio_otro') radio.disabled = true;
        });
        padreInfo.style.display = 'none';
        madreInfo.style.display = 'none';
        otroInfo.style.display = 'none';
        padreNombreSpan.textContent = '';
        madreNombreSpan.textContent = '';
        padreIdInput.value = '';
        madreIdInput.value = '';
    }

    // --- 2. Lógica para obtener representantes ---
    function fetchRepresentantes(studentId) {
        if (!studentId) return;

        fetch(`/ceia_swga/api/buscar_representante.php?estudiante_id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.padre && data.padre.id && data.padre.nombre_completo) {
                    padreNombreSpan.textContent = data.padre.nombre_completo;
                    padreIdInput.value = data.padre.id;
                    radioPadre.disabled = false;
                } else {
                    padreNombreSpan.textContent = 'No registrado';
                }

                if (data.madre && data.madre.id && data.madre.nombre_completo) {
                    madreNombreSpan.textContent = data.madre.nombre_completo;
                    madreIdInput.value = data.madre.id;
                    radioMadre.disabled = false;
                } else {
                    madreNombreSpan.textContent = 'No registrada';
                }
            })
            .catch(error => {
                console.error('Error al buscar representantes:', error);
                padreNombreSpan.textContent = 'Error al cargar';
                madreNombreSpan.textContent = 'Error al cargar';
            });
    }

    // --- 3. Lógica de visibilidad de secciones del autorizado ---
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            padreInfo.style.display = radioPadre.checked ? 'block' : 'none';
            madreInfo.style.display = radioMadre.checked ? 'block' : 'none';
            otroInfo.style.display = radioOtro.checked ? 'block' : 'none';
        });
    });

    // --- 4. Lógica para la fecha y hora automáticas ---
    function actualizarFechaHora() {
        const ahora = new Date();
        const anio = ahora.getFullYear();
        const mes = (ahora.getMonth() + 1).toString().padStart(2, '0');
        const dia = ahora.getDate().toString().padStart(2, '0');
        const horas = ahora.getHours().toString().padStart(2, '0');
        const minutos = ahora.getMinutes().toString().padStart(2, '0');

        if (!fechaSalidaInput.value) fechaSalidaInput.value = `${anio}-${mes}-${dia}`;
        if (!horaSalidaInput.value) horaSalidaInput.value = `${horas}:${minutos}`;
    }
    actualizarFechaHora();

    // --- 5. Lógica de envío de formulario ---
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        if (!studentIdInput.value) {
            alert('Por favor, seleccione un estudiante.');
            return;
        }

        if (!radioPadre.checked && !radioMadre.checked && !radioOtro.checked) {
            alert('Por favor, seleccione una persona autorizada para retirar.');
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        guardarYGenerarPdf(data);
    });

    function guardarYGenerarPdf(data) {
        submitButton.textContent = 'Guardando...';
        submitButton.disabled = true;

        fetch('/ceia_swga/api/guardar_planilla_salida.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.salida_id) {
                submitButton.textContent = 'Generando PDF...';
                alert('Autorización guardada con éxito. Ahora se generará el PDF.');
                
                                const pdfUrl = `/ceia_swga/src/reports_generators/generar_plantilla_salida_pdf.php?id=${result.salida_id}`;
                window.open(pdfUrl, '_blank');

            } else {
                throw new Error(result.message || 'No se pudo guardar la autorización.');
            }
        })
        .catch(error => {
            console.error('Error en el proceso:', error);
            alert(`Hubo un error: ${error.message}`);
        })
        .finally(() => {
            submitButton.textContent = 'Guardar y Generar PDF';
            submitButton.disabled = false;
            form.reset();
            resetAutorizadoFields();
            studentSearchInput.value = '';
            actualizarFechaHora();
        });
    }
});
