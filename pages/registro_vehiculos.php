<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

//Declaracion de variables
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

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
$periodo_stmt = $conn->query(query: "SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

if ($periodo_stmt->rowCount() === 0) {
    // Si no hay período activo, se guarda un mensaje de error en la sesión.
    // La ventana modal se encargará de mostrarlo.
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar o crear uno en el menú de Mantenimiento para poder continuar.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estudiante_id = $_POST["estudiante_id"];
    $placa = $_POST["placa"];
    $conductor_nombre = $_POST["conductor_nombre"];

    $sql = "INSERT INTO vehiculos_autorizados (estudiante_id, placa, conductor_nombre) VALUES (:estudiante_id, :placa, :conductor_nombre)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':estudiante_id' => $estudiante_id, ':placa' => $placa, ':conductor_nombre' => $conductor_nombre]);

    $mensaje = "✅ Vehículo registrado correctamente.";
}

$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Vehículos Autorizados</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url("/public/img/fondo.jpg"); background-size: cover; background-position: top;  font-family: 'Arial', sans-serif;} 
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .content { text-align: center; margin-top: 10px; color: white; text-shadow: 1px 1px 2px black;}
        .content img { width: 180px;}
    </style>    
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h2>Registro de Vehículos Autorizados</h2>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

        <form method="POST">
            <label>Seleccione Estudiante:</label><br>
            <select name="estudiante_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <input type="text" name="placa" placeholder="Placa del Vehículo" required><br><br>
            <input type="text" name="conductor_nombre" placeholder="Nombre del Conductor" required><br><br>

            <button type="submit">Registrar Vehículo</button>
        </form>
    </div>
</body>
</html>