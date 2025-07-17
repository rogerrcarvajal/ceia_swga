<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
$acceso_stmt = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");

$usuario_rol = $acceso_stmt;

if ($_SESSION['usuario']['rol'] !== 'admin') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
        // Aquí puedes redirigir o cargar la ventana modal según tu lógica
    }
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

?>  

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Control de Acceso - CEIA</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 70px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .form-seccion { width: 45%; color: white; min-width: 350px; }
        .form-seccion h3 { text-align: center; border-bottom: 1px solidrgb(42, 42, 42); padding-bottom: 10px; }
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 150px; }
        .lista-profesores { list-style: none; padding: 0; max-height: 400px; overflow-y: auto; }
        .lista-profesores li { background-color: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .lista-profesores a { color: #87cefa; text-decoration: none; margin-left: 10px; }
        .alerta.exito { background-color: rgba(46, 204, 113, 0.8); border-left: 5px solid #2ecc71; }
        .alerta.advertencia { background-color: rgba(241, 196, 15, 0.8); border-left: 5px solid #f1c40f; color: #333; }
        .alerta.error { background-color: rgba(231, 76, 60, 0.8); border-left: 5px solid #e74c3c; }
        .qr-code { margin: 20px auto; text-align: center; }
        .qr-code img { width: 150px; height: 150px; }
    </style>    
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
            <h1>Control de Acceso - Late-Pass</h1>
            <?php if ($periodo_activo): ?>
                <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
            <?php endif; ?>
        </div>
        <div class="form-seccion">
            <h3>Control de Acceso</h3>
            <p>Escanea el código QR del estudiante para registrar su entrada.</p>
            <form id="qr-form" method="POST">
                <input type="password" id="qr-input" name="qr_code" placeholder="Escanea el QR aquí..." autofocus required>
            </form>
            <div id="qr-result" class="alerta"></div>
            <div id="qr-code" class="qr-code"></div>
            <br><br>
            <!-- Botón para salir -->
            <a href="/pages/menu_latepass.php" class="btn">Salir</a>
        </div> 
    </div>
    <div id="log-registros"></div>
    <script src="/public/js/control_acceso.js"></script>
</body>
</html>