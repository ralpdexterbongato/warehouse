<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\MIRSDetail;
use App\MIRSMaster;
use App\MaterialsTicketDetail;
use App\MCTMaster;
use DB;

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
    $MCTMast=MCTMaster::where('MIRSNo',$request->MIRSNo)->get();
    $MTDetails=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $MCTMast[0]->MCTNo)->get();
    $AccountCodeGroup = DB::table("MaterialsTicketDetails")
	    ->select(DB::raw("SUM(Amount) as totals"),DB::raw("AccountCode as AccountCode"))
      ->where('MTType', 'MCT')->where('MTNo', $MCTMast[0]->MCTNo)
      ->orderBy("AccountCode")
	    ->groupBy(DB::raw("AccountCode"))
	    ->get();

      $totalsum=0;
      foreach ($AccountCodeGroup as $codegrouped)
      {
        $totalsum= $totalsum +$codegrouped->totals;
      }

    $pdf = PDF::loadView('Warehouse.MCTprintable',compact('MCTMast','MTDetails','AccountCodeGroup','totalsum'));
    return $pdf->download('MCT.pdf');
  }
}
