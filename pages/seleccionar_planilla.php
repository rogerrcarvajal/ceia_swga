<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

//Declaracion de variables
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
// Obtenemos solo estudiantes del período activo
$periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$estudiantes = [];
if ($periodo_activo) {
    $stmt = $conn->prepare("SELECT id, nombre_completo, apellido_completo FROM estudiantes WHERE periodo_id = :pid ORDER BY apellido_completo, nombre_completo");
    $stmt->execute([':pid' => $periodo_activo['id']]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Planilla</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css">
    <style>
        .content { text-align: center; margin-top: 20px; color: white; text-shadow: 1px 1px 2px black;}
        .content img { width: 200px; margin-bottom: 20px;}
        .content h1 { font-size: 50px; margin-bottom: 20px;}
        .content p { font-size: 20px;}
        .right-panel { width: 30%; flex: 1; background-color: rgba(0,0,0,0.3); backdrop-filter:blur(5px); padding: 15px; border-radius: 8px; }

    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Generar Planilla de Inscripción</h1></div>
    <div class="right-panel">
        <form action="/src/reports_generators/generar_planilla_pdf.php" method="GET" target="_blank">
            <label>Seleccione un Estudiante del Período Actual:</label>
            <select name="id" required>
                <option value="">-- Por favor, elija --</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit">Generar PDF</button>
            <!-- Botón para volver al Home -->
            <a href="/pages/dashboard.php" class="btn">Volver</a> 

        </form>
    </div>
</body>
</html>