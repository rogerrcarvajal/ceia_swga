<?php
    // Database connection settings
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ceia_db');
    define('DB_USER', 'postgres');
    define('DB_PASSWORD', '4674');

    // Path to pg_dump.exe (adjust if your PostgreSQL installation is in a different location)
    define('PG_DUMP_PATH', 'C:\Program Files\PostgreSQL\17\bin\pg_dump.exe');

    try {
        $conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }
?>