<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

$periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_id = $periodo_activo['id'] ?? 0;

$estudiantes = $profesores = $vehiculos = [];

if ($periodo_id) {
    $stmt_est = $conn->prepare("SELECT e.id, e.nombre_completo, e.apellido_completo FROM estudiante_periodo ep JOIN estudiantes e ON ep.estudiante_id = e.id WHERE ep.periodo_id = :pid ORDER BY e.apellido_completo");
    $stmt_est->execute([':pid' => $periodo_id]);
    $estudiantes = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

    $stmt_prof = $conn->prepare("SELECT p.id, p.nombre_completo FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid ORDER BY p.nombre_completo");
    $stmt_prof->execute([':pid' => $periodo_id]);
    $profesores = $stmt_prof->fetchAll(PDO::FETCH_ASSOC);

    $stmt_veh = $conn->query("SELECT v.id, v.placa, v.modelo, e.nombre_completo || ' ' || e.apellido_completo AS estudiante FROM vehiculos v JOIN estudiantes e ON v.estudiante_id = e.id WHERE v.autorizado = TRUE ORDER BY v.placa ASC");
    $vehiculos = $stmt_veh->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Generar Códigos QR</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_admin.css">
    <style>
        body { background-color: rgba(0, 0, 0, 0.3); font-family: Arial, sans-serif; }
        .content { text-align: center; margin: 40px auto; }
        .main-container { background-color: rgba(0, 0, 0, 0.3); padding: 20px; max-width: 960px; margin: auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .selector-container { margin-bottom: 30px; }
        .form-section { display: none; margin-top: 20px; }
        select, button { width: 100%; padding: 10px; margin-top: 10px; }
        h3 { border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .btn { background-color: #444; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; }
        .btn:hover { background-color: #333; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
    <h1>Generar Códigos QR</h1>
</div>

<div class="main-container">
    <div class="selector-container">
        <label><input type="radio" name="selector" value="estudiantes"> Estudiantes</label>
        <label><input type="radio" name="selector" value="staff"> Staff / Profesores</label>
        <label><input type="radio" name="selector" value="vehiculos"> Vehículos</label>
        <br><br>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver al Menú</a>
    </div>

    <!-- Estudiantes -->
    <div id="form-estudiantes" class="form-section">
        <h3>Generar QR de Estudiante</h3>
        <form action="/ceia_swga/src/reports_generators/generar_qr_pdf.php" method="GET" target="_blank">
            <label for="select-estudiante">Seleccione un Estudiante:</label>
            <select id="select-estudiante" name="id" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Generar PDF</button>
        </form>
    </div>

    <!-- Staff -->
    <div id="form-staff" class="form-section">
        <h3>Generar QR de Staff</h3>
        <form action="/ceia_swga/src/reports_generators/generar_qr_staff_pdf.php" method="GET" target="_blank">
            <label for="select-staff">Seleccione un Miembro del Staff:</label>
            <select id="select-staff" name="id" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Generar PDF</button>
        </form>
    </div>

    <!-- Vehículos -->
    <div id="form-vehiculos" class="form-section">
        <h3>Generar QR de Vehículo</h3>
        <form action="/ceia_swga/src/reports_generators/generar_qr_vehiculo_pdf.php" method="GET" target="_blank">
            <label for="select-vehiculo">Seleccione un Vehículo:</label>
            <select id="select-vehiculo" name="id" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($vehiculos as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['placa'] . ' - ' . $v['modelo'] . ' (' . $v['estudiante'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Generar PDF</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const radios = document.querySelectorAll('input[name="selector"]');
    const forms = {
        estudiantes: document.getElementById('form-estudiantes'),
        staff: document.getElementById('form-staff'),
        vehiculos: document.getElementById('form-vehiculos')
    };

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            for (let key in forms) forms[key].style.display = 'none';
            forms[radio.value].style.display = 'block';
        });
    });
});
</script>
</body>
</html>