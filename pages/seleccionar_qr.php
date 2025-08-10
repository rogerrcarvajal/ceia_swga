<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

$periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_id = $periodo_activo['id'] ?? 0;

$estudiantes = $vehiculos = [];
$staff_administrativo = $staff_docente = $staff_mantenimiento = $staff_vigilancia = [];

if ($periodo_id) {
    // Estudiantes del período activo
    $stmt_est = $conn->prepare("SELECT e.id, e.nombre_completo, e.apellido_completo FROM estudiante_periodo ep JOIN estudiantes e ON ep.estudiante_id = e.id WHERE ep.periodo_id = :pid ORDER BY e.apellido_completo");
    $stmt_est->execute([':pid' => $periodo_id]);
    $estudiantes = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

    // Vehículos autorizados
    $stmt_veh = $conn->query("SELECT v.id, v.placa, v.modelo, e.nombre_completo || ' ' || e.apellido_completo AS estudiante FROM vehiculos v JOIN estudiantes e ON v.estudiante_id = e.id WHERE v.autorizado = TRUE ORDER BY v.placa ASC");
    $vehiculos = $stmt_veh->fetchAll(PDO::FETCH_ASSOC);

    // Staff por categorías
    $sql_staff = "SELECT p.id, p.nombre_completo FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid AND p.categoria = :categoria ORDER BY p.nombre_completo";
    
    $stmt_staff = $conn->prepare($sql_staff);
    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Administrativo']);
    $staff_administrativo = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);
    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Docente']);
    $staff_docente = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);
    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Mantenimiento']);
    $staff_mantenimiento = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);
    $stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Vigilancia']);
    $staff_vigilancia = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Generar Códigos QR</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 180px; }
        .main-container { display: flex; max-width: 1200px; margin: 20px auto; gap: 20px; background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); padding: 20px; border-radius: 10px; }
        .menu-lateral { flex: 1; }
        .panel-seleccion { flex: 2; padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.2); }
        .menu-lateral ul { list-style: none; padding: 0; }
        .menu-lateral li { padding: 12px; margin-bottom: 8px; background-color: rgba(255,255,255,0.1); border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
        .menu-lateral li:hover, .menu-lateral li.active { background-color: rgba(255,255,255,0.3); }
        .form-section { display: none; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
    <h1>Generar Códigos QR</h1>
</div>

<div class="main-container">
    <div class="menu-lateral">
        <h3>Seleccione Categoría</h3>
        <ul>
            <li data-target="form-administrativo">Staff Administrativo</li>
            <li data-target="form-docente">Staff Docente</li>
            <li data-target="form-estudiantes">Estudiantes</li>
            <li data-target="form-mantenimiento">Staff Mantenimiento</li>
            <li data-target="form-vigilancia">Staff Vigilancia</li>
            <li data-target="form-vehiculos">Vehículos</li>
        </ul>
         <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a>
    </div>

    <div class="panel-seleccion">
        <div id="panel-informativo">
            <p>Seleccione una categoría del menú de la izquierda para empezar.</p>
        </div>

        <div id="form-container" class="form-section">
            <h3 id="form-title"></h3>
            <form id="qr-form" method="GET" target="_blank">
                <label for="select-item">Seleccione un item:</label>
                <select id="select-item" name="id" required>
                    <!-- Opciones se llenarán con JS -->
                </select>
                <br><br>
                <button type="submit">Generar PDF con QR</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Pasar datos de PHP a JS
    const data = {
        administrativo: <?= json_encode($staff_administrativo) ?>,
        docente: <?= json_encode($staff_docente) ?>,
        estudiantes: <?= json_encode($estudiantes) ?>,
        mantenimiento: <?= json_encode($staff_mantenimiento) ?>,
        vigilancia: <?= json_encode($staff_vigilancia) ?>,
        vehiculos: <?= json_encode($vehiculos) ?>
    };

    const menuItems = document.querySelectorAll('.menu-lateral li');
    const formContainer = document.getElementById('form-container');
    const panelInformativo = document.getElementById('panel-informativo');
    const formTitle = document.getElementById('form-title');
    const selectItem = document.getElementById('select-item');
    const qrForm = document.getElementById('qr-form');

    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            // Resaltar item activo
            menuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            const target = item.getAttribute('data-target').replace('form-', '');
            let options = '<option value="">-- Seleccione --</option>';
            let dataSource = [];

            panelInformativo.style.display = 'none';
            formContainer.style.display = 'block';

            let formAction = '';
            let title = '';

            switch (target) {
                case 'administrativo':
                    dataSource = data.administrativo;
                    formAction = '/ceia_swga/src/reports_generators/generar_qr_staff_pdf.php';
                    title = 'Generar QR para Staff Administrativo';
                    break;
                case 'docente':
                    dataSource = data.docente;
                    formAction = '/ceia_swga/src/reports_generators/generar_qr_staff_pdf.php';
                    title = 'Generar QR para Staff Docente';
                    break;
                case 'estudiantes':
                    dataSource = data.estudiantes;
                    formAction = '/ceia_swga/src/reports_generators/generar_qr_pdf.php';
                    title = 'Generar QR para Estudiantes';
                    break;
                case 'mantenimiento':
                    dataSource = data.mantenimiento;
                    formAction = '/ceia_swga/src/reports_generators/generar_qr_staff_pdf.php';
                    title = 'Generar QR para Staff Mantenimiento';
                    break;
                case 'vigilancia':
                    dataSource = data.vigilancia;
                    formAction = '/ceia_swga/src/reports_generators/generar_qr_staff_pdf.php';
                    title = 'Generar QR para Staff Vigilancia';
                    break;
                case 'vehiculos':
                    dataSource = data.vehiculos;
                    formAction = '/ceia_swga/src/reports_generators/generar_qr_vehiculo_pdf.php';
                    title = 'Generar QR para Vehículos';
                    break;
            }

            if (target === 'estudiantes') {
                dataSource.forEach(d => { options += `<option value="${d.id}">${d.apellido_completo}, ${d.nombre_completo}</option>`; });
            } else if (target === 'vehiculos') {
                 dataSource.forEach(d => { options += `<option value="${d.id}">${d.placa} - ${d.modelo} (${d.estudiante})</option>`; });
            } else { // Todas las categorías de staff
                dataSource.forEach(d => { options += `<option value="${d.id}">${d.nombre_completo}</option>`; });
            }

            qrForm.action = formAction;
            formTitle.textContent = title;
            selectItem.innerHTML = options;
        });
    });
});
</script>
</body>
</html>