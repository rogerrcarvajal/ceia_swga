<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

//Declaracion de variables
$mensaje = "";

// Roles permitidos
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master', 'consulta'])) {
    $_SESSION['error_acceso'] = "Acceso deneg ado. Solo usuarios autorizados tienen acceso a éste módulo.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}


// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Manual de Usuario Interactivo</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_admin.css">
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="main-container">
        <div class="right-panel">
            <h1>Manual de Usuario Interactivo: Sistema Web de Gestión Académica de Inscripcion y Late-Pass (SWGA)</h1>

            <div class="module">
                <h2>Introducción</h2>
                <p>¡Bienvenido al Sistema Web de Gestión Académica (SWGA) del Centro Educativo Internacional Anzoátegui (CEIA)!</p>
                <p>Este manual interactivo te guiará a través de las funcionalidades clave del sistema, diseñado para optimizar y automatizar los procesos de inscripción y control de Late-Pass.</p>
            </div>

            <div class="module">
                <h2>1. Acceso al Sistema</h2>
                <p>Para comenzar, abre tu navegador web e ingresa a la dirección IP del servidor del SWGA. Verás la pantalla de inicio de sesión.</p>
                <ul>
                    <li><strong>Usuario:</strong> Ingresa tu nombre de usuario asignado.</li>
                    <li><strong>Contraseña:</strong> Ingresa tu contraseña.</li>
                    <li><strong>Botón "Ingresar":</strong> Haz clic para acceder al sistema.</li>
                </ul>
            </div>

            <div class="module">
                <h2>2. Gestión de Estudiantes</h2>
                <p>Una vez dentro, el menú principal te dará acceso a las diferentes secciones. Para todo lo relacionado con los estudiantes, dirígete al módulo de "Estudiantes".</p>

                <h3>2.1. Inscripción de un Nuevo Estudiante</h3>
                <ol>
                    <li>Selecciona "Planilla de Inscripción".</li>
                    <li>Completa todos los campos con la información del estudiante.</li>
                    <li>En la sección de "Datos del Padre" y "Datos de la Madre", ingresa primero el número de cédula. Si el representante ya existe en el sistema, podrás vincularlo para evitar duplicar información.</li>
                    <li>Completa la Ficha Médica.</li>
                    <li>Haz clic en "Guardar Inscripción".</li>
                </ol>

                <h3>2.2. Administrar y Editar Información de Estudiantes</h3>
                <ol>
                    <li>Selecciona "Gestionar Planilla de Inscripción".</li>
                    <li>Busca y selecciona al estudiante que deseas modificar.</li>
                    <li>Realiza los cambios necesarios en cualquiera de las secciones (Datos del Estudiante, Padre, Madre o Ficha Médica).</li>
                    <li>Haz clic en el botón "Actualizar" correspondiente a la sección que modificaste.</li>
                </ol>

                <h3>2.3. Asignar Estudiantes a un Período Escolar</h3>
                <ol>
                    <li>Selecciona "Gestionar Estudiantes".</li>
                    <li>Elige el período escolar activo.</li>
                    <li>Selecciona al estudiante de la lista de "No asignados" y asígnale el grado correspondiente.</li>
                </ol>

                <h3>2.4. Autorización de Salida de Estudiantes</h3>
                <p>Este sub-módulo permite registrar y formalizar la salida de un estudiante durante el horario escolar.</p>
                <h4>Generar una Autorización:</h4>
                <ol>
                    <li>En el menú de Estudiantes, selecciona "Planilla de Salida".</li>
                    <li>Busca y selecciona al estudiante que se retira.</li>
                    <li>Indica si lo retira su Padre, Madre u otra persona autorizada.</li>
                    <li>Completa la fecha, hora y motivo de la salida.</li>
                    <li>Haz clic en "Guardar Autorización". Una vez guardado, podrás generar el comprobante en PDF.</li>
                </ol>
                <h4>Consultar Autorizaciones:</h4>
                <ol>
                    <li>En el menú de Estudiantes, selecciona "Gestionar Planilla de Salida".</li>
                    <li>Utiliza los filtros por semana o estudiante para encontrar registros anteriores.</li>
                </ol>
            </div>

            <div class="module">
                <h2>3. Gestión de Personal (Staff)</h2>
                <p>Este módulo te permite registrar y administrar al personal del CEIA.</p>
                <ol>
                    <li>Ve a la sección "Staff".</li>
                    <li>Para un nuevo ingreso, completa el formulario y haz clic en "Agregar Staff".</li>
                    <li>Para asignar o editar un miembro del personal, búscalo en la lista y haz clic en "Gestionar". Podrás asignarle una posición y vincularlo al período escolar activo.</li>
                </ol>

                <h3>3.1. Autorización de Salida de Personal</h3>
                <p>Permite registrar y autorizar las salidas temporales del personal durante la jornada laboral.</p>
                <h4>Generar una Autorización:</h4>
                <ol>
                    <li>En el menú de Staff, selecciona "Planilla de Salida de Staff".</li>
                    <li>Elige la categoría (Docente, Administrativo, etc.) y selecciona al empleado.</li>
                    <li>Completa los detalles del permiso: fecha, hora de salida, duración y motivo.</li>
                    <li>Haz clic en "Guardar Autorización" para registrar el permiso y habilitar la generación del PDF.</li>
                </ol>
                <h4>Consultar Autorizaciones:</h4>
                <ol>
                    <li>En el menú de Staff, selecciona "Gestionar Salidas de Staff".</li>
                    <li>Filtra por semana, categoría o empleado para ver el historial de permisos.</li>
                </ol>
            </div>

            <div class="module">
                <h2>4. Gestión de Late-Pass</h2>
                <p>Este módulo centraliza todo lo relacionado con el control de puntualidad.</p>

                <h3>4.1. Generar Códigos QR</h3>
                <ol>
                    <li>Selecciona "Generar Códigos QR".</li>
                    <li>Elige la categoría (Estudiantes, Staff, Vehículos, etc.).</li>
                    <li>Selecciona el nombre de la persona o el vehículo.</li>
                    <li>Haz clic en "Generar PDF". El sistema creará un código QR único.</li>
                </ol>

                <h3>4.2. Control de Acceso</h3>
                <ol>
                    <li>Selecciona "Control de Acceso".</li>
                    <li>Utiliza un lector de códigos QR para escanear el código del estudiante, miembro del personal o vehículo. El sistema registrará la entrada automáticamente.</li>
                </ol>

                <h3>4.3. Consultar Registros de Late-Pass</h3>
                <ol>
                    <li>Selecciona "Gestión y consulta de Late-Pass".</li>
                    <li>Filtra por semana y grado para ver los registros de llegadas tarde de los estudiantes.</li>
                </ol>
            </div>

            <div class="module">
                <h2>5. Generación de Reportes</h2>
                <p>El SWGA te permite generar documentos oficiales de forma rápida y sencilla.</p>
                <ul>
                    <li><strong>Planilla de Inscripción:</strong> Selecciona a un estudiante para generar su planilla en formato PDF.</li>
                    <li><strong>Roster Actualizado:</strong> Genera el listado oficial del personal y los estudiantes del período activo en formato PDF.</li>
                </ul>
            </div>

            <div class="module">
                <h2>6. Mantenimiento del Sistema</h2>
                <p>Este módulo es para tareas administrativas avanzadas.</p>
                <ul>
                    <li><strong>Gestión de Períodos Escolares:</strong> Crea, activa o desactiva los años escolares.</li>
                    <li><strong>Gestión de Usuarios del Sistema:</strong> Crea y administra las cuentas de usuario y sus permisos.</li>
                </ul>
            </div>

            <p>¡Esperamos que este manual te sea de gran ayuda para aprovechar al máximo todas las ventajas que el SWGA ofrece al CEIA!</p>
        </div>
    </div>
</body>
</html>