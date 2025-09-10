<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

if ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'master' && $_SESSION['usuario']['rol'] !== 'consulta') {
    $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

require_once __DIR__ . '/../config.php';

$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_activo_encontrado = false;
$staff_admin = [];
$staff_docente_por_area = [
    'Preschool' => [],
    'Elementary' => [],
    'Secondary' => [],
    'Especiales' => []
];
$estudiantes_por_grado = [];
$maestros_por_grado = [];

if ($periodo_activo) {
    $periodo_activo_encontrado = true;
    $periodo_id = $periodo_activo['id'];
    $nombre_periodo = $periodo_activo['nombre_periodo'];

    $stmt_prof = $conn->prepare("SELECT p.nombre_completo, p.categoria, pp.posicion FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid AND (p.categoria = 'Staff Administrativo' OR p.categoria = 'Staff Docente')");
    $stmt_prof->execute([':pid' => $periodo_id]);
    $profesores = $stmt_prof->fetchAll(PDO::FETCH_ASSOC);

    $orden_admin = ["Director", "Bussiness Manager", "Administrative Assistant", "IT Manager", "Psychology"];
    $mapa_areas = [
        'Preschool' => ["Daycare, Pk-3", "Daycare, Pk-3 (Asist.)", "Pk-4, Kindergarten", "Pk-4, Kindergarten (Asist.)"],
        'Elementary' => ["Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Spanish teacher - Grade 1-6", "ESL - Elementary"],
        'Secondary' => ["Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10", "Grade 11", "Grade 12", "Spanish teacher - Grade 7-12", "Social Studies - Grade 6-12", "Science Teacher - Grade 6-12", "ESL - Secondary", "Language Arts - Grade 6-9", "Math teacher - Grade 6-9", "Math teacher - Grade 10-12"],
        'Especiales' => ["IT Teacher - Grade Pk-3-12", "PE - Grade Pk3-12", "Librarian" , "Art Teacher - Grade Pk3-12", "Music Teacher - Grade Pk3-12", "Counselor - Grade Pk3-12", "Substitute Teacher"]
    ];

    foreach ($profesores as $profesor) {
        if ($profesor['categoria'] === 'Staff Administrativo') {
            $staff_admin[] = $profesor;
        } elseif ($profesor['categoria'] === 'Staff Docente') {
            $area_encontrada = false;
            foreach ($mapa_areas as $area => $posiciones) {
                if (in_array($profesor['posicion'], $posiciones)) {
                    $staff_docente_por_area[$area][] = $profesor;
                    $area_encontrada = true;
                    break;
                }
            }
        }
    }

    usort($staff_admin, function($a, $b) use ($orden_admin) {
        $pos_a = array_search($a['posicion'], $orden_admin);
        $pos_b = array_search($b['posicion'], $orden_admin);
        if ($pos_a === false) $pos_a = 999;
        if ($pos_b === false) $pos_b = 999;
        return $pos_a - $pos_b;
    });

    $stmt_homeroom = $conn->prepare("SELECT p.nombre_completo, pp.homeroom_teacher FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid AND pp.homeroom_teacher IS NOT NULL AND pp.homeroom_teacher != ''");
    $stmt_homeroom->execute([':pid' => $periodo_id]);
    foreach($stmt_homeroom->fetchAll(PDO::FETCH_ASSOC) as $teacher) {
        $maestros_por_grado[$teacher['homeroom_teacher']] = $teacher['nombre_completo'];
    }

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
    <title>Roster Actual - <?php echo htmlspecialchars($nombre_periodo ?? ''); ?></title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_roster.css">
</head>
<body>
    <?php require_once __DIR__ . '/../templates/navbar.php'; ?>

    <div class="content"><img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA"></div>

    <div class="container">
        <div class="header">
            <h1>Roster <?php echo htmlspecialchars($nombre_periodo ?? ''); ?></h1>
            <div class="export-buttons">
                <?php if ($periodo_activo_encontrado): ?>
                    <a href="/ceia_swga/src/reports_generators/generar_roster_pdf.php" target="_blank" class="btn">Generar PDF</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($periodo_activo_encontrado): ?>
            <h2 class="section-title">Staff Administrativo</h2>
            <table class="staff-table">
                <tbody>
                    <?php foreach ($staff_admin as $profesor): ?>
                        <tr>
                            <td><?= htmlspecialchars($profesor['posicion']) ?></td>
                            <td><?= htmlspecialchars($profesor['nombre_completo']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2 class="section-title">Staff Docente</h2>
            <?php foreach ($staff_docente_por_area as $area => $docentes): ?>
                <?php if (!empty($docentes)): ?>
                    <h3 class="grade-title"><?= $area ?></h3>
                    <table class="staff-table">
                        <tbody>
                            <?php foreach ($docentes as $profesor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($profesor['posicion']) ?></td>
                                    <td><?= htmlspecialchars($profesor['nombre_completo']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>

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
            <a href="/ceia_swga/pages/dashboard.php" class="btn">Volver</a> 
    </div>

</body>
</html>