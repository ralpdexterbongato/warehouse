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
use App\MCTValidator;
class MIRSController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function MIRScreate()
  {
    $GenMan=User::orderBy('id','DESC')->where('Role','2')->whereNotNull('IsActive')->take(1)->get(['Fname','Lname','id']);
    $allManager=User::where('Role', '0')->whereNotNull('IsActive')->get(['id','Fname','Lname','Position']);
    return view('Warehouse.MIRS.MIRSCreate',compact('allManager','GenMan'));
  }
  public function fetchSessionMIRS()
  {
      return Session::get('ItemSelected');
  }
  public function addingSessionItem(Request $request)
  {
    $this->SessionValidator($request);
    $MTDetails=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('ItemCode',$request->ItemCode_id)->value('CurrentQuantity');
    if($MTDetails >=$request->Quantity)
    {
      $itemselected =[
      'ItemCode_id' => $request->ItemCode_id,'Particulars' => $request->Particulars,'Unit' => $request->Unit,'Remarks'=>$request->Remarks,'Quantity' => $request->Quantity,
      ];
      if (Session::has('ItemSelected'))
      {
        foreach (Session::get('ItemSelected') as $selected)
        {
          if ($selected->ItemCode_id == $request->ItemCode_id) {
            return response()->json(['error'=>'This Item has been added already']);
          }
        }
      }
        $itemselected = (object)$itemselected;
        Session::push('ItemSelected',$itemselected);
        return redirect('/MIRS-add');
    }else
    {
      return redirect()->back()->with('message', 'Quantity stock is not enough for your request');
    }
  }
  public function deletePartSession($id)
  {
    if(Session::has('ItemSelected'))
    {
      $items=(array)Session::get('ItemSelected');
      foreach ($items as $key=>$item)
      {
        if ($item->ItemCode_id == $id)
        {
          unset($items[$key]);
        }
      }
      Session::put('ItemSelected',$items);
      return redirect()->back();
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
      $date=Carbon::today();
      $year=Carbon::today()->format('y');
        $lastinserted=MIRSMaster::orderBy('id','DESC')->take(1)->value('MIRSNo');
        if (count($lastinserted)>0)
        {
          $numOnly=substr($lastinserted,'3');
          $numOnly = (int)$numOnly;
          $soloID=$numOnly + 1;
          $incremented = $year .'-' . sprintf("%04d",$soloID);
        }else
        {
            $incremented = $year . '-' . sprintf("%04d",'1');
        }
      $recommend=User::whereNotNull('IsActive')->where('id',$request->Recommendedby)->get(['Position','Fname','Lname']);
      $approve=User::whereNotNull('IsActive')->where('id',$request->Approvedby)->get(['Position','Fname','Lname']);
      $master=new MIRSMaster;
      $master->MIRSNo = $incremented;
      $master->Purpose =$request->Purpose;
      $master->Preparedby =Auth::user()->Fname . ' ' .Auth::user()->Lname;
      $master->PreparedSignature=Auth::user()->Signature;
      $master->PreparedPosition=Auth::user()->Position;
      $master->Recommendedby =$recommend[0]->Fname .' '. $recommend[0]->Lname;
      $master->RecommendPosition=$recommend[0]->Position;
      $master->Approvedby = $approve[0]->Fname .' '. $approve[0]->Lname;
      $master->ApprovePosition=$approve[0]->Position;
      $master->MIRSDate = $date;
      if ($recommend[0]->Fname.' '.$recommend[0]->Lname == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        $master->RecommendSignature=Auth::user()->Signature;
      }
      if ($approve[0]->Fname.' '.$approve[0]->Lname == Auth::user()->Fname.' '.Auth::user()->Lname)
      {
        $master->ApproveSignature=Auth::user()->Signature;
      }
      $master->save();
      $selectedITEMS=Session::get('ItemSelected');
      $selectedITEMS = (array)$selectedITEMS;
      $forMIRSDetailtbl = array();
      foreach ($selectedITEMS as $items)
      {
        $forMIRSDetailtbl[] = array('MIRSNo' => $incremented ,'ItemCode'=>$items->ItemCode_id,'Particulars'=>$items->Particulars,'Unit'=>$items->Unit,'Remarks'=>$items->Remarks,'Quantity'=>$items->Quantity);
      }
      MIRSDetail::insert($forMIRSDetailtbl);
        Session::forget('ItemSelected');
        return ['redirect'=>route('full-mirs',[$incremented])];
    }else
    {
      return redirect()->back()->with('message', 'Items cannot be empty');
    }
  }
  public function storingMIRSValidator($request)
  {
    $this->validate($request,[
      'Purpose'=>'required',
      'Recommendedby'=>'required',
      'Approvedby'=>'required',
    ]);
  }
  public function fullMIRSview($id)
  {
    $MCTValidatorQty=MCTValidator::where('MIRSNo',$id)->get(['Quantity']);
    $unclaimed=$MCTValidatorQty->sum('Quantity');
    $MIRSDetail=MIRSDetail::where('MIRSNo', $id)->get();
    $MIRSMaster=MIRSMaster::where('MIRSNo', $id)->get();
    $MCTNumber=MCTMaster::where('MIRSNo', $id)->value('MCTNo');
    return view('Warehouse.MIRS.MIRSpreview',compact('MIRSDetail','MIRSMaster','MCTNumber','unclaimed'));
  }
  public function searchMIRSNo(Request $request)
  {
    $mirsResult=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get(['MIRSNo','Purpose','Preparedby','PreparedSignature','Recommendedby','RecommendSignature','Approvedby','ApproveSignature','MIRSDate','IfDeclined','ApprovalReplacerSignature']);
    if (empty($mirsResult[0]->MIRSNo))
    {
      return redirect()->route('MIRSgridview');
    }
    return view('Warehouse.MIRS.MIRS-index',compact('mirsResult'));
  }
  public function DeniedMIRS($id)
  {
    MIRSMaster::where('MIRSNo',$id)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
    return redirect()->route('MIRSgridview');
  }

  public function Indexgrid()
  {
    $AllmasterMIRS=MIRSMaster::orderBy('id','DESC')->paginate(10,['MIRSNo','Purpose','Preparedby','PreparedSignature','Recommendedby','RecommendSignature','Approvedby','ApproveSignature','MIRSDate','IfDeclined','ApprovalReplacerSignature']);
    return view('Warehouse.MIRS.MIRS-index',compact('AllmasterMIRS'));
  }

  public function MIRSSignature(Request $request)
  {
    $signableNames=MIRSMaster::where('MIRSNo',$request->MIRSNo)->get(['Recommendedby','Approvedby']);
    if($signableNames[0]->Recommendedby==Auth::user()->Fname .' '.Auth::user()->Lname)
    {
      MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['RecommendSignature'=>Auth::user()->Signature]);
    }
    if ($signableNames[0]->Approvedby==Auth::user()->Fname .' '.Auth::user()->Lname)
    {
      MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['ApproveSignature'=>Auth::user()->Signature,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
    }
    $signaturesCheck=MIRSMaster::where('MIRSNo',$request->MIRSNo)->get(['RecommendSignature','ApproveSignature']);
    if (($signaturesCheck[0]->RecommendSignature!=null)&&($signaturesCheck[0]->ApproveSignature!=null))
    {
      $MIRSitems=MIRSDetail::where('MIRSNo',$request->MIRSNo)->get(['MIRSNo','ItemCode','Particulars','Unit','Quantity','Remarks']);
      $forValidatortbl = array();
      foreach ($MIRSitems as $item)
      {
        $forValidatortbl[] = array('MIRSNo' =>$item->MIRSNo ,'ItemCode'=> $item->ItemCode,'Particulars'=>$item->Particulars,'Unit'=>$item->Unit,'Quantity'=>$item->Quantity,'Remarks'=>$item->Remarks);
      }
      MCTValidator::insert($forValidatortbl);
    }
    return redirect()->back();

  }
  public function mirsRequestcheck()
  {
    $myrequestMIRS=MIRSMaster::orderBy('id','DESC')->whereNull('IfDeclined')->where('Preparedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('PreparedSignature')->whereNull('ApprovalReplacerSignature')
                    ->orWhere('Recommendedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('RecommendSignature')->whereNull('IfDeclined')->whereNull('ApprovalReplacerSignature')
                    ->orWhere('Approvedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('ApproveSignature')->whereNull('IfDeclined')->whereNull('ApprovalReplacerSignature')
                    ->paginate(10,['MIRSNo','Purpose','Preparedby','Approvedby','Recommendedby','MIRSDate','RecommendSignature','PreparedSignature','ApproveSignature']);
    return view('Warehouse.MIRS.myMIRSrequest',compact('myrequestMIRS'));
  }
  public function readyForMCT()
  {
    $readyformct=MIRSMaster::orderBy('MIRSNo','DESC')->whereNull('WithMCT')->where('RecommendSignature','!=','')->where('ApproveSignature','!=','')->where('PreparedSignature','!=','')->orWhere('ApprovalReplacerSignature','!=',null)
    ->paginate(10,['MIRSNo','Purpose','Preparedby','Recommendedby','Approvedby','MIRSDate']);
  return view('Warehouse.MIRS.MIRSReadyList',compact('readyformct'));
  }
  public function ApproveMIRSinBehalf($id)
  {
    if (Auth::user()->Role==0)
    {
      MIRSMaster::where('MIRSNo', $id)->update(['ApprovalReplacerFname'=>Auth::user()->Fname,'ApprovalReplacerLname'=>Auth::user()->Lname]);
    }
    return redirect()->back();
  }
  public function CancelApproveMIRSinBehalf($id)
  {
    if (Auth::user()->Role==0)
    {
        $ManagerApproving=MIRSMaster::where('MIRSNo', $id)->get(['ApprovalReplacerFname','ApprovalReplacerLname','ApprovalReplacerSignature']);
        if (($ManagerApproving[0]->ApprovalReplacerFname.' '.$ManagerApproving[0]->ApprovalReplacerLname==Auth::user()->Fname.' '.Auth::user()->Lname)&&($ManagerApproving[0]->ApprovalReplacerSignature==null))
        {
          MIRSMaster::where('MIRSNo', $id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null]);
        }
        return redirect()->back();
    }
  }
  public function DenyRequestofManagerMIRS($id)
  {
    if (Auth::user()->Role==1)
    {
      $ManagerApproving=MIRSMaster::where('MIRSNo', $id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null]);
    }
    return redirect()->back();
  }
  public function letManagerSignatureGM($id)
  {
    if (Auth::user()->Role==1)
    {
      $MIRSitems=MIRSDetail::where('MIRSNo',$id)->get(['MIRSNo','ItemCode','Particulars','Unit','Quantity','Remarks']);
      $forValidatortbl = array();
      foreach ($MIRSitems as $item)
      {
        $forValidatortbl[] = array('MIRSNo' =>$item->MIRSNo ,'ItemCode'=> $item->ItemCode,'Particulars'=>$item->Particulars,'Unit'=>$item->Unit,'Quantity'=>$item->Quantity,'Remarks'=>$item->Remarks);
      }
      MCTValidator::insert($forValidatortbl);

      $approveReplacer=MIRSMaster::where('MIRSNo',$id)->get(['ApprovalReplacerFname','ApprovalReplacerLname']);
      $replacer=User::whereNotNull('IsActive')->where('Fname',$approveReplacer[0]->ApprovalReplacerFname)->where('Lname',$approveReplacer[0]->ApprovalReplacerLname)->get(['Signature','Position']);
      MIRSMaster::where('MIRSNo',$id)->update(['ApprovalReplacerSignature'=>$replacer[0]->Signature,'ApprovalReplacerPosition'=>$replacer[0]->Position]);
      return redirect()->back();
    }
  }

}
