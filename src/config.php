<?php
<<<<<<< HEAD
    // Database connection settings
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ceia_db');
    define('DB_USER', 'postgres');
    define('DB_PASSWORD', '4674');

    // Path to pg_dump.exe (adjust if your PostgreSQL installation is in a different location)
    define('PG_DUMP_PATH', 'C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe');

    try {
        $conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Error en la conexi\u00f3n: " . $e->getMessage());
=======
    $host = "localhost";
    $db = "ceia_db";
    $user = "postgres";
    $password = "4674";

    try {
        $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad
    }
?>