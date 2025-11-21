<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin', 'consulta'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// --- 1. Recepción y Validación del ID ---

$estudiante_id = $_GET['id'] ?? 0;
if (empty($estudiante_id)) {
    die("Error: No se proporcionó un ID de estudiante válido.");
}

// --- 2. Obtención de Datos ---

try {
    // Período Activo

    $periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $periodo_id_activo = $periodo_activo['id'] ?? 0;

    // Datos del Estudiante

    $stmt_est = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
    $stmt_est->execute([':id' => $estudiante_id]);
    $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);
    if (!$estudiante) {
        die("Error: Estudiante no encontrado.");
    }

    // Datos del Padre

    $padre = null;
    if ($estudiante['padre_id']) {
        $stmt_padre = $conn->prepare("SELECT * FROM padres WHERE padre_id = :id");
        $stmt_padre->execute([':id' => $estudiante['padre_id']]);
        $padre = $stmt_padre->fetch(PDO::FETCH_ASSOC);
    }

    // Datos de la Madre

    $madre = null;
    if ($estudiante['madre_id']) {
        $stmt_madre = $conn->prepare("SELECT * FROM madres WHERE madre_id = :id");
        $stmt_madre->execute([':id' => $estudiante['madre_id']]);
        $madre = $stmt_madre->fetch(PDO::FETCH_ASSOC);
    }

    // Ficha Médica

    $stmt_ficha = $conn->prepare("SELECT * FROM salud_estudiantil WHERE estudiante_id = :id");
    $stmt_ficha->execute([':id' => $estudiante_id]);
    $ficha_medica = $stmt_ficha->fetch(PDO::FETCH_ASSOC);

    // Grado Cursado

    $grado_cursado = 'N/A';
    if ($periodo_id_activo) {
        $stmt_grado = $conn->prepare("SELECT grado_cursado FROM estudiante_periodo WHERE estudiante_id = :eid AND periodo_id = :pid");
        $stmt_grado->execute([':eid' => $estudiante_id, ':pid' => $periodo_id_activo]);
        $grado = $stmt_grado->fetch(PDO::FETCH_ASSOC);
        if ($grado) {
            $grado_cursado = $grado['grado_cursado'];
        }
    }
} catch (Exception $e) {
    die("Error al consultar la base de datos: " . $e->getMessage());
}


// --- 3. Clase PDF Personalizada ---

class PlanillaPDF extends FPDF
{
    private $periodo_nombre;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $periodo_nombre = 'N/A')
    {
        parent::__construct($orientation, $unit, $size);
        $this->periodo_nombre = $periodo_nombre;
    }

    function Header()
    {
        $this->Image(__DIR__.'/../../public/img/logo_ceia.png', 10, 12, 25);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('PLANILLA DE INSCRIPCIÓN'), 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, utf8_decode('Período Escolar: ' . $this->periodo_nombre), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode("Centro Educativo Internacional Anzoátegui (CEIA)"), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode("Teléfono: +58 123 456 7890 - Email: info@ceia.com"), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(0, 8, utf8_decode($title), 1, 1, 'L', true);
        $this->Ln(2);
    }

    function DataRow($label, $value, $is_multiline = false)
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 6, utf8_decode($label . ':'), 0, 0);
        $this->SetFont('Arial', '', 9);
        if ($is_multiline) {
            $this->MultiCell(0, 6, utf8_decode($value ?: 'N/A'), 0, 1);
        } else {
            $this->Cell(0, 6, utf8_decode($value ?: 'N/A'), 0, 1);
        }
    }
    
    function CheckboxRow($label, $value)
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 6, utf8_decode($label . ':'), 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, $value ? 'Sí' : 'No', 0, 1);
    }
}

// --- 4. Generación del PDF ---

$pdf = new PlanillaPDF('P', 'mm', 'Letter', $periodo_activo['nombre_periodo']);
$pdf->AliasNbPages();
$pdf->AddPage();

// Datos del Estudiante

$pdf->SectionTitle('Datos del Estudiante');
$pdf->DataRow('Grado a Cursar', $grado_cursado);
$pdf->DataRow('Nombres', $estudiante['nombre_completo']);
$pdf->DataRow('Apellidos', $estudiante['apellido_completo']);
$pdf->DataRow('Fecha de Nacimiento', $estudiante['fecha_nacimiento']);
$pdf->DataRow('Lugar de Nacimiento', $estudiante['lugar_nacimiento']);
$pdf->DataRow('Nacionalidad', $estudiante['nacionalidad']);
$pdf->DataRow('Idiomas que habla', $estudiante['idioma']);
$pdf->DataRow('Dirección', $estudiante['direccion'], true);
$pdf->DataRow('Teléfono Casa', $estudiante['telefono_casa']);
$pdf->DataRow('Teléfono Móvil', $estudiante['telefono_movil']);
$pdf->DataRow('Teléfono Emergencia', $estudiante['telefono_emergencia']);
$pdf->DataRow('Fecha Inscripción', $estudiante['fecha_inscripcion']);
$pdf->DataRow('Recomendado por', $estudiante['recomendado_por']);
$pdf->DataRow('Edad', $estudiante['edad_estudiante']);
$pdf->DataRow('Hermanos en el CEIA', $estudiante['estudiante_hermanos'], true);
$pdf->DataRow('Colegios Anteriores', $estudiante['colegios_anteriores'], true);
$pdf->CheckboxRow('Es personal del Staff', $estudiante['staff']);
$pdf->Ln(5);

