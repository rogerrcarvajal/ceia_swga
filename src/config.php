<?php
// --- RUTA AL EJECUTABLE PG_DUMP ---
// Define la ruta completa al ejecutable pg_dump.exe de PostgreSQL.
// Esta ruta es necesaria para que la funcionalidad de respaldo de la base de datos funcione correctamente.
// Asegúrate de que la ruta sea correcta para tu entorno de sistema.
// Se utiliza str_replace para asegurar la compatibilidad de las barras invertidas en diferentes sistemas operativos.
define('PG_DUMP_PATH', str_replace('\\', '/', 'C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe'));

// --- CONFIGURACIÓN DE LA BASE DE DATOS ---
// Define las constantes para la conexión a la base de datos.
// Usar constantes en lugar de variables para estos valores es una buena práctica de seguridad,
// ya que previene que sus valores sean alterados accidentalmente en otras partes del código.
define('DB_HOST', 'localhost');
define('DB_NAME', 'ceia_db');
define('DB_USER', 'postgres');
define('DB_PASSWORD', '4674');

try {
    // Establece la conexión a la base de datos usando las constantes definidas.
    $conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    // Configura el modo de error de PDO para que lance excepciones en caso de errores.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Si la conexión falla, detiene la ejecución del script y muestra un mensaje de error.
    die("Error en la conexión: " . $e->getMessage());
}
?>