<?php
session_start();
// --- Bloques de seguridad y conexión ---
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

// --- Obtener datos para los selectores ---
$periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$estudiantes = [];
$profesores = [];

if ($periodo_activo) {
    $periodo_id = $periodo_activo['id'];
    
    // Obtener estudiantes asignados al período activo
    $sql_est = "SELECT e.id, e.nombre_completo, e.apellido_completo FROM estudiante_periodo ep JOIN estudiantes e ON ep.estudiante_id = e.id WHERE ep.periodo_id = :pid ORDER BY e.apellido_completo, e.nombre_completo";
    $stmt_est = $conn->prepare($sql_est);
    $stmt_est->execute([':pid' => $periodo_id]);
    $estudiantes = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

    // Obtener personal asignado al período activo
    $sql_prof = "SELECT p.id, p.nombre_completo FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid ORDER BY p.nombre_completo";
    $stmt_prof = $conn->prepare($sql_prof);
    $stmt_prof->execute([':pid' => $periodo_id]);
    $profesores = $stmt_prof->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Generar Códigos QR</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_admin.css">
    <style>
        .form-section {
            width: 45%;
            min-width: 400px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>Generar Códigos QR</h1>
    </div>

    <div class="main-container" style="flex-direction: row; justify-content: space-around; align-items: flex-start;">
        
        <!-- Formulario para Estudiantes -->
        <div class="form-section">
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

        <!-- Formulario para Staff/Profesores -->
        <div class="form-section">
            <h3>Para Staff / Profesores</h3>
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
        <!-- Botón para volver al Home -->
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a> 
    </div>
</body>
</html>