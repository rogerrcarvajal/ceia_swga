<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// Verificación de periodo activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estudiante_id = $_POST["estudiante_id"];
    $placa = strtoupper(trim($_POST["placa"]));
    $modelo = trim($_POST["modelo"]);
    $autorizado = isset($_POST["autorizado"]) ? true : false;

    $stmt = $conn->prepare("INSERT INTO vehiculos (estudiante_id, placa, modelo, autorizado) VALUES (:estudiante_id, :placa, :modelo, :autorizado)");
    $stmt->execute([
        ':estudiante_id' => $estudiante_id,
        ':placa' => $placa,
        ':modelo' => $modelo,
        ':autorizado' => $autorizado,
    ]);

    $mensaje = "✅ Vehículo registrado correctamente.";
}

// Obtener estudiantes
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Registro de Vehículos</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 180px; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 500px; }
        .formulario-contenedor h2 { text-align: center; }
        .formulario-contenedor label { font-weight: bold; }
        .formulario-contenedor input, .formulario-contenedor select { width: 50%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: none; }
        .formulario-contenedor button { background-color: rgb(48, 48, 48); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .alerta { background-color: #2ecc71; color: white; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>

<div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Vehículos autorizados</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

<div class="formulario-contenedor">
    <div class="form-seccion">
        <h3>Registrar Nuevo Ingreso</h3>
    <?php if ($mensaje): ?>
        <div class="alerta"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="estudiante_id">Estudiante:</label>
        <select name="estudiante_id" id="estudiante_id" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($estudiantes as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="placa" placeholder="Placa del Vehículo" id="placa" required>

        <input type="text" name="modelo" placeholder="Modelo del Vehículo" id="modelo" required>

        <label><input type="checkbox" name="autorizado" checked> Autorizado</label><br><br>

        <button type="submit">Registrar Vehículo</button>
    </form>
</div>
</body>
</html>