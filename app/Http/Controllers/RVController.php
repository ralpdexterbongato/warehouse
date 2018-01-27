<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\User;
use App\RVMaster;
use App\RVDetail;
use Carbon\Carbon;
use App\MasterItem;
use App\POMaster;
use Auth;
use App\RRMaster;
use App\MaterialsTicketDetail;
use App\Jobs\NewRVCreatedJob;
use App\Jobs\NewRVApprovedJob;
use App\Jobs\RVApprovalReplacer;
use App\Jobs\RVManagerReplacer;
use App\Jobs\SMSDeclinedRV;
use App\Signatureable;
class RVController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    public function RVcreate()
    {
      Session::forget('ItemSessionList');
      $mymanager=User::where('id', Auth::user()->Manager)->get(['FullName']);
      $currentBudgetOfficer=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '7')->take(1)->get(['FullName']);
      $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '2')->take(1)->get(['FullName']);
      return view('Warehouse.RV.RVCreateViews',compact('GM','mymanager','currentBudgetOfficer'));
    }
    public function SaveSession(Request $request)
    {
      $this->validate($request,[
        'Description'=>'required|unique:MasterItems',
        'Unit'=>'required',
        'Quantity'=>'required'
      ]);

      if (Session::get('ItemSessionList'))
      {
        foreach (Session::get('ItemSessionList') as $items)
        {
          if ($items->Description==$request->Description)
          {
            return ['error'=>'Items cannot have thesame description'];
          }
        }
      }
      $itemDetails = array('Description' =>$request->Description ,'Unit'=>$request->Unit,'Quantity'=>$request->Quantity,'Remarks'=>$request->Remarks,'AccountCode'=>null,'ItemCode'=>null);
      $itemDetails=(object)$itemDetails;
      Session::push('ItemSessionList',$itemDetails);
    }
    public function DeleteSession($id)
    {
      $itemList=Session::get('ItemSessionList');
      unset($itemList[$id]);
      Session::put('ItemSessionList',$itemList);
    }
    public function savingToTable(Request $request)
    {
      $this->validate($request,[
        'Purpose'=> 'required',
        'BudgetAvailable'=>'max:50',
      ]);
      if (Session::get('ItemSessionList')==null)
      {
        return ['error'=>'Item is Required'];
      }
      $currentBudgetOfficer=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '7')->take(1)->get(['id']);//also using this at the bottom for RVdetails
      $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '2')->take(1)->get(['id']);//also using this at the bottom for RVdetails
      if (empty($currentBudgetOfficer[0]))
      {
        return ['error'=>'Budget Officer cannot be empty'];
      }
      if (empty($GM[0]))
      {
        return ['error'=>'General Manager cannot be empty'];
      }
      $year=Carbon::now()->format('y');
      $date=Carbon::now();
      $currentRVid=RVMaster::orderBy('id','DESC')->take(1)->value('RVNo');
      $explodedRV = explode('-',$currentRVid);
      if ($currentRVid!=null && $year == $explodedRV[0])
      {
        $numOnly=substr($currentRVid,'3');
        $numOnly=(int)$numOnly;
        $newID=$numOnly+1;
        $incremented=$year.'-'.sprintf("%04d",$newID);
      }else
      {
        $incremented=$year.'-'.sprintf("%04d",'1');
      }
      $RVMaster=new RVMaster;
      $RVMaster->RVNo=$incremented;
      $RVMaster->RVDate=$date;
      $RVMaster->Purpose=$request->Purpose;
      $RVMaster->notification_date_time = $date;
      $RVMaster->save();
      $approveReplacerId=User::whereNotNull('IfApproveReplacer')->get(['id']);
      if (!empty($approveReplacerId[0]))
      {
        $forSignatureableTbl = array(
          array('user_id' =>Auth::user()->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'Requisitioner'),
          array('user_id' =>Auth::user()->Manager,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'RecommendedBy'),
          array('user_id' =>$currentBudgetOfficer[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'BudgetOfficer'),
          array('user_id' =>$GM[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'ApprovedBy'),
          array('user_id' =>$approveReplacerId[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'ApprovalReplacer')
        );
      }else
      {
        $forSignatureableTbl = array(
          array('user_id' =>Auth::user()->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'Requisitioner'),
          array('user_id' =>Auth::user()->Manager,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'RecommendedBy'),
          array('user_id' =>$currentBudgetOfficer[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'BudgetOfficer'),
          array('user_id' =>$GM[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\RVMaster','SignatureType'=>'ApprovedBy')
        );
      }
      $forRVdetailDB = array();
      foreach (Session::get('ItemSessionList') as $SessionItem)
      {
        $forRVdetailDB[] = array('RVNo' =>$incremented ,'Particulars'=>$SessionItem->Description,'Unit'=>$SessionItem->Unit,'Quantity'=>$SessionItem->Quantity,'QuantityValidator'=>$SessionItem->Quantity,'Remarks'=>$SessionItem->Remarks,'AccountCode'=>$SessionItem->AccountCode,'ItemCode'=>$SessionItem->ItemCode);
      }
      Signatureable::insert($forSignatureableTbl);
      RVDetail::insert($forRVdetailDB);
      Session::forget('ItemSessionList');
      Session::forget('SessionForStock');
      return ['redirect' => route('RVfullpreviewing',[$incremented])];
    }
    public function RVindexView()
    {
      return view('Warehouse.RV.RVindex');
    }
    public function RVfullPreview($id)
    {
      $RVNumber = array('RVNo' =>$id);
      $RVNumber=json_encode($RVNumber);
      return view('Warehouse.RV.FullRVpreview',compact('RVNumber'));
    }
    public function RVfullpreviewFetchData($id)
    {
      $RVDetails=RVDetail::where('RVNo',$id)->get(['RVNo','Particulars','Unit','Quantity','Remarks']);
      $RVMaster=RVMaster::with('users')->where('RVNo',$id)->get();
      $checkRR=RRMaster::where('RVNo', $id)->take(1)->value('RRNo');
      $checkPO=POMaster::where('RVNo',$id)->take(1)->value('PONo');
      $undeliveredTotal=null;
      if (($checkPO==null)&&($checkRR!=null))
      {
       $undeliveredTotal=RVDetail::where('RVNo',$id)->sum('QuantityValidator');
      }
      $response = array('RVMaster'=>$RVMaster ,'RVDetails'=>$RVDetails,'checkPO'=>$checkPO,'checkRR'=>$checkRR,'undeliveredTotal'=>$undeliveredTotal);
      return response()->json($response);
    }
    public function Signature($id,Request $request)
    {
      $RequisitionerID=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'Requisitioner')->get(['user_id']);
      $RecommendedByID=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'RecommendedBy')->get(['user_id']);
      $BudgetOfficerID=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'BudgetOfficer')->get(['user_id']);
      $ApprovalReplacerID=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'ApprovalReplacer')->get(['user_id']);
      $GMID=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'ApprovedBy')->get(['user_id']);
      $RVMasterTurn=RVMaster::where('RVNo', $id)->value('SignatureTurn');

      if (Auth::user()->id==$RequisitionerID[0]->user_id && $RVMasterTurn==0)
      {
        Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'Requisitioner')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
        RVMaster::where('RVNo', $id)->update(['SignatureTurn'=>'1']);
        $NotifyThisPerson = array('NotificReceiver'=>$RecommendedByID[0]->user_id);
        $NotifyThisPerson=(object)$NotifyThisPerson;
        $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }

      if ($BudgetOfficerID[0]->user_id == Auth::user()->id && $RVMasterTurn==2)
      {
        $this->validate($request,[
            'BudgetAvailable'=>'max:50',
        ]);
        Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'BudgetOfficer')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
        RVMaster::where('RVNo',$id)->update(['BudgetAvailable'=>$request->BudgetAvailable,'SignatureTurn'=>'3']);
        $NotifyThisPerson = array('NotificReceiver' =>$GMID[0]->user_id);
        $NotifyThisPerson=(object)$NotifyThisPerson;
        $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
        if (!empty($ApprovalReplacerID[0]))
        {
          $NotifyThisPerson = array('NotificReceiver' => $ApprovalReplacerID[0]->user_id);
          $NotifyThisPerson=(object)$NotifyThisPerson;
          $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
          dispatch($job);
        }
      }
      if ($RecommendedByID[0]->user_id == Auth::user()->id)
      {
        Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'RecommendedBy')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
        Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'ManagerReplacer')->delete();
        RVMaster::where('RVNo',$id)->update(['SignatureTurn'=>'2']);
        $NotifyThisPerson = array('NotificReceiver' => $BudgetOfficerID[0]->user_id);
        $NotifyThisPerson=(object)$NotifyThisPerson;
        $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
      if ($GMID[0]->user_id == Auth::user()->id)
      {
        Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'ApprovedBy')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
        RVMaster::where('RVNo',$id)->update(['SignatureTurn'=>'4','Status'=>'0','UnreadNotification'=>'0','notification_date_time'=>Carbon::now()]);
        Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'ApprovalReplacer')->delete();

        $requisitionerMobile=User::where('id',$RequisitionerID[0]->user_id)->get(['Mobile']);
        $notifyData = array('RequisitionerMobile' =>$requisitionerMobile[0]->Mobile,'RVNo'=>$id);
        $notifyData=(object)$notifyData;
        $job = (new NewRVApprovedJob($notifyData))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
    }
    public function RVrequest()
    {
    $myRVPendingrequest=Auth::user()->RVSignatureTurn()->paginate(10);
      return view('Warehouse.RV.MyRVrequest',compact('myRVPendingrequest'));
    }
    public function declineRV($id)
    {
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('user_id', Auth::user()->id)->update(['Signature'=>'1']);
      RVMaster::where('RVNo', $id)->update(['Status'=>'1','UnreadNotification'=>'0','notification_date_time'=>Carbon::now()]);
      if (Auth::user()->Role==2)
      {
        Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
      }
      $requisitioner=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType', 'Requisitioner')->get(['user_id']);
      if ($requisitioner[0]->user_id != Auth::user()->id)
      {
        $requisitionerMobile=User::where('id', $requisitioner[0]->user_id)->value('Mobile');
        $forSMS = array('Decliner' =>Auth::user()->FullName,'requisitionerMobile'=>$requisitionerMobile,'RVNo'=>$id);
        $forSMS=(object)$forSMS;
        $job = (new SMSDeclinedRV($forSMS))
        ->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
    }
    public function searchRV(Request $request)
    {
      $allRVMaster=RVMaster::orderBy('RVNo')->where('RVNo','LIKE',$request->RVNo)->paginate(1,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined']);
      return view('Warehouse.RVindex',compact('allRVMaster'));
    }
    public function searchRVforStock(Request $request)
    {
       $MasterResults=MasterItem::orderBy('ItemCode','DESC')
       ->where('Description','LIKE','%'.$request->Search.'%')
       ->orWhere('ItemCode','LIKE','%'.$request->Search.'%')
       ->paginate(8,['AccountCode','ItemCode','Description','Unit','CurrentQuantity','AlertIfBelow']);
       return response()->json(['MasterResults'=>$MasterResults]);
    }
    public function addtoStockSession(Request $request)
    {
      $this->validate($request,[
        'Quantity'=>'required|numeric|min:1',
      ]);
      if (Session::has('ItemSessionList'))
      {
        foreach (Session::get('ItemSessionList') as $sessionItem)
        {
          if ($sessionItem->Description==$request->Description) {
            return ['error'=>'Sorry, you can`t duplicate items '];
          }
        }
      }
      $itemselected = array('Description' =>$request->Description ,'Unit'=>$request->Unit,'Quantity'=>$request->Quantity,'Remarks'=>$request->Remarks,'AccountCode'=>$request->AccountCode,'ItemCode'=>$request->ItemCode);
      $itemselected=(object)$itemselected;
      Session::push('ItemSessionList',$itemselected);
    }

    public function RVIndexSearch(Request $request)
    {
      return $allRVMaster=RVMaster::orderBy('RVNo','DESC')->with('users')->where('RVNo','LIKE','%'.$request->search.'%')->paginate(10,['RVNo','Purpose','RVDate','Status']);
    }
    public function UnpurchaseList()
    {
      $unpurchaselist=RVMaster::orderBy('RVNo','ASC')
      ->where('Status', '0')
      ->where('IfPurchased', null)
      ->paginate(10,['RVNo','Purpose','BudgetAvailable','RVDate']);
      return view('Warehouse.RV.myUnpurchaseRVlist',compact('unpurchaselist'));
    }
    public function updateBudgetAvailable($id, Request $request)
    {
      $this->validate($request,[
        'BudgetUpdate' => 'max:50',
      ]);

      RVMaster::where('RVNo',$id)->update(['BudgetAvailable'=>$request->BudgetUpdate]);
    }
    public function cancelSignatureApproveInBehalf($id)
    {
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','ApprovalReplacer')->where('user_id', Auth::user()->id)->delete();
    }
    public function AcceptSignatureBehalf($id)
    {
      $RVStatus=RVMaster::where('RVNo', $id)->get(['Status']);
      if ($RVStatus[0]->Status!=null)
      {
        return ['success'=>'success'];
      }
      RVMaster::where('RVNo', $id)->update(['SignatureTurn'=>'4','Status'=>'0','UnreadNotification'=>'0','notification_date_time'=>Carbon::now()]);
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','ApprovalReplacer')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
      $RequisitionerId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','Requisitioner')->get(['user_id']);
      $GMID=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','ApprovedBy')->get(['user_id']);

      $RequisitionerMobile=User::where('id',$RequisitionerId[0]->user_id)->get(['Mobile']);
      $GMMoble=User::where('id',$GMID[0]->user_id)->get(['Mobile']);
      $NotifData = array('RequisitionerMobile' =>$RequisitionerMobile[0]->Mobile,'RVNo'=>$id,'ApprovalReplacer'=>Auth::user()->FullName,'GMMobile'=>$GMMoble[0]->Mobile);
      $NotifData=(object)$NotifData;
      $job = (new RVApprovalReplacer($NotifData))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
      return ['success'=>'success'];
    }
    public function fetchSessionRV()
    {
      return $items=Session::get('ItemSessionList');
      // if (isset($items))
      // {
      //   return array_reverse($items);
      // }
    }
    public function getBelowminimumItems()
    {
      return MasterItem::whereColumn('AlertIfBelow','>','CurrentQuantity')->orderBy('CurrentQuantity','ASC')->paginate('8');
    }
    public function fetchAllManagerRV()
    {
      return User::whereNotNull('IsActive')->where('Role',0)->get(['FullName','id']);
    }
    public function sendRequestManagerReplacer($id,Request $request)
    {
      if ($request->ManagerID==null)
      {
        return ['error'=>'required'];
      }
      $signatureTbl=new Signatureable;
      $signatureTbl->user_id=$request->ManagerID;
      $signatureTbl->signatureable_id = $id;
      $signatureTbl->signatureable_type = 'App\RVMaster';
      $signatureTbl->SignatureType = 'ManagerReplacer';
      $signatureTbl->save();

      $NotifyThisPerson = array('NotificReceiver' =>$request->ManagerID);
      $NotifyThisPerson=(object)$NotifyThisPerson;
      $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    public function cancelrequestsentReplacer($id)
    {
      RVMaster::where('RVNo', $id)->update(['SignatureTurn'=>'1']);
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','ManagerReplacer')->delete();
    }
    public function AcceptManagerReplacer($id)
    {
      RVMaster::where('RVNo', $id)->update(['SignatureTurn'=>'2']);
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','ManagerReplacer')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
      $BudgetOfficerID=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','BudgetOfficer')->get(['user_id']);
      $NotifyThisPerson = array('NotificReceiver' => $BudgetOfficerID[0]->user_id);
      $NotifyThisPerson=(object)$NotifyThisPerson;
      $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);

      $RealSignaturerId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\RVMaster')->where('SignatureType','RecommendedBy')->value('user_id');
      $RealSignaturerMobile=User::where('id', $RealSignaturerId)->value('Mobile');
      $data = array('Mobile' =>$RealSignaturerMobile,'RVNo'=>$id,'Replacer'=>Auth::user()->FullName);
      $data=(object)$data;
      $job = (new RVManagerReplacer($data))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);

      return null;
    }
    public function savePendingRemark($id,Request $request)
    {
      $this->validate($request,[
          'PendingRemarks'=>'required',
      ]);
      RVMaster::where('RVNo',$id)->update(['PendingRemarks'=>$request->PendingRemarks]);
    }
    public function PedingRemarkRemove($id)
    {
      RVMaster::where('RVNo',$id)->update(['PendingRemarks'=>NULL]);
    }
    public function showBORemarks($id)
    {
      return RVMaster::where('RVNo',$id)->get(['PendingRemarks']);
    }
    public function RVRequestCount()
    {
      $myRVPendingrequest = Auth::user()->RVSignatureTurn()->count();
      $response = array('RVRequestCount'=>$myRVPendingrequest);
      return response()->json($response);
    }
    public function RefreshRVWaitingForRRCount()
    {
      $RVWaitingPurchaseCount=RVMaster::orderBy('RVNo','ASC')->whereNull('IfPurchased')->where('Status', '0')->count();
      $response = array('RVwaitingRR' =>$RVWaitingPurchaseCount);
      return response()->json($response);
    }
}
