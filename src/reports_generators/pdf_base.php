<?php
require_once __DIR__ . '/../lib/fpdf.php';

class LatePassPDF extends FPDF
{
    private $nombre_periodo;

    function __construct($orientation, $unit, $size, $periodo_nombre) {
        parent::__construct($orientation, $unit, $size);
        $this->nombre_periodo = $periodo_nombre;
    }

    function Header() {
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/logo_ceia.png', 10, 8, 25);
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
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/color_line.png', 10, $this->GetY(), $this->GetPageWidth() - 20);
        
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
?>