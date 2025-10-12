<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

$staff_id = filter_input(INPUT_GET, 'staff_id', FILTER_VALIDATE_INT);
if (!$staff_id) {
    die("ID de staff no proporcionado.");
}

// Obtener nombre del staff para el título
$stmt = $conn->prepare("SELECT nombre_completo FROM profesores WHERE id = :id");
$stmt->execute([':id' => $staff_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_staff = $staff ? $staff['nombre_completo'] : 'Personal Desconocido';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Autorizaciones de <?= htmlspecialchars($nombre_staff) ?></title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 20px; padding-top: 10px;}
        .content img { width: 250px; }
        .container { max-width: 1000px; margin: 20px auto; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(10px); padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; color: white; }
        th, td { padding: 12px; border: 1px solid rgba(255,255,255,0.2); text-align: left; }
        th { background-color: rgba(0,0,0,0.4); }
        tbody tr:hover { background-color: rgba(255,255,255,0.1); }
        .btn { background-color: rgb(48, 48, 48); color: white; padding: 8px 12px; text-decoration: none; display: inline-block; border-radius: 5px; cursor: pointer; border: none; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA";>
    <h1>Historial de Autorizaciones</h1>
    <h2><?= htmlspecialchars($nombre_staff) ?></h2>
</div>

<div class="container">
    <table id="tabla-autorizaciones">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora Salida</th>
                <th>Duración (H)</th>
                <th>Motivo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <!-- Filas se llenarán con JS -->
        </tbody>
    </table>
    <br>
    <a href="/ceia_swga/pages/regenerar_autorizaciones.php" class="btn">Volver a la Selección</a>
</div>

<script src="/ceia_swga/public/js/autorizaciones_staff.js"></script>
</body>
</html>