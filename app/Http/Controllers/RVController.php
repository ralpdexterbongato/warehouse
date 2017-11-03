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
use App\RRValidatorNoPO;
use App\RRMaster;
use App\MaterialsTicketDetail;
use App\Jobs\NewRVCreatedJob;
use App\Jobs\NewRVApprovedJob;
use App\Jobs\RVApprovalReplacer;
class RVController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    public function RVcreate()
    {
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
        'Quantity'=>'required',
      ]);
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
      $currentBudgetOfficer=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '7')->take(1)->get(['FullName']);//also using this at the bottom for RVdetails
      $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '2')->take(1)->get(['FullName']);//also using this at the bottom for RVdetails
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
      if ($currentRVid)
      {
        $numOnly=substr($currentRVid,'3');
        $numOnly=(int)$numOnly;
        $newID=$numOnly+1;
        $incremented=$year.'-'.sprintf("%04d",$newID);
      }else
      {
        $incremented=$year.'-'.sprintf("%04d",'1');
      }
      $recommended=User::whereNotNull('IsActive')->where('id',Auth::user()->Manager)->get(['FullName','Position']);
      $RVMaster=new RVMaster;
      $RVMaster->RVNo=$incremented;
      $RVMaster->RVDate=$date;
      $RVMaster->Purpose=$request->Purpose;
      $RVMaster->Requisitioner=Auth::user()->FullName;
      $RVMaster->RequisitionerPosition=Auth::user()->Position;
      $RVMaster->RequisitionerSignature=Auth::user()->Signature;
      $approveReplacer=User::whereNotNull('IfApproveReplacer')->get(['FullName']);
      if (!empty($approveReplacer[0]))
      {
        $RVMaster->ApprovalReplacer=$approveReplacer[0]->FullName;
      }
      $RVMaster->Recommendedby=$recommended[0]->FullName;
      $RVMaster->RecommendedbyPosition=$recommended[0]->Position;
      if (($request->BudgetAvailable!=null)&&(Auth::user()->Role==7))
      {
        $RVMaster->BudgetAvailable=$request->BudgetAvailable;
      }
      $RVMaster->BudgetOfficer=$currentBudgetOfficer[0]->FullName;
      $RVMaster->GeneralManager=$GM[0]->FullName;
      $RVMaster->save();

      $forRVdetailDB = array();
      foreach (Session::get('ItemSessionList') as $SessionItem)
      {
        $forRVdetailDB[] = array('RVNo' =>$incremented ,'Particulars'=>$SessionItem->Description,'Unit'=>$SessionItem->Unit,'Quantity'=>$SessionItem->Quantity,'Remarks'=>$SessionItem->Remarks,'AccountCode'=>$SessionItem->AccountCode,'ItemCode'=>$SessionItem->ItemCode);
      }
      RVDetail::insert($forRVdetailDB);
      Session::forget('ItemSessionList');
      Session::forget('SessionForStock');
      $nospaceName=str_replace(' ','',$recommended[0]->FullName);
      $NotifyThisPerson = array('NotificReceiver'=>$nospaceName);
      $NotifyThisPerson=(object)$NotifyThisPerson;
      $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
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
      $RVMaster=RVMaster::where('RVNo',$id)->get();
      $checkRR=RRMaster::where('RVNo', $id)->take(1)->value('RRNo');
      $checkPO=POMaster::where('RVNo',$id)->take(1)->value('PONo');
      $undeliveredTotal=null;
      if (($checkPO==null)&&($checkRR!=null))
      {
       $undeliveredTotal=RRValidatorNoPO::where('RVNo',$id)->sum('Quantity');
      }
      $response = array('RVMaster'=>$RVMaster ,'RVDetails'=>$RVDetails,'checkPO'=>$checkPO,'checkRR'=>$checkRR,'undeliveredTotal'=>$undeliveredTotal);
      return response()->json($response);
    }
    public function Signature($id,Request $request)
    {
      $RVMasterNames=RVMaster::where('RVNo',$id)->get(['Requisitioner','BudgetOfficer','Recommendedby','GeneralManager','ApprovalReplacer']);
      if ($RVMasterNames[0]->BudgetOfficer == Auth::user()->FullName)
      {
        $this->validate($request,[
            'BudgetAvailable'=>'max:50',
        ]);
        RVMaster::where('RVNo',$id)->update(['BudgetOfficerSignature'=>Auth::user()->Signature,'BudgetAvailable'=>$request->BudgetAvailable]);
        $nospaceName=str_replace(' ','',$RVMasterNames[0]->GeneralManager);
        $NotifyThisPerson = array('NotificReceiver' => $nospaceName);
        $NotifyThisPerson=(object)$NotifyThisPerson;
        $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
        if (!empty($RVMasterNames[0]->ApprovalReplacer))
        {
          $nospaceName=str_replace(' ','',$RVMasterNames[0]->ApprovalReplacer);
          $NotifyThisPerson = array('NotificReceiver' => $nospaceName);
          $NotifyThisPerson=(object)$NotifyThisPerson;
          $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
          dispatch($job);
        }
      }
      if ($RVMasterNames[0]->Recommendedby == Auth::user()->FullName)
      {
        RVMaster::where('RVNo',$id)->update(['RecommendedbySignature'=>Auth::user()->Signature,'ManagerReplacer'=>null,'ManagerReplacerSignature'=>null]);
        $nospaceName=str_replace(' ','',$RVMasterNames[0]->BudgetOfficer);
        $NotifyThisPerson = array('NotificReceiver' => $nospaceName);
        $NotifyThisPerson=(object)$NotifyThisPerson;
        $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
      if ($RVMasterNames[0]->GeneralManager == Auth::user()->FullName)
      {
        RVMaster::where('RVNo',$id)->update(['GeneralManagerSignature'=>Auth::user()->Signature,'ApprovalReplacer'=>null,'ApprovalReplacerSignature'=>null]);
        $requisitionerMobile=User::where('FullName',$RVMasterNames[0]->Requisitioner)->get(['Mobile']);
        $notifyData = array('RequisitionerMobile' =>$requisitionerMobile[0]->Mobile,'RVNo'=>$id);
        $notifyData=(object)$notifyData;
        $job = (new NewRVApprovedJob($notifyData))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
      $RVSignatures=RVMaster::where('RVNo',$id)->get(['RequisitionerSignature','BudgetOfficerSignature','RecommendedbySignature','GeneralManagerSignature','ApprovalReplacerSignature','ManagerReplacerSignature']);
      if ((($RVSignatures[0]->RequisitionerSignature!=null)&&($RVSignatures[0]->BudgetOfficerSignature!=null)&&(($RVSignatures[0]->RecommendedbySignature!=null)||($RVSignatures[0]->ManagerReplacerSignature!=null))&&($RVSignatures[0]->GeneralManagerSignature!=null))||(($RVSignatures[0]->RequisitionerSignature!=null)&&($RVSignatures[0]->BudgetOfficerSignature!=null)&&($RVSignatures[0]->RecommendedbySignature!=null)&&($RVSignatures[0]->ApprovalReplacerSignature!=null)))
      {
        $RVitems=RVDetail::where('RVNo', $id)->get();
        $forRRNoPOValidator = array();
        foreach ($RVitems as $rvitem)
        {
          $forRRNoPOValidator[] = array('RVNo' =>$rvitem->RVNo ,'Particulars'=>$rvitem->Particulars,'Unit'=>$rvitem->Unit ,'Quantity'=>$rvitem->Quantity ,'Remarks'=>$rvitem->Remarks,'ItemCode'=>$rvitem->ItemCode,'AccountCode'=>$rvitem->AccountCode);
        }
        RRValidatorNoPO::insert($forRRNoPOValidator);
      }

    }
    public function RVrequest()
    {
    $myRVPendingrequest=RVMaster::orderBy('RVNo','DESC')
    ->orWhere('Recommendedby',Auth::user()->FullName)
    ->whereNull('RecommendedbySignature')
    ->whereNull('IfDeclined')
    ->whereNotNull('RequisitionerSignature')
    ->whereNull('ManagerReplacerSignature')
    ->orWhere('BudgetOfficer',Auth::user()->FullName)
    ->whereNull('BudgetOfficerSignature')
    ->whereNotNull('RecommendedbySignature')
    ->whereNull('IfDeclined')
    ->orWhere('BudgetOfficer',Auth::user()->FullName)
    ->whereNull('BudgetOfficerSignature')
    ->whereNotNull('ManagerReplacerSignature')
    ->whereNull('IfDeclined')
    ->orWhere('GeneralManager',Auth::user()->FullName)
    ->whereNull('GeneralManagerSignature')
    ->whereNull('ApprovalReplacerSignature')
    ->whereNull('IfDeclined')
    ->whereNotNull('RequisitionerSignature')
    ->whereNotNull('RecommendedbySignature')
    ->whereNotNull('BudgetOfficerSignature')
    ->whereNull('ApprovalReplacerSignature')
    ->orWhere('GeneralManager',Auth::user()->FullName)
    ->whereNull('GeneralManagerSignature')
    ->whereNull('ApprovalReplacerSignature')
    ->whereNull('IfDeclined')
    ->whereNotNull('ManagerReplacerSignature')
    ->whereNotNull('BudgetOfficerSignature')
    ->orWhere('ManagerReplacer',Auth::user()->FullName)
    ->whereNull('ManagerReplacerSignature')
    ->whereNull('RecommendedbySignature')
    ->orWhere('ApprovalReplacer',Auth::user()->FullName)
    ->whereNull('ApprovalReplacerSignature')
    ->whereNull('GeneralManagerSignature')
    ->whereNotNull('RecommendedbySignature')
    ->whereNotNull('BudgetOfficerSignature')
    ->orWhere('ApprovalReplacer',Auth::user()->FullName)
    ->whereNull('ApprovalReplacerSignature')
    ->whereNull('GeneralManagerSignature')
    ->whereNotNull('ManagerReplacerSignature')
    ->whereNotNull('BudgetOfficerSignature')
    ->paginate(10,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','ApprovalReplacerSignature','ManagerReplacerSignature']);
      return view('Warehouse.RV.MyRVrequest',compact('myRVPendingrequest'));
    }
    public function declineRV($id)
    {
      RVMaster::where('RVNo',$id)->update(['IfDeclined'=>Auth::user()->FullName,'ApprovalReplacer'=>null,'ApprovalReplacerSignature'=>null]);
    }
    public function searchRV(Request $request)
    {
      $allRVMaster=RVMaster::orderBy('RVNo')->where('RVNo','LIKE',$request->RVNo)->paginate(1,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined']);
      return view('Warehouse.RVindex',compact('allRVMaster'));
    }
    public function searchRVforStock(Request $request)
    {
       $MasterResults=MasterItem::orderBy('id','DESC')->where('Description','LIKE','%'.$request->Description.'%')->paginate(8,['AccountCode','ItemCode','Description','Unit','CurrentQuantity','AlertIfBelow']);
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
      $allRVMaster=RVMaster::orderBy('RVNo','DESC')->where('RVNo','LIKE','%'.$request->search.'%')->paginate(10,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined','ApprovalReplacerSignature',
      'ManagerReplacerSignature']);
      $response=[
        'pagination'=>[
          'total'=> $allRVMaster->total(),
          'per_page'=>$allRVMaster->perPage(),
          'current_page'=>$allRVMaster->currentPage(),
          'last_page'=>$allRVMaster->lastPage(),
          'from'=>$allRVMaster->firstitem(),
          'to'=>$allRVMaster->lastitem(),
        ],
        'model'=>$allRVMaster
      ];

      return response()->json($response);
    }
    public function UnpurchaseList()
    {
      $unpurchaselist=RVMaster::orderBy('RVNo','ASC')
      ->whereNotNull('RecommendedbySignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->whereNotNull('GeneralManagerSignature')
      ->orWhereNotNull('ApprovalReplacerSignature')
      ->whereNotNull('RecommendedbySignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->orWhereNotNull('ApprovalReplacerSignature')
      ->whereNotNull('ManagerReplacerSignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->orWhereNotNull('ManagerReplacerSignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->whereNotNull('GeneralManagerSignature')
      ->paginate(10,['RVNo','Purpose','Requisitioner','RVDate']);
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
        RVMaster::where('RVNo', $id)->update(['ApprovalReplacer'=>null]);
    }
    public function AcceptSignatureBehalf($id)
    {
      RVMaster::where('RVNo', $id)->update(['ApprovalReplacerSignature'=>Auth::user()->Signature]);
      $RVMaster=RVMaster::where('RVNo',$id)->get(['Requisitioner','GeneralManager','RequisitionerSignature','BudgetOfficerSignature','ManagerReplacerSignature','RecommendedbySignature','GeneralManagerSignature','ApprovalReplacerSignature']);
      if(($RVMaster[0]->BudgetOfficerSignature!=null)&&(($RVMaster[0]->RecommendedbySignature!=null)||($RVMaster[0]->ManagerReplacerSignature!=null))&&(($RVMaster[0]->GeneralManagerSignature!=null)||($RVMaster[0]->ApprovalReplacerSignature!=null)))
      {
        $RVitems=RVDetail::where('RVNo', $id)->get();
        $forRRNoPOValidator = array();
        foreach ($RVitems as $rvitem)
        {
          $forRRNoPOValidator[] = array('RVNo' =>$rvitem->RVNo ,'Particulars'=>$rvitem->Particulars,'Unit'=>$rvitem->Unit ,'Quantity'=>$rvitem->Quantity ,'Remarks'=>$rvitem->Remarks,'AccountCode'=>$rvitem->AccountCode,'ItemCode'=>$rvitem->ItemCode);
        }
        RRValidatorNoPO::insert($forRRNoPOValidator);
        $RequisitionerMobile=User::where('FullName',$RVMaster[0]->Requisitioner)->get(['Mobile']);
        $GMMoble=User::where('FullName',$RVMaster[0]->GeneralManager)->get(['Mobile']);
        $NotifData = array('RequisitionerMobile' =>$RequisitionerMobile[0]->Mobile,'RVNo'=>$id,'ApprovalReplacer'=>Auth::user()->FullName,'GMMobile'=>$GMMoble[0]->Mobile);
        $NotifData=(object)$NotifData;
        $job = (new RVApprovalReplacer($NotifData))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
    }
    public function fetchSessionRV()
    {
      return Session::get('ItemSessionList');
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
      $replacer=User::where('id',$request->ManagerID)->get(['FullName']);
      RVMaster::where('RVNo',$id)->update(['ManagerReplacer'=>$replacer[0]->FullName]);
      $nospaceName=str_replace(' ','',$replacer[0]->FullName);
      $NotifyThisPerson = array('NotificReceiver' => $nospaceName);
      $NotifyThisPerson=(object)$NotifyThisPerson;
      $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    public function cancelrequestsentReplacer($id)
    {
      RVMaster::where('RVNo', $id)->update(['ManagerReplacer'=>null]);
    }
    public function AcceptManagerReplacer($id)
    {
      RVMaster::where('RVNo', $id)->update(['ManagerReplacerSignature'=>Auth::user()->Signature,'RecommendedbySignature'=>null]);
      $BudgetOfficer=RVMaster::where('RVNo', $id)->value('BudgetOfficer');
      $nospaceName=str_replace(' ','',$BudgetOfficer);
      $NotifyThisPerson = array('NotificReceiver' => $nospaceName);
      $NotifyThisPerson=(object)$NotifyThisPerson;
      $job = (new NewRVCreatedJob($NotifyThisPerson))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    public function savePendingRemark($id,Request $request)
    {
      $this->validate($request,[
          'PendingRemarks'=>'required',
      ]);
      RVMaster::where('RVNo',$id)->update(['PendingRemarks'=>$request->PendingRemarks]);
    }
    public function showBORemarks($id)
    {
      return RVMaster::where('RVNo',$id)->get(['PendingRemarks']);
    }
    public function RVRequestCount()
    {
      $myRVPendingrequest=RVMaster::orderBy('RVNo','DESC')
      ->orWhere('Recommendedby',Auth::user()->FullName)
      ->whereNull('RecommendedbySignature')
      ->whereNull('IfDeclined')
      ->whereNotNull('RequisitionerSignature')
      ->whereNull('ManagerReplacerSignature')
      ->orWhere('BudgetOfficer',Auth::user()->FullName)
      ->whereNull('BudgetOfficerSignature')
      ->whereNotNull('RecommendedbySignature')
      ->whereNull('IfDeclined')
      ->orWhere('BudgetOfficer',Auth::user()->FullName)
      ->whereNull('BudgetOfficerSignature')
      ->whereNotNull('ManagerReplacerSignature')
      ->whereNull('IfDeclined')
      ->orWhere('GeneralManager',Auth::user()->FullName)
      ->whereNull('GeneralManagerSignature')
      ->whereNull('ApprovalReplacerSignature')
      ->whereNull('IfDeclined')
      ->whereNotNull('RequisitionerSignature')
      ->whereNotNull('RecommendedbySignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('ApprovalReplacerSignature')
      ->orWhere('GeneralManager',Auth::user()->FullName)
      ->whereNull('GeneralManagerSignature')
      ->whereNull('ApprovalReplacerSignature')
      ->whereNull('IfDeclined')
      ->whereNotNull('ManagerReplacerSignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->orWhere('ManagerReplacer',Auth::user()->FullName)
      ->whereNull('ManagerReplacerSignature')
      ->whereNull('RecommendedbySignature')
      ->orWhere('ApprovalReplacer',Auth::user()->FullName)
      ->whereNull('ApprovalReplacerSignature')
      ->whereNull('GeneralManagerSignature')
      ->whereNotNull('RecommendedbySignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->orWhere('ApprovalReplacer',Auth::user()->FullName)
      ->whereNull('ApprovalReplacerSignature')
      ->whereNull('GeneralManagerSignature')
      ->whereNotNull('ManagerReplacerSignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->count();
      $response = array('RVRequestCount'=>$myRVPendingrequest);
      return response()->json($response);
    }
    public function RefreshRVWaitingForRRCount()
    {
      $RVWaitingPurchaseCount=RVMaster::orderBy('RVNo','ASC')
      ->whereNotNull('RecommendedbySignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->whereNotNull('GeneralManagerSignature')
      ->orWhereNotNull('ApprovalReplacerSignature')
      ->whereNotNull('RecommendedbySignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->orWhereNotNull('ApprovalReplacerSignature')
      ->whereNotNull('ManagerReplacerSignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->orWhereNotNull('ManagerReplacerSignature')
      ->whereNotNull('BudgetOfficerSignature')
      ->whereNull('IfPurchased')
      ->whereNotNull('GeneralManagerSignature')
      ->count();
      $response = array('RVwaitingRR' =>$RVWaitingPurchaseCount);
      return response()->json($response);
    }
}
