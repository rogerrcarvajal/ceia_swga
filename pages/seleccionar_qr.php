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

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
$periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$estudiantes = [];

if ($periodo_activo) {
    $periodo_id = $periodo_activo['id'];
    
    // ESTA ES LA CONSULTA CORREGIDA
    // Selecciona los estudiantes que tienen una entrada en 'estudiante_periodo' para el período activo.
    $sql = "SELECT e.id, e.nombre_completo, e.apellido_completo
            FROM estudiante_periodo ep
            JOIN estudiantes e ON ep.estudiante_id = e.id
            WHERE ep.periodo_id = :pid
            ORDER BY e.apellido_completo, e.nombre_completo";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':pid' => $periodo_id]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Generar Código QR</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css">
    <style>
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 50px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .content { text-align: center; margin-top: 20px; color: white; text-shadow: 1px 1px 2px black;}
        .content img { width: 200px; margin-bottom: 20px;}
        .content h1 { font-size: 50px; margin-bottom: 20px;}
        .content p { font-size: 20px;}
        .right-panel { width: 30%; flex: 1; background-color: rgba(0,0,0,0.3); backdrop-filter:blur(5px); padding: 15px; border-radius: 8px; }
        .lista-gestion {
            list-style: none;
            padding: 0;
            max-width: 800px;
            margin: 20px auto;
        }
        .lista-gestion li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .lista-gestion li:nth-child(odd) {
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter:blur(10px);
            box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
            border:2px solid rgba(255,255,255,0.18);
        }
        .lista-gestion .btn-gestionar {
            background-color: rgb(48, 48, 48);
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .lista-gestion .btn-gestionar:hover {
            background-color: rgb(48, 48, 48);
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
            <h1>Generar QR</h1></div>
        <div class="right-panel"></div>
            <form action="/src/reports_generators/generar_qr_pdf.php" method="GET" target="_blank">
                <label>Seleccione un Estudiante:</label>
                <select name="id" required>
                    <option value="">-- Por favor, elija --</option>
                    <?php foreach ($estudiantes as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <button type="submit">Generar QR en PDF</button>
            </form>
    </div>
</body>
</html>