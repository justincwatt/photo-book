<?php
require('../fpdf/fpdf.php');
define('FPDF_FONTPATH','../fpdf/');

// ironically, in order to use international characters with FPDF, 
// the file must be saved with the ISO-8859-15 encoding, not unicode

// to test, on the command line run:
// php test.php > test.pdf

$pdf = new FPDF('P', 'in', 'Letter');

$pdf->AddFont('DejaVuSerif');
$pdf->SetFont('DejaVuSerif', '', 16);

$pdf->AddPage();

$text = "Iñtërnâtiônàlizætiøn";

$pdf->Cell(2, 2, $text);

$pdf->Output();

