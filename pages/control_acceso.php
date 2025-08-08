<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// Verificación del período escolar activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "Debe activar un período escolar.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Acceso - CEIA</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("/ceia_swga/public/img/fondo.jpg");
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: white;
        }
        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter:blur(10px);
            margin: 70px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 80%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }
        .form-seccion {
            width: 45%;
            color: white;
            min-width: 350px;
        }
        .content {
            text-align: center;
            color: white;
            text-shadow: 1px 1px 2px black;
            margin-top: 30px;
        }
        .alerta.exito { background-color: rgba(46, 204, 113, 0.8); border-left: 5px solid #2ecc71; }
        .alerta.advertencia { background-color: rgba(241, 196, 15, 0.8); border-left: 5px solid #f1c40f; color: #333; }
        .alerta.error { background-color: rgba(231, 76, 60, 0.8); border-left: 5px solid #e74c3c; }
        .alerta.info { background-color: rgba(255,255,255,0.7); border-left: 5px solid #aaa; color: black; }
        .qr-code { margin: 20px auto; text-align: center; }
        .qr-code img { width: 150px; height: 150px; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="formulario-contenedor">
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>Late-Pass - Control de Acceso</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>
    <div class="form-seccion">
        <h3>Escaneo del código QR</h3>
        <p>Coloque el QR frente al dispositivo para escanearlo</p>
        <form id="qr-form" method="POST">
            <input type="password" id="qr-input" name="qr_code" placeholder="Escanea el QR aquí..." autofocus required>
        </form>
        <div id="qr-result" class="alerta"></div>
        <div id="qr-code" class="qr-code"></div>
        <br><br>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a>
    </div>
</div>
<div id="log-registros"></div>

<script src="/ceia_swga/public/js/control_acceso.js"></script>
</body>
</html>