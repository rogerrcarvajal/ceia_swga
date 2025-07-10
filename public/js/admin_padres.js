document.addEventListener('DOMContentLoaded', () => {
    const listaUI = document.getElementById('lista_representantes');
    const panelInformativo = document.getElementById('panel_informativo');
    const panelDatos = document.getElementById('panel_datos_representante');
    const filtro = document.getElementById('filtro_representantes');

    // Clic en un representante de la lista
    listaUI.addEventListener('click', (e) => {
        if (e.target && e.target.nodeName === 'LI') {
            const id = e.target.dataset.id;
            const tipo = e.target.dataset.tipo;
            panelInformativo.style.display = 'none';
            panelDatos.style.display = 'block';
            cargarDatosRepresentante(id, tipo);
        }
    });

    // Detectar si venimos con un ID en la URL (desde la página de estudiantes)
    const params = new URLSearchParams(window.location.search);
    if (params.has('id') && params.has('tipo')) {
        cargarDatosRepresentante(params.get('id'), params.get('tipo'));
    }

    // Lógica del filtro de búsqueda
    filtro.addEventListener('keyup', () => {
        const texto = filtro.value.toLowerCase();
        document.querySelectorAll('#lista_representantes li').forEach(item => {
            if (item.textContent.toLowerCase().includes(texto)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Enviar formulario del representante (FALTA LA API CORRESPONDIENTE)
    document.getElementById('form_representante').addEventListener('submit', async (e) => {
        e.preventDefault();
        // Aquí iría la lógica para llamar a actualizar_padre.php o actualizar_madre.php
        // similar a como lo hicimos en admin_estudiantes.js
        console.log('Formulario de representante enviado.');
    });
});

async function cargarDatosRepresentante(id, tipo) {
    // 1. Cargar datos del padre/madre
    try {
        const res = await fetch(`/api/obtener_${tipo}.php?id=${id}`);
        if (!res.ok) throw new Error('Error de red al obtener representante.');
        const data = await res.json();

        if (data && !data.error) {
            // Rellenar formulario del representante
            document.getElementById('padre_id').value = data.id;
            document.getElementById('representante_tipo').value = tipo;
            // Usa los nombres de columna correctos de tu BD: padre_nombre, madre_nombre, etc.
            document.getElementById('rep_nombre').value = data[`${tipo}_nombre`];
            document.getElementById('rep_apellido').value = data[`${tipo}_apellido`];
            document.getElementById('rep_fecha_nacimiento').value = data[`${tipo}_fecha_nacimiento`];
            document.getElementById('rep_cedula_pasaporte').value = data[`${tipo}_cedula_pasaporte`];
            document.getElementById('rep_nacionalidad').value = data[`${tipo}_nacionalidad`];
            document.getElementById('rep_idioma').value = data[`${tipo}_idioma`];
            document.getElementById('rep_profesion').value = data[`${tipo}_profesion`];
            document.getElementById('rep_empresa').value = data[`${tipo}_empresa`];
            document.getElementById('rep_telefono_trabajo').value = data[`${tipo}_telefono_trabajo`];
            document.getElementById('rep_celular').value = data[`${tipo}_celular`];
            document.getElementById('rep_email').value = data[`${tipo}_email`];
        }

        // 2. Cargar estudiantes vinculados
        const listaEstudiantes = document.getElementById('lista_estudiantes_vinculados');
        listaEstudiantes.innerHTML = '<li>Cargando...</li>';
        const resEst = await fetch(`/api/obtener_estudiantes_por_padre.php?id=${id}&tipo=${tipo}`);
        if (!resEst.ok) throw new Error('Error de red al obtener estudiantes vinculados.');
        const dataEst = await resEst.json();

        let htmlEst = '';
        if (dataEst && dataEst.length > 0) {
            dataEst.forEach(est => {
                htmlEst += `<li>${est.apellido_completo}, ${est.nombre_completo}</li>`;
            });
        } else {
            htmlEst = '<li>No hay estudiantes vinculados.</li>';
        }
        listaEstudiantes.innerHTML = htmlEst;

    } catch (error) {
        console.error('Error al cargar datos del representante:', error);
        document.getElementById('panel_datos_representante').innerHTML = `<p style="color:red;">${error.message}</p>`;
    }
}