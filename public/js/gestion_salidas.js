document.addEventListener('DOMContentLoaded', function () {
    const studentSearchInput = document.getElementById('estudiante_search');
    const studentIdInput = document.getElementById('estudiante_id');
    const datalist = document.getElementById('estudiantes_datalist');

    const chkPadre = document.getElementById('chk_padre');
    const chkMadre = document.getElementById('chk_madre');
    const chkOtro = document.getElementById('chk_otro');

    const padreInfo = document.getElementById('padre_info');
    const madreInfo = document.getElementById('madre_info');
    const otroInfo = document.getElementById('otro_autorizado_info');

    const padreNombreSpan = document.getElementById('padre_nombre');
    const madreNombreSpan = document.getElementById('madre_nombre');
    
    const retiradoPorPadreIdInput = document.getElementById('retirado_por_padre_id');
    const retiradoPorMadreIdInput = document.getElementById('retirado_por_madre_id');

    // --- 1. Lógica de selección de estudiante ---
    studentSearchInput.addEventListener('input', function() {
        const selectedOption = Array.from(datalist.options).find(option => option.value === studentSearchInput.value);
        if (selectedOption) {
            const studentId = selectedOption.getAttribute('data-value');
            studentIdInput.value = studentId;
            fetchRepresentantes(studentId);
        } else {
            studentIdInput.value = '';
            // Limpiar y ocultar todo si no hay estudiante
            padreInfo.style.display = 'none';
            madreInfo.style.display = 'none';
            otroInfo.style.display = 'none';
            padreNombreSpan.textContent = '';
            madreNombreSpan.textContent = '';
            chkPadre.checked = false;
            chkMadre.checked = false;
            chkOtro.checked = false;
            chkPadre.disabled = true;
            chkMadre.disabled = true;
        }
    });

    // --- 2. Lógica para obtener representantes ---
    function fetchRepresentantes(studentId) {
        if (!studentId) return;

        fetch(`/ceia_swga/api/buscar_representante.php?estudiante_id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                // Procesar datos del padre
                if (data.padre && data.padre.nombre_completo) {
                    padreNombreSpan.textContent = data.padre.nombre_completo;
                    retiradoPorPadreIdInput.value = data.padre.id;
                    chkPadre.disabled = false;
                } else {
                    padreNombreSpan.textContent = 'No registrado';
                    chkPadre.disabled = true;
                }

                // Procesar datos de la madre
                if (data.madre && data.madre.nombre_completo) {
                    madreNombreSpan.textContent = data.madre.nombre_completo;
                    retiradoPorMadreIdInput.value = data.madre.id;
                    chkMadre.disabled = false;
                } else {
                    madreNombreSpan.textContent = 'No registrada';
                    chkMadre.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error al buscar representantes:', error);
                chkPadre.disabled = true;
                chkMadre.disabled = true;
            });
    }

    // --- 3. Lógica de checkboxes ---
    const checkboxes = [chkPadre, chkMadre, chkOtro];
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Desmarcar los otros si este está marcado
            if (this.checked) {
                checkboxes.forEach(otherChk => {
                    if (otherChk !== this) {
                        otherChk.checked = false;
                    }
                });
            }
            actualizarVisibilidad();
        });
    });

    function actualizarVisibilidad() {
        padreInfo.style.display = chkPadre.checked ? 'block' : 'none';
        madreInfo.style.display = chkMadre.checked ? 'block' : 'none';
        otroInfo.style.display = chkOtro.checked ? 'block' : 'none';
    }

    // Inicializar deshabilitados hasta que se seleccione un estudiante
    chkPadre.disabled = true;
    chkMadre.disabled = true;
});
