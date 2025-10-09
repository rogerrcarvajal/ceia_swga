<?php
session_start();
require_once __DIR__ . '/../src/config.php';

// --- CONTROL DE ACCESO ---
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
if (!in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

// --- OBTENER DATOS PARA FORMULARIO ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_activo_id = $periodo_activo ? $periodo_activo['id'] : null;

// Obtener categorías de personal
$categorias = $conn->query("SELECT DISTINCT categoria FROM profesores ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Autorización de Salida de Staff</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(10px); border-radius: 8px; }
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; }
        .content img { width: 250px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full-width { grid-column: span 2; }
        .form-group label { font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px; border-radius: 5px; border: 1px solid #ccc; background-color: rgba(255,255,255,0.9); color: #333; }
        .btn-container { grid-column: span 2; text-align: center; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; cursor: pointer; border: none; margin: 5px; }
        .btn-primary { background-color: #007bff; }
        .btn-secondary { background-color: #6c757d; }
        .btn-pdf { background-color: #dc3545; }
        .alert { padding: 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Planilla de Autorización de Salida de Personal</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="container">
        <div id="alert-container"></div>
        <form id="form-salida-staff">
            <input type="hidden" name="periodo_id" value="<?= $periodo_activo_id ?>">
            <input type="hidden" name="registrado_por_usuario_id" value="<?= $_SESSION['usuario']['id'] ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="categoria">Categoría de Personal</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Seleccione una categoría...</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= htmlspecialchars($categoria) ?>"><?= htmlspecialchars($categoria) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="profesor_id">Nombre del Personal</label>
                    <select id="profesor_id" name="profesor_id" required>
                        <option value="">Seleccione una categoría primero...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="posicion">Posición / Cargo</label>
                    <input type="text" id="posicion" name="posicion" readonly style="background-color: #e9ecef;">
                </div>

                <div class="form-group">
                    <label for="cedula">Cédula</label>
                    <input type="text" id="cedula" name="cedula" readonly style="background-color: #e9ecef;">
                </div>

                <div class="form-group">
                    <label for="fecha_permiso">Fecha del Permiso</label>
                    <input type="date" id="fecha_permiso" name="fecha_permiso" required>
                </div>

                <div class="form-group">
                    <label for="hora_salida">Hora de Salida</label>
                    <input type="time" id="hora_salida" name="hora_salida" required>
                </div>

                <div class="form-group">
                    <label for="duracion_horas">Duración (Horas)</label>
                    <input type="number" id="duracion_horas" name="duracion_horas" step="0.5" min="0.5" required>
                </div>

                <div class="form-group full-width">
                    <label for="motivo">Motivo del Permiso</label>
                    <textarea id="motivo" name="motivo" rows="3"></textarea>
                </div>

                <div class="btn-container">
                    <button type="submit" id="btnGuardar" class="btn btn-primary">Guardar Autorización</button>
                    <button type="button" id="btnGenerarPDF" class="btn btn-pdf" disabled>📄 Generar PDF</button>
                    <a href="/ceia_swga/pages/gestion_autorizacion_staff.php" class="btn btn-info">Consultar Autorizaciones</a>
                    <a href="/ceia_swga/pages/menu_staff.php" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </form>
    </div>

    <script src="/ceia_swga/public/js/planilla_salida_staff.js" defer></script>

</body>
</html>
