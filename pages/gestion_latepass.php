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

if ($_SESSION['usuario']['rol'] == 'admin' and $_SESSION['usuario']['rol'] == 'consulta') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
        // Aquí puedes redirigir o cargar la ventana modal según tu lógica
    }
}

// --- Obtener datos para los filtros ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Obtener la lista de grados que tienen estudiantes asignados en este período
$sql_grados = "SELECT DISTINCT grado_cursado FROM estudiante_periodo WHERE periodo_id = :pid ORDER BY grado_cursado";
$stmt_grados = $conn->prepare($sql_grados);
$stmt_grados->execute([':pid' => $periodo_activo['id']]);
$grados_con_estudiantes = $stmt_grados->fetchAll(PDO::FETCH_COLUMN, 0);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión y consulta de Late-Pass</title>
    <link rel="stylesheet" href="/public/css/estilo_roster.css"> <style>
        .filtros-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .filtros-container select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>Gestión y consulta de Late-Pass</h1>
        <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
    </div>

    <div class="container">
        <div class="filtros-container">
            <div>
                <label for="filtro_semana">Seleccionar Semana:</label>
                <input type="week" id="filtro_semana">
            </div>
            <div>
                <label for="filtro_grado">Seleccionar Grado:</label>
                <select id="filtro_grado">
                    <option value="todos">Todos los Grados</option>
                    <?php foreach ($grados_con_estudiantes as $grado): ?>
                        <option value="<?= htmlspecialchars($grado) ?>"><?= htmlspecialchars($grado) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Grado</th>
                    <th>Fecha de Llegada</th>
                    <th>Hora de Llegada</th>
                    <th style="text-align:center">Stikes Semanales</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody id="tabla_resultados_latepass">
                <tr><td colspan="5" style="text-align:center;">Seleccione una semana y un grado para ver los registros.</td></tr>
            </tbody>
        </table>
        <br><br>
        <!-- Botón para salir -->
        <a href="/pages/menu_latepass.php" class="btn">Salir</a>
    </div>
    <script src="/public/js/gestion_latepass.js"></script>
</body>
</html>