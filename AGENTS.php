<?php
<<<<<<< HEAD
// Tu clave secreta de la API de OpenAI
$apiKey = 'TU_API_KEY_AQUI'; // <-- REEMPLAZA ESTO CON TU CLAVE REAL
=======
// Para mayor seguridad y siguiendo las buenas prácticas, la clave de API
// se carga desde una variable de entorno en lugar de estar hardcodeada.
// Esto evita exponer información sensible en el código fuente.
//
// Para que esto funcione, debes crear un archivo .env en la raíz de tu proyecto
// con el siguiente contenido:
// OPENAI_API_KEY="sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
//
// Por simplicidad, aquí usamos getenv() que funciona si la variable está configurada en el servidor.
$apiKey = getenv('OPENAI_API_KEY');
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0

// La pregunta que quieres hacer
$prompt = "Explica brevemente qué es un sistema de gestión académica.";

// Configuración de la API
$url = 'https://api.openai.com/v1/chat/completions';
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
];

$data = [
    'model' => 'gpt-3.5-turbo', // Puedes usar 'gpt-4' si tienes acceso
    'messages' => [
        [
            'role' => 'user',
            'content' => $prompt,
        ],
    ],
    'temperature' => 0.7,
];

<<<<<<< HEAD
=======
// Verificación de que la clave de API fue cargada
if (empty($apiKey)) {
    die("<h1>Error de Configuración</h1><p>La variable de entorno OPENAI_API_KEY no está configurada o está vacía. Asegúrate de que esté definida en tu servidor o en un archivo .env.</p>");
}

>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0
// Inicializar cURL
$ch = curl_init($url);

// Configurar las opciones de cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Ejecutar la petición
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Cerrar la conexión cURL
curl_close($ch);

// Procesar la respuesta
if ($httpcode == 200) {
    $result = json_decode($response, true);
    $answer = $result['choices'][0]['message']['content'];
    
    echo "<h1>Pregunta:</h1>";
    echo "<p>" . htmlspecialchars($prompt) . "</p>";
    echo "<h1>Respuesta de la IA:</h1>";
    echo "<p>" . htmlspecialchars($answer) . "</p>";

} else {
    echo "<h1>Error al contactar la API de OpenAI</h1>";
    echo "<p>Código de estado: " . htmlspecialchars($httpcode) . "</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

?>
