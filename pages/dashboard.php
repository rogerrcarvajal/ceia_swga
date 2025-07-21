<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Obtener el período escolar activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder inscribir estudiantes.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEIA - Sistema de Gestión Académica</title>  
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        .content { text-align: center; margin-top: 100px; color: white; text-shadow: 1px 1px 2px black;}
        .content img { width: 200px; margin-bottom: 20px;}
        .content h1 { font-size: 50px; margin-bottom: 20px;}
        .content p { font-size: 15px;}
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
<img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Bienvenidos <br>Sistema Web de Gestión Académica</h1></br>
        <h2>Centro Educativo Internacional Anzoátegui</h2>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
        <h4><p>Powered by R.R.C - @Copyright TM 2025.</p></h4>
    </div>
</body>
</html>