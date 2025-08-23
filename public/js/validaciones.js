function validarEstudiante() {
    const nombre = document.querySelector('input[name="nombre"]').value;
    if (nombre.trim().length < 3) {
        alert("El nombre debe tener al menos 3 caracteres.");
        return false;
    }
    return true;
}