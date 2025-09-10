<?php
// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

$username = 'superusuario';
$password = password_hash('master', PASSWORD_DEFAULT);
$rol = 'master';
$sql = 'INSERT INTO usuarios (username, password, rol) VALUES (?, ?, ?)';
$stmt = $conn->prepare($sql);
$stmt->execute([$username, $password, $rol]);
echo 'Usuario master creado.';
?>