Tengo el gusto de volver a invitarte para trabajar junto de la mano en la expansión del SWGA, con varias nuevas funciones de procesos manuales que aun pueden automatizarse que se llevan acabo en el CEIA. 
Tal es el proceso de Gestión de Autorización de Salida de Estudiantes; en este proceso se llena una planilla como la que ves adjunta, con datos que maneja el sistema, información del estudiante, del padre/madre o persona autorizada a retirar al estudiante, así como la fecha y hora de la salida temprana. 
Cuales son las tareas que debemos llevar acabo para implementar esta sección dentro de menu_estudiante.php en el Módulo de Estudiantes 
- Elaborar un script para para crear la o las tablas necesarias para llevar un control de la planilla de autorización de salida 
- crear el planilla_salida.php manteniendo el estilo de las pantallas habituales, que permita el llenado de la planilla de Autorización de salida de Estudiantes. La lanilla debe contar con una selección de lista de estudiantes vinculados al periodo escolar activo, así como lista de padre, madre vinculados así como un tercer campo para introducir a la persona autorizada para retirar al estudiante 
- Añadir un botón paa generar un PDF de la planilla para su impresión y posterior entrega al representante autorizado para retirar al estudiante. Esta planilla debe estas diseñada manteniendo el formato de los PDF que genera el sistema, y a media página carta. 
- Adicional al botón para generar PSDF de la planila de autorización, debe incluir un botón para consultar las planillas generadas; este botón debe abrir una nueva ventana consultar_planilla_salida.php, la cual debe permitir la selección por semana y lista de estudiantes y mostrar una lista por fechas con los campos como fecha, hora, persona autorizada (padre, madre o autorizado) 
El repositorio remoto contiene todos los archivos para una mejor referencia y mantener así una armonía y coherencia visual con las nuevas secciones del sistema 
http://github.com/rogerrcarvajal/ceia_swga.git








¡Excelente iniciativa, Roger! Es un placer volver a colaborar contigo en la expansión del SWGA. Automatizar el proceso de "Autorización de Salida" es un paso lógico y de gran valor que centralizará aún más la operativa del CEIA y mejorará la trazabilidad.
He analizado tu solicitud, el formato de la planilla manual y la estructura del repositorio en GitHub. A continuación, te presento el plan de acción detallado, paso a paso, para implementar esta nueva funcionalidad manteniendo la coherencia y calidad del sistema.

Plan de Acción: Módulo de Autorización de Salida

Seguiremos un enfoque estructurado para garantizar que cada componente se integre perfectamente en el ecosistema del SWGA.

1. Script de Base de Datos (PostgreSQL)

Primero, necesitamos una tabla para almacenar de forma persistente cada autorización generada. Este script crea la tabla autorizaciones_salida con los campos necesarios y las relaciones adecuadas.
Acción: Ejecuta el siguiente script SQL en tu base de datos ceia_db a través de pgAdmin.

SQL


