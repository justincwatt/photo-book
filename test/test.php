#!/usr/bin/php
<?php
require('../fpdf/fpdf.php');
define('FPDF_FONTPATH','../fpdf/');

// ironically, in order to use international characters with FPDF, 
// the file must be saved with the ISO-8859-15 encoding, not unicode

// to test, on the command line run:
// ./test.php > test.pdf

$pdf = new FPDF('P', 'in', 'Letter');

$pdf->AddFont('DejaVuSerif');
$pdf->SetFont('DejaVuSerif', '', 16);

$pdf->AddPage();

// with apologies to sam ruby (http://intertwingly.net/stories/2004/04/14/i18n.html)
$text = "Iñtërnâtiônàlizætiøn";

$pdf->Cell(2, 2, $text);

$pdf->Output();
