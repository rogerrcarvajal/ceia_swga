<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

$mensaje = "";

// Verificar si se proporciona un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de vehículo no válido.";
    exit;
}

$vehiculo_id = $_GET['id'];

// Obtener los datos del vehículo
$stmt = $conn->prepare("SELECT estudiante_id, placa, modelo, autorizado FROM vehiculos WHERE id = :id");
$stmt->execute([':id' => $vehiculo_id]);
$vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehiculo) {
    echo "Vehículo no encontrado.";
    exit;
}

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

    // Actualizar los datos del vehículo
    $stmt = $conn->prepare("UPDATE vehiculos SET estudiante_id = :estudiante_id, placa = :placa, modelo = :modelo, autorizado = :autorizado WHERE id = :id");
    $stmt->execute([
        ':estudiante_id' => $estudiante_id,
        ':placa' => $placa,
        ':modelo' => $modelo,
        ':autorizado' => $autorizado,
        ':id' => $vehiculo_id
    ]);

    $mensaje = "✅ Vehículo actualizado correctamente.";
    header("Location: registro_vehiculos.php"); // Redirigir a la página principal
    exit;
    }
}

// Obtener la lista de estudiantes para el select
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Vehículo</title>
</head>
<body>
    <h2>Editar Vehículo</h2>
    <form method="POST">
        <label for="estudiante_id">Estudiante:</label>
        <select name="estudiante_id" id="estudiante_id" required>
            <?php foreach ($estudiantes as $e): ?>
                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $vehiculo['estudiante_id']) ? 'selected' : '' ?>><?= htmlspecialchars($e['nombre_completo']) ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <input type="text" name="placa" value="<?= htmlspecialchars($vehiculo['placa']) ?>" required><br><br>
        <input type="text" name="modelo" value="<?= htmlspecialchars($vehiculo['modelo']) ?>" required><br><br>
        <label><input type="checkbox" name="autorizado" <?= $vehiculo['autorizado'] ? 'checked' : '' ?>> Autorizado</label><br><br>
        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>
