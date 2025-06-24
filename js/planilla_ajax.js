document.addEventListener('DOMContentLoaded', () => {
    const lista = document.getElementById('lista_estudiantes');
    const form = document.getElementById('form_estudiante');
    const mensaje = document.getElementById('mensaje_actualizacion');

    lista.addEventListener('change', () => {
        const id = lista.value;

        fetch(`obtener_estudiante.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                form.estudiante_id.value = data.id;
                form.nombre_completo.value = data.nombre_completo;
                form.direccion.value = data.direccion;
                form.telefono_casa.value = data.telefono_casa;
                form.telefono_movil.value = data.telefono_movil;
                form.telefono_emergencia.value = data.telefono_emergencia;
                form.grado_ingreso.value = data.grado_ingreso;
                form.activo.checked = data.activo == 1;

                document.getElementById('foto_perfil').src = data.foto_perfil;
        });

        fetch(`obtener_padres_madres.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('datos_padres_madres').style.display = 'block';

                document.getElementById('padre_id').value = data.padre_id;
                document.getElementById('padre_nombre').value = data.padre_nombre;
                document.getElementById('padre_apellido').value = data.padre_apellido;
                document.getElementById('padre_celular').value = data.padre_celular;
                document.getElementById('padre_email').value = data.padre_email;

                document.getElementById('madre_id').value = data.madre_id;
                document.getElementById('madre_nombre').value = data.madre_nombre;
                document.getElementById('madre_apellido').value = data.madre_apellido;
                document.getElementById('madre_celular').value = data.madre_celular;
                document.getElementById('madre_email').value = data.madre_email;
        });

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        document.getElementById('actualizar_padres_madres').addEventListener('click', () => {
            const formData = new FormData();

            formData.append('padre_id', document.getElementById('padre_id').value);
            formData.append('padre_nombre', document.getElementById('padre_nombre').value);
            formData.append('padre_apellido', document.getElementById('padre_apellido').value);
            formData.append('padre_celular', document.getElementById('padre_celular').value);
            formData.append('padre_email', document.getElementById('padre_email').value);

            formData.append('madre_id', document.getElementById('madre_id').value);
            formData.append('madre_nombre', document.getElementById('madre_nombre').value);
            formData.append('madre_apellido', document.getElementById('madre_apellido').value);
            formData.append('madre_celular', document.getElementById('madre_celular').value);
            formData.append('madre_email', document.getElementById('madre_email').value);

            fetch('actualizar_padres_madres.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
            });
        });
        
        fetch('actualizar_estudiante.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                mensaje.textContent = data;
                mensaje.style.color = 'green';
            });
    });
});