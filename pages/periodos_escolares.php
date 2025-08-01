<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /ceia_swga/public/index.php");
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

// --- LÓGICA DE NEGOCIO ---
// Desactivar período escolar (NUEVA FUNCIONALIDAD)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['desactivar'])) {
    $periodo_id = $_POST['periodo_id'];
    $stmt = $conn->prepare("UPDATE periodos_escolares SET activo = FALSE WHERE id = :id");
    $stmt->execute([':id' => $periodo_id]);
    $mensaje = "✅ Período escolar desactivado.";
}

// Activar período escolar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activar'])) {
    $periodo_id = $_POST['periodo_id'];
    $conn->beginTransaction();
    $conn->query("UPDATE periodos_escolares SET activo = FALSE");
    $stmt = $conn->prepare("UPDATE periodos_escolares SET activo = TRUE WHERE id = :id");
    $stmt->execute([':id' => $periodo_id]);
    $conn->commit();
    $mensaje = "✅ Período escolar activado correctamente.";
}

// Verificar si existe un período activo para la lógica de la vista
$check_active_stmt = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");
$periodo_activo_existe = ($check_active_stmt->rowCount() > 0);

// Registrar nuevo período
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear'])) {
    if ($periodo_activo_existe) {
        $mensaje = "⚠️ No se puede crear un nuevo período mientras otro esté activo.";
    } else {
        $nombre = $_POST['nombre_periodo'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $sql = "INSERT INTO periodos_escolares (nombre_periodo, fecha_inicio, fecha_fin) VALUES (:nombre, :inicio, :fin)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':nombre' => $nombre, ':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
        $mensaje = "✅ Período escolar creado correctamente. Ahora puede activarlo.";
    }
}

// Obtener el período escolar activo
$periodos = $conn->query("SELECT * FROM periodos_escolares ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Períodos Escolares</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 0px auto; padding: 30px; border-radius: 10px; max-width: 65%; display: flex; flex-wrap: wrap; justify-content: space-around;}
        .formulario {background-color: rgba(0, 0, 0, 0.3); color: white; padding: 25px; margin: 30px auto; width: 30%; border-radius: 8px;}
        .form-seccion { width: 30%; color: white; min-width: 300px; margin-bottom: 20px;}
        h3 { text-align: center; margin-bottom: 15px; border-bottom: 2px solidrgb(0, 0, 0); padding-bottom: 5px;}
        .act { color: green;}
        .content { text-align: center; margin-top: 30px; color: white; text-shadow: 1px 1px 2px black;}
        .content img { width: 180px; margin-bottom: 20px;}
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 12px; font-size: 16px;}
        button { background-color:rgb(42, 42, 42); color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;}
        button[name="desactivar"] { background-color: #ffc107; color: #333; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Períodos Escolares</h1>
    </div>
    
    <div class="formulario-contenedor">
        <div class="form-seccion">
            <h3>Crear Período Escolar</h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

            <?php if (!$periodo_activo_existe): ?>
                <form method="POST">
                    <input type="text" name="nombre_periodo" placeholder="Ej: Agosto 2025 - Junio 2026" required>
                    <label>Fecha de Inicio:</label>
                    <input type="date" name="fecha_inicio" required>
                    <label>Fecha de Fin:</label>
                    <input type="date" name="fecha_fin" required>
                    <br><br>
                    <button type="submit" name="crear">Crear Período</button>
                </form>
            <?php else: ?>
                <div class="info-mensaje">
                    <p><h3>⚠️ Ya existe un período escolar activo</h3><br>Solo puede activar o desactivar un perido escolar activo. Para crear uno nuevo, debe esperar a que éste termine su ejecución.</p>
                </div>
            <?php endif; ?>
            <br>
            <!-- Botón para volver aperiodos escolares -->
            <a href="/ceia_swga/pages/menu_mantto.php" class="btn">Volver</a>
        </div>

        <div class="form-seccion">
            <h3>Períodos Registrados</h3>
            <ul class="lista-periodos">
                <?php foreach ($periodos as $p): ?>
                    <li class="<?= $p['activo'] ? 'activo' : '' ?>">
                        <span>
                            <?= htmlspecialchars($p['nombre_periodo']) ?>
                            <?= $p['activo'] ? "<strong>(Activo)</strong>" : "" ?>
                        </span>
                        <div>
                            <?php if ($p['activo'] && $_SESSION['usuario']['rol'] === 'admin'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="periodo_id" value="<?= $p['id'] ?>">
                                    <button type="submit" name="desactivar">Desactivar</button>
                                </form>
                            <?php elseif (!$p['activo'] && $_SESSION['usuario']['rol'] === 'admin'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="periodo_id" value="<?= $p['id'] ?>">
                                    <button type="submit" name="activar">Activar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>