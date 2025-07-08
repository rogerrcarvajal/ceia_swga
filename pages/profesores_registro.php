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
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// --- Lógica para registrar un nuevo profesor ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $nombre_completo = $_POST["nombre_completo"] ?? '';
    $cedula = $_POST["cedula"] ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $email = $_POST["email"] ?? '';

    $check = $conn->prepare("SELECT id FROM profesores WHERE cedula = :cedula");
    $check->execute([':cedula' => $cedula]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Un miembro del staff ya está registrado con esta cédula.";
    } else {
        $sql = "INSERT INTO profesores (nombre_completo, cedula, telefono, email) VALUES (:nombre, :cedula, :telefono, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':nombre' => $nombre_completo, ':cedula' => $cedula, ':telefono' => $telefono, ':email' => $email]);
        $mensaje = "✅ Staff / Profesor registrado correctamente. Ahora puede asignarlo al período activo desde la lista.";
    }
}

// --- Obtener la lista de TODO el personal y su estado de asignación en el período activo ---
$periodo_id_activo = $periodo_activo ? $periodo_activo['id'] : 0;

$sql_profesores = "SELECT 
                        p.id, 
                        p.nombre_completo, 
                        p.cedula,
                        pp.id AS asignacion_id,  -- Será NULL si no está asignado
                        pp.posicion
                   FROM profesores p
                   LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id AND pp.periodo_id = :periodo_id
                   ORDER BY p.nombre_completo ASC";
$stmt_profesores = $conn->prepare($sql_profesores);
$stmt_profesores->execute([':periodo_id' => $periodo_id_activo]);
$profesores = $stmt_profesores->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Staff / Profesores</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url('/public/img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.5); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .form-seccion { width: 45%; color: white; min-width: 350px; }
        .form-seccion h3 { text-align: center; border-bottom: 1px solidrgb(42, 42, 42); padding-bottom: 10px; }
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 180px; }
        .lista-profesores { list-style: none; padding: 0; max-height: 400px; overflow-y: auto; }
        .lista-profesores li { background-color: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .lista-profesores a { color: #87cefa; text-decoration: none; margin-left: 10px; }
    </style>    
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Staff / Profesores</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="formulario-contenedor">
        <div class="form-seccion">
            <h3>Registrar Nuevo Ingreso</h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
            <form method="POST">
                <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
                <input type="text" name="cedula" placeholder="Cédula" required>
                <input type="text" name="telefono" placeholder="Teléfono">
                <input type="email" name="email" placeholder="Correo electrónico">
                <br><br>
                <button type="submit" name="agregar">Agregar Staff</button>
            </form>
        </div> 

        <div class="form-seccion">
            <h3>Personal Registrado</h3>
            <ul class="lista-profesores">
                <?php if (empty($profesores)): ?>
                    <li>No hay personal registrado en el sistema.</li>
                <?php else: ?>
                    <?php foreach ($profesores as $p): ?>
                        <li>
                            <span>
                                <?= htmlspecialchars($p['nombre_completo']) ?> (C.I: <?= htmlspecialchars($p['cedula']) ?>)
                                <?php if ($p['asignacion_id']): ?>
                                    <br><small style="color:#a2ff96;">Asignado como: <?= htmlspecialchars($p['posicion']) ?></small>
                                <?php else: ?>
                                    <br><small style="color:#ffc107;">No asignado a este período</small>
                                <?php endif; ?>
                            </span>
                            <div>
                                <a href="/pages/editar_profesor.php?id=<?= $p['id'] ?>">Gestionar</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>