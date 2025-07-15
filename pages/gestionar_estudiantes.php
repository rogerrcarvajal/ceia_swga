<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

//Declaracion de variables
$mensaje = "";
$profesor_id = $_GET['id'] ?? null;
if (!$profesor_id) {
    header("Location: /pages/profesores_registro.php");
    exit();
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// Obtener el ID del período activo
$periodo_id_activo = $periodo_activo['id'];

// Obtener la asignación actual del estudiante en este período (si existe)
$sql_asignacion = "SELECT * FROM estudiante_periodo WHERE estudiante_id = :estudiante_id AND periodo_id = :periodo_id";
$stmt_asignacion = $conn->prepare($sql_asignacion);
$stmt_asignacion->execute([':estudiante_id' => $estudiante_id, ':periodo_id' => $periodo_id_activo]);
$asignacion_actual = $stmt_asignacion->fetch(PDO::FETCH_ASSOC);

// Lógica para actualizar datos y asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Gestionar la asignación al período
    $asignar_periodo = isset($_POST['asignar_periodo']);
    $grado_actual = $_POST['grado_cursado'] ?? '';

    if ($asignar_periodo) {
        // El usuario quiere que esté asignado
        if ($asignacion_actual) {
            // Ya existía una asignación, así que la ACTUALIZAMOS
            $sql_asig = "UPDATE estudiante_periodo SET grado_cursado = :grado WHERE id = :id";
            $stmt_asig = $conn->prepare($sql_asig);
            $stmt_asig->execute([':grado' => $grado_actual, ':id' => $asignacion_actual['id']]);
        } else {
            // No existía, así que la INSERTAMOS
            $sql_asig = "INSERT INTO estudiante_periodo (estudiante_id, periodo_id, grado_cursado) VALUES (:estudiante_id, :periodo_id, :grado)";
            $stmt_asig = $conn->prepare($sql_asig);
            $stmt_asig->execute([':estudiante_id' => $estudiante_id, ':periodo_id' => $periodo_id_activo, ':grado' => $grado_actual]);
        }
        $mensaje .= "✅ Asignación guardada para el período activo.";
    } else {
        // El usuario NO quiere que esté asignado, así que borramos la asignación si existe
        if ($asignacion_actual) {  
            $sql_del = "DELETE FROM estudiante_periodo WHERE id = :id";
            $stmt_del = $conn->prepare($sql_del);
            $stmt_del->execute([':id' => $asignacion_actual['id']]);
            $mensaje .= "✅ Asignación eliminada del período activo.";
        }  
    }
    // Recargar los datos de la asignación para reflejar los cambios en el formulario
    $stmt_asignacion->execute([':estudiante_id' => $estudiante_id, ':periodo_id' => $periodo_id_activo]);
    $asignacion_actual = $stmt_asignacion->fetch(PDO::FETCH_ASSOC);
}

$Grados = ["N/A", "Daycare", "Preschool", "Prekinder 3", "Prekinder 4", "Kindergarten", "Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10", "Grade 11", "Grade 12"];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Estudiante</title>
    <link rel="stylesheet" href="/public/css/estilo_planilla.css">
    <style>
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 500px; }
        .content { text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black; }
        .content img { width: 150px; }
    </style>
    <script>
        // Pequeño script para mostrar/ocultar los campos de asignación
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('asignar_periodo');
            const camposAsignacion = document.getElementById('campos-asignacion');

            function toggleCampos() {
                camposAsignacion.style.display = checkbox.checked ? 'block' : 'none';
            }

            checkbox.addEventListener('change', toggleCampos);
            toggleCampos(); // Ejecutar al cargar la página
        });
    </script>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestionar Estudiante</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="formulario-contenedor" style="max-width: 600px;">
        <div class="form-seccion" style="width: 100%;">
            <h3>Editando a: <?= htmlspecialchars($estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo']) ?></h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

            <form method="POST">    
                <fieldset>
                    <legend>Datos Registrados</legend>
                    <legend>Asignación al Período: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></legend>
                    <label>
                        <input type="checkbox" id="asignar_periodo" name="asignar_periodo" <?= $asignacion_actual ? 'checked' : '' ?>>
                        Asignar a este período escolar
                    </label>
                    <div id="campos-asignacion" style="display: none; margin-top: 15px;">
                        <label for="grado_cursado">Grado a cursar:</label>
                        <select id="grado_cursado" name="grado_cursado">
                            <?php foreach ($Grados as $grado): ?>
                                <option value="<?= $grado ?>" <?= ($asignacion_actual && $asignacion_actual['grado_cursado'] == $grado) ? 'selected' : '' ?>>
                                    <?= $grado ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>          
                <br><br>
                <button type="submit">Guardar Cambios</button>
                <!-- Botón para volver al Home -->
                <a href="/pages/asignar_estudiante_periodo.php" class="btn">Volver</a> 

            </form>
        </div>
    </div>
</body>
</html>