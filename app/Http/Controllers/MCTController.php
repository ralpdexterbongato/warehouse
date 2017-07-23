<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MCTMaster;
use App\MIRSMaster;
use App\MIRSDetail;
use App\MaterialsTicketDetail;
use Carbon\Carbon;
use App\MRTMaster;
use DB;
use Auth;
use Session;
use App\User;
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
    $receiver=MIRSMaster::where('MIRSNo',$request->MIRSNo)->get(['Preparedby','PreparedPosition']);
    $MCTMasterDB=new MCTMaster;
    $MCTMasterDB->MCTNo = $MCTIncremented;
    $MCTMasterDB->MIRSNo= $request->MIRSNo;
    $MCTMasterDB->MCTDate=$date;
    $MCTMasterDB->Particulars = $request->Particulars;
    $MCTMasterDB->AddressTo = $request->AddressTo;
    $MCTMasterDB->Issuedby =Auth::user()->Fname.' '.Auth::user()->Lname;
    $MCTMasterDB->IssuedbyPosition=Auth::user()->Position;
    $MCTMasterDB->IssuedbySignature=Auth::user()->Signature;
    if ($receiver[0]->Preparedby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $MCTMasterDB->ReceivedbySignature=Auth::user()->Signature;
    }
    $MCTMasterDB->Receivedby= $receiver[0]->Preparedby;
    $MCTMasterDB->ReceivedbyPosition=$receiver[0]->PreparedPosition;
    $MCTMasterDB->save();
    $MIRSDetails= MIRSDetail::where('MIRSNo',$request->MIRSNo)->get(['ItemCode','Quantity']);
    MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['WithMCT'=>'0']);
    foreach ($MIRSDetails as $detail)
    {
      $latestdetail=MaterialsTicketDetail::where('ItemCode',$detail->ItemCode)->orderBy('MTDate','DESC')->take(1)->get();

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
      $ticketDetailDB->MTDate=$date;
      $ticketDetailDB->save();
    }
      return redirect()->back();
  }

  public function previewMCT(Request $request)
  {
    Session::forget('MCTSelected');//to refresh the session that is not submited
    $MCTMast=MCTMaster::where('MIRSNo',$request->MIRSNo)->get();
    $MTDetails=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $MCTMast[0]->MCTNo)->get();
    $MRTcheck=MRTMaster::where('MCTNo',$MCTMast[0]->MCTNo)->value('MRTNo');
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
      return view('Warehouse.MCTpreview',compact('MCTMast','MTDetails','AccountCodeGroup','totalsum','MRTcheck'));
  }
  public function summaryMCT()
  {
    return view('Warehouse.MCT-summary');
  }
  public function SignatureMCT(Request $request)
  {
    $mctmaster=MCTMaster::where('MCTNo', $request->MCTNo)->get(['Issuedby','Receivedby']);
    if ($mctmaster[0]->Receivedby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      MCTMaster::where('MCTNo',$request->MCTNo)->update(['ReceivedbySignature'=>Auth::user()->Signature]);
    }
    return redirect()->back();
  }
  public function mctRequestcheck()
  {
    $myrequestMCT=MCTMaster::orderBy('id','DESC')->where('Issuedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('IssuedbySignature')
                    ->orWhere('Receivedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('ReceivedbySignature')
                    ->paginate(10,['MIRSNo','MCTNo','Issuedby','Receivedby','Particulars','MCTDate','AddressTo','IssuedbySignature','ReceivedbySignature']);
                    return view('Warehouse.myMCTrequest',compact('myrequestMCT'));
  }
}
