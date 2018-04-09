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
use App\MCTConfirmationDetail;
use App\MasterItem;
use App\Jobs\NewCreatedMCTJob;
use App\Jobs\GlobalNotifJob;
use App\Signatureable;
use App\Notification;
class MCTController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }
  public function StoreMCT(Request $request)
  {
    if ($request->AddressTo==null)
    {
      return ['error'=>'Address to where?'];
    }
    if (empty(Session::get('MCTSessionItems')))
    {
      return response()->json(['error'=>'Items is required']);
    }
    $date=Carbon::now();
    $year=Carbon::today()->format('y');
    $latest=MCTMaster::orderBy('MCTNo','DESC')->take(1)->value('MCTNo');
    $explodedMCTNo = explode('-',$latest);
    $Receivedby=Signatureable::where('Signatureable_id',$request->MIRSNo)->where('Signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->get(['user_id']);
    if (isset($latest) && $explodedMCTNo[0] == $year)
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
    $MCTMasterDB->MIRSNo = $request->MIRSNo;
    $MCTMasterDB->mctdate = $date;
    $MCTMasterDB->Particulars = $request->Particulars;
    $MCTMasterDB->AddressTo = $request->AddressTo;
    $MCTMasterDB->CreatorID = Auth::user()->id;
    $MCTMasterDB->save();

    $forSignatureTbl = array(
       array('user_id' =>Auth::user()->id ,'Signatureable_id'=>$MCTIncremented,'Signatureable_type'=>'App\MCTMaster','SignatureType'=>'IssuedBy'),
       array('user_id' =>$Receivedby[0]->user_id ,'Signatureable_id'=>$MCTIncremented,'Signatureable_type'=>'App\MCTMaster','SignatureType'=>'ReceivedBy')
    );
    Signatureable::insert($forSignatureTbl);
    MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['WithMCT'=>'0']);
    $ForMCTConfirmation = array();
    foreach (Session::get('MCTSessionItems') as $detail)
    {
      $validatorItemQTY=MIRSDetail::where('MIRSNo',$request->MIRSNo)->where('ItemCode',$detail->ItemCode)->get(['QuantityValidator']);
      $qtyValidatorleft=$validatorItemQTY[0]->QuantityValidator - $detail->Quantity;
      MIRSDetail::where('MIRSNo',$request->MIRSNo)->where('ItemCode',$detail->ItemCode)->Update(['QuantityValidator'=>$qtyValidatorleft]);
      $latestRR=MaterialsTicketDetail::where('ItemCode',$detail->ItemCode)->where('MTType', 'RR')->orderBy('id','DESC')->take(1)->get(['AccountCode','UnitCost']);
      $AMT=$latestRR[0]->UnitCost*$detail->Quantity;
      $ForMCTConfirmation[]=array('AccountCode'=>$latestRR[0]->AccountCode,'ItemCode' =>$detail->ItemCode,'Description'=>$detail->Particulars,'MCTNo' =>$MCTIncremented,'UnitCost' =>$latestRR[0]->UnitCost ,'Quantity' =>$detail->Quantity,'Unit' =>$detail->Unit ,'Amount' =>$AMT);
    }
    MCTConfirmationDetail::insert($ForMCTConfirmation);
     Session::forget('MCTSessionItems');
     return ['redirect'=>route('MCTpageOnly',[$MCTIncremented])];
  }
  public function previewMCTPage($id)
  {
    $MCTNo = array('MCTNo' => $id);
    $MCTNo=json_encode($MCTNo);
    return view('Warehouse.MCT.MCTpreview',compact('MCTNo'));
  }
  public function previewMCT($id)
  {
    Session::forget('MCTSelected');//to refresh the session that is not submited
    $MCTMast=MCTMaster::with('users')->where('MCTNo',$id)->get();
    $MCTConfirmDetails=MCTConfirmationDetail::where('MCTNo',$id)->get();
    $MRTcheck=MRTMaster::where('MCTNo',$id)->whereNull('Status')->whereNull('IsRollBack')
    ->orWhere('Status','0')->where('MCTNo',$id)->whereNull('IsRollBack')
    ->orWhere('IsRollBack','1')->where('MCTNo',$id)->whereNull('Status')
    ->orWhere('Status','0')->where('IsRollBack','1')->where('MCTNo',$id)->value('MRTNo');
    $AccountCodeGroup = DB::table("MCTConfirmationDetails")
	    ->select(DB::raw("SUM('Amount') as totals"),DB::raw("AccountCode as AccountCode"))
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
    $IssuerID=Signatureable::where('Signatureable_id',$id)->where('Signatureable_type', 'App\MCTMaster')->where('SignatureType', 'IssuedBy')->get(['user_id','Signature']);
    $ReceiversId=Signatureable::where('Signatureable_id',$id)->where('Signatureable_type', 'App\MCTMaster')->where('SignatureType', 'ReceivedBy')->get(['user_id']);
    if (($IssuerID[0]->user_id==Auth::user()->id)&&($IssuerID[0]->Signature==null))
    {
      Signatureable::where('Signatureable_id',$id)->where('Signatureable_type', 'App\MCTMaster')->where('SignatureType', 'IssuedBy')->update(['Signature'=>'0']);
      MCTMaster::where('MCTNo',$id)->update(['SignatureTurn'=>1]);
      $ReceiverID = array('Receiver' =>$ReceiversId[0]->user_id);
      $ReceiverID=(object)$ReceiverID;
      $jobs=(new NewCreatedMCTJob($ReceiverID))->delay(Carbon::now()->addSeconds(5));
      dispatch($jobs);

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $ReceiversId[0]->user_id;
      $NotificationTbl->NotificationType = 'Request';
      $NotificationTbl->FileType = 'MCT';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$ReceiversId[0]->user_id);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }else
    {
      $MCTMaster=MCTMaster::where('MCTNo',$id)->get(['mctdate','CreatorID']);
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
         MasterItem::where('ItemCode',$itemconfirmed->ItemCode)->update(['CurrentQuantity'=>$newQTY]);
         $forMTDetailstable[]=array('ItemCode' =>$itemconfirmed->ItemCode,'MTType'=>'MCT','MTNo' =>$id,'AccountCode' =>$itemconfirmed->AccountCode ,'UnitCost' =>$latestPriceWhenCreated,'Quantity' =>$itemconfirmed->Quantity,'Amount' =>$minusAmount
         ,'CurrentCost' =>$newcurrentcost,'CurrentQuantity' =>$newQTY ,'CurrentAmount' =>$newAmount ,'MTDate' =>$MCTMaster[0]->mctdate);
      }
      MaterialsTicketDetail::insert($forMTDetailstable);
      Signatureable::where('Signatureable_id',$id)->where('Signatureable_type', 'App\MCTMaster')->where('SignatureType', 'ReceivedBy')->update(['Signature'=>'0']);
      MCTMaster::where('MCTNo',$id)->update(['Status'=>0]);

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $MCTMaster[0]->CreatorID;
      $NotificationTbl->NotificationType = 'Approved';
      $NotificationTbl->FileType = 'MCT';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$MCTMaster[0]->CreatorID);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
  }
  public function mctRequestcheck()
  {
      $curretnUser=User::find(Auth::user()->id);
      $myrequestMCT=$curretnUser->MCTSignatureTurn()->paginate(10);
      return view('Warehouse.MCT.myMCTrequest',compact('myrequestMCT'));
  }
  public function MCTofMIRS($id)
  {
    $MCTMaster=MCTMaster::orderBy('MCTNo','DESC')->where('MIRSNo',$id)->paginate(10,['MIRSNo','MCTNo','mctdate','Particulars','AddressTo','Status','IsRollBack']);
    return view('Warehouse.MCT.MCTofMIRSlist',compact('MCTMaster'));
  }
  public function CreateMCT($id)
  {
    Session::forget('MCTSessionItems');
    $MIRSMasterPurpose=MIRSMaster::where('MIRSNo', $id)->get(['Purpose']);
    $MIRSNumber = array('MIRSNo' =>$id );
    $MIRSNumber=json_encode($MIRSNumber);
    return view('Warehouse.MCT.CreateMCT',compact('MIRSNumber','MIRSMasterPurpose'));
  }
  public function FetchMCTvalidator($id)
  {
     $FromMIRSDetail=MIRSDetail::where('MIRSNo',$id)->paginate(5);
     return response()->json(['FromMIRSDetail'=>$FromMIRSDetail]);
  }
  public function MCTSessionSaving(Request $request)
  {
    if ($request->Quantity==null)
    {
        return response()->json(['error'=>'Quantity is required']);
    }
    $StockinWarehouse=MaterialsTicketDetail::where('ItemCode',$request->ItemCode)->orderBy('id','DESC')->get(['CurrentQuantity']);
    if ($StockinWarehouse[0]->CurrentQuantity<$request->Quantity)
    {
      return response()->json(['error'=>'Not enough warehouse stock for this item']);
    }
    $ItemRemaining=MIRSDetail::where('MIRSNo', $request->MIRSNo)->where('ItemCode',$request->ItemCode)->get(['QuantityValidator']);
    if ($ItemRemaining[0]->QuantityValidator < $request->Quantity)
    {
      return response()->json(['error'=>'Sorry '.$ItemRemaining[0]->QuantityValidator.' left']);
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
    if (isset($SessionData))
    {
      $SessionData=array_reverse($SessionData);
    }
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

    $datesearch=explode('-',$request->monthInput);
    $MCTsummaryItems=MaterialsTicketDetail::orderBy('ItemCode')->whereNull('IsRollBack')->where('MTType','MCT')->whereYear("MTDate", $datesearch[0])
    ->whereMonth("MTDate", $datesearch[1])->groupBy('ItemCode')->selectRaw('sum("Quantity") as totalissued, "ItemCode" as ItemCode')->get();
    $ForDisplay = array();
    foreach ($MCTsummaryItems as $key=> $items)
    {
    $ForDisplay[$key]=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode',$items->ItemCode)->whereYear("MTDate", $datesearch[0])
    ->whereMonth("MTDate", $datesearch[1])->take(1)->get(['AccountCode','ItemCode','CurrentQuantity','MTDate']);
    $UnitCost=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('MTType','RR')->where('ItemCode',$items->ItemCode)->take(1)->get(['UnitCost']);
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
    $MCTMaster=MCTMaster::where('MCTNo',$id)->get(['MIRSNo','Status']);
    if ($MCTMaster[0]->Status!=null)
    {
      return ['error'=>'Refreshed'];
    }
    MCTMaster::where('MCTNo',$id)->update(['AddressTo'=>$request->NewAddressTo]);
    $DetailsToBeUpdated=MCTConfirmationDetail::where('MCTNo',$id)->get(['ItemCode','Quantity','UnitCost']);
    //each item validaton
    foreach ($DetailsToBeUpdated as $key=> $confirmationMCT)
    {
      $currentValidatorQuantity=MIRSDetail::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->get(['QuantityValidator']);
      if($confirmationMCT->Quantity<$request->NewQuantity[$key])
      {
        $tobeused=$request->NewQuantity[$key]-$confirmationMCT->Quantity;
        $NewValidatorQty=$currentValidatorQuantity[0]->QuantityValidator-$tobeused;
        if ($NewValidatorQty<0)
        {
          return ['error'=>'Sorry,you have reached the maximum quantity left in your MIRS'];
        }
      }
    }

    foreach ($DetailsToBeUpdated as $key=> $confirmationMCT)
    {
      $currentValidatorQuantity=MIRSDetail::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->get(['QuantityValidator']);
      if ($confirmationMCT->Quantity>$request->NewQuantity[$key])
      {
        $tobeused=$confirmationMCT->Quantity-$request->NewQuantity[$key];
        $NewValidatorQty=$currentValidatorQuantity[0]->QuantityValidator+$tobeused;
        MIRSDetail::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->update(['QuantityValidator'=>$NewValidatorQty]);
      }elseif($confirmationMCT->Quantity<$request->NewQuantity[$key])
      {
        $tobeused=$request->NewQuantity[$key]-$confirmationMCT->Quantity;
        $NewValidatorQty=$currentValidatorQuantity[0]->QuantityValidator-$tobeused;
        MIRSDetail::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$confirmationMCT->ItemCode)->update(['QuantityValidator'=>$NewValidatorQty]);
      }
      $newAMT=$confirmationMCT->UnitCost*$request->NewQuantity[$key];
      MCTConfirmationDetail::where('MCTNo',$id)->where('ItemCode',$confirmationMCT->ItemCode)->update(['Quantity'=>$request->NewQuantity[$key],'Amount'=>$newAMT]);
    }
    $receiverId=Signatureable::where('Signatureable_id', $id)->where('Signatureable_type', 'App\MCTMaster')->where('SignatureType','ReceivedBy')->value('user_id');
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $receiverId;
    $NotificationTbl->NotificationType = 'Updated';
    $NotificationTbl->FileType = 'MCT';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$receiverId);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function declineMCT($id)
  {
    $MCTMaster=MCTMaster::where('MCTNo',$id)->get(['MIRSNo','CreatorID']);
    $MCTconfirmation=MCTConfirmationDetail::where('MCTNo',$id)->get(['ItemCode','Quantity']);
    foreach ($MCTconfirmation as $confirmation)
    {
      $currentMCTValidatorQty=MIRSDetail::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode', $confirmation->ItemCode)->get(['QuantityValidator']);
      $newMCTValidatorQty=$currentMCTValidatorQty[0]->QuantityValidator+$confirmation->Quantity;
      MIRSDetail::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode', $confirmation->ItemCode)->update(['QuantityValidator'=>$newMCTValidatorQty]);
    }
    Signatureable::where('Signatureable_id',$id)->where('Signatureable_type', 'App\MCTMaster')->where('user_id', Auth::user()->id)->update(['Signature'=>'1']);
    MCTMaster::where('MCTNo',$id)->update(['Status'=>1]);

    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $MCTMaster[0]->CreatorID;
    $NotificationTbl->NotificationType = 'Declined';
    $NotificationTbl->FileType = 'MCT';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$MCTMaster[0]->CreatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function MCTRequestSignatureCount()
  {
    $curretnUser=User::find(Auth::user()->id);
    $myrequestMCT=$curretnUser->MCTSignatureTurn()->count();
                    $response = array('MCTRequestCount' => $myrequestMCT);
                    return response()->json($response);
  }
  public function MCTindexPage()
  {
    return view('Warehouse.MCT.MCTindex');
  }
  public function fetchSearchIndexMCTlist(Request $request)
  {
    return MCTMaster::with('users')->orderBy('MCTNo','DESC')->where('MCTNo','LIKE','%'.$request->MCTNo.'%')->paginate(10,['MCTNo','mctdate','MIRSNo','AddressTo','Particulars','Status','IsRollBack']);
  }
  public function RollBack($mctNo,$mirsNo)
  {
    $dataToRollBack=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $mctNo)->whereNull('IsRollBack')->get();
    MCTMaster::where('MCTNo',$mctNo)->update(['IsRollBack'=>'0']);
    foreach ($dataToRollBack as $data)
    {
      $idOfMCTHistory = MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $mctNo)->where('ItemCode',$data->ItemCode)->value('id');
      MaterialsTicketDetail::where('id', $idOfMCTHistory)->update(['IsRollBack'=>'0']);
      $affectedRows = MaterialsTicketDetail::where('ItemCode',$data->ItemCode)->whereNull('IsRollBack')->where('id','>',$idOfMCTHistory)
      ->chunk(5, function ($affectedRows) use ($data)
      {
           foreach ($affectedRows as $affectedrow)
           {
             if ($affectedrow->MTType=='MCT' || $affectedrow->MTType=='DMG')
             {
              $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
              $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
              $newAmt = $affectedrow->Quantity * $uCostLatestRR;
              $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity - $affectedrow->Quantity;
              $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount - $newAmt;
              if ($newCurrentQty!=0)
              {
                $newCurrentCost= $newCurrentAmount / $newCurrentQty;
              }else
              {
                $newCurrentCost = 0;
              }
              $affectedrow->update(['UnitCost'=>$uCostLatestRR,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
             }
             if ($affectedrow->MTType=='MRT')
             {
              $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
              $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
              $newAmt = $affectedrow->Quantity * $uCostLatestRR;
              $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity + $affectedrow->Quantity;
              $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount + $newAmt;
              if ($newCurrentQty!=0)
              {
                $newCurrentCost= $newCurrentAmount / $newCurrentQty;
              }else
              {
                $newCurrentCost = 0;
              }
              $affectedrow->update(['UnitCost'=>$uCostLatestRR,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
             }
             if ($affectedrow->MTType=='RR')
             {
              $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
              $newAmt = $affectedrow->Quantity * $affectedrow->UnitCost;
              $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity + $affectedrow->Quantity;
              $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount + $newAmt;
              if ($newCurrentQty!=0)
              {
                $newCurrentCost= $newCurrentAmount / $newCurrentQty;
              }else
              {
                $newCurrentCost = 0;
              }
              $affectedrow->update(['UnitCost'=>$affectedrow->UnitCost,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
             }
           }
       });
       $CurrentQuantityOfItem=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode', $data->ItemCode)->take(1)->value('CurrentQuantity');
       MasterItem::where('ItemCode',$data->ItemCode)->update(['CurrentQuantity' => $CurrentQuantityOfItem]);
    }

    $MCTconfirmation=MCTConfirmationDetail::where('MCTNo',$mctNo)->get(['ItemCode','Quantity']);
    foreach ($MCTconfirmation as $confirmation)
    {
      $currentMCTValidatorQty=MIRSDetail::where('MIRSNo',$mirsNo)->where('ItemCode', $confirmation->ItemCode)->get(['QuantityValidator']);
      $newMCTValidatorQty=$currentMCTValidatorQty[0]->QuantityValidator+$confirmation->Quantity;
      MIRSDetail::where('MIRSNo',$mirsNo)->where('ItemCode', $confirmation->ItemCode)->update(['QuantityValidator'=>$newMCTValidatorQty]);
    }
    $creatorID = MCTMaster::where('MCTNo',$mctNo)->value('CreatorID');
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $creatorID;
    $NotificationTbl->NotificationType = 'Invalid';
    $NotificationTbl->FileType = 'MCT';
    $NotificationTbl->FileNo =$mctNo;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$creatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function UndoRollBack($mctNo,$mirsNo)
  {
    $dataToUndoRollBack=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $mctNo)->get();
    MCTMaster::where('MCTNo',$mctNo)->update(['IsRollBack'=>'1']);
    foreach ($dataToUndoRollBack as $data)
    {
      MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $mctNo)->where('ItemCode',$data->ItemCode)->update(['IsRollBack'=>NULL]);
      $idOfMCTHistory = MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $mctNo)->where('ItemCode',$data->ItemCode)->value('id');
      $affectedRows = MaterialsTicketDetail::where('ItemCode',$data->ItemCode)->whereNull('IsRollBack')->where('id','>',$idOfMCTHistory)
      ->chunk(5, function ($affectedRows) use ($data)
      {
           foreach ($affectedRows as $affectedrow)
           {
             if ($affectedrow->MTType=='MCT' || $affectedrow->MTType=='DMG')
             {
              $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
              $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
              $newAmt = $affectedrow->Quantity * $uCostLatestRR;
              $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity - $affectedrow->Quantity;
              $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount - $newAmt;
              if ($newCurrentQty!=0)
              {
                $newCurrentCost= $newCurrentAmount / $newCurrentQty;
              }else
              {
                $newCurrentCost = 0;
              }
              $affectedrow->update(['UnitCost'=>$uCostLatestRR,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
             }
             if ($affectedrow->MTType=='MRT')
             {
              $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
              $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
              $newAmt = $affectedrow->Quantity * $uCostLatestRR;
              $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity + $affectedrow->Quantity;
              $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount + $newAmt;
              if ($newCurrentQty!=0)
              {
                $newCurrentCost= $newCurrentAmount / $newCurrentQty;
              }else
              {
                $newCurrentCost = 0;
              }
              $affectedrow->update(['UnitCost'=>$uCostLatestRR,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
             }
             if ($affectedrow->MTType=='RR')
             {
              $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
              $newAmt = $affectedrow->Quantity * $affectedrow->UnitCost;
              $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity + $affectedrow->Quantity;
              $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount + $newAmt;
              if ($newCurrentQty!=0)
              {
                $newCurrentCost= $newCurrentAmount / $newCurrentQty;
              }else
              {
                $newCurrentCost = 0;
              }
              $affectedrow->update(['UnitCost'=>$affectedrow->UnitCost,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
             }
           }
       });
       $CurrentQuantityOfItem=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode', $data->ItemCode)->take(1)->value('CurrentQuantity');
       MasterItem::where('ItemCode',$data->ItemCode)->update(['CurrentQuantity' => $CurrentQuantityOfItem]);
    }

    $MCTconfirmation=MCTConfirmationDetail::where('MCTNo',$mctNo)->get(['ItemCode','Quantity']);
    foreach ($MCTconfirmation as $confirmation)
    {
      $currentMCTValidatorQty=MIRSDetail::where('MIRSNo',$mirsNo)->where('ItemCode', $confirmation->ItemCode)->get(['QuantityValidator']);
      $newMCTValidatorQty=$currentMCTValidatorQty[0]->QuantityValidator - $confirmation->Quantity;
      MIRSDetail::where('MIRSNo',$mirsNo)->where('ItemCode', $confirmation->ItemCode)->update(['QuantityValidator'=>$newMCTValidatorQty]);
    }

    $creatorID = MCTMaster::where('MCTNo',$mctNo)->value('CreatorID');
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $creatorID;
    $NotificationTbl->NotificationType = 'UndoInvalid';
    $NotificationTbl->FileType = 'MCT';
    $NotificationTbl->FileNo =$mctNo;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$creatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
}
