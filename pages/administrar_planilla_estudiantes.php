<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo el Usuario Master y Admin pueden gestionar el módulo Staff.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo el Usuario Master y Admin pueden gestionar el módulo Staff."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Administrar Expedientes de Estudiantes</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_admin.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Administrar Expedientes de Estudiantes</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
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
                
                <div class="form-grid-quad">
                    <form id="form_estudiante">
                    <h3>Datos del Estudiante</h3>
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
                        <input type="date" name="fecha_inscripcion" id="fecha_inscripcion" required>
                        <input type="text" name="recomendado_por" id="recomendado_por" placeholder="Recomendado por">
                        <input type="number" name="edad_estudiante" id="edad_estudiante" placeholder="Edad" required>
                        <textarea name="estudiante_hermanos" id="estudiante_hermanos" placeholder="Hermanos estudiando en el CEIA"></textarea>
                        <input type="text" name="colegios_anteriores" id="colegios_anteriores" placeholder="Colegio(s) donde estudió antes">
<<<<<<< HEAD
                                                <label><input type="checkbox" name="staff" id="staff"> Estudiante Staff</label><br><br>
=======
                        <label><input type="checkbox" name="staff" id="staff"> Estudiante Staff</label><br><br>
                        <label><input type="checkbox" name="activo" id="activo" checked> Inscribir como Activo en este período</label><br><br>
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad
                        
                        <button type="submit">Actualizar Estudiante</button>
                    </form>  
                
                    <form id="form_padre">
                    <h3>Datos del Padre</h3>
                        <input type="hidden" name="padre_id" id="padre_id">                
                        <input type="text" name="padre_nombre" id="padre_nombre" placeholder="Nombre del Padre" >
                        <input type="text" name="padre_apellido" id="padre_apellido" placeholder="Apellido del Padre" >
                        <input type="date" name="padre_fecha_nacimiento" id="padre_fecha_nacimiento" >
                        <input type="text" name="padre_cedula_pasaporte" id="padre_cedula_pasaporte" placeholder="Cédula o Pasaporte" >
                        <input type="text" name="padre_nacionalidad" id="padre_nacionalidad" placeholder="Nacionalidad" >
                        <input type="text" name="padre_idioma" id="padre_idioma" placeholder="Idiomas que habla" >
                        <input type="text" name="padre_profesion" id="padre_profesion" placeholder="Profesión" >                        <input type="text" name="padre_empresa" id="padre_empresa" placeholder="Empresa donde trabaja" >
                        <input type="text" name="padre_telefono_trabajo" id="padre_telefono_trabajo" placeholder="Teléfono trabajo" >
                        <input type="text" name="padre_celular" id="padre_celular" placeholder="Celular" >
                        <input type="email" name="padre_email" id="padre_email" placeholder="Correo electrónico" ><br><br>

                        <button type="submit">Actualizar Padre</button>

                    </form>
                
                    <form id="form_madre">    
                    <h3>Datos de la Madre</h3>
                        <input type="hidden" name="madre_id" id="madre_id">
                        <input type="text" name="madre_nombre" id="madre_nombre" placeholder="Nombre de la Madre" >
                        <input type="text" name="madre_apellido" id="madre_apellido" placeholder="Apellido de la Madre" >
                        <input type="date" name="madre_fecha_nacimiento" id="madre_fecha_nacimiento" >
                        <input type="text" name="madre_cedula_pasaporte" id="madre_cedula_pasaporte" placeholder="Cédula o Pasaporte" >
                        <input type="text" name="madre_nacionalidad" id="madre_nacionalidad" placeholder="Nacionalidad" >
                        <input type="text" name="madre_idioma" id="madre_idioma" placeholder="Idiomas que habla" >
                        <input type="text" name="madre_profesion" id="madre_profesion" placeholder="Profesión" >
                        <input type="text" name="madre_empresa" id="madre_empresa" placeholder="Empresa donde trabaja" >
                        <input type="text" name="madre_telefono_trabajo" id="madre_telefono_trabajo" placeholder="Teléfono trabajo" >
                        <input type="text" name="madre_celular" id="madre_celular" placeholder="Celular" >
                        <input type="email" name="madre_email" id="madre_email" placeholder="Correo electrónico" ><br><br>

                        <button type="submit">Actualizar Madre</button>
                    </form>

                    <form id="form_ficha_medica">
                    <h3>Ficha Médica</h3>
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
                </div>
            </div>   
            <!-- Botón para volver al Home -->
            <a href="/ceia_swga/pages/menu_estudiantes.php" class="btn">Volver</a> 
 
        </div>
    </div>

    <script src="/ceia_swga/public/js/admin_estudiantes.js"></script>
</body>
</html>