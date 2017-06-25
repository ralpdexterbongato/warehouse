<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
class PDFController extends Controller
{
  public function pdf()
  {
    $pdf = \PDF::loadView('Warehouse.MIRSprintable');
    return $pdf->download('MIRS.pdf');
  }
}
