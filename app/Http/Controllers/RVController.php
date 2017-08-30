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
class RVController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    public function RVcreate()
    {
      $currentBudgetOfficer=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '7')->take(1)->get(['Fname','Lname']);
      $managers=User::where('Role','0')->whereNotNull('IsActive')->get(['Fname','Lname','id']);
      $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '2')->take(1)->get(['Fname','Lname']);
      return view('Warehouse.RV.RVCreateViews',compact('GM','managers','currentBudgetOfficer'));
    }
    public function SaveSession(Request $request)
    {
      $this->validate($request,[
        'Description'=>'required|unique:MasterItems',
      ]);
      $itemDetails = array('Description' =>$request->Description ,'Unit'=>$request->Unit,'Quantity'=>$request->Quantity,'Remarks'=>$request->Remarks,'AccountCode'=>null,'ItemCode'=>null);
      $itemDetails=(object)$itemDetails;
      Session::push('ItemSessionList',$itemDetails);
      return redirect()->back();
    }
    public function DeleteSession($id)
    {
      $itemList=Session::get('ItemSessionList');
      unset($itemList[$id]);
      Session::put('ItemSessionList',$itemList);
      return redirect()->back();
    }
    public function savingToTable(Request $request)
    {
      $this->validate($request,[
        'Purpose'=> 'required',
        'Recommendedby'=>'required',
        'BudgetAvailable'=>'regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
      ]);
      if (Session::get('ItemSessionList')==null)
      {
        return redirect()->back()->with('message', 'Item is Required');
      }
      $currentBudgetOfficer=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '7')->take(1)->get(['Fname','Lname']);//also using this at the bottom for RVdetails
      $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role', '2')->take(1)->get(['Fname','Lname']);//also using this at the bottom for RVdetails
      if (empty($currentBudgetOfficer[0]))
      {
        return redirect()->back()->with('message','Budget Officer cannot be empty');
      }
      if (empty($GM[0]))
      {
        return redirect()->back()->with('message','General Manager cannot be empty');
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
      $recommended=User::whereNotNull('IsActive')->where('id',$request->Recommendedby)->get(['Fname','Lname','Position']);
      $RVMaster=new RVMaster;
      $RVMaster->RVNo=$incremented;
      $RVMaster->RVDate=$date;
      $RVMaster->Purpose=$request->Purpose;
      $RVMaster->Requisitioner=Auth::user()->Fname.' '.Auth::user()->Lname;
      $RVMaster->RequisitionerPosition=Auth::user()->Position;
      $RVMaster->RequisitionerSignature=Auth::user()->Signature;
      if ($recommended[0]->Fname.' '.$recommended[0]->Lname==Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        $RVMaster->RecommendedbySignature=Auth::user()->Signature;
      }
      if ($currentBudgetOfficer[0]->Fname.' '.$currentBudgetOfficer[0]->Lname==Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        $RVMaster->BudgetOfficerSignature=Auth::user()->Signature;
      }
      if ($GM[0]->Fname.' '.$GM[0]->Lname==Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        $RVMaster->GeneralManagerSignature=Auth::user()->Signature;
      }
      $RVMaster->Recommendedby=$recommended[0]->Fname.' '.$recommended[0]->Lname;
      $RVMaster->RecommendedbyPosition=$recommended[0]->Position;
      if (($request->BudgetAvailable==null)&&(Auth::user()->Role==7))
      {
        return redirect()->back()->with('message', 'Budget available is required');
      }elseif(Auth::user()->Role=='7')
      {
        $budgetNocomma=str_replace(',','',$request->BudgetAvailable);
        $RVMaster->BudgetAvailable=$budgetNocomma;
      }
      $RVMaster->BudgetOfficer=$currentBudgetOfficer[0]->Fname.' '.$currentBudgetOfficer[0]->Lname;
      $RVMaster->GeneralManager=$GM[0]->Fname.' '.$GM[0]->Lname;
      $RVMaster->save();

      $forRVdetailDB = array();
      foreach (Session::get('ItemSessionList') as $SessionItem)
      {
        $forRVdetailDB[] = array('RVNo' =>$incremented ,'Particulars'=>$SessionItem->Description,'Unit'=>$SessionItem->Unit,'Quantity'=>$SessionItem->Quantity,'Remarks'=>$SessionItem->Remarks,'AccountCode'=>$SessionItem->AccountCode,'ItemCode'=>$SessionItem->ItemCode);
      }
      RVDetail::insert($forRVdetailDB);
      Session::forget('ItemSessionList');
      Session::forget('SessionForStock');
      return redirect()->route('RVindexView')->with('message','Success');
    }

    public function RVindexView()
    {
      return view('Warehouse.RV.RVindex');
    }
    public function RVfullPreview($id)
    {
      $RVDetails=RVDetail::where('RVNo',$id)->get(['RVNo','Particulars','Unit','Quantity','Remarks']);
      $RVMaster=RVMaster::where('RVNo',$id)->get();
      $checkRR=RRMaster::where('RVNo', $id)->take(1)->value('RRNo');
      $checkPO=POMaster::where('RVNo',$id)->take(1)->value('PONo');
      if (($checkPO==null)&&($checkRR!=null))
      {
       $undeliveredTotal=RRValidatorNoPO::where('RVNo',$id)->sum('Quantity');
      }
      return view('Warehouse.RV.FullRVpreview',compact('RVMaster','RVDetails','checkPO','checkRR','undeliveredTotal'));
    }
    public function Signature(Request $request)
    {
      $RVMasterNames=RVMaster::where('RVNo',$request->RVNo)->get(['Requisitioner','BudgetOfficer','Recommendedby','GeneralManager']);
      if ($RVMasterNames[0]->Requisitioner == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        RVMaster::where('RVNo',$request->RVNo)->update(['RequisitionerSignature'=>Auth::user()->Signature]);
      }
      if ($RVMasterNames[0]->BudgetOfficer == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        $this->validate($request,[
            'BudgetAvailable'=>'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
        ]);
        $budget=str_replace(',','',$request->BudgetAvailable);
        RVMaster::where('RVNo',$request->RVNo)->update(['BudgetOfficerSignature'=>Auth::user()->Signature,'BudgetAvailable'=>$budget]);
      }
      if ($RVMasterNames[0]->Recommendedby == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        RVMaster::where('RVNo',$request->RVNo)->update(['RecommendedbySignature'=>Auth::user()->Signature]);
      }
      if ($RVMasterNames[0]->GeneralManager == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        RVMaster::where('RVNo',$request->RVNo)->update(['GeneralManagerSignature'=>Auth::user()->Signature,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
      }
      $RVSignatures=RVMaster::where('RVNo',$request->RVNo)->get(['RequisitionerSignature','BudgetOfficerSignature','RecommendedbySignature','GeneralManagerSignature','ApprovalReplacerSignature']);
      if ((($RVSignatures[0]->RequisitionerSignature!=null)&&($RVSignatures[0]->BudgetOfficerSignature!=null)&&($RVSignatures[0]->RecommendedbySignature!=null)&&($RVSignatures[0]->GeneralManagerSignature!=null))||(($RVSignatures[0]->RequisitionerSignature!=null)&&($RVSignatures[0]->BudgetOfficerSignature!=null)&&($RVSignatures[0]->RecommendedbySignature!=null)&&($RVSignatures[0]->ApprovalReplacerSignature!=null)))
      {
        $RVitems=RVDetail::where('RVNo', $request->RVNo)->get();
        $forRRNoPOValidator = array();
        foreach ($RVitems as $rvitem)
        {
          $forRRNoPOValidator[] = array('RVNo' =>$rvitem->RVNo ,'Particulars'=>$rvitem->Particulars,'Unit'=>$rvitem->Unit ,'Quantity'=>$rvitem->Quantity ,'Remarks'=>$rvitem->Remarks,'ItemCode'=>$rvitem->ItemCode,'AccountCode'=>$rvitem->AccountCode);
        }
        RRValidatorNoPO::insert($forRRNoPOValidator);
      }
      return redirect()->back();
    }
    public function RVrequest()
    {
    $myRVPendingrequest=RVMaster::orderBy('RVNo','DESC')->where('Requisitioner',Auth::user()->Fname.' '.Auth::user()->Lname)
    ->whereNull('RequisitionerSignature')
    ->whereNull('IfDeclined')
    ->orWhere('Recommendedby',Auth::user()->Fname.' '.Auth::user()->Lname)
    ->whereNull('RecommendedbySignature')
    ->whereNull('IfDeclined')
    ->orWhere('BudgetOfficer',Auth::user()->Fname.' '.Auth::user()->Lname)
    ->whereNull('BudgetOfficerSignature')
    ->whereNull('IfDeclined')
    ->orWhere('GeneralManager',Auth::user()->Fname.' '.Auth::user()->Lname)
    ->whereNull('GeneralManagerSignature')
    ->whereNull('IfDeclined')
    ->whereNull('ApprovalReplacerSignature')
    ->paginate(10,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','ApprovalReplacerSignature']);
      return view('Warehouse.RV.MyRVrequest',compact('myRVPendingrequest'));
    }
    public function declineRV($id)
    {
      RVMaster::where('RVNo',$id)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
      return redirect()->back();
    }
    public function searchRV(Request $request)
    {
      $allRVMaster=RVMaster::orderBy('RVNo')->where('RVNo','LIKE',$request->RVNo)->paginate(1,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined']);
      return view('Warehouse.RVindex',compact('allRVMaster'));
    }
    public function searchRVforStock(Request $request)
    {
      $forStockRV=MasterItem::orderBy('id','DESC')->where('Description','LIKE','%'.$request->Description.'%')->paginate(5,['AccountCode','ItemCode_id','Description','Unit']);
      Session::put('SessionForStock',$forStockRV);
      return redirect()->back();
    }
    public function addtoStockSession(Request $request)
    {
      if (Session::has('ItemSessionList'))
      {
        foreach (Session::get('ItemSessionList') as $sessionItem)
        {
          if ($sessionItem->Description==$request->Description) {
            return redirect()->back()->with('message','This item is already in the list');
          }
        }
      }
      $itemselected = array('Description' =>$request->Description ,'Unit'=>$request->Unit,'Quantity'=>$request->Quantity,'Remarks'=>$request->Remarks,'AccountCode'=>$request->AccountCode,'ItemCode'=>$request->ItemCode);
      $itemselected=(object)$itemselected;
      Session::push('ItemSessionList',$itemselected);
      return redirect()->back();
    }

    public function RVIndexSearch(Request $request)
    {
      $allRVMaster=RVMaster::orderBy('RVNo','DESC')->where('RVNo','LIKE','%'.$request->search.'%')->paginate(10,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined','ApprovalReplacerSignature']);
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
      $unpurchaselist=RVMaster::orderBy('RVNo','DESC')->whereNotNull('RecommendedbySignature')->whereNotNull('BudgetOfficerSignature')->whereNull('IfPurchased')->whereNotNull('GeneralManagerSignature')->orWhereNotNull('ApprovalReplacerSignature')->whereNotNull('RecommendedbySignature')->whereNotNull('BudgetOfficerSignature')->whereNull('IfPurchased')->paginate(10,['RVNo','Purpose','Requisitioner','RVDate']);
      return view('Warehouse.RV.myUnpurchaseRVlist',compact('unpurchaselist'));
    }
    public function updateBudgetAvailable($id, Request $request)
    {
      $this->validate($request,[
        'BudgetUpdate' => 'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
      ]);
      $noComma=str_replace(',','',$request->BudgetUpdate);
      RVMaster::where('RVNo',$id)->update(['BudgetAvailable'=>$noComma]);
      return redirect()->back();
    }
    public function SignatureApproveInBehalf($id)
    {
      if (Auth::user()->Role==0)
      {
        RVMaster::where('RVNo', $id)->update(['ApprovalReplacerFname'=>Auth::user()->Fname,'ApprovalReplacerLname'=>Auth::user()->Lname]);
      }
      return redirect()->back();
    }
    public function SignatureApproveInBehalfCancel($id)
    {
      $RVMaster=RVMaster::where('RVNo', $id)->get(['ApprovalReplacerFname','ApprovalReplacerLname','ApprovalReplacerSignature']);
      if (($RVMaster[0]->ApprovalReplacerFname.' '.$RVMaster[0]->ApprovalReplacerLname==Auth::user()->Fname.' '.Auth::user()->Lname)&&($RVMaster[0]->ApprovalReplacerSignature==null))
      {
        RVMaster::where('RVNo', $id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null]);
      }
      return redirect()->back();
    }
    public function SignatureBehalfDenybyadmin($id)
    {
      RVMaster::where('RVNo', $id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null]);
      return redirect()->back();
    }
    public function confirmSignatureBehalf($id)
    {
      $RVMaster=RVMaster::where('RVNo', $id)->get(['ApprovalReplacerFname','ApprovalReplacerLname']);
      $manager=User::whereNotNull('IsActive')->where('Fname',$RVMaster[0]->ApprovalReplacerFname)->where('Lname',$RVMaster[0]->ApprovalReplacerLname)->get(['Signature','Position']);
      RVMaster::where('RVNo', $id)->update(['ApprovalReplacerSignature'=>$manager[0]->Signature,'ApprovalReplacerPosition'=>$manager[0]->Position]);

      $RVSignatures=RVMaster::where('RVNo',$id)->get(['RequisitionerSignature','BudgetOfficerSignature','RecommendedbySignature','GeneralManagerSignature','ApprovalReplacerSignature']);
      if ((($RVSignatures[0]->RequisitionerSignature!=null)&&($RVSignatures[0]->BudgetOfficerSignature!=null)&&($RVSignatures[0]->RecommendedbySignature!=null)&&($RVSignatures[0]->GeneralManagerSignature!=null))||(($RVSignatures[0]->RequisitionerSignature!=null)&&($RVSignatures[0]->BudgetOfficerSignature!=null)&&($RVSignatures[0]->RecommendedbySignature!=null)&&($RVSignatures[0]->ApprovalReplacerSignature!=null)))
      {
        $RVitems=RVDetail::where('RVNo', $id)->get();
        $forRRNoPOValidator = array();
        foreach ($RVitems as $rvitem)
        {
          $forRRNoPOValidator[] = array('RVNo' =>$rvitem->RVNo ,'Particulars'=>$rvitem->Particulars,'Unit'=>$rvitem->Unit ,'Quantity'=>$rvitem->Quantity ,'Remarks'=>$rvitem->Remarks,'AccountCode'=>$rvitem->AccountCode,'ItemCode'=>$rvitem->ItemCode);
        }
        RRValidatorNoPO::insert($forRRNoPOValidator);
      }
      return redirect()->back();
    }
}
