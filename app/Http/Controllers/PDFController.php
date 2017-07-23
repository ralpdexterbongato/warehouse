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
use App\RRMaster;
use App\RRconfirmationDetails;

class PDFController extends Controller
{
  public function pdf(Request $request)
  {
    $master=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get();
    $details=MIRSDetail::where('MIRSNo', $request->MIRSNo)->get();
    $pdf = PDF::loadView('Warehouse.MIRSprintable',compact('master','details'));
    return $pdf->download('MIRS_id:'.$master[0]->MIRSNo.'.pdf');
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
  public function mrtpdf(Request $request)
  {
      $datesearch=$request->monthInput;
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode','Unit')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode , Unit as Unit ')->get();
      if (!empty($itemsummary[0]))
      {
        $detailMTNum =MaterialsTicketDetail::orderBy('MTDate','DESC')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['MTNo']);
        $mrtmaster=MRTMaster::where('MRTNo',$detailMTNum[0]->MTNo)->get(['Receivedby','ReturnDate']);
        $pdf = PDF::loadView('Warehouse.printableSummaryMRT',compact('itemsummary','mrtmaster'));
        return $pdf->download('MCT.pdf');
      }else
      {
        return redirect('/summary-mrt');
      }
  }
  public function rrdownload(Request $request)
  {
    $RRconfirmMasterResult=RRMaster::where('RRNo',$request->RRNo)->get();
    $RRconfirmDetails=RRconfirmationDetails::where('RRNo',$request->RRNo)->get(['ItemCode','Unit','RRQuantityDelivered','Description','QuantityAccepted','UnitCost','Amount']);
    $netsales=$RRconfirmDetails->sum('Amount');
    $VAT=$netsales*.12;
    $totalAmmount=$VAT+$netsales;
    $pdf = PDF::loadView('Warehouse.printableRR',compact('RRconfirmMasterResult','RRconfirmDetails','netsales','VAT','totalAmmount'));
    return $pdf->download('RR_id:'.$RRconfirmMasterResult[0]->RRNo.'.pdf');
  }
}
