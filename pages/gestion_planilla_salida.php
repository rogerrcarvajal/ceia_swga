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

// Roles permitidos
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    $_SESSION['error_acceso'] = "Acceso deneg ado. Solo usuarios autorizados tienen acceso a éste módulo.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

// Obtener el período escolar activo
$periodoActivo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodoActivoId = $periodoActivo ? $periodoActivo['id'] : null;

// Obtener período activo y lista de estudiantes para el filtro
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$estudiantes = [];
if ($periodo_activo) {
    $stmt = $conn->prepare(
        "SELECT e.id, e.nombre_completo, e.apellido_completo 
         FROM estudiantes e 
         JOIN estudiante_periodo ep ON e.id = ep.estudiante_id 
         WHERE ep.periodo_id = :periodo_id 
         ORDER BY e.apellido_completo, e.nombre_completo"
    );
    $stmt->execute([':periodo_id' => $periodo_activo['id']]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Gestión de Autorizaciones de Salida</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
       .container { max-width: 90%; margin: 20px auto; padding: 20px; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(10px); border-radius: 8px; }
       .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; }
       .content img { width: 250px; }
       .filtros-container { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; padding: 15px; background-color: rgba(0,0,0,0.3); border-radius: 8px; align-items: center; }
       .filtros-container label { font-weight: bold; }
       .filtros-container input, .filtros-container select { padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
       .staff-table { width: 100%; border-collapse: collapse; }
       .staff-table th, .staff-table td { padding: 12px 15px; border: 1px solid rgba(255,255,255,0.2); text-align: left; }
       .staff-table thead th { background-color: rgba(0,0,0,0.3); }
       .staff-table tbody tr:nth-child(even) { background-color: rgba(255,255,255,0.05); }
       .staff-table tbody tr:hover { background-color: rgba(0,0,0,0.3); }
       .btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; cursor: pointer; border: none; background-color: #6c757d; margin-top: 20px; margin-right: 10px;}
       .btn-pdf { background-color: #6c757d; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Autorizaciones de Salida de Estudiantes</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="filtros-container">
            <div>
                <label for="filtro_semana">Seleccionar Semana:</label>
                <input type="week" id="filtro_semana">
            </div>
            <div>
                <label for="filtro_estudiante">Seleccionar Estudiante:</label>
                <select id="filtro_estudiante">
                    <option value="todos">Todos los Estudiantes</option>
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <option value="<?= htmlspecialchars($estudiante['id']) ?>">
                            <?= htmlspecialchars($estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estudiante</th>
                    <th>Retirado por</th>
                    <th>Parentesco</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody id="tabla_resultados">
                <tr><td colspan="6" style="text-align:center;">Seleccione una semana para ver los registros.</td></tr>
            </tbody>
        </table>
        <br>
        <button id="btnGenerarPDF" class="btn btn-pdf">📄 Generar PDF</button>
        <a href="/ceia_swga/pages/planilla_salida.php" class="btn">Volver</a>
    </div>
    <script src="/ceia_swga/public/js/consultar_salidas.js" defer></script>
    <script>
        document.getElementById('btnGenerarPDF').addEventListener('click', function() {
            const semana = document.getElementById('filtro_semana').value;
            const estudianteId = document.getElementById('filtro_estudiante').value;
            if (!semana) {
                alert('Por favor, seleccione una semana primero.');
                return;
            }
            // Se asume la existencia de un script generador de reportes
            window.open(`/ceia_swga/src/reports_generators/generar_reporte_salidas.php?semana=${semana}&estudiante_id=${estudianteId}`, '_blank');
        });
    </script>
</body>
</html>