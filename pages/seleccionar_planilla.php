<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("Location: /index.php"); exit(); }
require_once __DIR__ . '/../src/config.php';

// Obtenemos solo estudiantes del período activo
$periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$estudiantes = [];
if ($periodo_activo) {
    $stmt = $conn->prepare("SELECT id, nombre_completo, apellido_completo FROM estudiantes WHERE periodo_id = :pid ORDER BY apellido_completo, nombre_completo");
    $stmt->execute([':pid' => $periodo_activo['id']]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Planilla</title>
    <link rel="stylesheet" href="/public/css/style.css">
    </head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content"><h1>Generar Planilla de Inscripción</h1></div>
    <div class="formulario-contenedor">
        <form action="/src/reports_generators/generar_planilla_pdf.php" method="GET" target="_blank">
            <label>Seleccione un Estudiante del Período Actual:</label>
            <select name="id" required>
                <option value="">-- Por favor, elija --</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Generar PDF</button>
        </form>
    </div>
</body>
</html>