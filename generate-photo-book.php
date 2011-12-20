<?php
require('./fpdf/fpdf.php');
ini_set("memory_limit","512M");
define('FPDF_FONTPATH','./fpdf/');

/*
// sample usage:

require('generate-photo-book.php');
$p = new PhotoBook();

// optionally show lines around content areas for "debugging"
// $p->setShowLines(true);

// insert cover as first page (full-bleed image: 2175x1575px)
$page = $p->newCover();
$page->addImage('front.jpg');

// insert back cover as second page (full-bleed image: 2175x1575px)
$page = $p->newCover();
$page->addImage('back.jpg');

// add two vertical images side by side (with aspect ratio of ~3:4, 936x1272px)
$page = $p->newPage();
$page->addImage('IMG_1001.JPG');
$page->addImage('IMG_1001.JPG');
$page->addCaption("Caption line 1");
$page->addCaption("Caption line 2");

// add one horizontal image (with an aspect ratio of 3:2, 1908x1272px)
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
    if ($image != '') {
      $this->images[] = $image;
    }
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
  private $covers = array();
  private $pages  = array();
  
  // settings are hardcoded for a 5x7" mini photo book 
  // specs from Viovio: http://www.viovio.com/wiki/PDF+Specs
  // values are millimeters (mm)
  private $width   = 184;
  private $height  = 133;
  private $trim    = 3;
  private $margin  = 6;
  private $spacing = 3; // between photos
  private $gutter  = 7;
  private $show_lines = false;

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

  function setShowLines($bool)
  {
    $this->show_lines = $bool;
  }

  function render() {
    $image_dir = 'images/';

    $pdf = new FPDF("L", 'mm', array($this->height, $this->width));
    $pdf->SetAutoPageBreak(false);

    $pdf->AddFont('DejaVuSerif');
    $pdf->SetFont('DejaVuSerif', '', 10);

    foreach ($this->covers as $cover) {
      $images   = $cover->images();
      $pdf->AddPage();
      $pdf->Image($image_dir . $images[0], 0, 0, $this->width);
    }
    
    $page_side = 'right';
    foreach ($this->pages as $page) {
      print ".";
      $images   = $page->images();
      $captions = $page->captions();

      $pdf->AddPage();
      
      if ($this->show_lines) {
        // trim
        $pdf->Rect($this->trim, 
                   $this->trim, 
                   $this->width - ($this->trim*2), 
                   $this->height - ($this->trim*2));

        // safety zone
        $pdf->Rect($this->trim+$this->margin, 
                   $this->trim+$this->margin, 
                   $this->width-($this->margin*2)-($this->trim*2), 
                   $this->height-($this->margin*2)-($this->trim*2));
      }


      if ($page_side == 'right') {
        $gutter_x = $this->trim + $this->margin;
        $gutter_y = $this->trim + $this->margin;
        $gutter_w = $this->gutter;
        $gutter_h = $this->height - ($this->trim * 2) - ($this->margin * 2);

        $landscape_x = $this->trim + $this->gutter + $this->margin;
        $landscape_y = $this->trim + $this->margin;
        $landscape_w = $this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter;
        $landscape_h = $this->height - ($this->margin * 2) - ($this->trim * 2) - 9;

        $portrait_l_x = $this->trim + $this->gutter + $this->margin;
        $portrait_l_y = $this->trim + $this->margin;
        $portrait_l_w = ($this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter - $this->spacing) / 2;
        $portrait_l_h = $this->height - ($this->margin * 2) - ($this->trim * 2) - 9;
  
        $portrait_r_x = $this->trim + $this->gutter + $this->margin + (($this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter - $this->spacing) / 2) + $this->spacing;
        $portrait_r_y = $this->trim + $this->margin;
        $portrait_r_w = ($this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter - $this->spacing) / 2;
        $portrait_r_h = $this->height - ($this->margin * 2) - ($this->trim * 2) - 9;

        $caption1_x = $this->margin + $this->trim + $this->gutter;
        $caption1_y = 113 + $this->trim;
        $caption1_w = $this->width - ($this->margin*2) - ($this->trim*2) - $this->gutter;
        $caption1_h = 5;

        $caption2_x = $this->margin + $this->trim + $this->gutter;
        $caption2_y = 117 + $this->trim;
        $caption2_w = $this->width - ($this->margin*2) - ($this->trim*2) - $this->gutter;
        $caption2_h = 5;

        $page_side = 'left';

      } else {
        $gutter_x = $this->width - ($this->trim + $this->margin) - $this->gutter;
        $gutter_y = $this->trim + $this->margin;
        $gutter_w = $this->gutter;
        $gutter_h = $this->height - ($this->trim * 2) - ($this->margin * 2);

        $landscape_x = $this->trim + $this->margin;
        $landscape_y = $this->trim + $this->margin;
        $landscape_w = $this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter;
        $landscape_h = $this->height - ($this->margin * 2) - ($this->trim * 2) - 9;

        $portrait_l_x = $this->trim + $this->margin;
        $portrait_l_y = $this->trim + $this->margin;
        $portrait_l_w = ($this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter - $this->spacing) / 2;
        $portrait_l_h = $this->height - ($this->margin * 2) - ($this->trim * 2) - 9;
  
        $portrait_r_x = $this->trim + $this->margin + (($this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter - $this->spacing) / 2) + $this->spacing;
        $portrait_r_y = $this->trim + $this->margin;
        $portrait_r_w = ($this->width - ($this->margin * 2) - ($this->trim * 2) - $this->gutter - $this->spacing) / 2;
        $portrait_r_h = $this->height - ($this->margin * 2) - ($this->trim * 2) - 9;

        $caption1_x = $this->margin + $this->trim;
        $caption1_y = 113 + $this->trim;
        $caption1_w = $this->width - ($this->margin*2) - ($this->trim*2) - $this->gutter;
        $caption1_h = 5;

        $caption2_x = $this->margin + $this->trim;
        $caption2_y = 117 + $this->trim;
        $caption2_w = $this->width - ($this->margin*2) - ($this->trim*2) - $this->gutter;
        $caption2_h = 5;

        $page_side = 'right';
      }
        
      // gutter
      if ($this->show_lines) {
        $pdf->Rect($gutter_x, $gutter_y, $gutter_w, $gutter_h); 
      }

      switch (count($images)) {
        case 1:

          if ($this->show_lines) {
            $pdf->Rect($landscape_x, $landscape_y, $landscape_w, $landscape_h);
          }

          $pdf->Image($image_dir . $images[0], $landscape_x, $landscape_y, $landscape_w, $landscape_h);
          break;

        case 2:
          if ($this->show_lines) {
            $pdf->Rect($portrait_l_x, $portrait_l_y, $portrait_l_w, $portrait_l_h);
            $pdf->Rect($portrait_r_x, $portrait_r_y, $portrait_r_w, $portrait_r_h);
          }

          $pdf->Image($image_dir . $images[0], $portrait_l_x, $portrait_l_y, $portrait_l_w, $portrait_l_h);
          $pdf->Image($image_dir . $images[1], $portrait_r_x, $portrait_r_y, $portrait_r_w, $portrait_r_h);
          break;
      }

      if ($captions[0]) {
        $pdf->SetXY($caption1_x, $caption1_y);
        $pdf->Cell($caption1_w, $caption1_h, utf8_decode($captions[0]), $this->show_lines, 0, 'C');
      }
      
      if ($captions[1]) {
        $pdf->SetXY($caption2_x, $caption2_y);
        $pdf->Cell($caption2_w, $caption2_h, utf8_decode($captions[1]), $this->show_lines, 0, 'C');
      }
    }

    $pdf->Output('book.pdf');
    print "\n";
  }
}

