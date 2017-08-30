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
use App\MCTValidator;
use App\MCTConfirmationDetail;
class MCTController extends Controller
{
  public function StoreMCT(Request $request)
  {
    $this->validate($request,[
      'AddressTo'=>'required',
    ]);
    if (empty(Session::get('MCTSessionItems')))
    {
      return redirect()->back()->with('message', 'Items is required');
    }
    $date=Carbon::now();
    $year=Carbon::today()->format('y');
    $latest=MCTMaster::orderBy('id','DESC')->take(1)->value('MCTNo');
    $Receivedby=MIRSMaster::where('MIRSNo',$request->MIRSNo)->get(['Preparedby','PreparedPosition']);
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
    $MCTMasterDB->MCTDate=$date;
    $MCTMasterDB->Particulars = $request->Particulars;
    $MCTMasterDB->AddressTo = $request->AddressTo;
    $MCTMasterDB->IssuedbySignature=Auth::user()->Signature;
    $MCTMasterDB->Issuedby =Auth::user()->Fname.' '.Auth::user()->Lname;
    $MCTMasterDB->IssuedbyPosition=Auth::user()->Position;
    $MCTMasterDB->Receivedby=$Receivedby[0]->Preparedby;
    $MCTMasterDB->ReceivedbyPosition=$Receivedby[0]->PreparedPosition;
    if ($Receivedby[0]->Preparedby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $MCTMasterDB->ReceivedbySignature=Auth::user()->Signature;
    }
    $MCTMasterDB->save();
    MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['WithMCT'=>'0']);
    $ItemsSelectedFromMIRS= Session::get('MCTSessionItems');
    $ForMCTConfirmation = array();
    foreach ($ItemsSelectedFromMIRS as $detail)
    {
      $validatorItemQTY=MCTValidator::where('MIRSNo',$request->MIRSNo)->where('ItemCode',$detail->ItemCode)->get(['Quantity']);
      $qtyValidatorleft=$validatorItemQTY[0]->Quantity - $detail->Quantity;
      MCTValidator::where('MIRSNo',$request->MIRSNo)->where('ItemCode',$detail->ItemCode)->Update(['Quantity'=>$qtyValidatorleft]);
      $latestRR=MaterialsTicketDetail::where('ItemCode',$detail->ItemCode)->where('MTType', 'RR')->orderBy('MTDate','DESC')->take(1)->get(['AccountCode','UnitCost']);
      $AMT=$latestRR[0]->UnitCost*$detail->Quantity;
      $ForMCTConfirmation[]=array('AccountCode'=>$latestRR[0]->AccountCode,'ItemCode' =>$detail->ItemCode,'Description'=>$detail->Particulars,'MCTNo' =>$MCTIncremented,'UnitCost' =>$latestRR[0]->UnitCost ,'Quantity' =>$detail->Quantity,'Unit' =>$detail->Unit ,'Amount' =>$AMT);
    }
    MCTConfirmationDetail::insert($ForMCTConfirmation);
    Session::forget('MCTSessionItems');
    return redirect()->route('previewMCT',[$MCTIncremented]);

