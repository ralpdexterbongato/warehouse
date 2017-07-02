<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\MIRSDetail;
use App\MIRSMaster;
class PDFController extends Controller
{
  public function pdf(Request $request)
  {
    $master=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get();
    $details=MIRSDetail::where('MIRSNo', $request->MIRSNo)->get();
    $pdf = PDF::loadView('Warehouse.MIRSprintable',compact('master','details'));
    return $pdf->download('MIRS.pdf');
  }
  public function mctpdf(Request $request)
  {
    $pdf = PDF::loadView('Warehouse.MCTprintable');
    return $pdf->download('MCT.pdf');
  }
}
