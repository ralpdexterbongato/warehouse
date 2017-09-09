<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\MIRSDetail;
use App\MIRSMaster;
use App\MaterialsTicketDetail;
use App\MCTMaster;
use App\MRTMaster;
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
  public function MRTSummary()
  {
    return view('Warehouse.printableSummaryMRT');
  }
  public function mrtpdf(Request $request)
  {
      $datesearch=$request->monthInput;
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MRT')->whereDate('created_at','LIKE',date($datesearch).'%')->groupBy('ItemCode','Unit')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode , Unit as Unit ')->get();
      if (!empty($itemsummary[0]))
      {
        $detailMTNum =MaterialsTicketDetail::orderBy('created_at','DESC')->where('MTType','MRT')->whereDate('created_at','LIKE',date($datesearch).'%')->take(1)->get(['MTNo']);
        $mrtmaster=MRTMaster::where('MRTNo',$detailMTNum[0]->MTNo)->get(['Receivedby','ReturnDate']);
        $pdf = PDF::loadView('Warehouse.printableSummaryMRT',compact('itemsummary','mrtmaster'));
        return $pdf->download('MCT.pdf');
      }else
      {
        return redirect('/summary-mrt');
      }
  }
}
