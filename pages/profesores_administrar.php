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

// Obtener datos para los selectores
$periodos = $conn->query(query: "SELECT id, nombre_periodo, activo FROM periodos_escolares ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$posiciones = [
    "Director", "Bussiness Manager", "Administrative Assistant", "IT Manager", "Psychology",
    "DC-Grade 12 Music", "Daycare, Pk-3", "Pk-4, Kindergarten", "Grade 1", "Grade 2", "Grade 3",
    "Grade 4", "Grade 5", "Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10", "Grade 11",
    "Grade 12", "Spanish teacher - Grade 1-6", "Spanish teacher - Grade 7-12",
    "Social Studies - Grade 6-12", "IT Teacher - Grade Pk-3-12", "Science Teacher - Grade 6-12",
    "ESL - Elementary", "ESL - Secondary", "PE - Grade Pk3-12", "Language Arts - Grade 6-9",
    "Math teacher - Grade 6-9", "Math teacher - Grade 10-12", "Librarian"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Staff / Profesores</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url('/public//img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .container { background-color: rgba(0, 0, 0, 0.7); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 85%; box-shadow: 0 4px 8px rgba(0,0,0,0.5); }
        .content { text-align: center; margin-top: 30px;}
        .content img { width: 180px; }
        h2 { margin-bottom: 25px; text-shadow: 1px 1px 2px black; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;}
        .toolbar label { font-weight: bold; }
        .toolbar select { padding: 8px; border-radius: 5px; min-width: 200px; }
        .toolbar button { padding: 10px 15px; border-radius: 5px; border: none; background-color: #28a745; color: white; cursor: pointer; }
        #form-asignar { display: none; background-color: #333; padding: 20px; margin-top: 20px; border-radius: 8px; text-align: left; }
        table { width: 100%; border-collapse: collapse; background-color: rgba(255, 255, 255, 0.9); color: #333; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
        thead { background-color: #004a8f; color: white; }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Administración de Staff / Profesores</h1></br>
    </div>

    <div class="container">
        <div class="toolbar">
            <div>
                <label for="periodo-select">Seleccionar Período Escolar:</label>
                <select id="periodo-select">
                    <?php foreach ($periodos as $periodo): ?>
                        <option value="<?= $periodo['id'] ?>" <?= $periodo['activo'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($periodo['nombre_periodo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button id="btn-mostrar-form-asignar">Asignar Staff / Profesor a este Período</button>
        </div>
        
        <div id="form-asignar">
            <h3>Asignar Nuevo Staff / Profesor</h3>
            <form id="form-asignacion-profesor">
                <div class="form-grid">
                    <div>
                        <label for="profesor-a-asignar">Staff / Profesor:</label>
                        <select id="profesor-a-asignar" name="profesor_id" required></select>
                    </div>
                    <div>
                        <label for="posicion-asignar">Posición / Especialidad:</label>
                        <select id="posicion-asignar" name="posicion" required>
                            <option value="">Seleccione una posición...</option>
                            <?php foreach ($posiciones as $posicion): ?>
                                <option value="<?= htmlspecialchars($posicion) ?>"><?= htmlspecialchars($posicion) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                       <label for="homeroom-asignar">Homeroom Teacher:</label>
                        <select id="homeroom-asignar" name="homeroom_teacher"></select>
                    </div>
                </div>
                <br>
                <button type="submit">Guardar Asignación</button>
                <button type="button" id="btn-cancelar-asignacion">Cancelar</button>
            </form>
        </div>

        <div id="status-message"></div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Cédula</th>
                        <th>Posición</th>
                        <th>Homeroom Teacher</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody id="tabla-profesores-body"></tbody>
            </table>
        </div>
        <br>
        <a href="/pages/dashboard.php" class="boton-link">Volver al Inicio</a>
    </div>

    <script src="/public/js/admin_profesores.js"></script>
</body>
</html>