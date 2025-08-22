<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// --- OBTENER PERIODO ID DE LA URL ---
$periodo_id = $_GET['periodo_id'] ?? null;
if (!$periodo_id) {
    // Si no hay periodo_id, no podemos continuar.
    // Podríamos redirigir o mostrar un error.
    // Por ahora, redirigimos a la página principal de asignación.
    header("Location: /ceia_swga/pages/asignar_estudiante_periodo.php");
    exit();
}

// --- OBTENER LISTA DE ESTUDIANTES ---
// La lógica de negocio aquí es mostrar TODOS los estudiantes para que puedan ser gestionados
// en el contexto del período seleccionado.
$estudiantes = $conn->query("SELECT id, nombre_completo, apellido_completo FROM estudiantes ORDER BY apellido_completo, nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Gestionar Asignaciones de Estudiantes</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_admin.css">
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
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestionar Asignaciones de Estudiantes</h1>
    </div>
<div class="formulario-contenedor">
    <ul class="lista-gestion">
        <?php if (count($estudiantes) > 0): ?>
            <?php foreach ($estudiantes as $e): ?>
                <li>
                    <span><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></span>
                    <a href="/ceia_swga/pages/gestionar_estudiantes.php?id=<?= $e['id'] ?>&periodo_id=<?= $periodo_id ?>" class="btn-gestionar">Gestionar</a>
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