    if ($Receivedby[0]->Preparedby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      //if receiver is the WarehouseMans
    }
  }

  public function previewMCT($id)
  {
    Session::forget('MCTSelected');//to refresh the session that is not submited
    $MCTMast=MCTMaster::where('MCTNo',$id)->get();
    $MCTConfirmDetails=MCTConfirmationDetail::where('MCTNo',$id)->get();
    $MRTcheck=MRTMaster::where('MCTNo',$id)->value('MRTNo');
    $AccountCodeGroup = DB::table("MCTConfirmationDetails")
	    ->select(DB::raw("SUM(Amount) as totals"),DB::raw("AccountCode as AccountCode"))
      ->where('MCTNo', $id)
      ->orderBy("AccountCode")
	    ->groupBy(DB::raw("AccountCode"))
	    ->get();

      $totalsum=0;
      foreach ($AccountCodeGroup as $codegrouped)
      {
        $totalsum= $totalsum +$codegrouped->totals;
      }
      return view('Warehouse.MCT.MCTpreview',compact('MCTMast','MTDetails','MCTConfirmDetails','AccountCodeGroup','totalsum','MRTcheck'));
  }
  public function summaryMCT()
  {
    return view('Warehouse.MCT.MCT-summary');
  }
  public function SignatureMCT(Request $request)
  {
    $mctmaster=MCTMaster::where('MCTNo',$request->MCTNo)->get(['Receivedby']);
    if ($mctmaster[0]->Receivedby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      MCTMaster::where('MCTNo',$request->MCTNo)->update(['ReceivedbySignature'=>Auth::user()->Signature]);
    }
      $date=Carbon::now();
     $ItemsConfirmed= MCTConfirmationDetail::where('MCTNo',$request->MCTNo)->get();
     $forMTDetailstable = array();
     foreach ($ItemsConfirmed as $itemconfirmed)
     {
       $latestdetail=MaterialsTicketDetail::where('ItemCode',$itemconfirmed->ItemCode)->orderBy('MTDate','DESC')->take(1)->get(['CurrentQuantity','CurrentAmount']);
       $latestPriceWhenCreated=$itemconfirmed->UnitCost;
       $minusAmount=$itemconfirmed->Amount;
       $newQTY= $latestdetail[0]->CurrentQuantity - $itemconfirmed->Quantity;
       $differenceof2AMT=$latestdetail[0]->CurrentAmount - $minusAmount;
       $newcurrentcost=$differenceof2AMT/$newQTY;
       $newAmount= $newQTY * $newcurrentcost;

         $forMTDetailstable[]=array('ItemCode' =>$itemconfirmed->ItemCode,'MTType'=>'MCT','MTNo' =>$request->MCTNo,'AccountCode' =>$itemconfirmed->AccountCode ,'UnitCost' =>$latestPriceWhenCreated,'Quantity' =>$itemconfirmed->Quantity,'Unit' =>$itemconfirmed->Unit ,'Amount' =>$minusAmount
        ,'CurrentCost' =>$newcurrentcost,'CurrentQuantity' =>$newQTY ,'CurrentAmount' =>$newAmount ,'MTDate' =>$date);
     }
     MaterialsTicketDetail::insert($forMTDetailstable);
    return redirect()->back();
  }
  public function mctRequestcheck()
  {
    $myrequestMCT=MCTMaster::orderBy('id','DESC')->where('Issuedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('IssuedbySignature')
                    ->orWhere('Receivedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('ReceivedbySignature')
                    ->paginate(10,['MIRSNo','MCTNo','Issuedby','Receivedby','Particulars','MCTDate','AddressTo','IssuedbySignature','ReceivedbySignature']);
                    return view('Warehouse.MCT.myMCTrequest',compact('myrequestMCT'));
  }
  public function MCTofMIRS($id)
  {
    $MCTMaster=MCTMaster::orderBy('MCTNo','DESC')->where('MIRSNo',$id)->paginate(10,['MIRSNo','MCTNo','MCTDate','Particulars','AddressTo','Receivedby']);
    return view('Warehouse.MCT.MCTofMIRSlist',compact('MCTMaster'));
  }
  public function CreateMCT($id)
  {
    $MIRSMasterPurpose=MIRSMaster::where('MIRSNo', $id)->value('Purpose');
    $FromValidator=MCTValidator::where('MIRSNo',$id)->paginate(5);
    return view('Warehouse.MCT.CreateMCT',compact('FromValidator','MIRSMasterPurpose'));
  }
  public function MCTSessionSaving(Request $request)
  {
    $this->validate($request,[
      'Quantity'=>'required|min:1',
    ]);
    $ItemRemaining=MCTValidator::where('MIRSNo', $request->MIRSNo)->where('ItemCode',$request->ItemCode)->get(['Quantity']);
    if ($ItemRemaining[0]->Quantity < $request->Quantity)
    {
      return redirect()->back()->with('message','Quantity left of '.$request->Particulars.' in this MIRS is only '.$ItemRemaining[0]->Quantity);
    }
    if (Session::has('MCTSessionItems'))
    {
      foreach (Session::get('MCTSessionItems') as $itemadded)
      {
        if ($itemadded->ItemCode==$request->ItemCode)
        {
          return redirect()->back()->with('message','cannot duplicate items');
        }
      }
    }
    $forsessionMCT = array('ItemCode' =>$request->ItemCode,'Particulars' =>$request->Particulars,'Unit' =>$request->Unit,'Remarks' =>$request->Remarks,'Quantity' =>$request->Quantity,);
    $forsessionMCT=(object)$forsessionMCT;
    Session::push('MCTSessionItems',$forsessionMCT);
    return redirect()->back();
  }
  public function deleteASession($id)
  {
    $items=(array)Session::get('MCTSessionItems');
    foreach ($items as $key=>$item)
    {
      if ($item->ItemCode == $id)
      {
        unset($items[$key]);
      }
    }
    Session::put('MCTSessionItems',$items);
    return redirect()->back();
  }
  public function searchMCTsummary(Request $request)
  {
    $this->validate($request,[
      'monthInput'=> 'required|min:7|max:7',
    ]);
    $datesearch=$request->monthInput;
    $MCTsummaryItems=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MCT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalissued, ItemCode as ItemCode')->get();
    $ForDisplay = array();
    foreach ($MCTsummaryItems as $key=> $items)
    {
    $ForDisplay[$key]=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('ItemCode',$items->ItemCode)->where('MTType','MCT')->take(1)->get(['AccountCode','ItemCode','UnitCost','Unit','CurrentQuantity','MTDate']);
    $issued=(object)['totalissued'=>$items->totalissued];
    $ForDisplay[$key]->push($issued);
    }
    return view('Warehouse.MCT.MCT-summary',compact('ForDisplay','datesearch'));
  }
}
