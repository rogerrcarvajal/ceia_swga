<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// Roles permitidos
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados tienen acceso a éste módulo.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

// Obtener el período escolar activo
$periodoActivo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodoActivoId = $periodoActivo ? $periodoActivo['id'] : null;
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
       .boton { background-color: rgb(48, 48, 48); }
       .boton-secundario { background-color: rgb(48, 48, 48); }
       .alerta { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
       .alerta.error { background-color: #dc3545; color: white; }
       .alerta.success { background-color: #28a745; color: white; }
       .alert { padding: 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
       .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
       .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
       .campo-inline { display: flex; align-items: center; gap: 20px; }
       .campo-inline .campo { flex: 1; }
       .radio-group { display: flex; gap: 20px; align-items: center; }
       .radio-group label { display: flex; align-items: center; gap: 5px; }
       .autorizado-info { padding: 10px; border: 1px dashed rgba(255,255,255,0.5); border-radius: 5px; margin-top: 10px; }
       .campo select option { color: black; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Autorización de Salida de Estudiantes</h1>
        <?php if ($periodoActivo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodoActivo['nombre_periodo']) ?></h3>
            <input type="hidden" id="periodo_activo_id" value="<?= htmlspecialchars($periodoActivoId) ?>">
        <?php else: ?>
            <h3 style="color: #ffc107;">No hay período escolar activo.</h3>
        <?php endif; ?>
    </div>

    <main class="container">
        <div id="alert-container"></div>
        <h2>Generar Autorización de Salida</h2>

        <?php if (!$periodoActivo): ?>
            <p class="alerta error">No se pueden generar autorizaciones porque no hay un período escolar activo.</p>
        <?php else: ?>
            <form id="form-salida">
                <fieldset>
                    <legend>Información de la Salida</legend>

                    <div class="campo">
                        <label for="estudiante_id">Estudiante:</label>
                        <select id="estudiante_id" name="estudiante_id" required >
                            <option value="">-- Seleccione un estudiante --</option>
                        </select>
                    </div>

                    <div class="campo-inline">
                        <div class="campo">
                            <label for="fecha_salida">Fecha de Salida:</label>
                            <input type="date" id="fecha_salida" name="fecha_salida" required >
                        </div>
                        <div class="campo">
                            <label for="hora_salida">Hora de Salida:</label>
                            <input type="time" id="hora_salida" name="hora_salida" required >
                        </div>
                    </div>

                    <div class="campo">
                        <label>Autorizado para Retirar:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="autorizado_por" value="padre" id="radio_padre" disabled >Padre</label>
                            <label><input type="radio" name="autorizado_por" value="madre" id="radio_madre" disabled >Madre</label>
                            <label><input type="radio" name="autorizado_por" value="otro" id="radio_otro" disabled >Otro</label>
                        </div>
                    </div>

                    <!-- Contenedor para la información del Padre -->
                    <div id="padre_info" class="autorizado-info" style="display:none;">
                        <p><strong>Padre:</strong> <span id="padre_nombre"></span></p>
                        <p><strong>Cédula:</strong> <span id="padre_cedula_pasaporte"></span></p>
                        <input type="hidden" id="padre_id" name="padre_id">
                    </div>

                    <!-- Contenedor para la información de la Madre -->
                    <div id="madre_info" class="autorizado-info" style="display:none;">
                        <p><strong>Madre:</strong> <span id="madre_nombre"></span></p>
                        <p><strong>Cédula:</strong> <span id="madre_cedula_pasaporte"></span></p>
                        <input type="hidden" id="madre_id" name="madre_id">
                    </div>

                    <!-- Contenedor para Otro Autorizado -->
                    <div id="otro_autorizado_info" class="autorizado-info" style="display:none;">
                        <div class="campo">
                            <label for="retirado_por_nombre">Retirado por:</label>
                            <input type="text" id="retirado_por_nombre" name="retirado_por_nombre" placeholder="Nombre completo de quien retira" >
                        </div>
                        <div class="campo">
                            <label for="retirado_por_parentesco">Parentesco:</label>
                            <input type="text" id="retirado_por_parentesco" name="retirado_por_parentesco" placeholder="Ej: Tío, Abuela, etc." >
                        </div>
                    </div>

                    <div class="campo">
                        <br>
                        <label for="motivo">Motivo de la Salida:</label>
                        <textarea id="motivo" name="motivo" rows="3" placeholder="Especifique el motivo de la salida..." ></textarea>
                    </div>

                </fieldset>

                <div class="acciones">
                    <button type="submit" id="btn-guardar" class="boton">Guardar Autorización</button>
                    <button type="button" id="btn-generar-pdf" class="boton" disabled>Generar PDF</button>
                    <a href="/ceia_swga/pages/gestion_planilla_salida.php" class="boton-secundario">Gestionar Salidas</a>
                </div>
                <input type="hidden" id="salida_id_guardada">
            </form>
        <?php endif; ?>
    </main>

    <script src="/ceia_swga/public/js/gestion_salidas.js"></script>
</body>
</html>
