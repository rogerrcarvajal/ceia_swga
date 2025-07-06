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

$mensaje = "";

// Obtener período escolar activo
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Dirijase al menú Mantenimiento para crear uno.");
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

$periodos = $conn->query("SELECT * FROM periodos_escolares ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Períodos Escolares</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("/public/img/fondo.jpg");
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }
        
        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 0px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 65%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .formulario {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            margin: 30px auto;
            width: 30%;
            border-radius: 8px;
        }

        .form-seccion {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0057A0;
            padding-bottom: 5px;
        }

        .act {
            color: green;
        }

        .content {
            text-align: center;
            margin-top: 50px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 180px;
            margin-bottom: 20px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }
        button {
            background-color: #0057A0;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button[name="desactivar"] { background-color: #ffc107; color: #333; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/img/logo_ceia.png" alt="Logo CEIA">
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
                    <p>Ya existe un período escolar activo. Para crear uno nuevo, debe desactivar el período actual.</p>
                </div>
            <?php endif; ?>
            <br>
            <a href="/pages/dashboard.php" class="boton-link">Volver al Inicio</a>
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