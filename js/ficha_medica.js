function cargarFichaMedica(estudianteId) {
    fetch(`obtener_ficha_medica.php?id=${estudianteId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('contacto_emergencia').value = data.contacto_emergencia;
            document.getElementById('telefono_emergencia1').value = data.telefono1;
            document.getElementById('telefono_emergencia2').value = data.telefono2;
            document.getElementById('observaciones').value = data.observaciones;
            document.getElementById('dislexia').checked = data.dislexia == 1;
            document.getElementById('atencion').checked = data.atencion == 1;
            document.getElementById('otros').checked = data.otros == 1;
            document.getElementById('info_adicional').value = data.info_adicional;
        });
}

document.getElementById('actualizar_ficha_medica').addEventListener('click', () => {
    const formData = new FormData(document.getElementById('form_ficha_medica'));

    fetch('actualizar_ficha_medica.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            alert(data);
        });
});