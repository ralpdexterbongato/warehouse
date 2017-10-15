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
use App\MasterItem;
use App\Jobs\NewCreatedMCTJob;
class MCTController extends Controller
{
  public function StoreMCT(Request $request)
  {
    $this->validate($request,[
      'AddressTo'=>'required',
    ]);
    if (empty(Session::get('MCTSessionItems')))
    {
      return response()->json(['error'=>'Items is required']);
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
    $ForMCTConfirmation = array();
    foreach (Session::get('MCTSessionItems') as $detail)
    {
      $validatorItemQTY=MCTValidator::where('MIRSNo',$request->MIRSNo)->where('ItemCode',$detail->ItemCode)->get(['Quantity']);
      $qtyValidatorleft=$validatorItemQTY[0]->Quantity - $detail->Quantity;
      MCTValidator::where('MIRSNo',$request->MIRSNo)->where('ItemCode',$detail->ItemCode)->Update(['Quantity'=>$qtyValidatorleft]);
      $latestRR=MaterialsTicketDetail::where('ItemCode',$detail->ItemCode)->where('MTType', 'RR')->orderBy('id','DESC')->take(1)->get(['AccountCode','UnitCost']);
      $AMT=$latestRR[0]->UnitCost*$detail->Quantity;
      $ForMCTConfirmation[]=array('AccountCode'=>$latestRR[0]->AccountCode,'ItemCode' =>$detail->ItemCode,'Description'=>$detail->Particulars,'MCTNo' =>$MCTIncremented,'UnitCost' =>$latestRR[0]->UnitCost ,'Quantity' =>$detail->Quantity,'Unit' =>$detail->Unit ,'Amount' =>$AMT);
    }
    MCTConfirmationDetail::insert($ForMCTConfirmation);
     Session::forget('MCTSessionItems');
    if ($Receivedby[0]->Preparedby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $forMTDetailstable = array();
      foreach ($ForMCTConfirmation as $mcttoMTD)
      {
        $mcttoMTD=(object)$mcttoMTD;
        $latestdetail=MaterialsTicketDetail::where('ItemCode',$mcttoMTD->ItemCode)->orderBy('id','DESC')->take(1)->get(['CurrentQuantity','CurrentAmount']);
        $minusAmount=$mcttoMTD->Amount;
        $newQTY= $latestdetail[0]->CurrentQuantity - $mcttoMTD->Quantity;
        $differenceof2AMT=$latestdetail[0]->CurrentAmount - $minusAmount;
        $newcurrentcost=$differenceof2AMT/$newQTY;
        $newAmount= $newQTY * $newcurrentcost;
        MasterItem::where('ItemCode_id',$mcttoMTD->ItemCode)->update(['CurrentQuantity'=>$newQTY]);
        $forMTDetailstable[]=array('ItemCode' =>$mcttoMTD->ItemCode,'MTType'=>'MCT','MTNo' =>$MCTIncremented,'AccountCode' =>$mcttoMTD->AccountCode ,'UnitCost' =>$mcttoMTD->UnitCost,'Quantity' =>$mcttoMTD->Quantity,'Amount' =>$minusAmount
       ,'CurrentCost' =>$newcurrentcost,'CurrentQuantity' =>$newQTY ,'CurrentAmount' =>$newAmount ,'MTDate' =>$date);
      }
      MaterialsTicketDetail::insert($forMTDetailstable);
    }
    //notification
    $Receiver=str_replace(' ','',$Receivedby[0]->Preparedby);
    $ReceiverName = array('Receiver' =>$Receiver);
    $ReceiverName=(object)$ReceiverName;
    $jobs=(new NewCreatedMCTJob($ReceiverName))->delay(Carbon::now()->addSeconds(5));
    dispatch($jobs);
    return ['redirect'=>route('MCTpageOnly',[$MCTIncremented])];
  }
  public function previewMCTPage($id)
  {
    $MCTNo = MCTMaster::where('MCTNo', $id)->get(['MCTNo']);
    return view('Warehouse.MCT.MCTpreview',compact('MCTNo'));
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
      $response = array(

          'MCTMaster' => $MCTMast,
          'MCTConfirmDetails'=>$MCTConfirmDetails,
          'AccountCodeGroup'=>$AccountCodeGroup,
          'totalsum'=>$totalsum,
          'MRTcheck'=>$MRTcheck

      );
      return response()->json($response);
  }
  public function summaryMCT()
  {
    return view('Warehouse.MCT.MCT-summary');
  }
  public function SignatureMCT($id)
  {
      $date=MCTMaster::where('MCTNo',$id)->get(['MCTDate']);
     $ItemsConfirmed= MCTConfirmationDetail::where('MCTNo',$id)->get();
     $forMTDetailstable = array();
     foreach ($ItemsConfirmed as $itemconfirmed)
     {
       $latestdetail=MaterialsTicketDetail::where('ItemCode',$itemconfirmed->ItemCode)->orderBy('id','DESC')->take(1)->get(['CurrentQuantity','CurrentAmount']);
       $latestPriceWhenCreated=$itemconfirmed->UnitCost;
       $minusAmount=$itemconfirmed->Amount;
       $newQTY= $latestdetail[0]->CurrentQuantity - $itemconfirmed->Quantity;
       $differenceof2AMT=$latestdetail[0]->CurrentAmount - $minusAmount;
       if ($newQTY>0)
       {
        $newcurrentcost=$differenceof2AMT/$newQTY;
      }else
      {
        $newcurrentcost=0;
      }
       $newAmount= $newQTY * $newcurrentcost;
        MasterItem::where('ItemCode_id',$itemconfirmed->ItemCode)->update(['CurrentQuantity'=>$newQTY]);
         $forMTDetailstable[]=array('ItemCode' =>$itemconfirmed->ItemCode,'MTType'=>'MCT','MTNo' =>$id,'AccountCode' =>$itemconfirmed->AccountCode ,'UnitCost' =>$latestPriceWhenCreated,'Quantity' =>$itemconfirmed->Quantity,'Amount' =>$minusAmount
        ,'CurrentCost' =>$newcurrentcost,'CurrentQuantity' =>$newQTY ,'CurrentAmount' =>$newAmount ,'MTDate' =>$date[0]->MCTDate);
     }
     MaterialsTicketDetail::insert($forMTDetailstable);
     $mctmaster=MCTMaster::where('MCTNo',$id)->get(['Receivedby']);
     if ($mctmaster[0]->Receivedby==Auth::user()->Fname.' '.Auth::user()->Lname)
     {
       MCTMaster::where('MCTNo',$id)->update(['ReceivedbySignature'=>Auth::user()->Signature]);
     }
  }
  public function mctRequestcheck()
  {
    $myrequestMCT=MCTMaster::orderBy('id','DESC')->where('Issuedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('IssuedbySignature')
                    ->whereNull('IfDeclined')
                    ->orWhere('Receivedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('ReceivedbySignature')
                    ->whereNull('IfDeclined')
                    ->paginate(10,['MIRSNo','MCTNo','Issuedby','Receivedby','Particulars','MCTDate','AddressTo','IssuedbySignature','ReceivedbySignature']);
                    return view('Warehouse.MCT.myMCTrequest',compact('myrequestMCT'));
  }
  public function MCTofMIRS($id)
  {
    $MCTMaster=MCTMaster::orderBy('MCTNo','DESC')->where('MIRSNo',$id)->paginate(10,['MIRSNo','MCTNo','MCTDate','Particulars','AddressTo','Receivedby','ReceivedbySignature','IfDeclined']);
    return view('Warehouse.MCT.MCTofMIRSlist',compact('MCTMaster'));
  }
  public function CreateMCT($id)
  {
    $MIRSMasterPurpose=MIRSMaster::where('MIRSNo', $id)->get(['Purpose']);
    $MIRSNumber = array('MIRSNo' =>$id );
    $MIRSNumber=json_encode($MIRSNumber);
    return view('Warehouse.MCT.CreateMCT',compact('MIRSNumber','MIRSMasterPurpose'));
  }
  public function FetchMCTvalidator($id)
  {
     $FromValidator=MCTValidator::where('MIRSNo',$id)->paginate(5);
     return response()->json(['FromValidator'=>$FromValidator]);
  }
  public function MCTSessionSaving(Request $request)
  {
    $this->validate($request,[
      'Quantity'=>'required|min:1',
    ]);
    $StockinWarehouse=MaterialsTicketDetail::where('ItemCode',$request->ItemCode)->orderBy('id','DESC')->get(['CurrentQuantity']);
    if ($StockinWarehouse[0]->CurrentQuantity<$request->Quantity)
    {
      return redirect()->back()->with('message','Sorry , our '.$request->ItemCode.' stock is not enough');
    }
    $ItemRemaining=MCTValidator::where('MIRSNo', $request->MIRSNo)->where('ItemCode',$request->ItemCode)->get(['Quantity']);
    if ($ItemRemaining[0]->Quantity < $request->Quantity)
    {
      return response()->json(['error'=>'Sorry only '.$ItemRemaining[0]->Quantity.' left']);
    }
    if (Session::has('MCTSessionItems'))
    {
      foreach (Session::get('MCTSessionItems') as $itemadded)
      {
        if ($itemadded->ItemCode==$request->ItemCode)
        {
          return response()->json(['error'=>'cannot duplicate items']);
        }
      }
    }
    $forsessionMCT = array('ItemCode' =>$request->ItemCode,'Particulars' =>$request->Particulars,'Unit' =>$request->Unit,'Remarks' =>$request->Remarks,'Quantity' =>$request->Quantity,);
    $forsessionMCT=(object)$forsessionMCT;
    Session::push('MCTSessionItems',$forsessionMCT);
  }
  public function displayMCTSessionStored()
  {
    $SessionData=Session::get('MCTSessionItems');
    return response()->json(['SessionData'=>$SessionData]);
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
    $ForDisplay[$key]=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode',$items->ItemCode)->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['AccountCode','ItemCode','CurrentQuantity','MTDate']);
    $UnitCost=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType','RR')->where('ItemCode',$items->ItemCode)->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['UnitCost']);

    $issued=(object)['totalissued'=>$items->totalissued,'UnitCost'=>$UnitCost[0]->UnitCost];
    $ForDisplay[$key]->push($issued);
    }
    return view('Warehouse.MCT.MCT-summary',compact('ForDisplay','datesearch'));
  }
  public function updateMCT(Request $request,$id)
  {
    $this->validate($request,[
      'NewAddressTo'=>'required',
      'NewQuantity.*'=>'required|numeric|min:1',
    ]);
    $MCTMaster=MCTMaster::where('MCTNo',$id)->get(['MIRSNo']);
    MCTMaster::where('MCTNo',$id)->update(['AddressTo'=>$request->NewAddressTo]);
    $DetailsToBeUpdated=MCTConfirmationDetail::where('MCTNo',$id)->get(['ItemCode','Quantity','UnitCost']);
    //each item validaton
    foreach ($DetailsToBeUpdated as $key=> $confirmationMCT)
    {
      $currentValidatorQuantity=MCTValidator::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->get(['Quantity']);
      if($confirmationMCT->Quantity<$request->NewQuantity[$key])
      {
        $tobeused=$request->NewQuantity[$key]-$confirmationMCT->Quantity;
        $NewValidatorQty=$currentValidatorQuantity[0]->Quantity-$tobeused;
        if ($NewValidatorQty<0)
        {
          return ['error'=>'Sorry,you have reached the maximum quantity left in your MIRS'];
        }
      }
    }

    foreach ($DetailsToBeUpdated as $key=> $confirmationMCT)
    {
      $currentValidatorQuantity=MCTValidator::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->get(['Quantity']);
      if ($confirmationMCT->Quantity>$request->NewQuantity[$key])
      {
        $tobeused=$confirmationMCT->Quantity-$request->NewQuantity[$key];
        $NewValidatorQty=$currentValidatorQuantity[0]->Quantity+$tobeused;
        MCTValidator::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->update(['Quantity'=>$NewValidatorQty]);
      }elseif($confirmationMCT->Quantity<$request->NewQuantity[$key])
      {
        $tobeused=$request->NewQuantity[$key]-$confirmationMCT->Quantity;
        $NewValidatorQty=$currentValidatorQuantity[0]->Quantity-$tobeused;
        MCTValidator::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->update(['Quantity'=>$NewValidatorQty]);
      }
      $newAMT=$confirmationMCT->UnitCost*$request->NewQuantity[$key];
      MCTConfirmationDetail::where('MCTNo',$id)->where('ItemCode',$confirmationMCT->ItemCode)->update(['Quantity'=>$request->NewQuantity[$key],'Amount'=>$newAMT]);
    }
  }
  public function declineMCT($id)
  {
    $MIRSNo=MCTMaster::where('MCTNo',$id)->value('MIRSNo');
    $MCTconfirmation=MCTConfirmationDetail::where('MCTNo',$id)->get(['ItemCode','Quantity']);
    foreach ($MCTconfirmation as $confirmation)
    {
      $currentMCTValidatorQty=MCTValidator::where('MIRSNo',$MIRSNo)->where('ItemCode', $confirmation->ItemCode)->get(['Quantity']);
      $newMCTValidatorQty=$currentMCTValidatorQty[0]->Quantity+$confirmation->Quantity;
      MCTValidator::where('MIRSNo',$MIRSNo)->where('ItemCode', $confirmation->ItemCode)->update(['Quantity'=>$newMCTValidatorQty]);
    }
    MCTMaster::where('MCTNo',$id)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
  }
  public function MCTRequestSignatureCount()
  {
      $myrequestMCT=MCTMaster::orderBy('id','DESC')->where('Issuedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('IssuedbySignature')
                    ->whereNull('IfDeclined')
                    ->orWhere('Receivedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('ReceivedbySignature')
                    ->whereNull('IfDeclined')->count();
                    $response = array('MCTRequestCount' => $myrequestMCT);
                    return response()->json($response);
  }
  public function MCTindexPage()
  {
    return view('Warehouse.MCT.MCTindex');
  }
  public function fetchSearchIndexMCTlist(Request $request)
  {
    return MCTMaster::orderBy('id','DESC')->where('MCTNo','LIKE','%'.$request->MCTNo.'%')->paginate(10,['MCTNo','MCTDate','MIRSNo','AddressTo','Particulars','Issuedby','Receivedby','ReceivedbySignature','IfDeclined']);
  }
}
