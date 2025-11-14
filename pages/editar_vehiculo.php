<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar vehículos.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar vehículos."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

$mensaje = "";

// Verificar si se proporciona un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje_vehiculo'] = "⚠️ ID de vehículo no válido.";
    header("Location: /ceia_swga/pages/registro_vehiculos.php");
    exit;
}

$vehiculo_id = $_GET['id'];

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar los datos del formulario
    if (empty($_POST["estudiante_id"]) || !is_numeric($_POST["estudiante_id"])) {
        $mensaje = "⚠️ Debe seleccionar un estudiante válido.";
    } elseif (empty($_POST["placa"])) {
        $mensaje = "⚠️ La placa es obligatoria.";
    } elseif (empty($_POST["modelo"])) {
        $mensaje = "⚠️ El modelo es obligatorio.";
    } else {

    $estudiante_id = $_POST["estudiante_id"];
    $placa = strtoupper(trim($_POST["placa"]));
    $modelo = trim($_POST["modelo"]);
    $autorizado = isset($_POST["autorizado"]) ? true : false;

        // Verificar si la placa ya existe en OTRO vehículo
        $check_stmt = $conn->prepare("SELECT id FROM vehiculos WHERE placa = :placa AND id != :id");
        $check_stmt->execute([':placa' => $placa, ':id' => $vehiculo_id]);

        if ($check_stmt->rowCount() > 0) {
            $mensaje = "⚠️ Ya existe otro vehículo registrado con esta placa.";
        } else {
            // Actualizar los datos del vehículo
            $stmt = $conn->prepare("UPDATE vehiculos SET estudiante_id = :estudiante_id, placa = :placa, modelo = :modelo, autorizado = :autorizado WHERE id = :id");
            $stmt->execute([
                ':estudiante_id' => $estudiante_id,
                ':placa' => $placa,
                ':modelo' => $modelo,
                ':autorizado' => $autorizado,
                ':id' => $vehiculo_id
            ]);

            $_SESSION['mensaje_vehiculo'] = "✅ Vehículo actualizado correctamente.";
            header("Location: /ceia_swga/pages/registro_vehiculos.php");
            exit;
        }
    }
}

// Obtener los datos del vehículo para mostrar en el formulario (después del POST por si hay error)
$stmt = $conn->prepare("SELECT estudiante_id, placa, modelo, autorizado FROM vehiculos WHERE id = :id");
$stmt->execute([':id' => $vehiculo_id]);
$vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehiculo) {
    $_SESSION['mensaje_vehiculo'] = "⚠️ Vehículo no encontrado.";
    header("Location: /ceia_swga/pages/registro_vehiculos.php");
    exit;
}

$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Editar Vehículo</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .content img { width: 180px; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 500px; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>

<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
    <h1>Editar Vehículo</h1>
    <?php if ($periodo_activo): ?>
        <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
    <?php endif; ?>
</div>

<div class="formulario-contenedor">
    <h3>Editando vehículo: <?= htmlspecialchars($vehiculo['placa']) ?></h3>
    <?php if ($mensaje): ?>
        <div class="alerta"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="estudiante_id">Estudiante:</label>
        <select name="estudiante_id" id="estudiante_id" required>
            <?php foreach ($estudiantes as $e): ?>
                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $vehiculo['estudiante_id']) ? 'selected' : '' ?>><?= htmlspecialchars($e['nombre_completo']) ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <label for="placa">Placa:</label>
        <input type="text" name="placa" id="placa" value="<?= htmlspecialchars($vehiculo['placa']) ?>" required><br><br>
        <label for="modelo">Modelo:</label>
        <input type="text" name="modelo" id="modelo" value="<?= htmlspecialchars($vehiculo['modelo']) ?>" required><br><br>
        <label><input type="checkbox" name="autorizado" <?= $vehiculo['autorizado'] ? 'checked' : '' ?>> Autorizado</label><br><br>
        <button type="submit">Guardar Cambios</button>
        <a href="/ceia_swga/pages/registro_vehiculos.php" class="btn">Cancelar</a>
    </form>
</div>
</body>
</html>