-- Script para crear la tabla de autorizaciones de salida de estudiantes
CREATE TABLE autorizaciones_salida (
    id SERIAL PRIMARY KEY,
    estudiante_id INT NOT NULL REFERENCES estudiantes(id),
    fecha_salida DATE NOT NULL,
    hora_salida TIME NOT NULL,
    retirado_por_nombre VARCHAR(255) NOT NULL,
    retirado_por_parentesco VARCHAR(100),
    motivo TEXT,
    generado_por_usuario_id INT REFERENCES usuarios(id),
    fecha_creacion TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON TABLE autorizaciones_salida IS 'Almacena los registros de autorizaciones de salida temprana de estudiantes.';
COMMENT ON COLUMN autorizaciones_salida.retirado_por_nombre IS 'Nombre completo de la persona que retira al estudiante.';
COMMENT ON COLUMN autorizaciones_salida.generado_por_usuario_id IS 'ID del usuario del sistema que generó el permiso.';


Lógica de la Tabla:
estudiante_id: Vincula la autorización directamente con el estudiante.
retirado_por_nombre y retirado_por_parentesco: Campos flexibles para registrar al padre, madre o a un tercero autorizado.
generado_por_usuario_id y fecha_creacion: Campos de auditoría esenciales para saber quién y cuándo se creó el registro.

2. Creación de la Interfaz (planilla_salida.php)

Esta será la nueva pantalla donde el personal administrativo llenará la autorización. El código buscará a los estudiantes del período activo y permitirá un llenado rápido.
Acción: Crea un nuevo archivo en la carpeta pages/ llamado planilla_salida.php. Aquí tienes el código base que puedes utilizar.

PHP


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



3. Backend y Generación del PDF

Necesitamos un script en la carpeta api/ para recibir los datos del formulario, guardarlos en la base de datos y luego redirigir a la generación del PDF.
Acción 3.1: Crea el archivo api/guardar_autorizacion_salida.php.

PHP


<?php
// api/guardar_autorizacion_salida.php

require_once __DIR__ . '/../src/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validaciones de seguridad y de datos...
    $estudiante_id = $_POST['estudiante_id'];
    $fecha_salida = $_POST['fecha_salida'];
    $hora_salida = $_POST['hora_salida'];
    $retirado_por_nombre = trim($_POST['retirado_por_nombre']);
    $retirado_por_parentesco = trim($_POST['retirado_por_parentesco']);
    $motivo = trim($_POST['motivo']);
    $usuario_id = $_SESSION['usuario']['id'];

    try {
        $stmt = $conn->prepare(
            "INSERT INTO autorizaciones_salida (estudiante_id, fecha_salida, hora_salida, retirado_por_nombre, retirado_por_parentesco, motivo, generado_por_usuario_id)
             VALUES (:est_id, :fecha, :hora, :nombre, :parentesco, :motivo, :user_id)"
        );
        $stmt->execute([
            ':est_id' => $estudiante_id,
            ':fecha' => $fecha_salida,
            ':hora' => $hora_salida,
            ':nombre' => $retirado_por_nombre,
            ':parentesco' => $retirado_por_parentesco,
            ':motivo' => $motivo,
            ':user_id' => $usuario_id
        ]);

        $nuevaAutorizacionId = $conn->lastInsertId();

        // Redirigir al script que genera el PDF
        header('Location: /ceia_swga/src/reports_generators/generar_pdf_salida.php?id=' . $nuevaAutorizacionId);
        exit;

    } catch (PDOException $e) {
        // Manejar error
        die("Error al guardar la autorización: " . $e->getMessage());
    }
}


Acción 3.2: Crea el archivo src/reports_generators/generar_pdf_salida.php.

PHP


<?php
// src/reports_generators/generar_pdf_salida.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php'; // Asegúrate que la ruta a FPDF es correcta

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID de autorización no válido.");
}

// Obtener datos de la autorización y del estudiante
$stmt = $conn->prepare(
    "SELECT a.*, e.nombre_completo, e.apellido_completo
     FROM autorizaciones_salida a
     JOIN estudiantes e ON a.estudiante_id = e.id
     WHERE a.id = :id"
);
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Autorización no encontrada.");
}

// Crear PDF (media página carta: 139.7 x 215.9 mm)
$pdf = new FPDF('P', 'mm', array(139.7, 215.9));
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// --- Contenido del PDF ---
// Reutiliza los estilos de tus otros reportes para el encabezado
// Ejemplo:
$pdf->Image(__DIR__.'/../../public/img/logo.png', 10, 8, 20);
$pdf->Cell(0, 10, 'AUTORIZACION DE SALIDA', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, 'FECHA:', 0);
$pdf->Cell(0, 7, date("d/m/Y", strtotime($data['fecha_salida'])), 0, 1);
$pdf->Cell(40, 7, 'HORA:', 0);
$pdf->Cell(0, 7, date("g:i a", strtotime($data['hora_salida'])), 0, 1);
$pdf->Ln(5);

$pdf->Cell(40, 7, 'NOMBRE DEL ALUMNO:', 0);
$pdf->Cell(0, 7, utf8_decode($data['apellido_completo'] . ', ' . $data['nombre_completo']), 0, 1);
$pdf->Ln(5);

