<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\User;
use App\RVMaster;
use App\RVDetail;
use Carbon\Carbon;
use Auth;
class RVController extends Controller
{
    public function RVcreate()
    {
      $currentBudgetOfficer=User::orderBy('id','DESC')->where('Role', '7')->take(1)->get(['Fname','Lname']);
      $managers=User::where('Role','0')->get(['Fname','Lname','id']);
      $GM=User::orderBy('id','DESC')->where('Role', '2')->take(1)->get(['Fname','Lname']);
      return view('Warehouse.RVCreateViews',compact('GM','managers','currentBudgetOfficer'));
    }
    public function SaveSession(Request $request)
    {

      $itemDetails = array('Description' =>$request->Description ,'Unit'=>$request->Unit,'Quantity'=>$request->Quantity,'Remarks'=>$request->Remarks);
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
      ]);
      if (Session::get('ItemSessionList')==null)
      {
        return redirect()->back()->with('message', 'Item is Required');
      }
      $currentBudgetOfficer=User::orderBy('id','DESC')->where('Role', '7')->take(1)->get(['Fname','Lname']);//also using this at the bottom for RVdetails
      $GM=User::orderBy('id','DESC')->where('Role', '2')->take(1)->get(['Fname','Lname']);//also using this at the bottom for RVdetails
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
      $recommended=User::where('id',$request->Recommendedby)->get(['Fname','Lname','Position']);
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
      $RVMaster->BudgetOfficer=$currentBudgetOfficer[0]->Fname.' '.$currentBudgetOfficer[0]->Lname;
      $RVMaster->GeneralManager=$GM[0]->Fname.' '.$GM[0]->Lname;
      $RVMaster->save();

      foreach (Session::get('ItemSessionList') as $SessionItem)
      {
        $RVDetailDB=new RVDetail;
        $RVDetailDB->RVNo=$incremented;
        $RVDetailDB->Particulars=$SessionItem->Description;
        $RVDetailDB->Unit=$SessionItem->Unit;
        $RVDetailDB->Quantity=$SessionItem->Quantity;
        $RVDetailDB->Remarks=$SessionItem->Remarks;
        $RVDetailDB->save();
      }
      Session::forget('ItemSessionList');
      return redirect()->back()->with('message', 'Success');
    }

    public function RVindexView()
    {
      $allRVMaster=RVMaster::orderBy('RVNo','DESC')->paginate(10,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined']);
      return view('Warehouse.RVindex',compact('allRVMaster'));
    }
    public function RVfullPreview($id)
    {
      $RVDetails=RVDetail::where('RVNo',$id)->get();
      $RVMaster=RVMaster::where('RVNo',$id)->get();
      return view('Warehouse.FullRVpreview',compact('RVMaster','RVDetails'));
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
        RVMaster::where('RVNo',$request->RVNo)->update(['BudgetOfficerSignature'=>Auth::user()->Signature]);
      }
      if ($RVMasterNames[0]->Recommendedby == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        RVMaster::where('RVNo',$request->RVNo)->update(['RecommendedbySignature'=>Auth::user()->Signature]);
      }
      if ($RVMasterNames[0]->GeneralManager == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        RVMaster::where('RVNo',$request->RVNo)->update(['GeneralManagerSignature'=>Auth::user()->Signature]);
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
    ->paginate(10,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate']);
      return view('Warehouse.MyRVrequest',compact('myRVPendingrequest'));
    }
    public function declineRV(Request $request)
    {
      $this->validate($request,[
        'RVNo'=>'required',
      ]);
      RVMaster::where('RVNo',$request->RVNo)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
      return redirect()->back()->with('message','Successfully Declined');
    }
    public function searchRV(Request $request)
    {
      $allRVMaster=RVMaster::orderBy('RVNo')->where('RVNo',$request->RVNo)->paginate(1,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined']);
      return view('Warehouse.RVindex',compact('allRVMaster'));
    }
    public function searchAJAX()
    {
      if ($request->ajax())
      {
        $output="";
        $RVmasters=RVMaster::orderBy('RVNo')->where('RVNo','LIKE','%'.$request->RVNo.'%')->paginate(1,['RVNo','Purpose','Requisitioner','RequisitionerSignature','Recommendedby','RecommendedbySignature','BudgetOfficer','BudgetOfficerSignature','GeneralManager','GeneralManagerSignature','RVDate','IfDeclined']);
        if ($RVmasters)
        {
          foreach ($RVmasters as $RVmaster)
          {
            if (($RVmaster->RequisitionerSignature!=null)&&($RVmaster->RecommendedbySignature!=null)&&($RVmaster->BudgetOfficerSignature!=null)&&($RVmaster->GeneralManagerSignature!=null))
            {
              $status='<i class="fa fa-thumbs-up"></i>';
            }elseif($RVmaster->IfDeclined!=null)
            {
              $status='<i class="fa fa-times decliner"></i>';
            }
            else
            {
              $status='<i class="fa fa-clock-o"></i>';
            }
            $output.='<tr>'.
                    '<td>'.$RVmaster->RVNo.'</td>'.
                    '<td>'.$RVmaster->Purpose.'</td>'.
                    '<td>'.$RVmaster->Requisitioner.'</td>'.
                    '<td>'.$RVmaster->Recommendedby.'</td>'.
                    '<td>'.$RVmaster->BudgetOfficer.'</td>'.
                    '<td>'.$RVmaster->GeneralManager.'</td>'.
                    '<td>'.$RVmaster->RVDate->format('m/d/Y').'</td>'.
                    '<td>'.$status.'</td>'.
                    '<td>'.'<i class="fa fa-eye"></i>'.'</td>'.
                    '</tr>';
          }
          $output.=$RVmasters->links();
          return \Response($output);
        }
      }
    }
}
