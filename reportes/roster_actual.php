<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    // Redirigir al login si no hay sesión, ajustando la ruta
    header("Location: ../../home.php");
    exit();
}
// Ajustar la ruta de conexión para que suba dos niveles desde la carpeta /reportes/
require_once "../conn/conexion.php";

$mensaje = "";

// --- OBTENER PERÍODO ESCOLAR ACTIVO ---
$periodo_stmt = $conn->query(query: "SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1");
$periodo = $periodo_stmt->fetch(mode: PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Diríjase al menú Mantenimiento para crear y activar uno.");
}
$periodo_id = $periodo['id'];
$nombre_periodo = $periodo['nombre_periodo'];


// --- CONSULTA PARA OBTENER EL PERSONAL (PROFESORES Y ADMINISTRATIVOS) ---
$profesores_sql = "SELECT nombre_completo, posicion FROM profesores ORDER BY posicion, nombre_completo";
$profesores_stmt = $conn->query($profesores_sql);
$profesores = $profesores_stmt->fetchAll(PDO::FETCH_ASSOC);


// --- CONSULTA PARA OBTENER LOS ESTUDIANTES DEL PERÍODO ACTIVO ---
// Utiliza los campos de tu tabla 'estudiantes'.
$estudiantes_sql = "SELECT 
                        nombre_completo, 
                        grado_ingreso 
                    FROM estudiantes 
                    WHERE activo = TRUE AND periodo_id = :periodo_id 
                    ORDER BY grado_ingreso, nombre_completo";

$estudiantes_stmt = $conn->prepare($estudiantes_sql);
$estudiantes_stmt->bindParam(':periodo_id', $periodo_id, PDO::PARAM_INT);
$estudiantes_stmt->execute();
$estudiantes_result = $estudiantes_stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar estudiantes por grado en un array
$estudiantes_por_grado = [];
foreach ($estudiantes_result as $estudiante) {
    $grado = $estudiante['grado_ingreso'];
    if (!isset($estudiantes_por_grado[$grado])) {
        $estudiantes_por_grado[$grado] = [];
    }
    $estudiantes_por_grado[$grado][] = $estudiante;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster Actual - <?php echo htmlspecialchars($nombre_periodo); ?></title>
    <style>
        /* Estilos sin cambios */
        body { font-family: 'Arial', sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #005a9c; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #003366; margin: 0; font-size: 1.8em; }
        .export-buttons button { padding: 10px 18px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px; color: white; transition: opacity 0.3s; }
        .export-buttons button:hover { opacity: 0.85; }
        .btn-pdf { background-color: #d9534f; }
        .section-title { color: #003366; border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px; margin-bottom: 20px; }
        .staff-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .staff-table th, .staff-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .staff-table thead { background-color: #005a9c; color: white; }
        .grades-container { column-count: 3; column-gap: 20px; }
        .grade-section { break-inside: avoid-column; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
        .grade-title { margin-top: 0; margin-bottom: 10px; color: #005a9c; font-size: 1.2em; }
        .student-list { list-style: none; padding: 0; margin: 0; }
        .student-list li { padding: 5px 0; border-bottom: 1px solid #eee; }
        .student-list li:last-child { border-bottom: none; }
        .no-data { text-align: center; padding: 20px; background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Roster <?php echo htmlspecialchars(string: $nombre_periodo); ?></h1>
        <div class="export-buttons">
            <a href="lib/pdf.php" target="_blank" class="btn-pdf" style="text-decoration: none; padding: 10px 18px; border-radius: 5px; color: white;">Exportar a PDF</a>
        </div>
    </div>

    <h2 class="section-title">Personal Administrativo y Docente</h2>
    <?php if (!empty($profesores)): ?>
        <table class="staff-table">
            <thead>
                <tr>
                    <th>Especialidad / Cargo</th>
                    <th>Nombre Completo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profesores as $profesor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(string: $profesor['posicion']); ?></td>
                        <td><?php echo htmlspecialchars(string: $profesor['nombre_completo']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No hay personal registrado para el período escolar activo.</p>
    <?php endif; ?>

    <h2 class="section-title">Listado de Estudiantes por Grado</h2>
    <?php if (!empty($estudiantes_por_grado)): ?>
        <div class="grades-container">
            <?php foreach ($estudiantes_por_grado as $grado => $estudiantes): ?>
                <div class="grade-section">
                    <h3 class="grade-title"><?php echo htmlspecialchars(string: $grado); ?> (<?= count(value: $estudiantes) ?>)</h3>
                    <ul class="student-list">
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <li><?php echo htmlspecialchars(string: $estudiante['nombre_completo']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-data">No hay estudiantes registrados para el período escolar activo.</p>
    <?php endif; ?>

</div>

</body>
</html>
