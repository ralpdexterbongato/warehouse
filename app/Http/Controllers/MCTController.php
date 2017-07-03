<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MCTMaster;
use App\MIRSMaster;
use App\MIRSDetail;
use App\MaterialsTicketDetail;
use Carbon\Carbon;
use DB;
class MCTController extends Controller
{
  public function StoreMCT(Request $request)
  {
    $date=Carbon::today();
    $year=Carbon::today()->format('y');
    $latest=MCTMaster::orderBy('id','DESC')->take(1)->value('MCTNo');
    if (count($latest)>0)
    {
      $numOnly=substr($latest,'3');
      $numOnly = (int)$numOnly;
      $soloId= $numOnly + 1;
      $genID=$year . '-' . sprintf("%04d",$soloId);
      $MCTIncremented=$genID;

    }else
    {
      $genID=$year .'-'. sprintf("%04d",'1');
      $MCTIncremented=$genID;
    }
    $MCTMasterDB=new MCTMaster;
    $MCTMasterDB->MCTNo = $MCTIncremented;
    $MCTMasterDB->MIRSNo= $request->MIRSNo;
    $MCTMasterDB->MIRSDate= $request->MIRSDate;
    $MCTMasterDB->Particulars = $request->Particulars;
    $MCTMasterDB->AddressTo = $request->AddressTo;
    $MCTMasterDB->Issuedby = $request->Issuedby;
    $MCTMasterDB->Recievedby= $request->Recievedby;
    $MCTMasterDB->save();
    $MIRSupdate = MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['Status'=>1]);

    $MIRSDetails= MIRSDetail::where('MIRSNo',$request->MIRSNo)->get(['ItemCode','Quantity']);
    foreach ($MIRSDetails as $detail)
    {
      $latestdetail=MaterialsTicketDetail::where('ItemCode',$detail->ItemCode)->orderBy('created_at','DESC')->take(1)->get();

      $minusAmount= $detail->Quantity * $latestdetail[0]->CurrentCost;
      $newQTY= $latestdetail[0]->CurrentQuantity - $detail->Quantity;
      $newAmount= $newQTY * $latestdetail[0]->CurrentCost;
      $ticketDetailDB=new MaterialsTicketDetail;
      $ticketDetailDB->ItemCode = $latestdetail[0]->ItemCode;
      $ticketDetailDB->MTType = 'MCT';
      $ticketDetailDB->MTNo = $MCTIncremented;
      $ticketDetailDB->AccountCode=$latestdetail[0]->AccountCode;
      $ticketDetailDB->UnitCost= $latestdetail[0]->UnitCost;
      $ticketDetailDB->Quantity=$detail->Quantity;
      $ticketDetailDB->Unit=$latestdetail[0]->Unit;
      $ticketDetailDB->Amount=$minusAmount;
      $ticketDetailDB->CurrentCost=$latestdetail[0]->CurrentCost;
      $ticketDetailDB->CurrentQuantity=$newQTY;
      $ticketDetailDB->CurrentAmount=$newAmount;
      $ticketDetailDB->created_at=$date;
      $ticketDetailDB->save();
    }
    return redirect()->route('PreviewMIRS');
  }

  public function previewMCT(Request $request)
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
      return view('Warehouse.MCTpreview',compact('MCTMast','MTDetails','AccountCodeGroup','totalsum'));
  }
}
