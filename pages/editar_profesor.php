<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Verificar si el rol del usuario NO es 'admin'
if ($_SESSION['usuario']['rol'] !== 'admin') {
    // Guardar un mensaje de error en la sesión para mostrarlo en el dashboard
    $_SESSION['error_mensaje'] = "Acceso denegado. No tiene permiso para ver esta página.";
    header("Location: /../pages/dashboard.php"); // Redirigir a una página segura
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---

$periodo_stmt = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

if ($periodo_stmt->rowCount() === 0) {
    // Si no hay período activo, se guarda un mensaje de error en la sesión.
    // La ventana modal se encargará de mostrarlo.
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar o crear uno en el menú de Mantenimiento para poder continuar.";
}

// Inicializar variables
$mensaje = "";
$profesor = null;
$profesor_id = $_GET['id'] ?? null;

if (!$profesor_id) {
    header("Location: /../pages/profesores.php");
    exit();
}

// Lógica para actualizar el profesor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = $_POST['nombre_completo'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';

    try {
        $sql = "UPDATE profesores SET nombre_completo = :nombre, cedula = :cedula, telefono = :telefono, email = :email WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre_completo,
            ':cedula' => $cedula,
            ':telefono' => $telefono,
            ':email' => $email,
            ':id' => $profesor_id
        ]);
        $mensaje = "✅ Datos actualizados correctamente.";
    } catch (PDOException $e) {
        $mensaje = "⚠️ Error al actualizar. Es posible que la cédula ya exista.";
    }
}

// Obtener los datos actuales del profesor para mostrarlos en el formulario
$stmt = $conn->prepare("SELECT * FROM profesores WHERE id = :id");
$stmt->execute([':id' => $profesor_id]);
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profesor) {
    header("Location: /../pages/profesores.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Staff / Profesor</title>
    <link rel="stylesheet" href="/public/css/estilo_planilla.css">
    <style>
        body { margin: 0; padding: 0; background-image: url('/public/img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.75); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 500px; }
        .content { text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black; }
        .content img { width: 150px; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Editar Staff / Profesor</h1></br>
    </div>

    <div class="formulario-contenedor">
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
        <form method="POST">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" value="<?= htmlspecialchars($profesor['nombre_completo']) ?>" required>

            <label for="cedula">Cédula:</label>
            <input type="text" id="cedula" name="cedula" value="<?= htmlspecialchars($profesor['cedula']) ?>" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($profesor['telefono']) ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($profesor['email']) ?>">
            
            <br><br>
            <button type="submit">Actualizar Datos</button>
            <a href="/pages/profesores_registro.php" class="boton-link" style="margin-left: 15px;">Volver</a>
        </form>
    </div>
</body>
</html>
