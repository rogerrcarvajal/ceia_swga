<?php
session_start();
// --- Bloque de seguridad ---
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: /index.php"); exit();
}
require_once __DIR__ . '/../src/config.php';

// Obtener todos los períodos escolares para el selector
$periodos = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares ORDER BY nombre_periodo DESC")->fetchAll(PDO::FETCH_ASSOC);

// Lista de grados disponibles para el formulario
$grados_disponibles = [
    'Daycare', 'Preschool', 'Prekinder 3', 'Prekinder 4', 'Kindergarten', 
    'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 
    'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Estudiantes a Período Escolar</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css"> </head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content"><h1>Asignar Estudiantes a Período</h1></div>

    <div class="main-container">
        <div class="left-panel">
            <h3>Seleccionar Período</h3>
            <select id="periodo_selector">
                <option value="">-- Elija un período --</option>
                <?php foreach ($periodos as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_periodo']) ?></option>
                <?php endforeach; ?>
            </select>
            <hr>
            <h3>Estudiantes Asignados</h3>
            <ul id="lista_estudiantes_asignados"></ul>
        </div>

        <div class="right-panel">
            <div id="panel_informativo"><p>Seleccione un período para ver y asignar estudiantes.</p></div>
            <div id="panel_asignacion" style="display:none;">
                <div id="mensaje_asignacion" class="mensaje" style="display:none;"></div>
                <h3>Asignar Estudiante a este Período</h3>
                <form id="form_asignar_estudiante">
                    <input type="hidden" id="periodo_id_hidden" name="periodo_id">
                    
                    <label for="estudiante_id">Estudiante (No Asignado):</label>
                    <select name="estudiante_id" id="estudiante_id" required></select>

                    <label for="grado_cursado">Grado a cursar:</label>
                    <select name="grado_cursado" id="grado_cursado" required>
                        <?php foreach ($grados_disponibles as $grado): ?>
                            <option value="<?= $grado ?>"><?= htmlspecialchars($grado) ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit">Asignar Estudiante</button>
                </form>
            </div>
        </div>
    </div>
    <script src="/public/js/admin_asignar_estudiante.js"></script>
</body>
</html>