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
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// --- 2. OBTENER LISTA DE ESTUDIANTES PARA EL PANEL IZQUIERDO ---
$estudiantes = $conn->query("SELECT id, nombre_completo, apellido_completo FROM estudiantes ORDER BY apellido_completo, nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Expedientes de Estudiantes</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css">
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Administrar Expedientes de Estudiantes</h1>
    </div>

    <div class="main-container">
        <div class="left-panel">
            <h3>Lista de Estudiantes</h3>
            <input type="text" id="filtro_estudiantes" placeholder="Buscar por apellido...">
            <ul id="lista_estudiantes">
                <?php foreach ($estudiantes as $e): ?>
                    <li data-id="<?= $e['id'] ?>"><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="right-panel">
            <div id="panel_informativo"><p>Seleccione un estudiante de la lista para ver su expediente.</p></div>
            
            <div id="panel_datos_estudiante" style="display:none;">
                <div id="mensaje_actualizacion" class="mensaje" style="display:none;"></div>
                <div class="form-grid">
                    <form id="form_estudiante">
                        <h3>Datos Personales</h3>
                        <input type="hidden" name="id" id="estudiante_id">
                        <input type="text" name="nombre_completo" id="nombre_completo" placeholder="Nombres completo" required>
                        <input type="text" name="apellido_completo" id="apellido_completo" placeholder="Apellidos completo" required>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                        <input type="text" name="lugar_nacimiento" id="lugar_nacimiento" placeholder="Lugar de nacimiento" required>
                        <input type="text" name="nacionalidad" id="nacionalidad" placeholder="Nacionalidad" required>
                        <input type="text" name="idioma" id="idioma" placeholder="Idiomas que habla" required>
                        <textarea name="direccion" id="direccion" placeholder="Dirección" required></textarea>
                        <input type="text" name="telefono_casa" id="telefono_casa" placeholder="Teléfono de casa">
                        <input type="text" name="telefono_movil" id="telefono_movil" placeholder="Teléfono celular">
                        <input type="text" name="telefono_emergencia" id="telefono_emergencia" placeholder="Teléfono de emergencia" required>
                        <input type="text" name="grado_ingreso" id="grado_ingreso" placeholder="Grado de ingreso" required>
                        <input type="date" name="fecha_inscripcion" id="fecha_inscripcion" required>
                        <input type="text" name="recomendado_por" id="recomendado_por" placeholder="Recomendado por">
                        <input type="number" name="edad_estudiante" id="edad_estudiante" placeholder="Edad" required>

                        <label><input type="checkbox" name="staff" id="staff"> Estudiante Staff</label><br><br>
                        <label><input type="checkbox" name="activo" id="activo"> Estudiante Activo</label><br><br>

                        <button type="submit">Actualizar Estudiante</button>
                    </form>

                    <div>
                        <form id="form_ficha_medica">
                            <h3>Ficha Médica</h3>
                            <div id="mensaje_actualizacion" style="color: lightgreen; margin-bottom: 15px;"></div>

                            <input type="hidden" name="estudiante_id" id="estudiante_id">
                            <input type="text" name="completado_por" id="completado_por" placeholder="Completado por" >
                            <input type="date" name="fecha_salud" id="fecha_salud" >
                            <input type="text" name="contacto_emergencia" id="contacto_emergencia" placeholder="Contacto de Emergencia" >
                            <input type="text" name="relacion_emergencia" id="relacion_emergencia" placeholder="Relación de Emergencia" >
                            <input type="text" name="telefono1" id="telefono1" placeholder="Teléfono 1" >
                            <input type="text" name="telefono2" id="telefono2" placeholder="Teléfono 2">
                            <textarea name="observaciones" id="observaciones" placeholder="Observaciones"></textarea>
                            <label><input type="checkbox" name="dislexia" id="dislexia"> Dislexia</label>
                            <label><input type="checkbox" name="atencion" id="atencion"> Déficit de Atención</label>
                            <label><input type="checkbox" name="otros" id="otros"> Otros</label>
                            <textarea name="info_adicional" id="info_adicional" placeholder="Información adicional"></textarea>
                            <textarea name="problemas_oido_vista" id="problemas_oido_vista" placeholder="Problemas de oído/vista"></textarea>
                            <input type="text" name="fecha_examen" id="fecha_examen" placeholder="Fecha último examen oído/vista">
                            <label><input type="checkbox" name="autorizo_medicamentos" id="autorizo_medicamentos"> Autorizo administración de medicamentos</label>
                            <textarea name="medicamentos_actuales" id="medicamentos_actuales" placeholder="Medicamentos actuales"></textarea>
                            <label><input type="checkbox" name="autorizo_emergencia" id="autorizo_emergencia"> Autorizo atención de emergencia</label><br><br>
                            <button type="submit">Actualizar Ficha Médica</button>
                        </form>

                        <div class="related-section">
                            <h3>Padres Vinculados</h3>
                            <ul id="lista_padres_vinculados"></ul>
                            <a href="/pages/administrar_planilla_representantes.php" class="btn-agregar" style="display: inline-block; margin-top: 20px; text-decoration: none; padding: 10px 15px; background-color:rgb(48, 48, 48); color: white; border-radius: 5px;">Administrar Padres</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/public/js/admin_estudiantes.js"></script>
</body>
</html>