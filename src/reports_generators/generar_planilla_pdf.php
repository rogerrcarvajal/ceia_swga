<?php
session_start();
// 1. Verificación de Seguridad: Si no hay sesión, no se puede generar el reporte.
if (!isset($_SESSION['usuario'])) {
    exit('Acceso denegado. Debe iniciar sesión.');
}

// 2. Incluir archivos necesarios.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// Obtener el período escolar activo
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nombre_del_periodo = $periodo_activo['nombre_periodo'] ?? 'No Definido';

// 3. Obtener el ID del estudiante desde la URL.
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: No se ha seleccionado ningún estudiante.');
}
$estudiante_id = $_GET['id'];

// --- BLOQUE DE OBTENCIÓN DE DATOS ---

// CONSULTA 1: Estudiante
$stmt_est = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$stmt_est->execute([':id' => $estudiante_id]);
$estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    die('Error: Estudiante no encontrado.');
}

// CONSULTA 2: Padre
$padre = [];
if (!empty($estudiante['padre_id'])) {
    $stmt_padre = $conn->prepare("SELECT * FROM padres WHERE padre_id = :id");
    $stmt_padre->execute([':id' => $estudiante['padre_id']]);
    $padre = $stmt_padre->fetch(PDO::FETCH_ASSOC);
}

// CONSULTA 3: Madre
$madre = [];
if (!empty($estudiante['madre_id'])) {
    $stmt_madre = $conn->prepare("SELECT * FROM madres WHERE madre_id = :id");
    $stmt_madre->execute([':id' => $estudiante['madre_id']]);
    $madre = $stmt_madre->fetch(PDO::FETCH_ASSOC);
}

// CONSULTA 4: Ficha Médica
$stmt_ficha = $conn->prepare("SELECT * FROM salud_estudiantil WHERE estudiante_id = :id");
$stmt_ficha->execute([':id' => $estudiante_id]);
$ficha_medica = $stmt_ficha->fetch(PDO::FETCH_ASSOC);


// --- CLASE PDF PERSONALIZADA ---
class PlanillaPDF extends FPDF
{
    private $nombre_periodo;

    function __construct($orientation, $unit, $size, $periodo_nombre) {
        parent::__construct($orientation, $unit, $size);
        $this->nombre_periodo = $periodo_nombre;
    }

    function Header() {
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Centro Educativo Internacional Anzoategui', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 100, 0); // Verde oscuro
        $this->Cell(0, 5, 'Periodo Escolar Activo: ' . $this->nombre_periodo, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0); // Restaurar a negro
        $this->Ln(10);
    }

    function Footer() {
        // Posición a 2 cm del final
        $this->SetY(-20);
        // Imagen de línea de colores
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/public/img/color_line.png', 10, $this->GetY(), 190);
        
        $this->SetY(-15); // Posición a 1.5 cm del final
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Av. José Antonio Anzoátegui, Km 98 - Anaco, Edo Anzoátegui 6003, Venezuela - +58 282 422 2683'), 0, 1, 'C');
        $this->Cell(0, 5, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 8, $title, 1, 1, 'L', true);
        $this->Ln(2);
    }

    function DataRow($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 7, utf8_decode($label), 'B', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 7, utf8_decode($value ?? 'N/A'), 'B', 1);
    }
}


