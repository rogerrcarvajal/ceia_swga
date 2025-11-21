<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
if (!in_array($_SESSION['usuario']['rol'], ['master','admin', 'consulta'])) {
    $_SESSION['error_acceso'] = "Acceso deneg ado. Solo usuarios autorizados tienen acceso a éste módulo.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_id = $periodo_activo['id'] ?? 0;

$estudiantes = $vehiculos = [];
<<<<<<< HEAD
$staff_administrativo = $staff_docente = $staff_mantenimiento = $staff_vigilancia = [];
=======
$staff_administrativo = $staff_docente = $staff_mantenimiento = [];
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0

if ($periodo_id) {
    // 1. Estudiantes del período activo con datos de padres
    $stmt_est = $conn->prepare("
        SELECT 
            e.id, 
            e.nombre_completo, 
            e.apellido_completo, 
            ep.grado_cursado,
            p.padre_nombre || ' ' || p.padre_apellido AS nombre_padre,
            m.madre_nombre || ' ' || m.madre_apellido AS nombre_madre,
            p.padre_celular,
            m.madre_celular,
            p.padre_email,
            m.madre_email
        FROM estudiantes e
        JOIN estudiante_periodo ep ON e.id = ep.estudiante_id
        LEFT JOIN padres p ON e.padre_id = p.padre_id
        LEFT JOIN madres m ON e.madre_id = m.madre_id
        WHERE ep.periodo_id = :pid 
        ORDER BY e.apellido_completo, e.nombre_completo
    ");
    $stmt_est->execute([':pid' => $periodo_id]);
    $estudiantes = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

    // 2. Staff por categorías
    $sql_staff = "
        SELECT 
            p.id,
            p.nombre_completo,
            pp.posicion,
            p.telefono AS telefono_celular,
            (SELECT COUNT(e.id)
             FROM estudiantes e
             LEFT JOIN padres pa ON e.padre_id = pa.padre_id
             LEFT JOIN madres ma ON e.madre_id = ma.madre_id
             WHERE e.staff = TRUE AND
                   (pa.padre_cedula_pasaporte = p.cedula OR ma.madre_cedula_pasaporte = p.cedula)
            ) AS numero_hijos_staff
        FROM profesor_periodo pp
        JOIN profesores p ON pp.profesor_id = p.id
        WHERE pp.periodo_id = :pid AND p.categoria = :categoria
        ORDER BY p.nombre_completo
    ";
    
    $stmt_staff = $conn->prepare($sql_staff);
    
    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Administrativo']);
    $staff_administrativo = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);

    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Docente']);
    $staff_docente = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);

    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Mantenimiento']);
    $staff_mantenimiento = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);

<<<<<<< HEAD
    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Vigilancia']);
    $staff_vigilancia = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);

