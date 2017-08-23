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
class MRController extends Controller
{
    public function __construct()
    {
      $this->middleware('IsWarehouse',['except'=>['confirmApproveinBehalf','denyApproveinBehalf','approveinBehalfcancel','approveinBehalfMRsent','myMRrequest','previewFullMR','MRofRRlist','SignatureMR','DeclineMR']]);
    }
    public function SaveMR(Request $request)
    {
     $this->validate($request,[
       'Note'=>'required|max:50',
       'ManagerID'=>'required|max:2',
       'Receivedby'=>'required|max:35',
       'ReceivedbyPosition'=>'required|max:30',
       'RRNo'=>'required'
     ]);
     $year=Carbon::now()->format('y');
     $MRNum=MRMaster::orderBy('MRNo','DESC')->take(1)->value('MRNo');
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
     $CurrentWarehouseMan=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role','4')->get(['Fname','Lname']);
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
     $MRMasterDB->GeneralManager=$GM[0]->Fname.' '.$GM[0]->Lname;
     $MRMasterDB->Receivedby=$request->Receivedby;
     $MRMasterDB->ReceivedbyPosition=$request->ReceivedbyPosition;
     $MRMasterDB->WarehouseMan=$CurrentWarehouseMan[0]->Fname.' '.$CurrentWarehouseMan[0]->Lname;
     $MRMasterDB->save();
     $ForMRDetailDB = array();
     foreach (Session::get('MRSession') as $items)
     {
      $ForMRDetailDB[]=array('MRNo' =>$incremented ,'Quantity'=>$items->Quantity,'Unit'=>$items->Unit,'NameDescription'=>$items->Description,'UnitValue'=>$items->UnitCost,'TotalValue'=>$items->Amount,'Remarks'=>$items->Remarks);
     }
     MRDetail::insert($ForMRDetailDB);
     Session::forget('MRSession');
     return ['redirect'=>route('fullMR',[$incremented])];
    }
    public function previewFullMR($id)
    {
      $MRMaster=MRMaster::where('MRNo',$id)->get();
      return view('Warehouse.MR.MRFullpreview',compact('MRMaster'));
    }
    public function createMR($id)
    {
      $RRItemsdetail=RRconfirmationDetails::where('RRNo',$id)->get(['QuantityAccepted','Unit','Description','UnitCost','Amount','ItemCode','RRNo']);
      $allmanager=User::where('Role', '0')->whereNotNull('IsActive')->get(['Fname','Lname','id']);
      return view('Warehouse.MR.CreateMRViews',compact('allmanager','RRItemsdetail'));
    }
    public function addSessionForMR(Request $request)
    {
      $this->validate($request,[
        'ItemCode'=>'required',
        'Quantity'=>'required|regex:/^[0-9]+$/|numeric|min:1|max:'.$request->QuantityValidator,
      ]);
      if (Session::has('MRSession')) {
        foreach (Session::get('MRSession') as $key => $items)
        {
          if ($items->ItemCode == $request->ItemCode )
          {
            $response = ['error'=>'An item with thesame code has already been added'];
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
        if ($item->ItemCode==$id)
        {
           unset($Items[$count]);
        }
      }
      Session::put('MRSession',$Items);
    }
    public function MRofRRlist($id)
    {
      $MRmaster=MRMaster::where('RRNo',$id)->get(['MRNo','GeneralManagerSignature','RecommendedbySignature','IfDeclined','ApprovalReplacerSignature']);
      return view('Warehouse.MR.MRofRRlist',compact('MRmaster'));
    }
    public function SignatureMR(Request $request)
    {
      $MRMaster=MRMaster::where('MRNo',$request->MRNo)->get(['Recommendedby','GeneralManager']);
      if (Auth::user()->Fname.' '.Auth::user()->Lname==$MRMaster[0]->Recommendedby)
      {
        MRMaster::where('MRNo',$request->MRNo)->update(['RecommendedbySignature'=>Auth::user()->Signature]);
      }
      if (Auth::user()->Fname.' '.Auth::user()->Lname==$MRMaster[0]->GeneralManager)
      {
        MRMaster::where('MRNo',$request->MRNo)->update(['GeneralManagerSignature'=>Auth::user()->Signature,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerPosition'=>null,'ApprovalReplacerSignature'=>null]);
      }
      return redirect()->back();
    }
    public function DeclineMR(Request $request)
    {
      $MRMaster=MRMaster::where('MRNo',$request->MRNo)->get(['Recommendedby','GeneralManager']);
      if (Auth::user()->Fname.' '.Auth::user()->Lname==$MRMaster[0]->GeneralManager)
      {
        MRMaster::where('MRNo',$request->MRNo)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerPosition'=>null,'ApprovalReplacerSignature'=>null]);
      }elseif(Auth::user()->Fname.' '.Auth::user()->Lname==$MRMaster[0]->Recommendedby)
      {
        MRMaster::where('MRNo',$request->MRNo)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
      }
      return redirect()->back();
    }
    public function myMRrequest()
    {
      $MRRequest=MRMaster::orderBy('MRNo','DESC')->where('Recommendedby', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('RecommendedbySignature')->whereNull('IfDeclined')
      ->orWhere('GeneralManager', Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('GeneralManagerSignature')->whereNull('IfDeclined')->whereNull('ApprovalReplacerSignature')->paginate(10,['MRNo','Note','Recommendedby','RecommendedbySignature','Receivedby','GeneralManager','GeneralManagerSignature','MRDate']);
      return view('Warehouse.MR.MyMRRequest',compact('MRRequest'));
    }

    public function approveinBehalfMRsent($id)
    {
      if (Auth::user()->Role==0)
      {
        MRMaster::where('MRNo',$id)->update(['ApprovalReplacerFname'=>Auth::user()->Fname,'ApprovalReplacerLname'=>Auth::user()->Lname]);
      }
      return redirect()->back();
    }
    public function approveinBehalfcancel($id)
    {
      $replacer=MRMaster::where('MRNo',$id)->get(['ApprovalReplacerFname','ApprovalReplacerLname']);
      if (Auth::user()->Fname.' '.Auth::user()->Lname==$replacer[0]->ApprovalReplacerFname.' '.$replacer[0]->ApprovalReplacerLname)
      {
        MRMaster::where('MRNo',$id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
      }
      return redirect()->back();
    }
    public function confirmApproveinBehalf($id)
    {
      $MRMaster=MRMaster::where('MRNo',$id)->get(['GeneralManagerSignature','ApprovalReplacerFname','ApprovalReplacerLname']);
      if ($MRMaster[0]->GeneralManagerSignature==null)
      {
        $replacerdata=User::whereNotNull('IsActive')->where('Fname', $MRMaster[0]->ApprovalReplacerFname)->where('Lname',$MRMaster[0]->ApprovalReplacerLname)->get(['Position','Signature']);
        MRMaster::where('MRNo',$id)->update(['ApprovalReplacerSignature'=>$replacerdata[0]->Signature,'ApprovalReplacerPosition'=>$replacerdata[0]->Position]);
      }
      return redirect()->back();
    }
    public function denyApproveinBehalf($id)
    {
      MRMaster::where('MRNo',$id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
      return redirect()->back();
    }

}