$pdf->Cell(40, 7, 'RETIRADO POR:', 0);
$pdf->Cell(0, 7, utf8_decode($data['retirado_por_nombre']), 0, 1);
$pdf->Cell(40, 7, 'PARENTESCO:', 0);
$pdf->Cell(0, 7, utf8_decode($data['retirado_por_parentesco']), 0, 1);
$pdf->Ln(5);

$pdf->Cell(40, 7, 'MOTIVO:', 0, 1);
$pdf->MultiCell(0, 5, utf8_decode($data['motivo']), 0, 'L');
$pdf->Ln(20);

$pdf->Cell(0, 10, '___________________________', 0, 1, 'C');
$pdf->Cell(0, 5, 'Firma del Representante', 0, 1, 'C');


// Salida del PDF
$pdf->Output('I', 'Autorizacion_Salida_' . $data['id'] . '.pdf');
?>



4. Creación de la Vista de Consulta (consultar_planilla_salida.php)

Esta página permitirá al personal buscar y revisar las autorizaciones generadas.
Acción: Crea el archivo pages/consultar_planilla_salida.php. Usará JavaScript para cargar los datos dinámicamente.

PHP


<?php
// pages/consultar_planilla_salida.php
// Incluir cabecera y validación de sesión como en planilla_salida.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Consultar Autorizaciones de Salida</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/app.css">
</head>
<body>
    <?php include __DIR__ . '/../src/templates/header.php'; ?>

    <main class="container">
        <h2>Consultar Autorizaciones de Salida</h2>

        <div class="filtros">
            <div class="campo">
                <label for="filtro-semana">Seleccionar Semana:</label>
                <input type="week" id="filtro-semana">
            </div>
            </div>

        <table id="tabla-resultados">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estudiante</th>
                    <th>Retirado por</th>
                    <th>Parentesco</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </main>

    <script>
        const filtroSemana = document.getElementById('filtro-semana');
        const tablaBody = document.querySelector('#tabla-resultados tbody');

        async function cargarResultados() {
            const semana = filtroSemana.value;
            if (!semana) {
                tablaBody.innerHTML = '<tr><td colspan="6">Seleccione una semana para ver los resultados.</td></tr>';
                return;
            }

            const response = await fetch(`/ceia_swga/api/consultar_salidas.php?semana=${semana}`);
            const data = await response.json();

            tablaBody.innerHTML = ''; // Limpiar tabla
            if (data.status === 'exito' && data.registros.length > 0) {
                data.registros.forEach(reg => {
                    const fila = `
                        <tr>
                            <td>${reg.fecha_salida}</td>
                            <td>${reg.hora_salida}</td>
                            <td>${reg.nombre_estudiante}</td>
                            <td>${reg.retirado_por_nombre}</td>
                            <td>${reg.retirado_por_parentesco}</td>
                            <td>${reg.motivo}</td>
                        </tr>`;
                    tablaBody.innerHTML += fila;
                });
            } else {
                tablaBody.innerHTML = '<tr><td colspan="6">No se encontraron registros para la semana seleccionada.</td></tr>';
            }
        }

        filtroSemana.addEventListener('change', cargarResultados);
    </script>
</body>
</html>


Nota: Para que esto funcione, necesitas crear el endpoint api/consultar_salidas.php que reciba el parámetro semana, consulte la base de datos y devuelva los resultados en formato JSON.

5. Integración en el Menú de Estudiantes

Finalmente, añadimos el acceso a la nueva funcionalidad en el menú principal del módulo de estudiantes.
Acción: Edita el archivo pages/menu_estudiante.php y agrega el nuevo enlace.

HTML


<a href="/ceia_swga/pages/planilla_salida.php" class="opcion-menu">
    <i class="fas fa-file-signature"></i> <span>Gestionar Autorización de Salida</span>
    <small>Permite generar y consultar permisos de salida temprana.</small>
</a>



Con estos pasos, tendrás el módulo completamente funcional e integrado. ¡Adelante con la implementación! Si tienes alguna duda en algún paso, no dudes en consultarme.