=======
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0
    // 3. Vehículos autorizados
    $stmt_veh = $conn->query("
        SELECT 
            v.id, 
            v.placa, 
            v.modelo, 
            v.autorizado,
            e.nombre_completo || ' ' || e.apellido_completo AS nombre_estudiante
        FROM vehiculos v 
        JOIN estudiantes e ON v.estudiante_id = e.id
        ORDER BY nombre_estudiante, v.placa
    ");
    $vehiculos = $stmt_veh->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Gestión de Reportes - Estudiantes/Staff</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 250px; }
        .main-container { display: flex; max-width: 1400px; margin: 20px auto; gap: 20px; background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); padding: 20px; border-radius: 10px; }
        .menu-lateral { flex: 1; }
        .panel-seleccion { flex: 4; padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.2); }
        .menu-lateral ul { list-style: none; padding: 0; }
        .menu-lateral li { padding: 12px; margin-bottom: 8px; background-color: rgba(255,255,255,0.1); border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
        .menu-lateral li:hover, .menu-lateral li.active { background-color: rgba(255,255,255,0.3); }
        .preview-section { display: none; }
        .preview-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .preview-table th, .preview-table td { padding: 8px; border: 1px solid rgba(255,255,255,0.2); text-align: left; font-size: 0.9em; }
        .preview-table th { background-color: rgba(0,0,0,0.3); }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
    <h1>Gestión de Reportes - Estudiantes/Staff</h1>
    <?php if ($periodo_activo): ?>
        <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
    <?php endif; ?>
</div>

<div class="main-container">
    <div class="menu-lateral">
        <h3>Seleccione Reporte</h3>
        <ul>
            <li data-target="preview-estudiantes">Estudiantes</li>
            <li data-target="preview-staff-admin">Staff Administrativo</li>
            <li data-target="preview-staff-docente">Staff Docente</li>
            <li data-target="preview-staff-mantenimiento">Staff Mantenimiento</li>
<<<<<<< HEAD
            <li data-target="preview-staff-vigilancia">Staff Vigilancia</li>
=======
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0
            <li data-target="preview-vehiculos">Vehículos Autorizados</li>
        </ul>
        <a href="/ceia_swga/pages/menu_reportes.php" class="btn" style="margin-top:10px;">Volver</a>
    </div>

    <div class="panel-seleccion">
        <div id="panel-informativo">
            <p>Seleccione una categoría del menú de la izquierda para previsualizar y generar un reporte.</p>
        </div>

        <!-- Vista Previa Estudiantes -->
        <div id="preview-estudiantes" class="preview-section">
            <h3>Reporte de Estudiantes</h3>
            <table class="preview-table">
                <thead><tr><th>Nombre Completo</th><th>Grado</th><th>Padre/Madre</th><th>Teléfonos</th><th>Email</th></tr></thead>
                <tbody>
                    <?php foreach($estudiantes as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($e['grado_cursado']) ?></td>
                        <td><?= htmlspecialchars($e['nombre_padre'] . ' / ' . $e['nombre_madre']) ?></td>
                        <td><?= htmlspecialchars($e['padre_celular'] . ' / ' . $e['madre_celular']) ?></td>
                        <td><?= htmlspecialchars($e['padre_email'] . ' / ' . $e['madre_email']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="/ceia_swga/src/reports_generators/generar_lista_estudiantes_PDF.php" method="POST" target="_blank" style="margin-top: 20px;">
                <button type="submit">📄 Generar PDF</button>
            </form>
        </div>

        <!-- Vista Previa Staff Administrativo -->
        <div id="preview-staff-admin" class="preview-section">
            <h3>Reporte de Staff Administrativo</h3>
            <table class="preview-table">
                <thead><tr><th>Nombre Completo</th><th>Posición</th><th>Teléfono</th><th>N° Hijos Staff</th></tr></thead>
                <tbody>
                    <?php foreach($staff_administrativo as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($s['posicion']) ?></td>
                        <td><?= htmlspecialchars($s['telefono_celular']) ?></td>
                        <td><?= htmlspecialchars($s['numero_hijos_staff']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="/ceia_swga/src/reports_generators/generar_lista_staff_admin_PDF.php" method="POST" target="_blank" style="margin-top: 20px;">
                <button type="submit">📄 Generar PDF</button>
            </form>
        </div>

        <!-- Vista Previa Staff Docente -->
        <div id="preview-staff-docente" class="preview-section">
            <h3>Reporte de Staff Docente</h3>
            <table class="preview-table">
                <thead><tr><th>Nombre Completo</th><th>Posición</th><th>Teléfono</th><th>N° Hijos Staff</th></tr></thead>
                <tbody>
                    <?php foreach($staff_docente as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($s['posicion']) ?></td>
                        <td><?= htmlspecialchars($s['telefono_celular']) ?></td>
                        <td><?= htmlspecialchars($s['numero_hijos_staff']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="/ceia_swga/src/reports_generators/generar_lista_staff_docente_PDF.php" method="POST" target="_blank" style="margin-top: 20px;">
                <button type="submit">📄 Generar PDF</button>
            </form>
        </div>

        <!-- Vista Previa Staff Mantenimiento -->
        <div id="preview-staff-mantenimiento" class="preview-section">
            <h3>Reporte de Staff Mantenimiento</h3>
            <table class="preview-table">
                <thead><tr><th>Nombre Completo</th><th>Posición</th><th>Teléfono</th></tr></thead>
                <tbody>
                    <?php foreach($staff_mantenimiento as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($s['posicion']) ?></td>
                        <td><?= htmlspecialchars($s['telefono_celular']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="/ceia_swga/src/reports_generators/generar_lista_staff_mantenimiento_PDF.php" method="POST" target="_blank" style="margin-top: 20px;">
                <button type="submit">📄 Generar PDF</button>
            </form>
        </div>

<<<<<<< HEAD
        <!-- Vista Previa Staff Vigilancia -->
        <div id="preview-staff-vigilancia" class="preview-section">
            <h3>Reporte de Staff Vigilancia</h3>
            <table class="preview-table">
                <thead><tr><th>Nombre Completo</th><th>Posición</th><th>Teléfono</th></tr></thead>
                <tbody>
                    <?php foreach($staff_vigilancia as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($s['posicion']) ?></td>
                        <td><?= htmlspecialchars($s['telefono_celular']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="/ceia_swga/src/reports_generators/generar_lista_staff_vigilancia_PDF.php" method="POST" target="_blank" style="margin-top: 20px;">
                <button type="submit">📄 Generar PDF</button>
            </form>
        </div>

=======
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0
        <!-- Vista Previa Vehículos -->
        <div id="preview-vehiculos" class="preview-section">
            <h3>Reporte de Vehículos Autorizados</h3>
            <table class="preview-table">
                <thead><tr><th>Estudiante</th><th>Placa</th><th>Modelo</th><th>Autorizado</th></tr></thead>
                <tbody>
                    <?php foreach($vehiculos as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['nombre_estudiante']) ?></td>
                        <td><?= htmlspecialchars($v['placa']) ?></td>
                        <td><?= htmlspecialchars($v['modelo']) ?></td>
                        <td><?= $v['autorizado'] ? 'Sí' : 'No' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="/ceia_swga/src/reports_generators/generar_lista_vehiculos_autorizados_PDF.php" method="POST" target="_blank" style="margin-top: 20px;">
                <button type="submit">📄 Generar PDF</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.menu-lateral li');
    const previewSections = document.querySelectorAll('.preview-section');
    const panelInformativo = document.getElementById('panel-informativo');

    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            const targetId = item.getAttribute('data-target');

            // Gestionar clase activa en el menú
            menuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            // Ocultar panel informativo y todas las secciones
            panelInformativo.style.display = 'none';
            previewSections.forEach(section => section.style.display = 'none');

            // Mostrar la sección seleccionada
            document.getElementById(targetId).style.display = 'block';
        });
    });
});
</script>
</body>
</html>