<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

// Obtener todos los períodos escolares para el selector
$periodos = $conn->query("SELECT id, nombre_periodo, activo FROM periodos_escolares ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);

// Lista de posiciones para el formulario
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
    <title>Administrar Profesores por Período - CEIA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos (sin cambios respecto a la versión anterior) */
        body { margin: 0; padding: 0; background-image: url('img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .container { background-color: rgba(0, 0, 0, 0.8); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 85%; box-shadow: 0 4px 8px rgba(0,0,0,0.5); }
        .content { text-align: center; }
        .content img { width: 150px; }
        h2 { margin-bottom: 25px; text-shadow: 1px 1px 2px black; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;}
        .toolbar label { font-weight: bold; }
        .toolbar select { padding: 8px; border-radius: 5px; min-width: 200px; }
        .toolbar button { padding: 10px 15px; border-radius: 5px; border: none; background-color: #28a745; color: white; cursor: pointer; }
        #status-message { padding: 12px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; display: none; }
        .status-success { background-color: #28a745; color: white; }
        .status-error { background-color: #dc3545; color: white; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background-color: rgba(255, 255, 255, 0.9); color: #333; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
        thead { background-color: #004a8f; color: white; }
        tbody tr:nth-child(even) { background-color: #f2f2f2; }
        td[data-field] { cursor: pointer; }
        td[data-field]:hover { background-color: #d0e0ff; }
        #form-asignar { display: none; background-color: #333; padding: 20px; margin-top: 20px; border-radius: 8px; text-align: left; }
        #form-asignar h3 { margin-top: 0; }
        #form-asignar .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        #form-asignar select, #form-asignar input { width: 100%; box-sizing: border-box; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="content">
            <img src="img/logo_ceia.png" alt="Logo CEIA">
            <h2>Administración de Profesores por Período</h2>

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
                <button id="btn-mostrar-form-asignar">Asignar Profesor a este Período</button>
            </div>
            
            <div id="form-asignar">
                <h3>Asignar Nuevo Profesor</h3>
                <form id="form-asignacion-profesor">
                    <div class="form-grid">
                        <div>
                            <label for="profesor-a-asignar">Profesor:</label>
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
            <a href="dashboard.php" class="boton-link">Volver al Inicio</a>
        </div>
    </div>

    <script src="js/admin_profesores.js"></script>
</body>
</html>
