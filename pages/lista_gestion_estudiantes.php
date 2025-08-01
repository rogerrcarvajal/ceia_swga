<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /CEia_swga/public/index.php");
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

// --- 2. OBTENER LISTA DE ESTUDIANTES PARA EL PANEL IZQUIERDO ---
$estudiantes = $conn->query("SELECT id, nombre_completo, apellido_completo FROM estudiantes ORDER BY apellido_completo, nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Asignaciones de Estudiantes</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 500px; }
        .content { text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black; }
        .content img { width: 150px; }
        .lista-gestion {
            list-style: none;
            padding: 0;
            max-width: 800px;
            margin: 20px auto;
        }
        .lista-gestion li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .lista-gestion li:nth-child(odd) {
            background-color: rgba(255,255,255,0.18);
            backdrop-filter:blur(10px);
            box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
            border:2px solid rgba(255,255,255,0.18);
        }
        .lista-gestion .btn-gestionar {
            background-color: rgb(48, 48, 48);
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .lista-gestion .btn-gestionar:hover {
            background-color: rgb(48, 48, 48);
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/Ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestionar Asignaciones de Estudiantes</h1>
    </div>
<div class="formulario-contenedor">
    <ul class="lista-gestion">
        <?php if (count($estudiantes) > 0): ?>
            <?php foreach ($estudiantes as $e): ?>
                <li>
                    <span><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></span>
                    <a href="/ceia_swga/pages/gestionar_estudiantes.php?id=<?= $e['id'] ?>" class="btn-gestionar">Gestionar</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No hay estudiantes registrados en el sistema.</li>
        <?php endif; ?>
    </ul>
    <a href="/ceia_swga/pages/asignar_estudiante_periodo.php" class="btn">Volver</a> 
</div>
</body>
</html>