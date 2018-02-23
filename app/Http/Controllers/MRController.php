<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MRMaster;
use App\MRDetail;
use Carbon\Carbon;
use App\RRMaster;
use App\RVMaster;
use App\User;
use App\RRconfirmationDetails;
use Session;
use Auth;
use App\Jobs\NewMRCreatedJob;
use App\Signatureable;
use App\Jobs\MRApprovedAlert;
use App\Jobs\GlobalNotifJob;
class MRController extends Controller
{
    public function __construct()
    {
      $this->middleware('IsWarehouse',['except'=>['MRindexFetchAndSearch','MRindexPage','previewFullMRFetchData','MyMRrequestCount','refuseMRApproveInBehalf','confirmApproveinBehalf','myMRrequest','previewFullMR','MRofRRlist','SignatureMR','DeclineMR']]);
    }
    public function updateMR(Request $request,$MRNo)
    {
      $MRMasterCurrent = MRMaster::where('MRNo', $MRNo)->get(['RRNo','Status']);
      $tobeUpdated=MRDetail::where('MRNo', $MRNo)->get(['id','NameDescription','Quantity','UnitValue']);

      // validation
      if ($MRMasterCurrent[0]->Status !=null)
      {
        return ['error'=>'Refreshed'];
      }
      foreach ($tobeUpdated as $keyloop => $tobe)
      {
        $itemRow =$keyloop+1;
        if ($request->NewQty[$keyloop] == null || $request->NewQty[$keyloop] == '')
        {
          return ['error'=>'The Qty is required'];
        }
        if (is_numeric($request->NewQty[$keyloop])==false)
        {
          return ['error'=>'Qty must be a number'];
        }
        if ($request->NewQty[0] < 1)
        {
          return ['error'=>'The Qty must be atleast 1'];
        }
        if ($tobe->Quantity < $request->NewQty[$keyloop])
        {
          $tobeSubtract =$request->NewQty[$keyloop] - $tobe->Quantity;
          $MRvalidator=RRconfirmationDetails::where('RRNo',$MRMasterCurrent[0]->RRNo)->where('Description',$tobe->NameDescription)->get(['QuantityValidator','id']);
          if ($MRvalidator[0]->QuantityValidator < $tobeSubtract)
          {
            return ['error'=>'The Qty to be added cannot be greater than '.$MRvalidator[0]->QuantityValidator.' at row '.$itemRow];
          }
        }
      }
      // updating
      foreach ($tobeUpdated as $key => $tobe)
      {
        if ($tobe->Quantity > $request->NewQty[$key])
        {
          $tobeReturned = $tobe->Quantity - $request->NewQty[$key];
          $MRvalidator=RRconfirmationDetails::where('RRNo',$MRMasterCurrent[0]->RRNo)->where('Description',$tobe->NameDescription)->get(['QuantityValidator','id']);
          $newValidator = $MRvalidator[0]->QuantityValidator + $tobeReturned;
          RRconfirmationDetails::where('id', $MRvalidator[0]->id)->update(['QuantityValidator'=>$newValidator]);
        }else if ($tobe->Quantity < $request->NewQty[$key])
        {
          $tobeSubtract =$request->NewQty[$key] - $tobe->Quantity;
          $MRvalidator=RRconfirmationDetails::where('RRNo',$MRMasterCurrent[0]->RRNo)->where('Description',$tobe->NameDescription)->get(['QuantityValidator','id']);
          $newValidator = $MRvalidator[0]->QuantityValidator - $tobeSubtract;
          RRconfirmationDetails::where('id', $MRvalidator[0]->id)->update(['QuantityValidator'=>$newValidator]);
        }
          $newTotal = $tobe->UnitValue * $request->NewQty[$key];
          MRDetail::where('id',$tobe->id)->update(['Quantity'=>$request->NewQty[$key],'TotalValue'=>$newTotal]);
      }
      MRMaster::where('MRNo',$MRNo)->update(['Note'=>$request->NewNote,'SignatureTurn'=>'0']);
      Signatureable::where('signatureable_type', 'App\MRMaster')->where('signatureable_id',$MRNo)->update(['Signature'=>NULL]);
    }
    public function SaveMR(Request $request)
    {
     $this->validate($request,[
       'Note'=>'max:191',
       'RecommendedBy'=>'required|max:2',
       'ReceivedBy'=>'required|max:2',
       'RRNo'=>'required'
     ]);
     if (Session::get('MRSession')==null)
     {
       return response()->json(['error'=>'Item is required']);
     }
     $QtyValidation=RRconfirmationDetails::where('RRNo', $request->RRNo)->get(['id','QuantityValidator','Description']);
     foreach (Session::get('MRSession') as $items)
     {
       foreach ($QtyValidation as $validator)
       {
         if ($validator->id==$items->id)
         {
            if ($validator->QuantityValidator < $items->Quantity)
            {
              return response()->json(['error'=>'The quantity of item '.$items->Description.' must be '.$validator->QuantityValidator.' or below']);
            }
         }
       }
     }
     $year=Carbon::now()->format('y');
     $MRNum=MRMaster::orderBy('MRNo','DESC')->take(1)->value('MRNo');
     $ApprovalReplacer=User::whereNotNull('IfApproveReplacer')->get(['id']);
     $explodedMRNum=explode('-',$MRNum);
     if (count($MRNum)>0 && $explodedMRNum[0] ==$year)
     {
       $numOnly=substr($MRNum,'3');
       $numOnly = (int)$numOnly;
       $newID=$numOnly + 1;
       $incremented= $year . '-' . sprintf("%04d",$newID);
     }else
     {
      $incremented=  $year . '-' . sprintf("%04d",'1');
     }
     $RRMasterData=RRMaster::where('RRNo',$request->RRNo)->get();
     $RVDate=RVMaster::where('RVNo',$RRMasterData[0]->RVNo)->value('RVDate');
     $Recommended=User::where('id', $request->RecommendedBy)->whereNotNull('IsActive')->get(['id']);
     $GM=User::orderBy('id','DESC')->where('Role', '2')->whereNotNull('IsActive')->take(1)->get(['id']);
     $Receiver=User::where('id',$request->ReceivedBy)->get(['id']);
     $MRMasterDB= new MRMaster;
     $MRMasterDB->MRNo=$incremented;
     $MRMasterDB->MRDate=Carbon::now();
     $MRMasterDB->RVNo=$RRMasterData[0]->RVNo;
     $MRMasterDB->RVDate=$RVDate;
     $MRMasterDB->RRNo=$RRMasterData[0]->RRNo;
     $MRMasterDB->RRDate=$RRMasterData[0]->RRDate;
     $MRMasterDB->PONo=$RRMasterData[0]->PONo;
     $MRMasterDB->Note=$request->Note;
     $MRMasterDB->Supplier=$RRMasterData[0]->Supplier;
     $MRMasterDB->InvoiceNo=$RRMasterData[0]->InvoiceNo;
     $MRMasterDB->WarehouseMan=Auth::user()->FullName;
     $MRMasterDB->notification_date_time = Carbon::now();
     $MRMasterDB->CreatorID = Auth::user()->id;
     $MRMasterDB->save();
     if (isset($ApprovalReplacer[0]))
     {
       $forSignatures = array(
        array('user_id' =>$Recommended[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MRMaster','SignatureType'=>'RecommendedBy'),
        array('user_id' =>$GM[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MRMaster','SignatureType'=>'ApprovedBy'),
        array('user_id' =>$Receiver[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MRMaster','SignatureType'=>'ReceivedBy'),
        array('user_id' =>$ApprovalReplacer[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MRMaster','SignatureType'=>'ApprovalReplacer')
       );
     }else
     {
       $forSignatures = array(
        array('user_id' =>$Recommended[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MRMaster','SignatureType'=>'RecommendedBy'),
        array('user_id' =>$GM[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MRMaster','SignatureType'=>'ApprovedBy'),
        array('user_id' =>$Receiver[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MRMaster','SignatureType'=>'ReceivedBy')
       );
     }
     $ForMRDetailDB = array();
     foreach (Session::get('MRSession') as $items)
     {
        $ForMRDetailDB[]=array('MRNo' =>$incremented ,'Quantity'=>$items->Quantity,'Unit'=>$items->Unit,'NameDescription'=>$items->Description,'UnitValue'=>$items->UnitCost,'TotalValue'=>$items->Amount,'Remarks'=>$items->Remarks);
        $confirmationDetail=RRconfirmationDetails::where('id',$items->id)->get(['QuantityValidator']);
        $newQty=$confirmationDetail[0]->QuantityValidator - $items->Quantity;
        RRconfirmationDetails::where('id',$items->id)->update(['QuantityValidator'=>$newQty]);
     }
     Signatureable::insert($forSignatures);
     MRDetail::insert($ForMRDetailDB);
     Session::forget('MRSession');
     $job=(new NewMRCreatedJob($Recommended[0]->id))->delay(Carbon::now()->addSeconds(5));
     dispatch($job);
     return ['redirect'=>route('fullMR',[$incremented])];
    }
    public function previewFullMR($id)
    {
      $MRNumber = array('MRNo' =>$id);
      $MRNumber=json_encode($MRNumber);
      return view('Warehouse.MR.MRFullpreview',compact('MRNumber'));
    }
    public function previewFullMRFetchData($id)
    {
      $MRMaster=MRMaster::with('users')->where('MRNo',$id)->get();
      $MRDetail=MRDetail::where('MRNo',$id)->get();
      $response = array('MRMaster' =>$MRMaster,'MRDetail'=>$MRDetail);
      return response()->json($response);
    }
    public function createMR($id)
    {
      Session::forget('MRSession');
      $RRItemsdetail=RRconfirmationDetails::where('RRNo',$id)->get(['Unit','Description','UnitCost','Amount','ItemCode','RRNo','id']);
      $allmanager=User::where('Role', '0')->whereNotNull('IsActive')->get(['FullName','id']);
      $AllActiveUsers=User::whereNotNull('IsActive')->orderBy('Role')->get(['FullName','id']);
      return view('Warehouse.MR.CreateMRViews',compact('allmanager','RRItemsdetail','AllActiveUsers'));
    }
    public function addSessionForMR(Request $request)
    {
      $this->validate($request,[
        'Quantity'=>'required|regex:/^[0-9]+$/|numeric|min:1',
      ]);
      $ItemsRemaining=RRconfirmationDetails::where('RRNo', $request->RRNo)->where('id',$request->id)->value('QuantityValidator');
      if ($request->Quantity > $ItemsRemaining)
      {
        return response()->json(['error'=>'The maximum qty is '.$ItemsRemaining]);
      }
      if (Session::has('MRSession')) {
        foreach (Session::get('MRSession') as $key => $items)
        {
          if ($items->id == $request->id )
          {
            $response = ['error'=>'Cannot duplicate items'];
            return response()->json($response);
          }
        }
      }
      $cost=$request->UnitCost;
      $qty=$request->Quantity;
      $amt=$cost*$qty;
      $toSession = array('id'=>$request->id,'ItemCode' =>$request->ItemCode,'Quantity' =>$request->Quantity,'Unit' =>$request->Unit,'Description' =>$request->Description,'UnitCost' =>$request->UnitCost,'Amount'=>$amt,'Remarks'=>$request->Remarks);
      $toSession=(object)$toSession;
      Session::push('MRSession',$toSession);
    }
    public function displayMRSessions()
    {
      $sessions=Session::get('MRSession');
      $response =['sessions'=>$sessions];
      return response()->json($response);
    }
    public function removingSession($id)
    {
      $Items=Session::get('MRSession');
      foreach ($Items as $count => $item) {

           unset($Items[$id]);
      }
      Session::put('MRSession',$Items);
    }
    public function MRofRRlist($id)
    {
      $MRmaster=MRMaster::where('RRNo',$id)->get(['MRNo','Status']);
      return view('Warehouse.MR.MRofRRlist',compact('MRmaster'));
    }
    public function SignatureMR($id)
    {
      $MRMaster=MRMaster::with('users')->where('MRNo',$id)->get();
      if ((Auth::user()->id==$MRMaster[0]->users[0]->id) && ($MRMaster[0]->users[0]->pivot->Signature==null))
      {
        MRMaster::where('MRNo',$id)->update(['SignatureTurn'=>'1']);
        Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->where('SignatureType', 'RecommendedBy')->update(['Signature'=>'0']);
        $job=(new NewMRCreatedJob($MRMaster[0]->users[1]->id))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
        if (isset($MRMaster[0]->users[3]))
        {
          $job2=(new NewMRCreatedJob($MRMaster[0]->users[3]->id))->delay(Carbon::now()->addSeconds(5));
          dispatch($job2);
        }
      }elseif ((Auth::user()->id==$MRMaster[0]->users[1]->id)&&($MRMaster[0]->users[1]->pivot->Signature==null))
      {
        MRMaster::where('MRNo',$id)->update(['SignatureTurn'=>'2']);
        Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->where('SignatureType', 'ApprovedBy')->update(['Signature'=>'0']);
        Signatureable::where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
        $job=(new NewMRCreatedJob($MRMaster[0]->users[2]->id))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);

        $data = array('MRNo' =>$id,'ReceiverMobile'=>$MRMaster[0]->users[2]->Mobile);
        $data=(object)$data;
        $job=(new MRApprovedAlert($data))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }elseif((Auth::user()->id==$MRMaster[0]->users[2]->id)&&($MRMaster[0]->users[2]->pivot->Signature==null))
      {
        MRMaster::where('MRNo',$id)->update(['SignatureTurn'=>'3','Status'=>'0','UnreadNotification'=>'0','notification_date_time'=>Carbon::now()]);
        Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->where('SignatureType', 'ReceivedBy')->update(['Signature'=>'0']);

        // notify warehouseman the creator
        $ReceiverID = array('id' =>$MRMaster[0]->CreatorID);
        $ReceiverID = (object)$ReceiverID;
        $job = (new GlobalNotifJob($ReceiverID))
        ->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }

    }
    public function DeclineMR($id)
    {
      MRMaster::where('MRNo',$id)->update(['Status'=>'1','UnreadNotification'=>'0','notification_date_time'=>Carbon::now()]);
      Signatureable::where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
      Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->update(['Signature'=>'1']);
      $RRNumber=MRMaster::where('MRNo',$id)->get(['RRNo']);
      $MRitemsDeclined=MRDetail::where('MRNo',$id)->get(['Quantity','NameDescription']);
      $RRDetailItems=RRconfirmationDetails::where('RRNo', $RRNumber[0]->RRNo)->get(['QuantityValidator','Description','id']);
      foreach ($MRitemsDeclined as $itemdeclined)
      {
        foreach ($RRDetailItems as $itemvalidator)
        {
          if ($itemdeclined->NameDescription == $itemvalidator->Description)
          {
            $newValidatorQty=$itemvalidator->QuantityValidator + $itemdeclined->Quantity;
            RRconfirmationDetails::where('id', $itemvalidator->id)->update(['QuantityValidator'=>$newValidatorQty]);
          }
        }
      }
      // notify warehouseman the creator
      $MRMaster = MRMaster::where('MRNo',$id)->get(['CreatorID']);
      $ReceiverID = array('id' =>$MRMaster[0]->CreatorID);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    public function myMRrequest()
    {
      $MRRequest=Auth::user()->MRSignatureTurn()->paginate(10,['MRNo','MRDate','Supplier','InvoiceNo','WarehouseMan']);
      return view('Warehouse.MR.MyMRRequest',compact('MRRequest'));
    }
    public function refuseMRApproveInBehalf($id)
    {
      Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
    }
    public function confirmApproveinBehalf($id)
    {
      $MRMaster=MRMaster::with('users')->where('MRNo',$id)->get();
      if ($MRMaster[0]->users[1]->pivot->Signature!=null)
      {
        return ['success'=>'success'];
      }
      if (isset($MRMaster[0]->users[3])&&($MRMaster[0]->users[3]->id==Auth::user()->id))
      {
        MRMaster::where('MRNo',$id)->update(['SignatureTurn'=>'2']);
        Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id',$id)->where('signatureable_type', 'App\MRMaster')->where('SignatureType', 'ApprovalReplacer')->update(['Signature'=>'0']);
        $job=(new NewMRCreatedJob($MRMaster[0]->users[2]->id))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
    }
    public function MyMRrequestCount()
    {
      $MRRequestCount=Auth::user()->MRSignatureTurn()->count();
      $response=[
        'CountMRRequest'=>$MRRequestCount,
      ];
      return response()->json($response);
    }
    public function MRindexPage()
    {
      return view('Warehouse.MR.MRindex');
    }
    public function MRindexFetchAndSearch(Request $request)
    {
      return MRMaster::with('users')->where('MRNo','LIKE','%'.$request->MRNo.'%')->orderBy('MRNo','DESC')->paginate(10,['MRNo','MRDate','RVNo','RRNo','PONo','Supplier','Status']);
    }
}
