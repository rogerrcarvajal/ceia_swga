<?php
// pages/planilla_salida.php

require_once __DIR__ . '/../src/config.php';
session_start();
// Asegurar que solo administradores puedan acceder
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: /ceia_swga/public/index.php');
    exit;
}

// Obtener el período escolar activo
$periodoActivoId = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetchColumn();

// Obtener estudiantes asignados al período activo
$stmt = $conn->prepare(
    "SELECT e.id, e.nombre_completo, e.apellido_completo
     FROM estudiantes e
     JOIN estudiante_periodo ep ON e.id = ep.estudiante_id
     WHERE ep.periodo_id = :periodo_id ORDER BY e.apellido_completo, e.nombre_completo"
);
$stmt->execute([':periodo_id' => $periodoActivoId]);
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aquí iría el código para manejar el POST del formulario, que llamaría a una API para guardar
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Autorización de Salida</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/app.css">
</head>
<body>
    <?php include __DIR__ . '/../src/templates/header.php'; ?>

    <main class="container">
        <h2>Generar Autorización de Salida de Estudiante</h2>

        <form id="form-salida" action="/ceia_swga/api/guardar_autorizacion_salida.php" method="POST">
            <fieldset>
                <legend>Información de la Salida</legend>

                <div class="campo">
                    <label for="estudiante_id">Estudiante:</label>
                    <select id="estudiante_id" name="estudiante_id" required>
                        <option value="" disabled selected>-- Seleccione un Estudiante --</option>
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <option value="<?= htmlspecialchars($estudiante['id']) ?>">
                                <?= htmlspecialchars($estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="fecha_salida">Fecha de Salida:</label>
                    <input type="date" id="fecha_salida" name="fecha_salida" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="campo">
                    <label for="hora_salida">Hora de Salida:</label>
                    <input type="time" id="hora_salida" name="hora_salida" value="<?= date('H:i') ?>" required>
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
                    <textarea id="motivo" name="motivo" rows="3"></textarea>
                </div>

            </fieldset>

            <div class="acciones">
                <button type="submit" class="boton">Guardar y Generar PDF</button>
                <a href="/ceia_swga/pages/consultar_planilla_salida.php" class="boton-secundario">Consultar Salidas</a>
            </div>
        </form>
    </main>

    <script>
        // Opcional: Lógica para autocompletar con datos de padre/madre
        document.getElementById('estudiante_id').addEventListener('change', async (e) => {
            const estudianteId = e.target.value;
            if (!estudianteId) return;

            // En un futuro, podrías hacer una llamada a una API para obtener los nombres de los padres
            // y mostrarlos como sugerencias o botones para autocompletar el campo "Retirado por".
        });
    </script>
</body>
</html>