<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    // CORRECCIÓN: Ajustar la ruta para que suba un nivel al directorio raíz.
    header("Location: home.php");
    exit();
}
// CORRECCIÓN: Ajustar la ruta para que suba un nivel y luego entre a la carpeta 'conn'.
require_once "../conn/conexion.php";

$mensaje = "";

// --- OBTENER PERÍODO ESCOLAR ACTIVO ---
$periodo_stmt = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1");
$periodo = $periodo_stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Diríjase al menú Mantenimiento para crear y activar uno.");
}
$periodo_id = $periodo['id'];
$nombre_periodo = $periodo['nombre_periodo'];


// --- CONSULTA PARA OBTENER EL PERSONAL DEL PERÍODO ACTIVO ---
$profesores_sql = "SELECT p.nombre_completo, pp.posicion
                   FROM profesor_periodo pp
                   JOIN profesores p ON pp.profesor_id = p.id
                   WHERE pp.periodo_id = :periodo_id
                   ORDER BY pp.posicion, p.nombre_completo";
$profesores_stmt = $conn->prepare($profesores_sql);
$profesores_stmt->execute([':periodo_id' => $periodo_id]);
$profesores = $profesores_stmt->fetchAll(PDO::FETCH_ASSOC);


// --- CONSULTA PARA OBTENER LOS ESTUDIANTES DEL PERÍODO ACTIVO ---
$estudiantes_sql = "SELECT 
                        nombre_completo, 
                        grado_ingreso 
                    FROM estudiantes 
                    WHERE activo = TRUE AND periodo_id = :periodo_id 
                    ORDER BY FIELD(grado_ingreso, 'Daycare', 'Prekinder 3', 'Prekinder 4', 'Kindergarten', '1er Grado', '2do Grado', '3er Grado', '4to Grado', '5to Grado', '6to Grado', '7mo Grado', '8vo Grado', '9no Grado', '10mo Grado', '11vo Grado', '12vo Grado'), nombre_completo";

$estudiantes_stmt = $conn->prepare($estudiantes_sql);
$estudiantes_stmt->execute([':periodo_id' => $periodo_id]);
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
       body { margin: 0; padding: 0; background-image: url('img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; }
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
<?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>Administrar Planilla de Inscripción</h1><br>
    </div>

<div class="container">
    <div class="header">
        <h1>Roster <?php echo htmlspecialchars($nombre_periodo); ?></h1>
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
                        <td><?php echo htmlspecialchars($profesor['posicion']); ?></td>
                        <td><?php echo htmlspecialchars($profesor['nombre_completo']); ?></td>
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
                    <h3 class="grade-title"><?php echo htmlspecialchars($grado); ?> (<?= count($estudiantes) ?>)</h3>
                    <ul class="student-list">
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <li><?php echo htmlspecialchars($estudiante['nombre_completo']); ?></li>
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