<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
$acceso_stmt = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");

$usuario_rol = $acceso_stmt;

if ($_SESSION['usuario']['rol'] !== 'admin') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
        // Aquí puedes redirigir o cargar la ventana modal según tu lógica
    }
}

// --- Obtener IDs ---
$estudiante_id = $_GET['id'] ?? 0;

// --- OBTENER DATOS PARA MOSTRAR ---
$stmt_est = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$stmt_est->execute([':id' => $estudiante_id]);
$estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

// ----> ¡AQUÍ ESTÁ LA CORRECCIÓN IMPORTANTE! <----
// Si no se encuentra un estudiante con ese ID, detenemos la ejecución con un mensaje claro.
if (!$estudiante) {
    die("Error: No se encontró ningún estudiante con el ID proporcionado.");
}

// Obtener el período activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) {
    die("No hay un período escolar activo. Por favor, active uno.");
}
$periodo_id_activo = $periodo_activo['id'];

$mensaje = "";

// --- Lógica para GUARDAR los cambios (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asignar_periodo = isset($_POST['asignar_periodo']);
    $grado_cursado = $_POST['grado_cursado'] ?? '';
    
    // Busca si ya existe una asignación para este estudiante en este período
    $stmt_check = $conn->prepare("SELECT id FROM estudiante_periodo WHERE estudiante_id = :eid AND periodo_id = :pid");
    $stmt_check->execute([':eid' => $estudiante_id, ':pid' => $periodo_id_activo]);
    $asignacion_existente = $stmt_check->fetch();

    if ($asignar_periodo) {
        if (empty($grado_cursado)) {
            $mensaje = "❌ Error: Debe seleccionar un grado para asignar al estudiante.";
        } else {
            if ($asignacion_existente) {
                // Ya existía, se ACTUALIZA el grado
                $sql = "UPDATE estudiante_periodo SET grado_cursado = :grado WHERE estudiante_id = :eid AND periodo_id = :pid";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':grado' => $grado_cursado, ':eid' => $estudiante_id, ':pid' => $periodo_id_activo]);
            } else {
                // No existía, se INSERTA la nueva asignación
                $sql = "INSERT INTO estudiante_periodo (estudiante_id, periodo_id, grado_cursado) VALUES (:eid, :pid, :grado)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':eid' => $estudiante_id, ':pid' => $periodo_id_activo, ':grado' => $grado_cursado]);
            }
            $mensaje = "✅ Asignación guardada para el período activo.";
        }
    } else {
        // Si el checkbox no está marcado, se ELIMINA la asignación
        if ($asignacion_existente) {
            $sql = "DELETE FROM estudiante_periodo WHERE estudiante_id = :eid AND periodo_id = :pid";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':eid' => $estudiante_id, ':pid' => $periodo_id_activo]);
            $mensaje = "✅ Asignación eliminada del período activo.";
        }
    }
}

// --- Obtener datos FRESCOS para mostrar en el formulario ---
// Datos del estudiante (CORREGIDO: busca en la tabla 'estudiantes')
$stmt_est = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$stmt_est->execute([':id' => $estudiante_id]);
$estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

// Asignación actual del estudiante en el período activo (para marcar el checkbox y el grado)
$stmt_asig = $conn->prepare("SELECT * FROM estudiante_periodo WHERE estudiante_id = :eid AND periodo_id = :pid");
$stmt_asig->execute([':eid' => $estudiante_id, ':pid' => $periodo_id_activo]);
$asignacion_actual = $stmt_asig->fetch(PDO::FETCH_ASSOC);

// Lista de grados disponibles para el formulario
$grados_disponibles = ['Daycare', 'Preschool', 'Prekinder 3', 'Prekinder 4', 'Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Asignación de Estudiante</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css">
    <style>
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
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>Gestionar Asignación</h1>
        <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
    </div>

    <div class="formulario-contenedor" style="max-width: 600px; margin: 20px auto;">
        <h3>Editando a: <strong><?= htmlspecialchars($estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo']) ?></strong></h3>
        <?php if ($mensaje): ?>
            <p class="mensaje <?= str_contains($mensaje, '✅') ? 'exito' : 'error' ?>"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <fieldset>
                <legend>Asignación para el Período Activo</legend>
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
            <a href="/pages/lista_gestion_estudiantes.php" class="btn">Volver y gestionar otro estudiante</a>
        </form>
    </div>
</body>
</html>