<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// --- GESTOR DE DESCARGAS ---
// Maneja las solicitudes de descarga de archivos de respaldo
if (isset($_GET['download_file'])) {
    $filename = basename($_GET['download_file']);
    $filepath = __DIR__ . '/../PostgreSQL-DB/' . $filename;

    // Medida de seguridad: solo permitir la descarga de archivos que coincidan con el patrón esperado
    if (preg_match('/^ceia_db_backup_(\d{8})_(\d{6})\.sql$/', $filename) && file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush(); // Limpiar el buffer de salida del sistema
        readfile($filepath);
        exit;
    } else {
        // Si el archivo no es válido o no existe, detener la ejecución.
        die('Acción no permitida: Archivo no válido o no encontrado.');
    }
}

$mensaje = "";

// --- BLOQUE DE CONTROL DE ACCESO ---
if (!isset($_SESSION['usuario']['rol']) || $_SESSION['usuario']['rol'] !== 'master') {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo el Usuario Master puede gestionar el módulo de mantenimiento.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo el Usuario Master puede gestionar el módulo de mantenimiento."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// --- LÓGICA DE RESPALDO MANUAL ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['backup_db'])) {
    $pg_dump_path = PG_DUMP_PATH;
    $backup_dir = __DIR__ . '/../PostgreSQL-DB/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    $backup_filename = 'ceia_db_backup_' . date('Ymd_His') . '.sql';
    $backup_filepath = $backup_dir . $backup_filename;

    putenv('PGPASSWORD=' . DB_PASSWORD);

    $command = sprintf(
        '"%s" -U "%s" -h "%s" -d "%s" -F p -E UTF-8 -f "%s" 2>&1',
        $pg_dump_path,
        DB_USER,
        DB_HOST,
        DB_NAME,
        $backup_filepath
    );

    $output = [];
    $return_var = -1;
    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        $error_details = htmlspecialchars(implode("\n", $output));
        $mensaje = "❌ Error al realizar el respaldo. Verifique la configuración y permisos.<br>";
        $mensaje .= "Código de retorno: " . $return_var . "<br>";
        $mensaje .= "Detalles: <pre>" . $error_details . "</pre>";
        $mensaje .= "Comando ejecutado: <pre>" . htmlspecialchars($command) . "</pre>";
    } else {
        $mensaje = "✅ Respaldo realizado con éxito: " . htmlspecialchars($backup_filename);
    }

    putenv('PGPASSWORD=');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Respaldo de Base de Datos</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        .body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .form-seccion { width: 45%; color: white; min-width: 350px; }
        .form-seccion h3 { text-align: center; border-bottom: 1px solidrgb(42, 42, 42); padding-bottom: 10px; }
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 180px; }
        .alerta { color: #ffcccc; text-align: center; margin-bottom: 10px; background-color: rgba(255,0,0,0.2); padding: 10px; border-radius: 5px;}
        .exito { color: #ccffcc; text-align: center; margin-bottom: 10px; background-color: rgba(0,255,0,0.2); padding: 10px; border-radius: 5px;}
        .info-mensaje { background-color: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;}
        .button { background-color:rgb(42, 42, 42); color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;}
        .button:hover { background-color: #555; }
        .btn { display: inline-block; background-color: rgb(48, 48, 48); color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; text-align: center; margin-top: 20px;}
        .btn:hover { background-color: rgb(60, 60, 60); }
        .backup-history { max-height: 300px; overflow-y: auto; padding: 10px; background-color: rgba(0,0,0,0.2); border-radius: 5px;}
        .backup-history ul { list-style: none; padding: 0; margin: 0; }
        .backup-history li { display: flex; justify-content: space-between; align-items: center; padding: 8px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .backup-history li:last-child { border-bottom: none; }
        .btn-download { background-color: rgb(48, 48, 48); color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px;}
        .btn-download:hover { background-color: rgb(60, 60, 60); }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Respaldos de Base de Datos</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>
    
    <div class="formulario-contenedor">
        <div class="form-seccion">
            <h3>Respaldo de la Base de Datos</h3>
            <div class="info-mensaje">
                <p><h3>⚠️ IMPORTANTE: Usted se encuentra en el modulo de respaldos</h3><br>Si desea realizar un respaldo manual ahora, presione el botón.</p>
            </div>
            <?php if ($mensaje): ?>
                <p class="<?php echo (strpos($mensaje, '❌') !== false) ? 'alerta' : 'exito'; ?>"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <form method="POST" action="backup_db.php">
            <button type="submit" name="backup_db" class="button">Realizar Respaldo Ahora</button>
            <!-- Botón para volver al Menu Mantenimiento -->
             <a href="/ceia_swga/pages/menu_mantto.php" class="btn">Volver al Menú de Mantenimiento</a>
            </form>
        </div>

        <div class="form-seccion">
            <h3>Historial de Respaldos</h3>
            <div class="backup-history">
                <ul>
                    <?php
                    $backup_dir = __DIR__ . '/../PostgreSQL-DB/';
                    $backup_files = glob($backup_dir . 'ceia_db_backup_*.sql');
                    rsort($backup_files); // Ordenar archivos del más nuevo al más antiguo

                    if (count($backup_files) > 0) {
                        foreach ($backup_files as $file) {
                            $filename = basename($file);
                            echo '<li><span>' . htmlspecialchars($filename) . '</span>' .
                                 ' <a href="?download_file=' . urlencode($filename) . '" class="btn-download">Descargar</a></li>';
                        }
                    } else {
                        echo '<li>No hay respaldos disponibles.</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