// --- GENERACIÓN DEL DOCUMENTO PDF ---
$pdf = new PlanillaPDF('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'PLANILLA DE INSCRIPCION', 0, 1, 'C');
$pdf->Ln(5);

// ... (El resto del código para las secciones es idéntico)

// Sección 1: Datos del Estudiante
$pdf->SectionTitle('Datos del Estudiante');
$pdf->DataRow('Nombre Completo:', $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo']);
$pdf->DataRow('Fecha de Nacimiento:', $estudiante['fecha_nacimiento']);
$pdf->DataRow('Lugar de Nacimiento:', $estudiante['lugar_nacimiento']);
$pdf->DataRow('Nacionalidad:', $estudiante['nacionalidad']);
$pdf->DataRow('Idioma(s):', $estudiante['idioma']);
$pdf->DataRow('Direccion:', $estudiante['direccion']);
$pdf->DataRow('Telefono de Casa:', $estudiante['telefono_casa']);
$pdf->DataRow('Telefono de Emergencia:', $estudiante['telefono_emergencia']);
$pdf->DataRow('Grado de Ingreso:', $estudiante['grado_ingreso']);
$pdf->DataRow('Fecha de Inscripcion:', $estudiante['fecha_inscripcion']);
$pdf->DataRow('Recomendado por:', $estudiante['recomendado_por']);
$pdf->DataRow('Hermanos estudiando en CEIA:', $estudiante['estudiante_hermanos']);
$pdf->DataRow('Colegio(s) donde estudio antes:', $estudiante['colegios_anteriores']);
$pdf->DataRow('Edad:', $estudiante['edad_estudiante']);
$pdf->DataRow('Staff:', $estudiante['staff'] ? 'Si' : 'No');
$pdf->DataRow('Activo:', $estudiante['activo'] ? 'Si' : 'No');  
$pdf->Ln(8);

// Sección 2: Datos del Padre
$pdf->SectionTitle('Datos del Padre');
$pdf->DataRow('Nombre Completo:', ($padre['padre_nombre'] ?? '') . ' ' . ($padre['padre_apellido'] ?? ''));
$pdf->DataRow('Fecha de Nacimiento:', $padre['padre_fecha_nacimiento'] ?? 'N/A');
$pdf->DataRow('Cedula/Pasaporte:', $padre['padre_cedula_pasaporte'] ?? 'N/A');
$pdf->DataRow('Nacionalidad:', $padre['padre_nacionalidad'] ?? 'N/A');
$pdf->DataRow('Idiomas:', $padre['padre_idioma'] ?? 'N/A');
$pdf->DataRow('Profesion:', $padre['padre_profesion'] ?? 'N/A');
$pdf->DataRow('Empresa:', $padre['padre_empresa'] ?? 'N/A');
$pdf->DataRow('Telefono de Trabajo:', $padre['padre_telefono_trabajo'] ?? 'N/A');
$pdf->DataRow('Celular:', $padre['padre_celular'] ?? 'N/A');
$pdf->DataRow('Email:', $padre['padre_email'] ?? 'N/A');
$pdf->Ln(25);

// Sección 3: Datos de la Madre
$pdf->SectionTitle('Datos de la Madre');
$pdf->DataRow('Nombre Completo:', ($madre['madre_nombre'] ?? '') . ' ' . ($madre['madre_apellido'] ?? ''));
$pdf->DataRow('Fecha de Nacimiento:', $madre['madre_fecha_nacimiento'] ?? 'N/A');
$pdf->DataRow('Cedula/Pasaporte:', $madre['madre_cedula_pasaporte'] ?? 'N/A');
$pdf->DataRow('Nacionalidad:', $madre['madre_nacionalidad'] ?? 'N/A');
$pdf->DataRow('Idiomas:', $madre['madre_idioma'] ?? 'N/A');
$pdf->DataRow('Profesion:', $madre['madre_profesion'] ?? 'N/A');
$pdf->DataRow('Empresa:', $madre['madre_empresa'] ?? 'N/A');
$pdf->DataRow('Telefono de Trabajo:', $madre['madre_telefono_trabajo'] ?? 'N/A');
$pdf->DataRow('Celular:', $madre['madre_celular'] ?? 'N/A');
$pdf->DataRow('Email:', $madre['madre_email'] ?? 'N/A');
$pdf->Ln(8);

// Sección 4: Ficha Médica
$pdf->SectionTitle('Ficha Medica');
$pdf->DataRow('Completado por:', $ficha_medica['completado_por'] ?? 'N/A');
$pdf->DataRow('Fecha de Salud:', $ficha_medica['fecha_salud'] ?? 'N/A');
$pdf->DataRow('Contacto de Emergencia:', $ficha_medica['contacto_emergencia'] ?? 'N/A');
$pdf->DataRow('Relacion de Emergencia:', $ficha_medica['relacion_emergencia'] ?? 'N/A');
$pdf->DataRow('Telefono de Emergencia:', $ficha_medica['telefono1'] ?? 'N/A');
$pdf->DataRow('Telefono de Emergencia 2:', $ficha_medica['telefono2'] ?? 'N/A');
$pdf->DataRow('Observaciones:', $ficha_medica['observaciones'] ?? 'N/A');
$pdf->DataRow('Dislexia:', ($ficha_medica['dislexia'] ?? false) ? 'Si' : 'No');
$pdf->DataRow('Deficit de Atencion:', ($ficha_medica['atencion'] ?? false) ? 'Si' : 'No');
$pdf->DataRow('Informacion Adicional:', ($ficha_medica['otros'] ?? false) ? 'Si' : 'No');
$pdf->DataRow('Problemas de Oido/Vista:', $ficha_medica['problemas_oido_vista'] ?? 'N/A');
$pdf->DataRow('Fecha de Examen:', $ficha_medica['fecha_examen'] ?? 'N/A');
$pdf->DataRow('Autorizo Administracion de Medicamentos:', ($ficha_medica['autorizo_medicamentos'] ?? false) ? 'Si' : 'No');
$pdf->DataRow('Medicamentos Actuales:', $ficha_medica['medicamentos_actuales'] ?? 'N/A');
$pdf->DataRow('Autorizo Atencion de Emergencia:', ($ficha_medica['autorizo_emergencia'] ?? false) ? 'Si' : 'No');
$pdf->Ln(1);


// AÑADIR ESTE BLOQUE AL FINAL, ANTES DEL $pdf->Output()
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("Yo/Nosotros ____________________________ aceptamos las reglas y regulaciones del Centro Educativo Internacional Anzoategui 2014 pautadas por la Junta Directiva y por el Manual de Padres/Estudiantes 2020-2021."), 0, 'L');
$pdf->Ln(5);
$pdf->Cell(0, 5, utf8_decode('Firma del padre o tutor: ________________________________________________________'), 0, 1, 'L');


// 5. Enviar el PDF al navegador
$pdf->Output('I', 'Planilla_Inscripcion_'. $estudiante['apellido_completo'] .'.pdf');
?>