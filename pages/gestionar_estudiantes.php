<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo el Usuario Master y Admin pueden gestionar el módulo de estudiantes.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo el Usuario Master y Admin pueden gestionar el módulo de estudiantes."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// --- Obtener IDs de la URL ---
$estudiante_id = $_GET['id'] ?? 0;
$periodo_id = $_GET['periodo_id'] ?? 0;

// --- Validar IDs ---
if (!$estudiante_id || !$periodo_id) {
    die("Error: Se requiere un ID de estudiante y un ID de período válidos.");
}

// --- OBTENER DATOS DEL ESTUDIANTE Y DEL PERÍODO ---
$stmt_est = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$stmt_est->execute([':id' => $estudiante_id]);
$estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

$stmt_per = $conn->prepare("SELECT * FROM periodos_escolares WHERE id = :id");
$stmt_per->execute([':id' => $periodo_id]);
$periodo = $stmt_per->fetch(PDO::FETCH_ASSOC);

if (!$estudiante || !$periodo) {
    die("Error: No se encontró el estudiante o el período especificado.");
}

$mensaje = "";

// --- Lógica para GUARDAR los cambios (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asignar_periodo = isset($_POST['asignar_periodo']);
    $grado_cursado = $_POST['grado_cursado'] ?? '';
    
    // Busca si ya existe una asignación para este estudiante en este período
    $stmt_check = $conn->prepare("SELECT id FROM estudiante_periodo WHERE estudiante_id = :eid AND periodo_id = :pid");
    $stmt_check->execute([':eid' => $estudiante_id, ':pid' => $periodo_id]);
    $asignacion_existente = $stmt_check->fetch();

    if ($asignar_periodo) {
        if (empty($grado_cursado)) {
            $mensaje = "❌ Error: Debe seleccionar un grado para asignar al estudiante.";
        } else {
            if ($asignacion_existente) {
                // Ya existía, se ACTUALIZA el grado
                $sql = "UPDATE estudiante_periodo SET grado_cursado = :grado WHERE id = :asig_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':grado' => $grado_cursado, ':asig_id' => $asignacion_existente['id']]);
            } else {
                // No existía, se INSERTA la nueva asignación
                $sql = "INSERT INTO estudiante_periodo (estudiante_id, periodo_id, grado_cursado) VALUES (:eid, :pid, :grado)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':eid' => $estudiante_id, ':pid' => $periodo_id, ':grado' => $grado_cursado]);
            }
            $mensaje = "✅ Asignación guardada para el período " . htmlspecialchars($periodo['nombre_periodo']);
        }
    } else {
        // Si el checkbox no está marcado, se ELIMINA la asignación
        if ($asignacion_existente) {
            $sql = "DELETE FROM estudiante_periodo WHERE id = :asig_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':asig_id' => $asignacion_existente['id']]);
            $mensaje = "✅ Asignación eliminada del período " . htmlspecialchars($periodo['nombre_periodo']);
        }
    }
}

// --- Obtener datos FRESCOS para mostrar en el formulario ---
// Asignación actual del estudiante en el período seleccionado (para marcar el checkbox y el grado)
$stmt_asig = $conn->prepare("SELECT * FROM estudiante_periodo WHERE estudiante_id = :eid AND periodo_id = :pid");
$stmt_asig->execute([':eid' => $estudiante_id, ':pid' => $periodo_id]);
$asignacion_actual = $stmt_asig->fetch(PDO::FETCH_ASSOC);

// Lista de grados disponibles
$grados_disponibles = ['Daycare', 'Preschool', 'Prekinder 3', 'Prekinder 4', 'Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Asignación de Estudiante</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_admin.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 500px; }
        .content { text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black; }
        .content img { width: 150px; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('asignar_periodo');
            const camposAsignacion = document.getElementById('campos-asignacion');
            function toggleCampos() {
                camposAsignacion.style.display = checkbox.checked ? 'block' : 'none';
            }
            checkbox.addEventListener('change', toggleCampos);
            toggleCampos();
        });
    </script>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>SWGA - Gestionar Asignación</h1>
        <h3 style="color: #a2ff96;">Período: <?= htmlspecialchars($periodo['nombre_periodo']) ?></h3>
    </div>

    <div class="formulario-contenedor" style="max-width: 600px; margin: 20px auto;">
        <h3>Editando a: <strong><?= htmlspecialchars($estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo']) ?></strong></h3>
        <?php if ($mensaje): ?>
            <p class="mensaje <?= str_contains($mensaje, '✅') ? 'exito' : 'error' ?>"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <fieldset>
                <legend>Asignación para el Período: <?= htmlspecialchars($periodo['nombre_periodo']) ?></legend>
                <label>
                    <input type="checkbox" id="asignar_periodo" name="asignar_periodo" <?= $asignacion_actual ? 'checked' : '' ?>>
                    Asignar a este período escolar
                </label>
                <div id="campos-asignacion" style="display: none; margin-top: 15px;">
                    <label for="grado_cursado">Grado a cursar:</label>
                    <select id="grado_cursado" name="grado_cursado">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($grados_disponibles as $grd): ?>
                            <option value="<?= $grd ?>" <?= ($asignacion_actual && $asignacion_actual['grado_cursado'] == $grd) ? 'selected' : '' ?>>
                                <?= $grd ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </fieldset>
            <br>
            <button type="submit">Guardar Cambios</button>
            <a href="/ceia_swga/pages/lista_gestion_estudiantes.php?periodo_id=<?= $periodo_id ?>" class="btn">Volver y gestionar otro estudiante</a>
        </form>
    </div>
</body>
</html>