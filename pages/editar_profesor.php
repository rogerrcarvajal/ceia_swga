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

// Obtener la asignación actual del profesor en este período (si existe)
$sql_asignacion = "SELECT * FROM profesor_periodo WHERE profesor_id = :profesor_id AND periodo_id = :periodo_id";
$stmt_asignacion = $conn->prepare($sql_asignacion);
$stmt_asignacion->execute([':profesor_id' => $profesor_id, ':periodo_id' => $periodo_id_activo]);
$asignacion_actual = $stmt_asignacion->fetch(PDO::FETCH_ASSOC);

// Lógica para actualizar datos y asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Actualizar datos básicos del profesor
    $nombre_completo = $_POST['nombre_completo'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';

    $sql_update_profesor = "UPDATE profesores SET nombre_completo = :nombre, cedula = :cedula, telefono = :telefono, email = :email WHERE id = :id";
    $stmt_update = $conn->prepare($sql_update_profesor);
    $stmt_update->execute([':nombre' => $nombre_completo, ':cedula' => $cedula, ':telefono' => $telefono, ':email' => $email, ':id' => $profesor_id]);
    $mensaje = "✅ Datos básicos actualizados. ";

    // 2. Gestionar la asignación al período
    $asignar_periodo = isset($_POST['asignar_periodo']);
    $posicion = $_POST['posicion'] ?? '';
    $homeroom = $_POST['homeroom_teacher'] ?? '';

    if ($asignar_periodo) {
        // El usuario quiere que esté asignado
        if ($asignacion_actual) {
            // Ya existía una asignación, así que la ACTUALIZAMOS
            $sql_asig = "UPDATE profesor_periodo SET posicion = :posicion, homeroom_teacher = :homeroom WHERE id = :id";
            $stmt_asig = $conn->prepare($sql_asig);
            $stmt_asig->execute([':posicion' => $posicion, ':homeroom' => $homeroom, ':id' => $asignacion_actual['id']]);
        } else {
            // No existía, así que la INSERTAMOS
            $sql_asig = "INSERT INTO profesor_periodo (profesor_id, periodo_id, posicion, homeroom_teacher) VALUES (:prof_id, :per_id, :pos, :homeroom)";
            $stmt_asig = $conn->prepare($sql_asig);
            $stmt_asig->execute([':prof_id' => $profesor_id, ':per_id' => $periodo_id_activo, ':pos' => $posicion, ':homeroom' => $homeroom]);
        }
        $mensaje .= "✅ Asignación guardada para el período activo.";
    } else {
        // El usuario NO quiere que esté asignado, así que borramos la asignación si existe
        if ($asignacion_actual) {
            $sql_del = "DELETE FROM profesor_periodo WHERE id = :id";
            $stmt_del = $conn->prepare($sql_del);
            $stmt_del->execute([':id' => $asignacion_actual['id']]);
            $mensaje .= "✅ Asignación eliminada del período activo.";
        }
    }
    // Recargar los datos de la asignación para reflejar los cambios en el formulario
    $stmt_asignacion->execute([':profesor_id' => $profesor_id, ':periodo_id' => $periodo_id_activo]);
    $asignacion_actual = $stmt_asignacion->fetch(PDO::FETCH_ASSOC);
}

// Obtener los datos del profesor para el formulario
$stmt_profesor = $conn->prepare("SELECT * FROM profesores WHERE id = :id");
$stmt_profesor->execute([':id' => $profesor_id]);
$profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);
if (!$profesor) {
    header("Location: /pages/profesores_registro.php");
    exit();
}

$posiciones = ["Director", "Bussiness Manager", "Administrative Assistant", "IT Manager", "Psychology", "DC-Grade 12 Music", "Daycare, Pk-3", "Pk-4, Kindergarten", "Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10", "Grade 11", "Grade 12", "Spanish teacher - Grade 1-6", "Spanish teacher - Grade 7-12", "Social Studies - Grade 6-12", "IT Teacher - Grade Pk-3-12", "Science Teacher - Grade 6-12", "ESL - Elementary", "ESL - Secondary", "PE - Grade Pk3-12", "Language Arts - Grade 6-9", "Math teacher - Grade 6-9", "Math teacher - Grade 10-12", "Librarian"];
$homerooms = ["N/A", "Daycare, Pk-3", "Pk-4, Kindergarten", "Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10", "Grade 11", "Grade 12"];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Staff / Profesor</title>
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
        <h1>Gestionar Staff / Profesor</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="formulario-contenedor" style="max-width: 600px;">
        <div class="form-seccion" style="width: 100%;">
            <h3>Editando a: <?= htmlspecialchars($profesor['nombre_completo']) ?></h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

            <form method="POST">
                <fieldset>
                    <legend>Datos Básicos</legend>
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" value="<?= htmlspecialchars($profesor['nombre_completo']) ?>" required>
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" value="<?= htmlspecialchars($profesor['cedula']) ?>" required>
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($profesor['telefono']) ?>">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($profesor['email']) ?>">
                </fieldset>
                
                <fieldset>
                    <legend>Asignación al Período: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></legend>
                    <label>
                        <input type="checkbox" id="asignar_periodo" name="asignar_periodo" <?= $asignacion_actual ? 'checked' : '' ?>>
                        Asignar a este período escolar
                    </label>

                    <div id="campos-asignacion" style="display: none; margin-top: 15px;">
                        <label for="posicion">Posición / Especialidad:</label>
                        <select id="posicion" name="posicion">
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($posiciones as $pos): ?>
                                <option value="<?= $pos ?>" <?= ($asignacion_actual && $asignacion_actual['posicion'] == $pos) ? 'selected' : '' ?>>
                                    <?= $pos ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="homeroom_teacher">Homeroom Teacher:</label>
                        <select id="homeroom_teacher" name="homeroom_teacher">
                            <?php foreach ($homerooms as $hr): ?>
                                <option value="<?= $hr ?>" <?= ($asignacion_actual && $asignacion_actual['homeroom_teacher'] == $hr) ? 'selected' : '' ?>>
                                    <?= $hr ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                
                <br><br>
                <button type="submit">Guardar Cambios</button>
                <!-- Botón para volver al Home -->
                <a href="/pages/dashboard.php" class="btn">Volver</a> 

            </form>
        </div>
    </div>
</body>
</html>