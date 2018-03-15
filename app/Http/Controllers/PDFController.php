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
use App\User;
use App\MCTConfirmationDetail;
class PDFController extends Controller
{
  public function mirspdf($id)
  {
    $master=MIRSMaster::with('users')->where('MIRSNo', $id)->get();
    $details=MIRSDetail::where('MIRSNo', $id)->get();
    $pdf = PDF::loadView('Warehouse.MIRS.MIRSprintable',compact('master','details'));
    return $pdf->stream('MIRS_id:'.$id.'.pdf');
  }
  public function mctpdf(Request $request)
  {
    $MCTMast=MCTMaster::with('users')->where('MCTNo',$request->MCTNo)->get();
    $MTDetails=MCTConfirmationDetail::where('MCTNo',$request->MCTNo)->get();
    $AccountCodeGroup = DB::table("MCTConfirmationDetails")
    ->select(DB::raw("SUM(Amount) as totals"),DB::raw("AccountCode as AccountCode"))
    ->where('MCTNo', $request->MCTNo)
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
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->whereNull('IsRollBack')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode ')->get();
      if (!empty($itemsummary[0]))
      {
        $MaterialDate =MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->value('MTDate');
        $WarehouseMan=User::where('isActive', '0')->where('Role', '4')->orderBy('id','DESC')->take(1)->get(['FullName','Position','Signature']);
        $pdf = PDF::loadView('Warehouse.MRT.printableSummaryMRT',compact('itemsummary','MaterialDate','WarehouseMan'));
        return $pdf->stream('MRT_Summary_'.$datesearch.'.pdf');
      }else
      {
        return redirect('/summary-mrt');
      }
  }
  public function rrdownload($id)
  {
    $RRconfirmMasterResult=RRMaster::with('users')->where('RRNo',$id)->get();
    $RRconfirmDetails=RRconfirmationDetails::where('RRNo',$id)->get(['ItemCode','Unit','RRQuantityDelivered','Description','QuantityAccepted','UnitCost','Amount']);
    $netsales=$RRconfirmDetails->sum('Amount');
    $VAT=$netsales*.12;
    $totalAmmount=$VAT+$netsales;
    $pdf = PDF::loadView('Warehouse.RR.printableRR',compact('RRconfirmMasterResult','RRconfirmDetails','netsales','VAT','totalAmmount'));
    return $pdf->stream('RR_id:'.$RRconfirmMasterResult[0]->RRNo.'.pdf');
  }
  public function RVdownload($id)
  {
    $RVDetails=RVDetail::where('RVNo',$id)->get();
    $RVMaster=RVMaster::with('users')->where('RVNo',$id)->get();
    $pdf=PDF::loadView('Warehouse.RV.RVpdf',compact('RVDetails','RVMaster'));
    return $pdf->stream('RV_No'.$id.'.pdf');
  }
  public function POdownload($id)
  {
    $MasterPO=POMaster::with('users')->where('PONo',$id)->get();
    $Totalamt=PODetail::where('PONo',$id)->get(['Amount'])->sum('Amount');
    $pdf=PDF::loadView('Warehouse.PO.printablePO',compact('MasterPO','Totalamt'));
    return $pdf->stream('PO_No'.$id.'.pdf');
  }
  public function MRprinting($id)
  {
    $MRMaster=MRMaster::with('users')->where('MRNo', $id)->get();
    $pdf=PDF::loadView('Warehouse.MR.PrintableMR',compact('MRMaster'));
    return $pdf->stream('MR_No'.$id.'.pdf');
  }
  public function MCTsummaryprint(Request $request)
  {
    $datesearch=$request->DateSearched;
    $MCTsummaryItems=MaterialsTicketDetail::where('MTType','MCT')->whereNull('IsRollBack')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalissued, ItemCode as ItemCode')->get();
    $ForDisplay = array();
    foreach ($MCTsummaryItems as $key=> $items)
    {
    $ForDisplay[$key]=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode',$items->ItemCode)->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['AccountCode','ItemCode','CurrentQuantity','MTDate']);
    $UnitCost=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('MTType','RR')->where('ItemCode',$items->ItemCode)->take(1)->get(['UnitCost']);
    $issued=(object)['totalissued'=>$items->totalissued,'UnitCost'=>$UnitCost[0]->UnitCost];
    $ForDisplay[$key]->push($issued);
    }
    $pdf=PDF::loadView('Warehouse.MCT.MCTsummaryPrintable',compact('ForDisplay'));
    $pdf->setPaper('A4','landscape');
    return $pdf->stream('MCTsummary.pdf');
  }
  public function MRTPrinting()
  {
    $pdf = PDF::loadView('Warehouse.MRT.MRTPrintable');
    return $pdf->stream('MRT.pdf');
  }
}
