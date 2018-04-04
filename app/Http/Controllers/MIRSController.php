<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\MIRSDetail;
use App\MIRSMaster;
use App\MaterialsTicketDetail;
use DB;
use App\User;
use App\MCTMaster;
use Auth;
use Redis;
use App\Jobs\SendMIRSNotification;
use App\Jobs\NewApprovedMIRSJob;
use App\Jobs\MIRSApprovalReplacer;
use App\Jobs\MIRSManagerReplacer;
use App\Signatureable;
use App\Jobs\SMSDeclinedMIRS;
use App\Jobs\GlobalNotifJob;
use App\MasterItem;
use App\Notification;
class MIRSController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function MIRScreate()
  {
    Session::forget('ItemSelected');
    $GenMan=User::orderBy('id','DESC')->where('Role','2')->whereNotNull('IsActive')->take(1)->get(['FullName','id']);
    $mymanager=User::where('id',Auth::user()->Manager)->get(['FullName']);
    return view('Warehouse.MIRS.MIRSCreate',compact('mymanager','GenMan'));
  }
  public function fetchSessionMIRS()
  {
      $items=Session::get('ItemSelected');
      if (isset($items))
      {
        return array_reverse($items);
      }
  }
  public function addingSessionItem(Request $request)
  {
    $this->SessionValidator($request);
    $MTDetails=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode',$request->ItemCode)->value('CurrentQuantity');
    if($MTDetails >=$request->Quantity)
    {
      $itemselected =[
      'ItemCode' => $request->ItemCode,'Particulars' => $request->Particulars,'Unit' => $request->Unit,'Remarks'=>$request->Remarks,'Quantity' => $request->Quantity,
      ];
      if (Session::has('ItemSelected'))
      {
        foreach (Session::get('ItemSelected') as $selected)
        {
          if ($selected->ItemCode == $request->ItemCode) {
            return response()->json(['error'=>'This Item has been added already']);
          }
        }
      }
        $itemselected = (object)$itemselected;
        Session::push('ItemSelected',$itemselected);
    }else
    {
      return response()->json(['error'=>'Quantity stock is not enough for your request']);
    }
  }
  public function deletePartSession($id)
  {
    if(Session::has('ItemSelected'))
    {
      $items=(array)Session::get('ItemSelected');
      foreach ($items as $key=>$item)
      {
        if ($item->ItemCode == $id)
        {
          unset($items[$key]);
        }
      }
      Session::put('ItemSelected',$items);
    }
  }
  public function SessionValidator($request)
  {
    return $this->validate($request,[
      'Quantity' => 'Required|Integer|min:1',
      'Remarks' => 'max:50',
    ]);
  }
  public function StoringMIRS(Request $request)
  {
    $this->storingMIRSValidator($request);
    if (count(Session::get('ItemSelected'))>0)
    {
        $date=Carbon::now();
        $year=Carbon::now()->format('y');
        $lastinserted=MIRSMaster::orderBy('MIRSNo','DESC')->take(1)->value('MIRSNo');
        $explodedMIRSNo = explode('-',$lastinserted);
        if ($lastinserted>0 && $explodedMIRSNo[0]==$year)
        {
          $numOnly=substr($lastinserted,'3');
          $numOnly = (int)$numOnly;
          $soloID=$numOnly + 1;
          $incremented = $year .'-' . sprintf("%04d",$soloID);
        }else
        {
            $incremented = $year . '-' . sprintf("%04d",'1');
        }
      $master=new MIRSMaster;
      $master->MIRSNo = $incremented;
      $master->Purpose =$request->Purpose;
      $master->MIRSDate = $date;
      $master->save();

      $ApproveReplacer=User::whereNotNull('IfApproveReplacer')->take(1)->get(['id']);
      if (!empty($ApproveReplacer[0]))
      {
        $forSignatureDB = array(
          array('user_id'=>Auth::user()->id,'Signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'PreparedBy'),
          array('user_id'=>Auth::user()->Manager,'Signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'RecommendedBy'),
          array('user_id'=>$request->Approvedby,'Signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'ApprovedBy'),
          array('user_id'=>$ApproveReplacer[0]->id,'Signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'ApprovalReplacer')
        );
      }else
      {
        $forSignatureDB = array(
          array('user_id'=>Auth::user()->id,'Signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'PreparedBy'),
          array('user_id'=>Auth::user()->Manager,'Signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'RecommendedBy'),
          array('user_id'=>$request->Approvedby,'Signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'ApprovedBy')
        );
      }
      Signatureable::insert($forSignatureDB);

      $selectedITEMS=Session::get('ItemSelected');
      $selectedITEMS = (array)$selectedITEMS;
      $forMIRSDetailtbl = array();
      foreach ($selectedITEMS as $items)
      {
        $forMIRSDetailtbl[] = array('MIRSNo' => $incremented ,'ItemCode'=>$items->ItemCode,'Particulars'=>$items->Particulars,'Remarks'=>$items->Remarks,'Quantity'=>$items->Quantity,'QuantityValidator'=>$items->Quantity,'Unit'=>$items->Unit);
      }
      MIRSDetail::insert($forMIRSDetailtbl);

      Session::forget('ItemSelected');
      return ['redirect'=>route('full-mirs',[$incremented])];
    }else
    {
      return ['error'=>'items cannot be empty'];
    }
  }
  public function storingMIRSValidator($request)
  {
    $this->validate($request,[
      'Purpose'=>'required',
      'Approvedby'=>'required',
    ]);
  }
  public function fullMIRSview($id)
  {
    $MIRSNumber=['MIRSNo'=>$id];
    $MIRSNumber=json_encode($MIRSNumber);
    return view('Warehouse.MIRS.MIRSpreview',compact('MIRSNumber'));
  }
  public function fetchFullMIRSData($id)
  {
    $QuantityValidator=MIRSDetail::where('MIRSNo',$id)->get(['QuantityValidator']);
    $unclaimed=$QuantityValidator->sum('QuantityValidator');
    $MIRSDetail=MIRSDetail::where('MIRSNo', $id)->get();
    $MIRSMaster=MIRSMaster::with('users')->where('MIRSNo', $id)->get();
    $MCTNumber=MCTMaster::where('MIRSNo', $id)->value('MCTNo');
    $response = array(
      'unclaimed' => $unclaimed,
      'MIRSDetail'=>$MIRSDetail,
      'MIRSMaster'=>$MIRSMaster,
      'MCTNumber'=>$MCTNumber,
   );
   return response()->json($response);
  }
  public function searchMIRSNoAndFetch(Request $request)
  {
    return MIRSMaster::with('users')->where('MIRSNo','LIKE','%'.$request->MIRSNo.'%')->orderBy('MIRSNo','DESC')->paginate(10,['MIRSNo','Purpose','MIRSDate']);
  }
  public function MIRSIndexPage()
  {
    return view('Warehouse.MIRS.MIRS-index');
  }
  public function DeniedMIRS($id)
  {
    Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('user_id', Auth::user()->id)->update(['Signature'=>'1']);
    MIRSMaster::where('MIRSNo', $id)->update(['Status'=>'1']);
    if (Auth::user()->Role==2)
    {
      Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
    }
    $requisitioner=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->get(['user_id']);
    if ($requisitioner[0]->user_id != Auth::user()->id)
    {
      $requisitionerMobile=User::where('id', $requisitioner[0]->user_id)->value('Mobile');
      $forSMS = array('Decliner' =>Auth::user()->FullName,'requisitionerMobile'=>$requisitionerMobile,'MIRSNo'=>$id);
      $forSMS=(object)$forSMS;
      $job = (new SMSDeclinedMIRS($forSMS))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }

    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $requisitioner[0]->user_id;
    $NotificationTbl->NotificationType = 'Declined';
    $NotificationTbl->FileType = 'MIRS';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$requisitioner[0]->user_id);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

  }
  public function MIRSSignature($id)
  {
    $PreparedId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->get(['user_id']);
    $RecommendId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'RecommendedBy')->get(['user_id']);
    $GMId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->get(['user_id']);
    $ApprovalReplacerId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->get(['user_id']);

    if ($PreparedId[0]->user_id==Auth::user()->id)
    {
      Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->update(['Signature'=>'0']);
      MIRSMaster::where('MIRSNo', $id)->update(['SignatureTurn'=>'1']);

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = Auth::user()->Manager;
      $NotificationTbl->NotificationType = 'Request';
      $NotificationTbl->FileType = 'MIRS';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>Auth::user()->Manager);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);

      $newmirs = array('tobeNotify'=>Auth::user()->Manager);
      $newmirs=(object)$newmirs;
      $job = (new SendMIRSNotification($newmirs))
                  ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }

    if ($RecommendId[0]->user_id==Auth::user()->id)
    {
      Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ManagerReplacer')->delete();
      Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'RecommendedBy')->update(['Signature'=>'0']);
      MIRSMaster::where('MIRSNo', $id)->update(['SignatureTurn'=>'2']);

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $GMId[0]->user_id;
      $NotificationTbl->NotificationType = 'Request';
      $NotificationTbl->FileType = 'MIRS';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$GMId[0]->user_id);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);

      $tobeNotifycontainer  = array('tobeNotify' =>$GMId[0]->user_id);
      $tobeNotifycontainer=(object)$tobeNotifycontainer;
      $job = (new SendMIRSNotification($tobeNotifycontainer))
                  ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
      if (!empty($ApprovalReplacerId[0]))
      {
        $NotificationTbl = new Notification;
        $NotificationTbl->user_id = $ApprovalReplacerId[0]->user_id;
        $NotificationTbl->NotificationType = 'Request';
        $NotificationTbl->FileType = 'MIRS';
        $NotificationTbl->FileNo = $id;
        $NotificationTbl->TimeNotified = Carbon::now();
        $NotificationTbl->save();

        // global notif trigger
        $ReceiverID = array('id' =>$ApprovalReplacerId[0]->user_id);
        $ReceiverID = (object)$ReceiverID;
        $job = (new GlobalNotifJob($ReceiverID))
        ->delay(Carbon::now()->addSeconds(5));
        dispatch($job);

        $tobeNotifycontainer  = array('tobeNotify' =>$ApprovalReplacerId[0]->user_id);
        $tobeNotifycontainer=(object)$tobeNotifycontainer;
        $job = (new SendMIRSNotification($tobeNotifycontainer))
                    ->delay(Carbon::now()->addSeconds(5));
        dispatch($job);

      }
    }
    if ($GMId[0]->user_id==Auth::user()->id)
    {
      Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
      Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->update(['Signature'=>'0']);
      MIRSMaster::where('MIRSNo', $id)->update(['Status'=>'0','SignatureTurn'=>'3']);
      $RequisitionerMobile=User::where('id',$PreparedId[0]->user_id)->get(['Mobile']);
      $NotifData = array('RequisitionerMobile' =>$RequisitionerMobile[0]->Mobile ,'MIRSNo'=>$id);
      $NotifData=(object)$NotifData;
      $job=(new NewApprovedMIRSJob($NotifData))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $PreparedId[0]->user_id;
      $NotificationTbl->NotificationType = 'Approved';
      $NotificationTbl->FileType = 'MIRS';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();
      // global notif trigger
      $ReceiverID = array('id' =>$PreparedId[0]->user_id);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
  }
  public function mirsRequestcheck()
  {
    $user=User::find(Auth::user()->id);
    $myrequestMIRS = $user->MIRSSignatureTurn()->paginate(10);
    return view('Warehouse.MIRS.myMIRSrequest',compact('myrequestMIRS'));
  }
  public function CancelApproveMIRSinBehalf($id)
  {
    Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
    $preparedby=Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->value('user_id');

    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $preparedby;
    $NotificationTbl->NotificationType = 'Refused';
    $NotificationTbl->FileType = 'MIRS';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$preparedby);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

  }
  public function AcceptApprovalRequest($id)
  {
    $MIRSStatus=MIRSMaster::where('MIRSNo', $id)->get(['Status']);
    if ($MIRSStatus[0]->Status!=null)
    {
      return ['success'=>'success'];
    }
    Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->update(['Signature'=>'0']);
    MIRSMaster::where('MIRSNo', $id)->update(['Status'=>'0','SignatureTurn'=>'3']);
    $PreparedId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->get(['user_id']);
    $GMId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->get(['user_id']);

    $RequisitionerMobile=User::where('id',$PreparedId[0]->user_id)->get(['Mobile']);
    $GMMobile=User::where('id',$GMId[0]->user_id)->get(['Mobile']);
    $NotifData = array('RequisitionerMobile' =>$RequisitionerMobile[0]->Mobile ,'MIRSNo'=>$id,'GMMobile'=>$GMMobile[0]->Mobile,'ApprovalReplacer'=>Auth::user()->FullName);
    $NotifData=(object)$NotifData;
    $job=(new MIRSApprovalReplacer($NotifData))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $PreparedId[0]->user_id;
    $NotificationTbl->NotificationType = 'Approved';
    $NotificationTbl->FileType = 'MIRS';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    //global notif trigger
    $ReceiverID = array('id' =>$PreparedId[0]->user_id);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $GMId[0]->user_id;
    $NotificationTbl->NotificationType = 'Replaced';
    $NotificationTbl->FileType = 'MIRS';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$GMId[0]->user_id);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
    return ['success'=>'success'];
  }
  public function fetchAllManager()
  {
    return User::whereNotNull('IsActive')->where('Role',0)->get(['FullName','id']);
  }
  public function SendRequestManagerReplacer($id,Request $request)
  {
    if (empty($request->ManagerReplacerID))
    {
      return ['error'=>'Required'];
    }
    $signatureDB=new Signatureable;
    $signatureDB->user_id = $request->ManagerReplacerID;
    $signatureDB->Signatureable_id = $id;
    $signatureDB->signatureable_type = 'App\MIRSMaster';
    $signatureDB->SignatureType= 'ManagerReplacer';
    $signatureDB->save();
    $notify = array('tobeNotify' =>$request->ManagerReplacerID);
    $notify=(object)$notify;
    $job = (new SendMIRSNotification($notify))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $request->ManagerReplacerID;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'MIRS';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$request->ManagerReplacerID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function CancelRequestManagerReplacer($id)
  {
    MIRSMaster::where('MIRSNo',$id)->update(['SignatureTurn'=>'1']);
    $replacer=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ManagerReplacer')->value('user_id');
    Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ManagerReplacer')->delete();
    $preparedby=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->value('user_id');
    if ($preparedby != Auth::user()->id)
    {
      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $preparedby;
      $NotificationTbl->NotificationType = 'Refused';
      $NotificationTbl->FileType = 'MIRS';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$preparedby);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }else
    {
      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $replacer;
      $NotificationTbl->NotificationType = 'Canceled';
      $NotificationTbl->FileType = 'MIRS';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$replacer);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
  }
  public function SignatureManagerReplacer($id)
  {
    Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ManagerReplacer')->update(['Signature'=>'0']);
    MIRSMaster::where('MIRSNo', $id)->update(['SignatureTurn'=>'2']);
    $GMId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->get(['user_id']);
    $ApprovalReplacerId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->get(['user_id']);
    if (!empty($ApprovalReplacerId[0]))
    {
      $notifyname = array('tobeNotify' =>$ApprovalReplacerId[0]->user_id);
      $notifyname=(object)$notifyname;
      $job = (new SendMIRSNotification($notifyname))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $ApprovalReplacerId[0]->user_id;
      $NotificationTbl->NotificationType = 'Request';
      $NotificationTbl->FileType = 'MIRS';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$ApprovalReplacerId[0]->user_id);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $GMId[0]->user_id;
    $NotificationTbl->NotificationType = 'Request';
    $NotificationTbl->FileType = 'MIRS';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$GMId[0]->user_id);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    $GMid = array('tobeNotify' => $GMId[0]->user_id);
    $GMid=(object)$GMid;
    $job = (new SendMIRSNotification($GMid))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    //smsAlert
    $RealSignaturerId=Signatureable::where('Signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'RecommendedBy')->value('user_id');
    $RealSignaturerMobile=User::where('id', $RealSignaturerId)->value('Mobile');
    $data = array('Mobile' =>$RealSignaturerMobile, 'MIRSNo'=>$id,'Replacer'=>Auth::user()->FullName);
    $data=(object)$data;
    $job = (new MIRSManagerReplacer($data))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

    $NotificationTbl = new Notification;
    $NotificationTbl->user_id = $RealSignaturerId;
    $NotificationTbl->NotificationType = 'Replaced';
    $NotificationTbl->FileType = 'MIRS';
    $NotificationTbl->FileNo = $id;
    $NotificationTbl->TimeNotified = Carbon::now();
    $NotificationTbl->save();

    // global notif trigger
    $ReceiverID = array('id' =>$RealSignaturerId);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);

  }
  public function MIRSNotification()
  {
    $user=User::find(Auth::user()->id);
    $myrequestMIRS = $user->MIRSSignatureTurn()->count();
    $response = ['MIRSrequest' =>$myrequestMIRS];
    return response()->json($response);
  }
  public function QuickUpdate(Request $request,$mirsNo)
  {
    $this->validate($request,[
      'purpose'=>'required|max:100',
      'remarks.*'=>'max:100'
    ]);
    foreach ($request->Qty as $loopingCount => $itemQuantity)
    {
      $INumber = 0;
      $INumber = $loopingCount+1;
      $itemQuantity =$itemQuantity + 0;
      if ($itemQuantity=='')
      {

        return ['error'=>'Item number '.$INumber.' Qty cannot be empty'];
      }elseif (is_int($itemQuantity) == false)
      {
        return ['error' => 'Item number '.$INumber.' Qty must be a number/integer'];
      }elseif ($itemQuantity < 1)
      {
        return ['error' => 'Qty must be atleast 1'];
      }
    }

    $tobeUpdated = MIRSDetail::where('MIRSNo', $mirsNo)->get(['id','ItemCode','Quantity','Remarks']);
    $MIRSMasterCurrent = MIRSMaster::where('MIRSNo', $mirsNo)->get(['Purpose','Status']);
    if ($MIRSMasterCurrent[0]->Status!=NULL)
    {
      return ['error'=>'Refreshed'];
    }
    $hasChanges = false;
    foreach ($tobeUpdated as $countkey => $data)
    {
      $currentQtyOfItem = MasterItem::where('ItemCode', $data->ItemCode)->value('CurrentQuantity');
      if ($currentQtyOfItem < $request->Qty[$countkey])
      {
        return ['error'=>'Sorry warehouse stocks is not enough'];
      }
      if ($data->Quantity != $request->Qty[$countkey]||$data->Remarks!=$request->remarks[$countkey])
      {
        $hasChanges =true;
      }
    }
    if ($MIRSMasterCurrent[0]->Purpose != $request->purpose)
    {
      $hasChanges =true;
    }
    if ($hasChanges == false)
    {
      return ['error'=>'No changes found'];
    }

    MIRSMaster::where('MIRSNo', $mirsNo)->update(['Purpose'=>$request->purpose,'SignatureTurn'=>0]);
    Signatureable::where('signatureable_type','App\MIRSMaster')->where('Signatureable_id',$mirsNo)->update(['Signature'=>NULL]);
    Signatureable::where('signatureable_type','App\MIRSMaster')->where('Signatureable_id',$mirsNo)->where('SignatureType','ManagerReplacer')->delete();
    foreach ($tobeUpdated as $key => $dataToUpdate)
    {
      MIRSDetail::where('id', $dataToUpdate->id)->update(['Quantity'=>$request->Qty[$key],'QuantityValidator'=>$request->Qty[$key],'Remarks'=>$request->remarks[$key]]);
    }
    $toAlertId=Signatureable::where('signatureable_type','App\MIRSMaster')->where('Signatureable_id',$mirsNo)->where('SignatureType','!=', 'PreparedBy')->get(['user_id']);

    foreach ($toAlertId as $key => $user)
    {
      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $user->user_id;
      $NotificationTbl->NotificationType = 'Updated';
      $NotificationTbl->FileType = 'MIRS';
      $NotificationTbl->FileNo = $mirsNo;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$user->user_id);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }

  }
}
