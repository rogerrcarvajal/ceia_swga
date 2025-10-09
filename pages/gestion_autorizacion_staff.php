<?php
session_start();
require_once __DIR__ . '/../src/config.php';

// --- CONTROL DE ACCESO ---
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

// --- OBTENER DATOS PARA FILTROS ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$categorias = $conn->query("SELECT DISTINCT categoria FROM profesores ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Consulta de Salidas de Staff</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
       .container { max-width: 95%; margin: 20px auto; padding: 20px; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(10px); border-radius: 8px; }
       .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; }
       .content img { width: 250px; }
       .filtros-container { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; padding: 15px; background-color: rgba(0,0,0,0.3); border-radius: 8px; align-items: center; }
       .filtros-container label { font-weight: bold; }
       .filtros-container input, .filtros-container select { padding: 10px; border-radius: 5px; border: 1px solid #ccc; background-color: rgba(255,255,255,0.9); color: #333; }
       .staff-table { width: 100%; border-collapse: collapse; }
       .staff-table th, .staff-table td { padding: 12px 15px; border: 1px solid rgba(255,255,255,0.2); text-align: left; }
       .staff-table thead th { background-color: rgba(0,0,0,0.3); }
       .staff-table tbody tr:hover { background-color: rgba(0,0,0,0.3); }
       .btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; cursor: pointer; border: none; background-color: #6c757d; margin-top: 20px; margin-right: 10px;}
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Consulta de Autorizaciones de Salida de Personal/Staff</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="filtros-container">
            <div>
                <label for="filtro_semana">Semana:</label>
                <input type="week" id="filtro_semana">
            </div>
            <div>
                <label for="filtro_categoria">Categoría:</label>
                <select id="filtro_categoria">
                    <option value="todas">Todas</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria) ?>"><?= htmlspecialchars($categoria) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filtro_staff">Personal:</label>
                <select id="filtro_staff">
                    <option value="todos">Todos</option>
                </select>
            </div>
        </div>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora Salida</th>
                    <th>Duración (H)</th>
                    <th>Personal</th>
                    <th>Categoría</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody id="tabla_resultados">
                <tr><td colspan="6" style="text-align:center;">Seleccione una semana para ver los registros.</td></tr>
            </tbody>
        </table>
        <br>
        <a href="/ceia_swga/pages/planilla_salida_staff.php" class="btn">Nueva Autorización</a>
    </div>

    <script src="/ceia_swga/public/js/consultar_salidas_staff.js" defer></script>
</body>
</html>