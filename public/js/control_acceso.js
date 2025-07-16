document.getElementById('qr-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('qr-input');
    const estudianteId = input.value;
    
    // Enviar a la API para registrar
    const response = await fetch('/api/registrar_llegada.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ estudiante_id: estudianteId })
    });
    const result = await response.json();
    
    // Mostrar alerta en pantalla (verde, amarillo, rojo)
    mostrarAlerta(result);
    
    input.value = ''; // Limpiar para el siguiente escaneo
});
// ... (Funciones para el reloj y mostrarAlerta)