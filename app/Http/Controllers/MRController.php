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
class MRController extends Controller
{
    public function __construct()
    {
      $this->middleware('IsWarehouse',['except'=>['MRindexFetchAndSearch','MRindexPage','previewFullMRFetchData','MyMRrequestCount','refuseMRApproveInBehalf','confirmApproveinBehalf','myMRrequest','previewFullMR','MRofRRlist','SignatureMR','DeclineMR']]);
    }
    public function SaveMR(Request $request)
    {
     $this->validate($request,[
       'Note'=>'max:50',
       'ManagerID'=>'required|max:2',
       'Receivedby'=>'required|max:2',
       'RRNo'=>'required'
     ]);
     if (Session::get('MRSession')==null) {
       return redirect()->back();
     }
     $year=Carbon::now()->format('y');
     $MRNum=MRMaster::orderBy('MRNo','DESC')->take(1)->value('MRNo');
     $ApprovalReplacer=User::whereNotNull('IfApproveReplacer')->get(['Fname','Lname']);
     if (count($MRNum)>0)
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
     $Recommended=User::where('id', $request->ManagerID)->whereNotNull('IsActive')->get(['Fname','Lname','Position']);
     $GM=User::orderBy('id','DESC')->where('Role', '2')->whereNotNull('IsActive')->take(1)->get(['Fname','Lname']);
     $Receiver=User::where('id',$request->Receivedby)->get(['Fname','Lname','Position']);
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
     $MRMasterDB->Recommendedby=$Recommended[0]->Fname.' '.$Recommended[0]->Lname;
     $MRMasterDB->RecommendedbyPosition=$Recommended[0]->Position;
     if (!empty($ApprovalReplacer[0]))
     {
     $MRMasterDB->ApprovalReplacer=$ApprovalReplacer[0]->Fname.' '.$ApprovalReplacer[0]->Lname;
     }
     $MRMasterDB->GeneralManager=$GM[0]->Fname.' '.$GM[0]->Lname;
     $MRMasterDB->Receivedby=$Receiver[0]->Fname.' '.$Receiver[0]->Lname;
     $MRMasterDB->ReceivedbyPosition=$Receiver[0]->Position;
     $MRMasterDB->WarehouseMan=Auth::user()->Fname.' '.Auth::user()->Lname;
     $MRMasterDB->save();
     $ForMRDetailDB = array();
     foreach (Session::get('MRSession') as $items)
     {
      $ForMRDetailDB[]=array('MRNo' =>$incremented ,'Quantity'=>$items->Quantity,'Unit'=>$items->Unit,'NameDescription'=>$items->Description,'UnitValue'=>$items->UnitCost,'TotalValue'=>$items->Amount,'Remarks'=>$items->Remarks);
     }
     MRDetail::insert($ForMRDetailDB);
     Session::forget('MRSession');
     $notifythis=str_replace(' ','',$Recommended[0]->Fname.$Recommended[0]->Lname);
     $job=(new NewMRCreatedJob($notifythis))->delay(Carbon::now()->addSeconds(5));
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
      $MRMaster=MRMaster::where('MRNo',$id)->get();
      $MRDetail=MRDetail::where('MRNo',$id)->get();
      $response = array('MRMaster' =>$MRMaster,'MRDetail'=>$MRDetail);
      return response()->json($response);
    }
    public function createMR($id)
    {
      $RRItemsdetail=RRconfirmationDetails::where('RRNo',$id)->get(['Unit','Description','UnitCost','Amount','ItemCode','RRNo']);
      $allmanager=User::where('Role', '0')->whereNotNull('IsActive')->get(['Fname','Lname','id']);
      $AllActiveUsers=User::whereNotNull('IsActive')->orderBy('Role')->get(['Fname','Lname','id']);
      return view('Warehouse.MR.CreateMRViews',compact('allmanager','RRItemsdetail','AllActiveUsers'));
    }
    public function addSessionForMR(Request $request)
    {
      $this->validate($request,[
        'Quantity'=>'required|regex:/^[0-9]+$/|numeric|min:1',
      ]);
      $ItemsRemaining=RRconfirmationDetails::where('RRNo', $request->RRNo)->where('Description',$request->Description)->value('QuantityAccepted');
      if ($request->Quantity > $ItemsRemaining)
      {
        return response()->json(['error'=>'The maximum qty is '.$ItemsRemaining]);
      }
      if (Session::has('MRSession')) {
        foreach (Session::get('MRSession') as $key => $items)
        {
          if ($items->Description == $request->Description )
          {
            $response = ['error'=>'Cannot duplicate items'];
            return response()->json($response);
          }
        }
      }
      $cost=$request->UnitCost;
      $qty=$request->Quantity;
      $amt=$cost*$qty;
      $toSession = array('ItemCode' =>$request->ItemCode,'Quantity' =>$request->Quantity,'Unit' =>$request->Unit,'Description' =>$request->Description,'UnitCost' =>$request->UnitCost,'Amount'=>$amt,'Remarks'=>$request->Remarks);
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
      $MRmaster=MRMaster::where('RRNo',$id)->get(['MRNo','GeneralManagerSignature','RecommendedbySignature','ReceivedbySignature','IfDeclined','ApprovalReplacerSignature']);
      return view('Warehouse.MR.MRofRRlist',compact('MRmaster'));
    }
    public function SignatureMR($id)
    {
      $MRMaster=MRMaster::where('MRNo',$id)->get(['Recommendedby','GeneralManager','Receivedby']);
      if (Auth::user()->Fname.' '.Auth::user()->Lname==$MRMaster[0]->Recommendedby)
      {
        MRMaster::where('MRNo',$id)->update(['RecommendedbySignature'=>Auth::user()->Signature]);
        $notifythis=str_replace(' ','',$MRMaster[0]->GeneralManager);
        $job=(new NewMRCreatedJob($notifythis))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
      if (Auth::user()->Fname.' '.Auth::user()->Lname==$MRMaster[0]->GeneralManager)
      {
        MRMaster::where('MRNo',$id)->update(['GeneralManagerSignature'=>Auth::user()->Signature,'ApprovalReplacer'=>null,'ApprovalReplacerSignature'=>null]);
        $notifythis=str_replace(' ','',$MRMaster[0]->Receivedby);
        $job=(new NewMRCreatedJob($notifythis))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
      if (Auth::user()->Fname.' '.Auth::user()->Lname==$MRMaster[0]->Receivedby)
      {
        MRMaster::where('MRNo',$id)->update(['ReceivedbySignature'=>Auth::user()->Signature]);
      }
    }
    public function DeclineMR($id)
    {
      $MRMaster=MRMaster::where('MRNo',$id)->get(['Recommendedby','GeneralManager']);
      if (Auth::user()->Role==2)
      {
        MRMaster::where('MRNo',$id)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname,'ApprovalReplacer'=>null,'ApprovalReplacerSignature'=>null]);
      }else
      {
        MRMaster::where('MRNo',$id)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
      }
    }
    public function myMRrequest()
    {
      $MRRequest=MRMaster::orderBy('MRNo','DESC')->where('Recommendedby', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('RecommendedbySignature')->whereNull('IfDeclined')
      ->orWhere('GeneralManager', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->whereNull('IfDeclined')->whereNull('ApprovalReplacerSignature')
      ->orWhere('Receivedby', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ReceivedbySignature')->whereNotNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->whereNull('IfDeclined')->whereNull('ApprovalReplacerSignature')
      ->orWhere('Receivedby', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ReceivedbySignature')->whereNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->whereNull('IfDeclined')->whereNotNull('ApprovalReplacerSignature')
      ->orWhere('ApprovalReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ApprovalReplacerSignature')->whereNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->paginate(10,['MRNo','Note','Recommendedby','RecommendedbySignature','Receivedby','ReceivedbySignature','GeneralManager','GeneralManagerSignature','ApprovalReplacerSignature','MRDate']);
      return view('Warehouse.MR.MyMRRequest',compact('MRRequest'));
    }
    public function refuseMRApproveInBehalf($id)
    {
      MRMaster::where('MRNo',$id)->update(['ApprovalReplacer'=>null]);
    }
    public function confirmApproveinBehalf($id)
    {
      $MRMaster=MRMaster::where('MRNo',$id)->get(['GeneralManagerSignature']);
      if ($MRMaster[0]->GeneralManagerSignature==null)
      {
        MRMaster::where('MRNo',$id)->update(['ApprovalReplacerSignature'=>Auth::user()->Signature]);
      }
    }
    public function MyMRrequestCount()
    {
      $MRRequestCount=MRMaster::orderBy('MRNo','DESC')->where('Recommendedby', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('RecommendedbySignature')->whereNull('IfDeclined')
      ->orWhere('GeneralManager', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->whereNull('IfDeclined')->whereNull('ApprovalReplacerSignature')
      ->orWhere('Receivedby', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ReceivedbySignature')->whereNotNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->whereNull('IfDeclined')->whereNull('ApprovalReplacerSignature')
      ->orWhere('Receivedby', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ReceivedbySignature')->whereNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->whereNull('IfDeclined')->whereNotNull('ApprovalReplacerSignature')
      ->orWhere('ApprovalReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ApprovalReplacerSignature')->whereNull('GeneralManagerSignature')->whereNotNull('RecommendedbySignature')->count();
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
      return MRMaster::where('MRNo','LIKE','%'.$request->MRNo.'%')->orderBy('MRNo','DESC')->paginate(10,['MRNo','MRDate','RVNo','RRNo','PONo','Supplier','Recommendedby','GeneralManager','Receivedby','RecommendedbySignature','GeneralManagerSignature','ReceivedbySignature','ApprovalReplacerSignature','IfDeclined']);
    }
}
