document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.menu-lateral li');
    const formContainer = document.getElementById('form-container');
    const panelInformativo = document.getElementById('panel-informativo');
    const formTitle = document.getElementById('form-title');
    const selectItem = document.getElementById('select-item');
    const selectionForm = document.getElementById('selection-form');

    let currentTarget = null;

    menuItems.forEach(item => {
        item.addEventListener('click', async () => {
            // Highlight active item
            menuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            currentTarget = item.getAttribute('data-target');
            selectItem.innerHTML = '<option value="">Cargando...</option>';

            panelInformativo.style.display = 'none';
            formContainer.style.display = 'block';

            let apiUrl = '';
            let title = '';

            if (currentTarget === 'estudiantes') {
                apiUrl = '/ceia_swga/api/obtener_todos_estudiantes.php';
                title = 'Seleccionar Estudiante';
            } else if (currentTarget === 'staff') {
                apiUrl = '/ceia_swga/api/obtener_todo_el_staff.php';
                title = 'Seleccionar Miembro del Staff';
            }

            formTitle.textContent = title;

            try {
                const response = await fetch(apiUrl);
                const data = await response.json();

                if (response.ok) {
                    let options = '<option value="">-- Seleccione --</option>';
                    if (currentTarget === 'estudiantes') {
                        data.forEach(d => { options += `<option value="${d.id}">${d.apellido_completo}, ${d.nombre_completo}</option>`; });
                    } else { // Staff
                        data.forEach(d => { options += `<option value="${d.id}">${d.nombre_completo}</option>`; });
                    }
                    selectItem.innerHTML = options;
                } else {
                    selectItem.innerHTML = `<option value="">Error: ${data.error || 'No se pudieron cargar los datos'}</option>`;
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                selectItem.innerHTML = '<option value="">Error de conexión.</option>';
            }
        });
    });

    selectionForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const selectedId = selectItem.value;
        if (!selectedId) {
            alert('Por favor, seleccione un item de la lista.');
            return;
        }

        let redirectUrl = '';
        if (currentTarget === 'estudiantes') {
            redirectUrl = `/ceia_swga/pages/autorizaciones_estudiantes_generadas.php?estudiante_id=${selectedId}`;
        } else if (currentTarget === 'staff') {
            redirectUrl = `/ceia_swga/pages/autorizaciones_staff_generadas.php?staff_id=${selectedId}`;
        }

        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
});