// Datos del Padre

if ($padre) {
    $pdf->SectionTitle('Datos del Padre');
    $pdf->DataRow('Nombres y Apellidos', $padre['padre_nombre'] . ' ' . $padre['padre_apellido']);
    $pdf->DataRow('Cédula/Pasaporte', $padre['padre_cedula_pasaporte']);
    $pdf->DataRow('Fecha de Nacimiento', $padre['padre_fecha_nacimiento']);
    $pdf->DataRow('Nacionalidad', $padre['padre_nacionalidad']);
    $pdf->DataRow('Profesión', $padre['padre_profesion']);
    $pdf->DataRow('Empresa', $padre['padre_empresa']);
    $pdf->DataRow('Teléfono Trabajo', $padre['padre_telefono_trabajo']);
    $pdf->DataRow('Celular', $padre['padre_celular']);
    $pdf->DataRow('Email', $padre['padre_email']);
    $pdf->Ln(5);
}

// Datos de la Madre

if ($madre) {
    $pdf->SectionTitle('Datos de la Madre');
    $pdf->DataRow('Nombres y Apellidos', $madre['madre_nombre'] . ' ' . $madre['madre_apellido']);
    $pdf->DataRow('Cédula/Pasaporte', $madre['madre_cedula_pasaporte']);
    $pdf->DataRow('Fecha de Nacimiento', $madre['madre_fecha_nacimiento']);
    $pdf->DataRow('Nacionalidad', $madre['madre_nacionalidad']);
    $pdf->DataRow('Profesión', $madre['madre_profesion']);
    $pdf->DataRow('Empresa', $madre['madre_empresa']);
    $pdf->DataRow('Teléfono Trabajo', $madre['madre_telefono_trabajo']);
    $pdf->DataRow('Celular', $madre['madre_celular']);
    $pdf->DataRow('Email', $madre['madre_email']);
    $pdf->Ln(5);
}

// Ficha Médica

if ($ficha_medica) {
    $pdf->SectionTitle('Ficha Médica');
    $pdf->DataRow('Completado por', $ficha_medica['completado_por']);
    $pdf->DataRow('Fecha de Actualización', $ficha_medica['fecha_salud']);
    $pdf->DataRow('Contacto de Emergencia', $ficha_medica['contacto_emergencia']);
    $pdf->DataRow('Relación', $ficha_medica['relacion_emergencia']);
    $pdf->DataRow('Teléfono 1', $ficha_medica['telefono1']);
    $pdf->DataRow('Teléfono 2', $ficha_medica['telefono2']);
    $pdf->DataRow('Observaciones', $ficha_medica['observaciones'], true);
    $pdf->CheckboxRow('Dislexia', $ficha_medica['dislexia']);
    $pdf->CheckboxRow('Déficit de Atención', $ficha_medica['atencion']);
    $pdf->CheckboxRow('Otros', $ficha_medica['otros']);
    $pdf->DataRow('Info Adicional (Alergias, etc.)', $ficha_medica['info_adicional'], true);
    $pdf->DataRow('Problemas Oído/Vista', $ficha_medica['problemas_oido_vista'], true);
    $pdf->DataRow('Fecha Examen Oído/Vista', $ficha_medica['fecha_examen']);
    $pdf->DataRow('Medicamentos Actuales', $ficha_medica['medicamentos_actuales'], true);
    $pdf->CheckboxRow('Autoriza Medicamentos', $ficha_medica['autorizo_medicamentos']);
    $pdf->CheckboxRow('Autoriza Atención de Emergencia', $ficha_medica['autorizo_emergencia']);
    $pdf->Ln(5);
}

// Bloque de Firmas

$pdf->SectionTitle('Aceptación y Firma');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode("Declaro que toda la información proporcionada en esta planilla es verdadera y correcta. Autorizo al Centro Educativo Internacional Anzoátegui (CEIA) a tomar las medidas necesarias en caso de emergencia médica."), 0, 'J');
$pdf->Ln(20);
$pdf->Cell(90, 6, '__________________________________', 0, 0, 'C');
$pdf->Cell(90, 6, '__________________________________', 0, 1, 'C');
$pdf->Cell(90, 6, 'Firma del Padre o Representante', 0, 0, 'C');
$pdf->Cell(90, 6, 'Firma del Director(a)', 0, 1, 'C');


// --- 5. Salida del PDF ---

$nombre_archivo = "Planilla_" . str_replace(' ', '_', $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo']) . ".pdf";
$pdf->Output('D', $nombre_archivo);

?>