<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /../public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

$mensaje = "";
$acceso_stmt = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");
$usuario_rol = $acceso_stmt;

if ($_SESSION['usuario']['rol'] !== 'admin') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado.";
    }
}

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
        .main-container { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227, 228, 237, 0.37); border: 2px solid rgba(255, 255, 255, 0.18); display: flex; max-width: 63%; margin: 0 auto; gap: 20px; }
        .form-section { width: 30%; min-width: 400px; margin-bottom: 20px; display: none; }
        .selector-container { margin-bottom: 20px; padding: 30px;}
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
    <h1>Generar Códigos QR</h1>
</div>

<div class="main-container" style="flex-direction: row; align-items: left;">
    <div class="selector-container">
        <label><input type="radio" name="selector" value="estudiantes">Estudiantes</label>
        <label><input type="radio" name="selector" value="staff">Staff/Profesores</label>
        <label><input type="radio" name="selector" value="vehiculos">Vehículos</label>
        <br><br>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a>
    </div>

    <!-- Estudiantes -->
    <div id="form-estudiantes" class="form-section">
        <br><br>
        <h3>Para Estudiantes</h3>
        <form action="/ceia_swga/src/reports_generators/generar_qr_pdf.php" method="GET" target="_blank">
            <label>Seleccione un Estudiante:</label>
            <select name="id" required>
                <option value="">-- Por favor, elija --</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <button type="submit">Generar QR de Estudiante</button>
        </form>
    </div>

    <!-- Staff -->
    <div id="form-staff" class="form-section">
        <br><br>
        <h3>Para Staff/Profesores</h3>
        <form action="/ceia_swga/src/reports_generators/generar_qr_staff_pdf.php" method="GET" target="_blank">
            <label>Seleccione un Miembro del Personal:</label>
            <select name="id" required>
                <option value="">-- Por favor, elija --</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <button type="submit">Generar QR de Staff</button>
        </form>
    </div>

    <!-- Vehículos -->
    <div id="form-vehiculos" class="form-section">
        <br><br>
        <h3>Para Vehículos Autorizados</h3>
        <form action="/ceia_swga/src/reports_generators/generar_qr_vehiculo_pdf.php" method="GET" target="_blank">
            <label>Seleccione un Vehículo:</label>
            <select name="id" required>
                <option value="">-- Por favor, elija --</option>
                <?php foreach ($vehiculos as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['placa'] . ' - ' . $v['modelo'] . ' (' . $v['estudiante'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <button type="submit">Generar QR de Vehículo</button>
        </form>
    </div>
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const radios = document.querySelectorAll('input[name="selector"]');
        const forms = {
            estudiantes: document.getElementById('form-estudiantes'),
            staff: document.getElementById('form-staff'),
            vehiculos: document.getElementById('form-vehiculos'),
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