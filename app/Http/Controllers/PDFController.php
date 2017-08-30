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
use App\RVDetail;
use App\RVMaster;
use App\POMaster;
use App\PODetail;
use App\MRMaster;
class PDFController extends Controller
{
  public function mirspdf(Request $request)
  {
    $master=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get();
    $details=MIRSDetail::where('MIRSNo', $request->MIRSNo)->get();
    $pdf = PDF::loadView('Warehouse.MIRS.MIRSprintable',compact('master','details'));
    return $pdf->stream('MIRS_id:'.$master[0]->MIRSNo.'.pdf');
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

    $pdf = PDF::loadView('Warehouse.MCT.MCTprintable',compact('MCTMast','MTDetails','AccountCodeGroup','totalsum'));
    return $pdf->stream('MCT.pdf');
  }
  public function mrtpdf(Request $request)
  {
      $datesearch=$request->monthInput;
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode','Unit')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode , Unit as Unit ')->get();
      if (!empty($itemsummary[0]))
      {
        $detailMTNum =MaterialsTicketDetail::orderBy('MTDate','DESC')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['MTNo']);
        $mrtmaster=MRTMaster::where('MRTNo',$detailMTNum[0]->MTNo)->get(['Receivedby','ReturnDate']);
        $pdf = PDF::loadView('Warehouse.MRT.printableSummaryMRT',compact('itemsummary','mrtmaster'));
        return $pdf->stream('MRT_Summary_'.$datesearch.'.pdf');
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
    $pdf = PDF::loadView('Warehouse.RR.printableRR',compact('RRconfirmMasterResult','RRconfirmDetails','netsales','VAT','totalAmmount'));
    return $pdf->stream('RR_id:'.$RRconfirmMasterResult[0]->RRNo.'.pdf');
  }
  public function RVdownload(Request $request)
  {
    $RVDetails=RVDetail::where('RVNo',$request->RVNo)->get();
    $RVMaster=RVMaster::where('RVNo',$request->RVNo)->get();
    $pdf=PDF::loadView('Warehouse.RV.RVpdf',compact('RVDetails','RVMaster'));
    return $pdf->stream('RV_No'.$request->RVNo.'.pdf');
  }
  public function POdownload(Request $request)
  {
    $MasterPO=POMaster::where('PONo',$request->PONo)->get();
    $Totalamt=PODetail::where('PONo',$request->PONo)->get(['Amount'])->sum('Amount');
    $pdf=PDF::loadView('Warehouse.PO.printablePO',compact('MasterPO','Totalamt'));
    return $pdf->stream('PO_No'.$request->PONo.'.pdf');
  }
  public function MRprinting(Request $request)
  {
    $MRMaster=MRMaster::where('MRNo', $request->MRNo)->get();
    $pdf=PDF::loadView('Warehouse.MR.PrintableMR',compact('MRMaster'));
    return $pdf->stream('MR_No'.$request->MRNo.'.pdf');
  }
  public function MCTsummaryprint(Request $request)
  {
    $datesearch=$request->DateSearched;
    $MCTsummaryItems=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MCT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalissued, ItemCode as ItemCode')->get();
    $ForDisplay = array();
    foreach ($MCTsummaryItems as $key=> $items)
    {
    $ForDisplay[$key]=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('ItemCode',$items->ItemCode)->where('MTType','MCT')->take(1)->get(['AccountCode','ItemCode','UnitCost','Unit','CurrentQuantity','MTDate']);
    $issued=(object)['totalissued'=>$items->totalissued];
    $ForDisplay[$key]->push($issued);
    }
    $pdf=PDF::loadView('Warehouse.MCT.MCTsummaryPrintable',compact('ForDisplay'));
    $pdf->setPaper('A4','landscape');
    return $pdf->stream('MCTsummary_.pdf');
  }
}
