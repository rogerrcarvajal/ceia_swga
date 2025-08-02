<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../config.php';

$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_activo_encontrado = false;
$profesores = [];
$estudiantes_por_grado = [];
$maestros_por_grado = [];

if ($periodo_activo) {
    $periodo_activo_encontrado = true;
    $periodo_id = $periodo_activo['id'];
    $nombre_periodo = $periodo_activo['nombre_periodo'];

    // 1. Obtener Personal asignado al período
    $stmt_prof = $conn->prepare("SELECT p.nombre_completo, pp.posicion FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid ORDER BY pp.posicion");
    $stmt_prof->execute([':pid' => $periodo_id]);
    $profesores = $stmt_prof->fetchAll(PDO::FETCH_ASSOC);

    // 2. Obtener Homeroom Teachers y agruparlos por grado
    $stmt_homeroom = $conn->prepare("SELECT p.nombre_completo, pp.homeroom_teacher FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid AND pp.homeroom_teacher IS NOT NULL AND pp.homeroom_teacher != ''");
    $stmt_homeroom->execute([':pid' => $periodo_id]);
    foreach($stmt_homeroom->fetchAll(PDO::FETCH_ASSOC) as $teacher) {
        $maestros_por_grado[$teacher['homeroom_teacher']] = $teacher['nombre_completo'];
    }

    // 3. Obtener Estudiantes asignados al período (usando la nueva tabla)
    $sql_est = "SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado
                FROM estudiante_periodo ep
                JOIN estudiantes e ON ep.estudiante_id = e.id
                WHERE ep.periodo_id = :pid
                ORDER BY ep.grado_cursado, e.apellido_completo, e.nombre_completo";
    $stmt_est = $conn->prepare($sql_est);
    $stmt_est->execute([':pid' => $periodo_id]);
    foreach ($stmt_est->fetchAll(PDO::FETCH_ASSOC) as $estudiante) {
        $estudiantes_por_grado[$estudiante['grado_cursado']][] = $estudiante;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster Actual - <?php echo htmlspecialchars($nombre_periodo); ?></title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_roster.css">
</head>
<body>
    <?php
    // Incluir la barra de navegación (que también contiene la lógica de la modal)
    require_once __DIR__ . '/../templates/navbar.php';
    ?>

    <div class="content"><img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA"></div>

    <div class="container">
        <div class="header">
            <h1>Roster <?php echo htmlspecialchars($nombre_periodo); ?></h1>
            <div class="export-buttons">
                <?php if ($periodo_activo_encontrado): ?>
                    <a href="/ceia_swga/src/reports_generators/generar_roster_pdf.php" target="_blank" class="btn">Generar PDF</a>
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
                    <?php ksort($estudiantes_por_grado); ?>
                    <?php foreach ($estudiantes_por_grado as $grado => $estudiantes): ?>
                        <div class="grade-section">
                            <h3 class="grade-title"><?= htmlspecialchars($grado) ?></h3>
                            <p class="teacher-name">
                                <strong>Profesor:</strong> <?= htmlspecialchars($maestros_por_grado[$grado] ?? 'No asignado') ?>
                            </p>
                            <ul class="student-list">
                                <?php foreach ($estudiantes as $estudiante): ?>
                                    <li><?= htmlspecialchars($estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo']) ?></li>
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
        <br>
            <!-- Botón para volver al Home -->
            <a href="/ceia_swga/pages/dashboard.php" class="btn">Volver</a> 
    </div>

</body>
</html>