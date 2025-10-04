<?php
require_once __DIR__ . '/../src/config.php';
session_start();

// Roles permitidos
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    $_SESSION['error_acceso'] = "Acceso denegado.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

// Obtener el período escolar activo
$periodoActivo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodoActivoId = $periodoActivo ? $periodoActivo['id'] : null;

$estudiantes = [];
if ($periodoActivoId) {
    // Obtener estudiantes asignados al período activo
    $stmt = $conn->prepare(
        "SELECT e.id, e.nombre_completo, e.apellido_completo
         FROM estudiantes e
         JOIN estudiante_periodo ep ON e.id = ep.estudiante_id
         WHERE ep.periodo_id = :periodo_id ORDER BY e.apellido_completo, e.nombre_completo"
    );
    $stmt->execute([':periodo_id' => $periodoActivoId]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Gestión de Autorización de Salida de Estudiantes</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
       .container { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 800px; }
       .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; }
       .content img { width: 250px; }
       h1, h2, h3 { text-align: center; }
       fieldset { border: 1px solid rgba(255,255,255,0.3); border-radius: 5px; padding: 20px; margin-bottom: 20px; }
       legend { color: #a2ff96; font-weight: bold; padding: 0 10px; }
       .campo { margin-bottom: 15px; }
       .campo label { display: block; margin-bottom: 5px; font-weight: bold; }
       .campo input[type="text"],
       .campo input[type="date"],
       .campo input[type="time"],
       .campo select,
       .campo textarea { width: 100%; padding: 10px; background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); border-radius: 5px; color: white; box-sizing: border-box;}
       .campo input::placeholder, .campo textarea::placeholder { color: rgba(255,255,255,0.7); }
       .acciones { text-align: center; }
       .boton, .boton-secundario { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; cursor: pointer; border: none; }
       .boton { background-color: #6c757d; }
       .boton-secundario { background-color: #6c757d; }
       .alerta { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
       .alerta.error { background-color: #dc3545; color: white; }
       .alerta.success { background-color: #28a745; color: white; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Autorización de Salida de Estudiantes</h1>
        <?php if ($periodoActivo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodoActivo['nombre_periodo']) ?></h3>
        <?php else: ?>
            <h3 style="color: #ffc107;">No hay período escolar activo.</h3>
        <?php endif; ?>
    </div>

    <main class="container">
        <h2>Generar Autorización de Salida</h2>

        <?php if (!$periodoActivo): ?>
            <p class="alerta error">No se pueden generar autorizaciones porque no hay un período escolar activo.</p>
        <?php elseif (empty($estudiantes)): ?>
            <p class="alerta">No hay estudiantes asignados al período escolar activo.</p>
        <?php else: ?>
            <form id="form-salida">
                <fieldset>
                    <legend>Información de la Salida</legend>

                    <div class="campo">
                        <label for="estudiante_search">Estudiante:</label>
                        <input type="text" id="estudiante_search" list="estudiantes_datalist" placeholder="Escriba para buscar un estudiante..." required>
                        <datalist id="estudiantes_datalist">
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <option data-value="<?= htmlspecialchars($estudiante['id']) ?>" value="<?= htmlspecialchars($estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo']) ?>">
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                        <input type="hidden" id="estudiante_id" name="estudiante_id">
                    </div>

                    <div class="campo">
                        <label for="fecha_salida">Fecha de Salida:</label>
                        <input type="date" id="fecha_salida" name="fecha_salida" required>
                    </div>

                    <div class="campo">
                        <label for="hora_salida">Hora de Salida:</label>
                        <input type="time" id="hora_salida" name="hora_salida" required>
                    </div>

                    <div class="campo">
                        <label for="retirado_por_nombre">Retirado por:</label>
                        <input type="text" id="retirado_por_nombre" name="retirado_por_nombre" placeholder="Nombre completo de quien retira" required>
                    </div>

                    <div class="campo">
                        <label for="retirado_por_parentesco">Parentesco:</label>
                        <input type="text" id="retirado_por_parentesco" name="retirado_por_parentesco" placeholder="Ej: Madre, Padre, Tío, etc.">
                    </div>

                    <div class="campo">
                        <label for="motivo">Motivo de la Salida:</label>
                        <textarea id="motivo" name="motivo" rows="3" placeholder="Especifique el motivo de la salida..."></textarea>
                    </div>

                </fieldset>

                <div class="acciones">
                    <button type="submit" class="boton">Guardar y Generar PDF</button>
                    <a href="/ceia_swga/pages/gestion_planilla_salida.php" class="boton-secundario">Gestionar Salidas</a>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <script src="/ceia_swga/public/js/planilla_salida.js" defer></script>
</body>
</html>