<?php
session_start();

// --- 1. VERIFICACIÓN DE SEGURIDAD ---
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php");
    exit();
}

// --- 2. INCLUIR CONFIGURACIÓN Y CONEXIÓN ---
// La ruta correcta desde /src/reportes_generators/ es subir un nivel a /src/ y luego tomar el config.
require_once __DIR__ . '/../config.php';

// --- 3. INICIALIZACIÓN DE VARIABLES ---
// Esto previene errores si no se encuentra un período activo.
$profesores = [];
$estudiantes_por_grado = [];
$nombre_periodo = "Ningún Período Activo";
$periodo_activo_encontrado = false;

// --- 4. VERIFICACIÓN Y OBTENCIÓN DE DATOS DEL PERÍODO ACTIVO ---
$periodo_stmt = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

if ($periodo_stmt->rowCount() > 0) {
    $periodo_activo = $periodo_stmt->fetch(PDO::FETCH_ASSOC);
    $periodo_id = $periodo_activo['id'];
    $nombre_periodo = $periodo_activo['nombre_periodo'];
    $periodo_activo_encontrado = true;

    // --- 5. CONSULTAS SQL CORREGIDAS (SOLO SI HAY PERÍODO ACTIVO) ---

    // Consulta para obtener el personal ASIGNADO AL PERÍODO ACTIVO
    $profesores_sql = "SELECT p.nombre_completo, pp.posicion 
                       FROM profesor_periodo pp
                       JOIN profesores p ON pp.profesor_id = p.id
                       WHERE pp.periodo_id = :periodo_id
                       ORDER BY pp.posicion, p.nombre_completo";
    $profesores_stmt = $conn->prepare($profesores_sql);
    $profesores_stmt->execute([':periodo_id' => $periodo_id]);
    $profesores = $profesores_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener los estudiantes del PERÍODO ACTIVO
    $estudiantes_sql = "SELECT nombre_completo, apellido_completo, grado_ingreso 
                        FROM estudiantes 
                        WHERE activo = TRUE AND periodo_id = :periodo_id 
                        ORDER BY grado_ingreso, nombre_completo, apellido_completo";
    $estudiantes_stmt = $conn->prepare($estudiantes_sql);
    $estudiantes_stmt->execute([':periodo_id' => $periodo_id]);
    $estudiantes_result = $estudiantes_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar estudiantes por grado
    foreach ($estudiantes_result as $estudiante) {
        $grado = $estudiante['grado_ingreso'];
        if (!isset($estudiantes_por_grado[$grado])) {
            $estudiantes_por_grado[$grado] = [];
        }
        $estudiantes_por_grado[$grado][] = $estudiante;
    }

} else {
    // Si no hay período activo, se guarda el mensaje para la ventana modal.
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para ver los reportes.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster Actual - <?php echo htmlspecialchars($nombre_periodo); ?></title>
    <style>
        body { margin: 0; padding: 0; background-image: url('/public/img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .container { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18);max-width: 900px; margin: 50px auto; padding: 25px; border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #005a9c; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color:rgb(255, 255, 255); margin: 0; font-size: 1.8em; }
        .btn-pdf { background-color:rgb(48, 48, 48); color: white; padding: 10px 18px; text-decoration: none; border-radius: 5px; }
        .section-title { color:rgb(255, 255, 255); border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px; margin-bottom: 20px; }
        .staff-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .staff-table th, .staff-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .staff-table thead { background-color:rgb(48, 48, 48); color: white; }
        .grades-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .grade-section { background-color:rgb(48, 48, 48); border: 1px solid #e0e0e0; border-radius: 6px; padding: 15px; }
        .grade-title { margin-top: 0; margin-bottom: 10px; color:rgb(255, 255, 255); font-size: 1.2em; }
        .student-list { list-style: none; padding: 0; margin: 0; }
        .student-list li { padding: 5px 0;}
        .no-data { text-align: center; padding: 20px; background-color: rgba(0, 0, 0, 0.50); border: 1px solidrgb(255, 255, 255); color:rgb(255, 255, 255); border-radius: 5px; }
    </style>
</head>
<body>
    <?php
    // Incluir la barra de navegación (que también contiene la lógica de la modal)
    require_once __DIR__ . '/../../src/templates/navbar.php'; 
    ?>

    <div class="container">
        <div class="header">
            <h1>Roster <?php echo htmlspecialchars($nombre_periodo); ?></h1>
            <div class="export-buttons">
                <?php if ($periodo_activo_encontrado): ?>
                    <a href="/src/reports_generators/planilla_estudiante.php" target="_blank" class="btn-pdf">Exportar a PDF</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($periodo_activo_encontrado): ?>
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
                <p class="no-data">No hay personal asignado a este período escolar.</p>
            <?php endif; ?>

            <h2 class="section-title">Listado de Estudiantes por Grado</h2>
            <?php if (!empty($estudiantes_por_grado)): ?>
                <div class="grades-container">
                    <?php ksort($estudiantes_por_grado); // Ordenar los grados alfabéticamente/numéricamente ?>
                    <?php foreach ($estudiantes_por_grado as $grado => $estudiantes): ?>
                        <div class="grade-section">
                            <h3 class="grade-title"><?php echo htmlspecialchars($grado); ?></h3>
                            <ul class="student-list">
                                <?php foreach ($estudiantes as $estudiante): ?>
                                    <li><?php echo htmlspecialchars($estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No hay estudiantes registrados para este período escolar.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="no-data">No se puede mostrar el Roster porque no hay un período escolar activo.</p>
        <?php endif; ?>
        <div class="export-buttons">
            <!-- Botón para volver al menú de reportes -->
            <a href="/src/reports_generators/generar_roster_pdf.php" target="_blank" class="btn-pdf">Exportar a PDF</a>
        </div>
    </div>

</body>
</html>