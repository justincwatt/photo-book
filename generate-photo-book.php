<?php
require('./fpdf/fpdf.php');
ini_set("memory_limit","512M");
define('FPDF_FONTPATH','./fpdf/');

/*
// sample usage:

require('generate-photo-book.php');
$p = new PhotoBook();

// insert cover as first page
$page = $p->newCover();
$page->addImage('front.jpg');

// insert back cover as second page
$page = $p->newCover();
$page->addImage('back.jpg');

// add two vertical images side by side (with aspect ratio of 3:4)
$page = $p->newPage();
$page->addImage('IMG_1001.JPG');
$page->addImage('IMG_1001.JPG');
$page->addCaption("Caption line 1");
$page->addCaption("Caption line 2");

// add one horizontal image (with an aspect ratio of ~3:2)
$page = $p->newPage();
$page->addImage('IMG_8915.JPG');
$page->addCaption("Caption line 1");
$page->addCaption("Caption line 2");

$p->render();
*/

class Page
{
  private $images   = array();
  private $captions = array();

  function addImage($image)
  {
    $this->images[] = $image;
  }

  function images()
  {
    return $this->images;
  }

  function addCaption($caption)
  {
    $this->captions[] = $caption;
  }

  function captions()
  {
    return $this->captions;
  }
}

class PhotoBook
{
  private $covers;
  private $pages;

  private $trim    = 3;
  private $width   = 178;
  private $height  = 127;
  private $margin  = 4;
  private $spacing = 2;

  function &newCover()
  {
    $this->covers[] = new Page();
    return $this->covers[count($this->covers)-1];
  }
  

  function &newPage()
  {
    $this->pages[] = new Page();
    return $this->pages[count($this->pages)-1];
  }

  function render() {
    $image_dir = 'images/';

    $pdf = new FPDF("L", 'mm', array($this->height + $this->trim * 2, $this->width + $this->trim * 2));
    $pdf->SetAutoPageBreak(false);
    $pdf->SetTopMargin($this->margin + $this->trim);
    $pdf->SetLeftMargin($this->margin + $this->trim);
    $pdf->SetRightMargin($this->margin + $this->trim);

    $pdf->AddFont('DejaVuSerif');
    $pdf->SetFont('DejaVuSerif', '', 10);
    
    foreach ($this->covers as $cover) {
      $images   = $cover->images();
      $pdf->AddPage();
      $pdf->Image($image_dir . $images[0], 0, 0, $this->width + $this->trim * 2);
    }


    foreach ($this->pages as $page) {
      $images   = $page->images();
      $captions = $page->captions();

      $pdf->AddPage();

      switch (count($images)) {
        case 1:
          $pdf->Image($image_dir . $images[0], $this->margin + $this->trim, 
                                               $this->margin + $this->trim, 
                                               $this->width - $this->margin * 2);
          break;

        case 2:
          $pdf->Image($image_dir . $images[0], $this->margin + $this->trim, 
                                               $this->margin + $this->trim, 
                                               ($this->width - ($this->margin * 2) - $this->spacing) / 2);
          $pdf->Image($image_dir . $images[1], $this->margin + $this->trim + ($this->width - ($this->margin * 2) - $this->spacing) / 2 + $this->spacing,
                                               $this->margin + $this->trim,
                                               ($this->width - ($this->margin * 2) - $this->spacing) / 2);
          break;
      }

      if ($captions[0]) {
        $pdf->SetX($this->margin + $this->trim);
        $pdf->SetY(117 + $this->trim); // set manually
        $pdf->Cell($this->width - ($this->margin * 2), 5, $captions[0], 0, 0, 'C');
      }
      
      if ($captions[1]) {
        $pdf->Ln(4);
        $pdf->Cell($this->width - ($this->margin * 2), 5, $captions[1], 0, 0, 'C');
      }
    }

    $pdf->Output();
  }
}

