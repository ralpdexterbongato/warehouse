<?php

namespace App\Http\Controllers;
use Session;
use App\MasterItem;
use Carbon\Carbon;
use App\RRMaster;
use App\MaterialsTicketDetail;
use App\User;
use App\RRconfirmationDetails;
use Auth;
use Illuminate\Http\Request;
use App\MRMaster;
use App\POMaster;
use App\RVDetail;
use App\RRDetailsNotForStock;
use App\RVMaster;
use App\Jobs\NewCreatedRRJob;
use App\PODetail;
use App\Signatureable;
use App\Jobs\RRNewAlertReceiver;
use App\Jobs\GlobalNotifJob;
use App\Notification;
class RRController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function updateRR(Request $request,$RRno)
  {
    $MasterCurrent= RRMaster::where('RRNo', $RRno)->get(['RRNo','PONo','RVNo','Status']);
    if ($MasterCurrent[0]->Status!=null)
    {
      return ['error'=>'Refreshed'];
    }
    // validation process
    $tobeUpdated=RRconfirmationDetails::where('RRNo',$RRno)->get(['QuantityAccepted','ItemCode','id','UnitCost','Description']);
    if ($MasterCurrent[0]->PONo != null)
    {
      foreach ($tobeUpdated as $key => $tobe)
      {
        $itemrow=$key + 1;
        if ($tobe->ItemCode!=null)
        {
          $RVCurrentDetail=PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('ItemCode',$tobe->ItemCode)->get(['QtyValidator','id']);
          if ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            if ($QtyToSub > $RVCurrentDetail[0]->QtyValidator)
            {
              return ['error'=>'Item row '.$itemrow.',the remaining unreceived items is '.$RVCurrentDetail[0]->QtyValidator];
            }
          }
        }else
        {
          $RVCurrentDetail=PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('Description',$tobe->Description)->get(['QtyValidator','id']);
          if ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            if ($QtyToSub > $RVCurrentDetail[0]->QtyValidator)
            {
              return ['error'=>'Item row '.$itemrow.',the remaining unreceived items is '.$RVCurrentDetail[0]->QtyValidator];
            }
          }
        }
      }
    }else
    {
      foreach ($tobeUpdated as $key => $tobe)
      {
        $itemrow=$key + 1;
        if ($tobe->ItemCode!=null)
        {
          $RVCurrentDetail=RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('ItemCode',$tobe->ItemCode)->get(['QuantityValidator','id']);
          if ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            if ($QtyToSub > $RVCurrentDetail[0]->QuantityValidator)
            {
              return ['error'=>'Item row '.$itemrow.',the remaining unreceived items is '.$RVCurrentDetail[0]->QuantityValidator];
            }
          }
        }else
        {
          $RVCurrentDetail=RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('Particulars',$tobe->Description)->get(['QuantityValidator','id']);
          if ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            if ($QtyToSub > $RVCurrentDetail[0]->QuantityValidator)
            {
              return ['error'=>'Item row '.$itemrow.',the remaining unreceived items is '.$RVCurrentDetail[0]->QuantityValidator];
            }
          }
        }
      }
    }
    // updating process
    if ($MasterCurrent[0]->PONo != null)
    {
      $tobeUpdated=RRconfirmationDetails::where('RRNo',$RRno)->get(['QuantityAccepted','ItemCode','id','UnitCost','Description']);
      foreach ($tobeUpdated as $key => $tobe)
      {
        if ($tobe->ItemCode!=null)
        {
          $RVCurrentDetail=PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('ItemCode',$tobe->ItemCode)->get(['QtyValidator','id']);
          if ($tobe->QuantityAccepted > $request->newQty[$key])
          {
            $QtyToReturn = $tobe->QuantityAccepted - $request->newQty[$key];
            $newValidator = $RVCurrentDetail[0]->QtyValidator + $QtyToReturn;
            PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('id',$RVCurrentDetail[0]->id)->update(['QtyValidator'=>$newValidator]);
          }elseif ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            $newValidator = $RVCurrentDetail[0]->QtyValidator - $QtyToSub;
            PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('id',$RVCurrentDetail[0]->id)->update(['QtyValidator'=>$newValidator]);
          }
        }else
        {
          $RVCurrentDetail=PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('Description',$tobe->Description)->get(['QtyValidator','id']);
          if ($tobe->QuantityAccepted > $request->newQty[$key])
          {
            $QtyToReturn = $tobe->QuantityAccepted - $request->newQty[$key];
            $newValidator = $RVCurrentDetail[0]->QtyValidator + $QtyToReturn;
            PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('id',$RVCurrentDetail[0]->id)->update(['QtyValidator'=>$newValidator]);
          }elseif ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            $newValidator = $RVCurrentDetail[0]->QtyValidator - $QtyToSub;
            PODetail::where('PONo', $MasterCurrent[0]->PONo)->where('id',$RVCurrentDetail[0]->id)->update(['QuantityValidator'=>$newValidator]);
          }
        }
        $newAmt = $request->newQty[$key] * $tobe->UnitCost;
        RRconfirmationDetails::where('id',$tobe->id)->update(['QuantityValidator'=>$request->newQty[$key],'QuantityAccepted'=>$request->newQty[$key],'RRQuantityDelivered'=>$request->newQtyDelivered[$key],'Amount'=>$newAmt]);
      }
    }else
    {
      $tobeUpdated=RRconfirmationDetails::where('RRNo',$RRno)->get(['QuantityAccepted','ItemCode','id','UnitCost','Description']);
      foreach ($tobeUpdated as $key => $tobe)
      {
        if ($tobe->ItemCode!=null)
        {
          $RVCurrentDetail=RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('ItemCode',$tobe->ItemCode)->get(['QuantityValidator','id']);
          if ($tobe->QuantityAccepted > $request->newQty[$key])
          {
            $QtyToReturn = $tobe->QuantityAccepted - $request->newQty[$key];
            $newValidator = $RVCurrentDetail[0]->QuantityValidator + $QtyToReturn;
            RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('id',$RVCurrentDetail[0]->id)->update(['QuantityValidator'=>$newValidator]);
          }elseif ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            $newValidator = $RVCurrentDetail[0]->QuantityValidator - $QtyToSub;
            RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('id',$RVCurrentDetail[0]->id)->update(['QuantityValidator'=>$newValidator]);
          }
        }else
        {
          $RVCurrentDetail=RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('Particulars',$tobe->Description)->get(['QuantityValidator','id']);
          if ($tobe->QuantityAccepted > $request->newQty[$key])
          {
            $QtyToReturn = $tobe->QuantityAccepted - $request->newQty[$key];
            $newValidator = $RVCurrentDetail[0]->QuantityValidator + $QtyToReturn;
            RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('id',$RVCurrentDetail[0]->id)->update(['QuantityValidator'=>$newValidator]);
          }elseif ($tobe->QuantityAccepted < $request->newQty[$key])
          {
            $QtyToSub =$request->newQty[$key] - $tobe->QuantityAccepted;
            $newValidator = $RVCurrentDetail[0]->QuantityValidator - $QtyToSub;
            RVDetail::where('RVNo', $MasterCurrent[0]->RVNo)->where('id',$RVCurrentDetail[0]->id)->update(['QuantityValidator'=>$newValidator]);
          }
        }
        $newAmt = $request->newQty[$key] * $tobe->UnitCost;
        RRconfirmationDetails::where('id',$tobe->id)->update(['QuantityValidator'=>$request->newQty[$key],'QuantityAccepted'=>$request->newQty[$key],'RRQuantityDelivered'=>$request->newQtyDelivered[$key],'Amount'=>$newAmt]);
      }
    }

    if ($MasterCurrent[0]->PONo==null)
    {
      RRMaster::where('RRNo', $RRno)->update(['Supplier'=>$request->newSupplier,'Address'=>$request->newAddress,'InvoiceNo'=>$request->newInvoice,'Carrier'=>$request->newCarrier,'DeliveryReceiptNo'=>$request->newDeliveryReceipt,'Note'=>$request->newNote]);
    }else
    {
      RRMaster::where('RRNo', $RRno)->update(['InvoiceNo'=>$request->newInvoice,'Carrier'=>$request->newCarrier,'DeliveryReceiptNo'=>$request->newDeliveryReceipt,'Note'=>$request->newNote]);
    }

    Signatureable::where('signatureable_type','App\RRMaster')->where('signatureable_id',$RRno)->update(['Signature'=>NULL]);
    $peopleToSign=Signatureable::where('signatureable_type','App\RRMaster')->where('signatureable_id',$RRno)->get(['user_id']);
    foreach ($peopleToSign as $key => $person)
    {
      // notify all for the update
      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $person->user_id;
      $NotificationTbl->NotificationType = 'Updated';
      $NotificationTbl->FileType = 'RR';
      $NotificationTbl->FileNo =$RRno;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // notify warehouseman the creator
      $ReceiverID = array('id' => $person->user_id);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }

  }
  public function storeRRSessionValidatorNoPO($request)
  {
    $QuantityAccepted=$request->QuantityAccepted;
    $this->validate($request,[
      'UnitCost'=>'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
      'Description'=>'required',
      'Unit'=>'required',
      'QuantityDelivered'=>'required|numeric|min:'.$QuantityAccepted,
      'QuantityAccepted'=>'required|numeric|min:1',
    ]);
  }
  public function storeRRSessionValidatorWithPO($request)
  {
    $QuantityAccepted=$request->QuantityAccepted;
    $this->validate($request,[
      'UnitCost'=>'required|min:0.1',
      'Description'=>'required',
      'Unit'=>'required',
      'QuantityDelivered'=>'required|numeric|min:'.$QuantityAccepted,
      'QuantityAccepted'=>'required|numeric|min:1',
    ]);
  }
  public function deleteSessionStored($id)
  {
    if (Session::has('RR-Items-Added'))
    {
      $SelectedRRitems=Session::get('RR-Items-Added');
      foreach ($SelectedRRitems as $key => $item)
      {
         if ($key==$id)
         {
           unset($SelectedRRitems[$key]);
         }
      }
      Session::put('RR-Items-Added',$SelectedRRitems);
    }
  }
  public function deleteSessionNoPo($id)
  {
    $itemList=Session::get('RRSessionDataNoPO');
    unset($itemList[$id]);
    Session::put('RRSessionDataNoPO',$itemList);
  }
  public function searchbyItemMasterCode(Request $request)
  {
    $itemMasters=MasterItem::where('ItemCode','LIKE','%'.$request->searchcode.'%')->paginate(5,['AccountCode','ItemCode','Description','Unit']);
    $response=[
      'pagination'=>[
        'total'=> $itemMasters->total(),
        'per_page'=>$itemMasters->perPage(),
        'current_page'=>$itemMasters->currentPage(),
        'last_page'=>$itemMasters->lastPage(),
        'from'=>$itemMasters->firstitem(),
        'to'=>$itemMasters->lastitem(),
      ],
      'model'=> $itemMasters
    ];
    return response()->json($response);
  }
  public function StoreSessionRRNoPO(Request $request)
  {
      $QuantityMax=RVDetail::where('RVNo',$request->RVNo)->where('Particulars', $request->Description)->value('QuantityValidator');
      if ($request->QuantityAccepted>$QuantityMax)
      {
        return response()->json(['error'=>'Sorry , you cannot accept more than '.$QuantityMax]);
      }
      $this->storeRRSessionValidatorNoPO($request);
      if ($request->UnitCost<=0)
      {
        return response()->json(['error'=>'UnitCost must be atleast 0.1']);
      }
      if (Session::has('RRSessionDataNoPO'))
      {
        foreach (Session::get('RRSessionDataNoPO') as $items)
        {
          if ($items->ItemCode!=null)
          {
            if ($items->ItemCode==$request->ItemCode)
            {
              return response()->json(['error'=>'Oops! cannot duplicate items']);
            }
          }else
          {
            if (($items->Description==$request->Description))
            {
              return response()->json(['error'=>'Oops! cannot duplicate items']);
            }
          }
        }
      }
        $nocommaUCost=str_replace(',','',$request->UnitCost);
        $Ucost=number_format($nocommaUCost, 2, '.', '');
        $AMT=$Ucost*$request->QuantityAccepted;
        $DataFromUserToArray = array('ItemCode'=>$request->ItemCode,'AccountCode'=>$request->AccountCode,'Description'=>$request->Description,'UnitCost'=>$Ucost,'Unit'=>$request->Unit,'QuantityDelivered'=>$request->QuantityDelivered,'QuantityAccepted'=>$request->QuantityAccepted,'Amount'=>$AMT);
        $DataFromUserToArray=(object)$DataFromUserToArray;
        Session::push('RRSessionDataNoPO',$DataFromUserToArray);
  }
  public function StoreSessionRRWithPO(Request $request)
  {
    $QtyOfValidator=PODetail::where('PONo',$request->PONo)->where('Description',$request->Description)->get(['QtyValidator']);
    if ($request->QuantityAccepted > $QtyOfValidator[0]->QtyValidator)
    {
      return response()->json(['error'=>'Sorry, You cannot accept more than '.$QtyOfValidator[0]->QtyValidator]);
    }
    $this->storeRRSessionValidatorWithPO($request);
    if (Session::has('RR-Items-Added'))
    {
      foreach (Session::get('RR-Items-Added') as $items)
      {
        if (($items->ItemCode==$request->ItemCode)||($items->Description==$request->Description))
        {
          return response()->json(['error'=>'Oops! cannot duplicate items']);
        }
      }
    }
    $nocommaUCost=str_replace(',','',$request->UnitCost);
    $AMT=$nocommaUCost*$request->QuantityAccepted;
    $DataFromUserToArray = array('ItemCode'=>$request->ItemCode,'AccountCode'=>$request->AccountCode,'Description'=>$request->Description,'UnitCost'=>$nocommaUCost,'Unit'=>$request->Unit,'QuantityDelivered'=>$request->QuantityDelivered,'QuantityAccepted'=>$request->QuantityAccepted,'Amount'=>$AMT);
    $DataFromUserToArray=(object)$DataFromUserToArray;
    Session::push('RR-Items-Added',$DataFromUserToArray);
  }
  public function showSessionRRData()
  {
    return Session::get('RR-Items-Added');
  }
  public function showSessionRRDataNoPO()
  {
    return Session::get('RRSessionDataNoPO');
  }
  public function StoreRRtoTableNoPO(Request $request)
  {
    $this->StoringRRTableNoPOValidator($request);
    if (empty(Session::get('RRSessionDataNoPO')))
    {
     return response()->json(['error'=>'Selecting item is required']);
    }
    $year=Carbon::now()->format('y');
    $date=Carbon::now();
    $latestID=RRMaster::orderBy('RRNo','DESC')->take(1)->value('RRNo');
    $explodedRRNo = explode('-',$latestID);
    if (isset($latestID[0]) && $explodedRRNo[0]==$year)
    {
      $numOnly=substr($latestID,'3');
      $numOnly=(int)$numOnly;
      $newID=$numOnly + 1;
      $incremented=$year.'-'.sprintf("%04d",$newID);
    }else
    {
      $incremented=$year.'-'.sprintf("%04d",'1');
    }
    $RRMasterDB=new RRMaster;
    $RRMasterDB->RRNo =$incremented;
    $RRMasterDB->RRDate=$date;
    $RRMasterDB->Supplier=$request->Supplier;
    $RRMasterDB->Address=$request->Address;
    $RRMasterDB->InvoiceNo=$request->InvoiceNo;
    $RRMasterDB->RVNo=$request->RVNo;
    $RRMasterDB->Carrier=$request->Carrier;
    $RRMasterDB->DeliveryReceiptNo=$request->DeliveryReceiptNo;
    $RRMasterDB->Note=$request->Note;
    $RRMasterDB->CreatorID = Auth::user()->id;
    $RRMasterDB->save();
    $forSignatures = array(
      array('user_id' =>$request->Receivedby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'ReceivedBy'),
      array('user_id' =>$request->Verifiedby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'VerifiedBy'),
      array('user_id' =>$request->ReceivedOriginalby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'ReceivedOriginalBy'),
      array('user_id' =>$request->PostedtoBINby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'PostedToBINBy'),
    );
    $ForRRconfirmItemsDB = array();
    $RVDetail=RVDetail::where('RVNo',$request->RVNo)->get(['Particulars','QuantityValidator']);
    foreach (Session::get('RRSessionDataNoPO') as $forconfirmDetail)
    {
      $ForRRconfirmItemsDB[] = array('ItemCode' =>$forconfirmDetail->ItemCode ,'RRNo' =>$incremented ,
      'AccountCode' =>$forconfirmDetail->AccountCode ,'Description' =>$forconfirmDetail->Description ,'UnitCost' =>$forconfirmDetail->UnitCost ,'RRQuantityDelivered' =>$forconfirmDetail->QuantityDelivered,
      'QuantityAccepted' =>$forconfirmDetail->QuantityAccepted,'QuantityValidator'=>$forconfirmDetail->QuantityAccepted,'Unit' =>$forconfirmDetail->Unit ,'Amount'=>$forconfirmDetail->Amount);
      foreach ($RVDetail as $rvdetail)
      {
        if ($rvdetail->Particulars==$forconfirmDetail->Description)
        {
          $newValidatorQty=$rvdetail->QuantityValidator-$forconfirmDetail->QuantityAccepted;
          RVDetail::where('RVNo',$request->RVNo)->where('Particulars',$rvdetail->Particulars)->update(['QuantityValidator'=>$newValidatorQty]);
        }
      }
    }
    Signatureable::insert($forSignatures);
    RRconfirmationDetails::insert($ForRRconfirmItemsDB);
    Session::forget('RRSessionDataNoPO');
    $NotifableName = array('first' =>$request->Verifiedby,'second'=>$request->ReceivedOriginalby,'third'=>$request->PostedtoBINby);
    $NotifableName=(object)$NotifableName;
    $job = (new NewCreatedRRJob($NotifableName))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    $MobileOfReceiver=User::where('id',$request->Receivedby)->value('Mobile');
    $AlertForReceiver = array('RRNo'=>$incremented,'Mobile'=>$MobileOfReceiver,'RVNo'=>$request->RVNo);
    $AlertForReceiver=(object)$AlertForReceiver;
    $job = (new RRNewAlertReceiver($AlertForReceiver))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for receiver
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->Receivedby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->Receivedby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for verified
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->Verifiedby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->Verifiedby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for received original by
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->ReceivedOriginalby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->ReceivedOriginalby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for posted to BIN by
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->PostedtoBINby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->PostedtoBINby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    return ['redirect'=>route('RRfullpreview',[$incremented])];
  }

  public function StoreRRtoTableWithPO(Request $request)
  {
    $this->StoringRRTableWithPOValidator($request);
    if (empty(Session::get('RR-Items-Added')))
    {
     return response()->json(['error'=>'Selecting items is required']);
    }
    $year=Carbon::now()->format('y');
    $date=Carbon::now();
    $latestID=RRMaster::orderBy('RRNo','DESC')->take(1)->value('RRNo');
    $explodedRRNo = explode('-',$latestID);
    if (isset($latestID[0]) && $explodedRRNo[0]==$year)
    {
      $numOnly=substr($latestID,'3');
      $numOnly=(int)$numOnly;
      $newID=$numOnly + 1;
      $incremented=$year.'-'.sprintf("%04d",$newID);
    }else
    {
      $incremented=$year.'-'.sprintf("%04d",'1');
    }
    $POMaster=POMaster::where('PONo',$request->PONo)->get(['Supplier','Address','RVNo']);
    $RRMasterDB=new RRMaster;
    $RRMasterDB->RRNo =$incremented;
    $RRMasterDB->RRDate=$date;
    $RRMasterDB->Supplier=$POMaster[0]->Supplier;
    $RRMasterDB->Address=$POMaster[0]->Address;
    $RRMasterDB->PONo=$request->PONo;
    $RRMasterDB->InvoiceNo=$request->InvoiceNo;
    $RRMasterDB->RVNo=$POMaster[0]->RVNo;
    $RRMasterDB->Carrier=$request->Carrier;
    $RRMasterDB->DeliveryReceiptNo=$request->DeliveryReceiptNo;
    $RRMasterDB->Note=$request->Note;
    $RRMasterDB->CreatorID = Auth::user()->id;
    $RRMasterDB->save();
    $forSignatures = array(
      array('user_id' =>$request->Receivedby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'ReceivedBy'),
      array('user_id' =>$request->Verifiedby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'VerifiedBy'),
      array('user_id' =>$request->ReceivedOriginalby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'ReceivedOriginalBy'),
      array('user_id' =>$request->PostedtoBINby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RRMaster','SignatureType'=>'PostedToBINBy'),
    );
    $ForRRconfirmItemsDB = array();
    $FromPODetail=PODetail::where('PONo',$request->PONo)->get(['QtyValidator','Description']);
    foreach (Session::get('RR-Items-Added') as $forconfirmDetail)
    {
      $ForRRconfirmItemsDB[] = array('ItemCode' =>$forconfirmDetail->ItemCode ,'RRNo' =>$incremented ,
      'AccountCode' =>$forconfirmDetail->AccountCode ,'Description' =>$forconfirmDetail->Description ,'UnitCost' =>$forconfirmDetail->UnitCost ,'RRQuantityDelivered' =>$forconfirmDetail->QuantityDelivered,
      'QuantityAccepted' =>$forconfirmDetail->QuantityAccepted ,'QuantityValidator'=>$forconfirmDetail->QuantityAccepted,'Unit' =>$forconfirmDetail->Unit ,'Amount' =>$forconfirmDetail->Amount);
      foreach ($FromPODetail as $frompodetail)
      {
        if ($frompodetail->Description==$forconfirmDetail->Description)
        {
          $newValidatorQty=$frompodetail->QtyValidator - $forconfirmDetail->QuantityAccepted;
          PODetail::where('PONo',$request->PONo)->where('Description',$frompodetail->Description)->update(['QtyValidator'=>$newValidatorQty]);
        }
      }
    }
    RRconfirmationDetails::insert($ForRRconfirmItemsDB);
    Signatureable::insert($forSignatures);
    Session::forget('RR-Items-Added');

    $NotifableName = array('first' =>$request->Verifiedby,'second'=>$request->ReceivedOriginalby,'third'=>$request->PostedtoBINby);
    $NotifableName=(object)$NotifableName;
    $job = (new NewCreatedRRJob($NotifableName))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    $MobileOfReceiver=User::where('id',$request->Receivedby)->value('Mobile');
    $AlertForReceiver = array('RRNo'=>$incremented,'Mobile'=>$MobileOfReceiver,'RVNo'=>$request->RVNo);
    $AlertForReceiver=(object)$AlertForReceiver;
    $job = (new RRNewAlertReceiver($AlertForReceiver))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for receiver
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->Receivedby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->Receivedby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for verified
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->Verifiedby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->Verifiedby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for received original by
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->ReceivedOriginalby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->ReceivedOriginalby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    // global notification for posted to BIN by
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->PostedtoBINby;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$incremented;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // global notif trigger
    $ReceiverID = array('id' =>$request->PostedtoBINby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
    return ['redirect'=>route('RRfullpreview',[$incremented])];
  }
  public function StoringRRTableNoPOValidator($request)
  {
    $this->validate($request,[
      'Supplier'=>'required|max:50',
      'Address'=>'required|max:30',
      'InvoiceNo'=>'max:12',
      'RVNo'=>'required',
      'Carrier'=>'max:30',
      'DeliveryReceiptNo'=>'max:12',
      'Note'=>'max:50',
      'Receivedby'=>'required',
      'Verifiedby'=>'required',
      'ReceivedOriginalby'=>'required',
      'PostedtoBINby'=>'required',
    ]);
  }
  public function StoringRRTableWithPOValidator($request)
  {
    $this->validate($request,[
      'InvoiceNo'=>'max:12|required',
      'Carrier'=>'max:30',
      'DeliveryReceiptNo'=>'max:12|required',
      'Note'=>'max:50',
      'Receivedby'=>'required',
      'Verifiedby'=>'required',
      'ReceivedOriginalby'=>'required',
      'PostedtoBINby'=>'required',
    ]);
  }
  public function RRindex()
  {
    return view('Warehouse.RR.RRindex');
  }
  public function RRindexSearchAndFetch(Request $request)
  {
    return RRMaster::with('users')->where('RRNo','LIKE','%'.$request->RRNo.'%')->orderBy('RRNo','DESC')->paginate(10,['RRNo','Supplier','RRDate','Status','IsRollBack']);
  }
  public function previewRR($id)
  {
    $RRNumber= array('RRNo' =>$id);
    $RRNumber=json_encode($RRNumber);
    return view('Warehouse.RR.RRfullpreview',compact('RRNumber'));
  }
  public function previewRRfetchdata($id)
  {
    $RRconfirmationDetails=RRconfirmationDetails::where('RRNo',$id)->get(['ItemCode','Unit','Description','RRQuantityDelivered','QuantityAccepted','UnitCost','Amount']);
    $Netsales=$RRconfirmationDetails->sum('Amount');
    $VAT=$Netsales*.12;
    $TOTALamt=$Netsales+$VAT;
    $RRMaster=RRMaster::with('users')->where('RRNo',$id)->with('creator')->get();
    $checkMR=MRMaster::orderBy('RRNo','DESC')->where('RRNo',$id)->take(1)->get(['MRNo']);
    $response = array('RRconfirmationDetails' =>$RRconfirmationDetails ,'Netsales'=>$Netsales,'VAT'=>$VAT,'TOTALamt'=>$TOTALamt,'RRMaster'=>$RRMaster,'checkMR'=>$checkMR);
    return response()->json($response);
  }
  public function signatureRR($id)
  {
    $RRMaster=RRMaster::with('users')->where('RRNo',$id)->get();
    $Signers = $RRMaster[0]->users;
    switch (Auth::user()->id) {
      case $Signers[0]->pivot->user_id:
        if($Signers[0]->pivot->Signature != null || $RRMaster[0]->Status != null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
      case $Signers[1]->pivot->user_id:
        if($Signers[1]->pivot->Signature != null || $RRMaster[0]->Status != null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
      case $Signers[2]->pivot->user_id:
        if($Signers[2]->pivot->Signature != null || $RRMaster[0]->Status != null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
      case $Signers[3]->pivot->user_id:
        if($Signers[3]->pivot->Signature != null || $RRMaster[0]->Status != null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
    }
    Signatureable::where('signatureable_id',$id)->where('signatureable_type','App\RRMaster')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
    if(($RRMaster[0]->users[0]->pivot->Signature =='0')&&($RRMaster[0]->users[1]->pivot->Signature =='0')&&($RRMaster[0]->users[2]->pivot->Signature =='0')&&($RRMaster[0]->users[3]->pivot->Signature =='0'))
    {
      RRMaster::where('RRNo', $id)->update(['Status'=>'0']);
      $RRconfirmDetails=RRconfirmationDetails::where('RRNo',$id)->get();
      $forMTDtable = array();
      $date=RRMaster::where('RRNo', $id)->get(['RRDate']);
      foreach ($RRconfirmDetails as $fromconfirmDetail)
      {
        if ($fromconfirmDetail->ItemCode)
        {
          $MTLatestDetail=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $fromconfirmDetail->ItemCode)->take(1)->get();
          $Amount=$fromconfirmDetail->UnitCost*$fromconfirmDetail->QuantityAccepted;
          $newQuantity=$MTLatestDetail[0]->CurrentQuantity + $fromconfirmDetail->QuantityAccepted;
          $addedAMT=$MTLatestDetail[0]->CurrentAmount + $Amount;
          $newCost=$addedAMT/$newQuantity;
          $currentAMT=$newQuantity*$newCost;
          MasterItem::where('ItemCode',$fromconfirmDetail->ItemCode)->update(['CurrentQuantity'=>$newQuantity]);
          $forMTDtable[] = array('ItemCode' =>$fromconfirmDetail->ItemCode ,'MTType' =>'RR' ,'MTNo' =>$fromconfirmDetail->RRNo ,
          'AccountCode' =>$MTLatestDetail[0]->AccountCode ,
          'UnitCost' =>$fromconfirmDetail->UnitCost,'Quantity' =>$fromconfirmDetail->QuantityAccepted,
          'Amount' =>$Amount ,'CurrentCost' =>$newCost ,'CurrentQuantity'=>$newQuantity,'CurrentAmount'=>$currentAMT,'MTDate'=>$date[0]->RRDate);
        }

      }
      MaterialsTicketDetail::insert($forMTDtable);
      if ($RRMaster[0]->PONo!=null)
      {
        $POofRV=POMaster::where('RVNo',$RRMaster[0]->RVNo)->get(['PONo']);
        $unpurchasedtotal=0;
        foreach ($POofRV as $POofrv)
        {
          $Qtyleft=PODetail::where('PONo', $POofrv->PONo)->get(['QtyValidator']);
          $unpurchasedtotal=$unpurchasedtotal + $Qtyleft[0]->QtyValidator;
        }
        if ($unpurchasedtotal==0)
        {
          RVMaster::where('RVNo',$RRMaster[0]->RVNo)->update(['IfPurchased'=>'0']);
        }
      }else
      {
        $remainingQuantity=RVDetail::where('RVNo',$RRMaster[0]->RVNo)->sum('QuantityValidator');
        if ($remainingQuantity==0)
        {
          RVMaster::where('RVNo',$RRMaster[0]->RVNo)->update(['IfPurchased'=>'0']);
        }
      }

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $RRMaster[0]->CreatorID;
      $NotificationTbl->NotificationType = 'Approved';
      $NotificationTbl->FileType = 'RR';
      $NotificationTbl->FileNo =$id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // notify warehouseman the creator
      $ReceiverID = array('id' =>$RRMaster[0]->CreatorID);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
  }
  public function RRsignatureRequest()
  {
    $requestRR=Auth::user()->RRSignatureTurn()->paginate(10,['RRNo','RRDate','Supplier','Address','RVNo']);
    return view('Warehouse.RR.myRRrequest',compact('requestRR'));
  }
  public function declineRR($id)
  {
    $RRMaster=RRMaster::where('RRNo',$id)->with('users')->get(['PONo','RVNo','RRNo','CreatorID','Status']);
    $Signers = $RRMaster[0]->users;
    switch (Auth::user()->id) {
      case $Signers[0]->pivot->user_id:
        if($Signers[0]->pivot->Signature != null || $RRMaster[0]->Status !=null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
      case $Signers[1]->pivot->user_id:
        if($Signers[1]->pivot->Signature != null || $RRMaster[0]->Status !=null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
      case $Signers[2]->pivot->user_id:
        if($Signers[2]->pivot->Signature != null || $RRMaster[0]->Status !=null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
      case $Signers[3]->pivot->user_id:
        if($Signers[3]->pivot->Signature != null || $RRMaster[0]->Status !=null)
        {
          return ['error'=>'Refreshed'];
        }
        break;
    }
    Signatureable::where('signatureable_id',$id)->where('signatureable_type','App\RRMaster')->where('user_id', Auth::user()->id)->update(['Signature'=>'1']);
    RRMaster::where('RRNo', $id)->update(['Status'=>'1']);
    if ($RRMaster[0]->PONo!=null)
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$id)->get(['Description','QuantityAccepted']);
      $FromPODetail=PODetail::where('PONo', $RRMaster[0]->PONo)->get(['Description','QtyValidator']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($FromPODetail as $frompodetail)
        {
          if ($frompodetail->Description==$canceldata->Description)
          {
            $NewQty=$frompodetail->QtyValidator + $canceldata->QuantityAccepted;
            PODetail::where('PONo', $RRMaster[0]->PONo)->where('Description', $frompodetail->Description)->update(['QtyValidator'=>$NewQty]);
          }
        }
      }
    }else
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$id)->get(['Description','QuantityAccepted']);
      $RVDetail=RVDetail::where('RVNo', $RRMaster[0]->RVNo)->get(['Particulars','QuantityValidator']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($RVDetail as $rvdetail)
        {
          if ($rvdetail->Particulars==$canceldata->Description)
          {
            $NewQty=$rvdetail->QuantityValidator + $canceldata->QuantityAccepted;
            RVDetail::where('RVNo',$RRMaster[0]->RVNo)->where('Particulars', $rvdetail->Particulars)->update(['QuantityValidator'=>$NewQty]);
          }
        }
      }
    }
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $RRMaster[0]->CreatorID;
    $NotificationTbl->NotificationType = 'Declined';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();
    // notify warehouseman the creator
    $ReceiverID = array('id' =>$RRMaster[0]->CreatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function displayRRcurrentSession()
  {
    $fromsession=Session::get('RR-Items-Added');
    $response=[
      'sessions'=> $fromsession
    ];
    return response()->json($response);
  }
  public function CreateRRNoPO($id)
  {
    Session::forget('RRSessionDataNoPO');
    $Auditors=User::where('Role', '5')->whereNotNull('IsActive')->get(['id','FullName']);
    $Managers=User::where('Role','0')->whereNotNull('IsActive')->get(['id','FullName']);
    $Clerks=User::where('Role','6')->whereNotNull('IsActive')->get(['id','FullName']);
    $AllActiveUsers=User::where('IsActive','0')->get(['id','FullName']);
    $fromRVDetail=RVDetail::where('RVNo',$id)->get(['RVNo','Particulars','Unit','Remarks','ItemCode','AccountCode']);
    return view('Warehouse.RR.CreateRRNoPO',compact('fromRVDetail','Auditors','AllActiveUsers','Managers','Clerks'));
  }
  public function CreateRRWithPO($id)
  {
    Session::forget('RR-Items-Added');
    $Auditors=User::where('Role', '5')->whereNotNull('IsActive')->get(['id','FullName']);
    $Managers=User::where('Role','0')->whereNotNull('IsActive')->get(['id','FullName']);
    $Clerks=User::where('Role','6')->whereNotNull('IsActive')->get(['id','FullName']);
    $AllActiveUsers=User::where('IsActive','0')->get(['id','FullName']);
    $POMaster=POMaster::where('PONo',$id)->get(['Supplier','Address','RVNo','PONo']);
    $fromPODetail=PODetail::where('PONo',$id)->get(['Price','Unit','Description','Amount','PONo','ItemCode','AccountCode']);
    return view('Warehouse.RR.CreateRRWithPO',compact('POMaster','fromPODetail','Auditors','Managers','Clerks','AllActiveUsers'));
  }
  public function RRofRVlist($id)
  {
    $RRofRV=RRMaster::where('RVNo',$id)->paginate(9,['RRNo','RVNo','RRDate','Supplier','Address','Status','IsRollBack']);
    return view('Warehouse.RR.RRlistOfRV',compact('RRofRV'));
  }
  public function RRofPOlist($poNum)
  {
    $PONum = array('PONumber' =>$poNum);
    $PONum=(object)$PONum;
    $RRofPO=RRMaster::orderBy('RRNo','DESC')->where('PONo',$poNum)->paginate(9,['RRNo','RRDate','Supplier','Address','Status','IsRollBack']);
    return view('Warehouse.RR.RRlistofPO',compact('RRofPO','PONum'));
  }
  public function refreshRRSignatureCount()
  {
    $requestRR=Auth::user()->RRSignatureTurn()->count();

    $response = [
      'RRrequestCount' =>$requestRR
    ];
    return response()->json($response);
  }
  public function RollBack($rrNo)
  {
    $RRMaster=RRMaster::where('RRNo',$rrNo)->get(['PONo','RVNo','CreatorID','Status','IsRollBack']);
    if($RRMaster[0]->Status == null || $RRMaster[0]->IsRollBack == '0')
    {
      return ['error'=>'Refreshed'];
    }
    $dataToRollBack=MaterialsTicketDetail::where('MTType', 'RR')->where('MTNo', $rrNo)->whereNull('IsRollBack')->get();
    RRMaster::where('RRNo',$rrNo)->update(['IsRollBack'=>'0']);
    foreach ($dataToRollBack as $data)
    {
      $idOfRRHistory = MaterialsTicketDetail::where('MTType', 'RR')->where('MTNo', $rrNo)->where('ItemCode',$data->ItemCode)->value('id');
      MaterialsTicketDetail::where('MTType', 'RR')->where('MTNo', $rrNo)->where('ItemCode',$data->ItemCode)->whereNull('IsRollBack')->update(['IsRollBack'=>'0']);
      $affectedRows = MaterialsTicketDetail::where('ItemCode',$data->ItemCode)->whereNull('IsRollBack')->where('id','>',$idOfRRHistory)
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
    // rollback the validator too
    if ($RRMaster[0]->PONo!=null)
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$rrNo)->get(['Description','QuantityAccepted']);
      $FromPODetail=PODetail::where('PONo', $RRMaster[0]->PONo)->get(['Description','QtyValidator']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($FromPODetail as $frompodetail)
        {
          if ($frompodetail->Description==$canceldata->Description)
          {
            $NewQty=$frompodetail->QtyValidator + $canceldata->QuantityAccepted;
            PODetail::where('PONo', $RRMaster[0]->PONo)->where('Description', $frompodetail->Description)->update(['QtyValidator'=>$NewQty]);
          }
        }
      }
    }else
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$rrNo)->get(['Description','QuantityAccepted']);
      $RVDetail=RVDetail::where('RVNo', $RRMaster[0]->RVNo)->get(['Particulars','QuantityValidator']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($RVDetail as $rvdetail)
        {
          if ($rvdetail->Particulars==$canceldata->Description)
          {
            $NewQty=$rvdetail->QuantityValidator + $canceldata->QuantityAccepted;
            RVDetail::where('RVNo',$RRMaster[0]->RVNo)->where('Particulars', $rvdetail->Particulars)->update(['QuantityValidator'=>$NewQty]);
          }
        }
      }
    }
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $RRMaster[0]->CreatorID;
    $NotificationTbl->NotificationType = 'Invalid';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$rrNo;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // notify warehouseman the creator
    $ReceiverID = array('id' =>$RRMaster[0]->CreatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function UndoRollBack($rrNo)
  {
    $RRMaster=RRMaster::where('RRNo',$rrNo)->get(['PONo','RVNo','CreatorID','Status','IsRollBack']);
    if($RRMaster[0]->Status == null || $RRMaster[0]->IsRollBack != '0')
    {
      return ['error'=>'Refreshed'];
    }
    $dataToRollBack=MaterialsTicketDetail::where('MTType', 'RR')->where('MTNo', $rrNo)->get();
    RRMaster::where('RRNo',$rrNo)->update(['IsRollBack'=>'1']);
    foreach ($dataToRollBack as $data)
    {
      MaterialsTicketDetail::where('MTType', 'RR')->where('MTNo', $rrNo)->where('ItemCode',$data->ItemCode)->update(['IsRollBack'=>NULL]);
      $idOfRRHistory = MaterialsTicketDetail::where('MTType', 'RR')->where('MTNo', $rrNo)->where('ItemCode',$data->ItemCode)->value('id');
      $affectedRows = MaterialsTicketDetail::where('ItemCode',$data->ItemCode)->whereNull('IsRollBack')->where('id','>',$idOfRRHistory)
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

    // undo the rollbacked item validator
    if ($RRMaster[0]->PONo!=null)
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$rrNo)->get(['Description','QuantityAccepted']);
      $FromPODetail=PODetail::where('PONo', $RRMaster[0]->PONo)->get(['Description','QtyValidator']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($FromPODetail as $frompodetail)
        {
          if ($frompodetail->Description==$canceldata->Description)
          {
            $NewQty=$frompodetail->QtyValidator - $canceldata->QuantityAccepted;
            PODetail::where('PONo', $RRMaster[0]->PONo)->where('Description', $frompodetail->Description)->update(['QtyValidator'=>$NewQty]);
          }
        }
      }
    }else
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$rrNo)->get(['Description','QuantityAccepted']);
      $RVDetail=RVDetail::where('RVNo', $RRMaster[0]->RVNo)->get(['Particulars','QuantityValidator']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($RVDetail as $rvdetail)
        {
          if ($rvdetail->Particulars==$canceldata->Description)
          {
            $NewQty=$rvdetail->QuantityValidator - $canceldata->QuantityAccepted;
            RVDetail::where('RVNo',$RRMaster[0]->RVNo)->where('Particulars', $rvdetail->Particulars)->update(['QuantityValidator'=>$NewQty]);
          }
        }
      }
    }
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $RRMaster[0]->CreatorID;
    $NotificationTbl->NotificationType = 'UndoInvalid';
    $NotificationTbl->FileType = 'RR';
    $NotificationTbl->FileNo =$rrNo;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // notify warehouseman the creator
    $ReceiverID = array('id' =>$RRMaster[0]->CreatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
}
