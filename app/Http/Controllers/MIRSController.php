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
use Redis;
use App\Jobs\SendMIRSNotification;
use App\Jobs\NewApprovedMIRSJob;
class MIRSController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function MIRScreate()
  {
    $GenMan=User::orderBy('id','DESC')->where('Role','2')->whereNotNull('IsActive')->take(1)->get(['Fname','Lname','id']);
    $mymanager=User::where('id',Auth::user()->Manager)->get(['Fname','Lname']);
    return view('Warehouse.MIRS.MIRSCreate',compact('mymanager','GenMan'));
  }
  public function fetchSessionMIRS()
  {
      return Session::get('ItemSelected');
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
      $ApproveReplacer=User::whereNotNull('IfApproveReplacer')->take(1)->get(['Fname','Lname']);
      $recommend=User::whereNotNull('IsActive')->where('id',Auth::user()->Manager)->get(['Position','Fname','Lname']);
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
      if (!empty($ApproveReplacer[0]))
      {
      $master->ApprovalReplacer=$ApproveReplacer[0]->Fname.' '.$ApproveReplacer[0]->Lname;
      }
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
        $forMIRSDetailtbl[] = array('MIRSNo' => $incremented ,'ItemCode'=>$items->ItemCode,'Particulars'=>$items->Particulars,'Remarks'=>$items->Remarks,'Quantity'=>$items->Quantity,'Unit'=>$items->Unit);
      }
      MIRSDetail::insert($forMIRSDetailtbl);
      Session::forget('ItemSelected');

      $Recommended=str_replace(' ','',$recommend[0]->Fname.$recommend[0]->Lname);
        $newmirs = array('tobeNotifyName'=>$Recommended);
        $newmirs=(object)$newmirs;
        $job = (new SendMIRSNotification($newmirs))
                    ->delay(Carbon::now()->addSeconds(5));

        dispatch($job);
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
    $MCTValidatorQty=MCTValidator::where('MIRSNo',$id)->get(['Quantity']);
    $unclaimed=$MCTValidatorQty->sum('Quantity');
    $MIRSDetail=MIRSDetail::where('MIRSNo', $id)->get();
    $MIRSMaster=MIRSMaster::where('MIRSNo', $id)->get();
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
    return MIRSMaster::where('MIRSNo','LIKE','%'.$request->MIRSNo.'%')->orderBy('MIRSNo','DESC')->paginate(10,['MIRSNo','Purpose','Preparedby','PreparedSignature','Recommendedby','RecommendSignature','Approvedby','ApproveSignature','MIRSDate','IfDeclined','ApprovalReplacerSignature','ManagerReplacerSignature']);
  }
  public function MIRSIndexPage()
  {
    return view('Warehouse.MIRS.MIRS-index');
  }
  public function DeniedMIRS($id)
  {
    MIRSMaster::where('MIRSNo',$id)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname,'ApprovalReplacerSignature'=>null]);
    return redirect()->route('MIRSgridview');
  }

  public function MIRSSignature($id)
  {
    $signableNames=MIRSMaster::where('MIRSNo',$id)->get(['Recommendedby','Approvedby','ApprovalReplacer']);
    if($signableNames[0]->Recommendedby==Auth::user()->Fname .' '.Auth::user()->Lname)
    {
      MIRSMaster::where('MIRSNo',$id)->update(['RecommendSignature'=>Auth::user()->Signature,'ManagerReplacerSignature'=>null,'ManagerReplacer'=>null]);
      $NospaceName=str_replace(' ','',$signableNames[0]->Approvedby);
      $tobeNotifycontainer  = array('tobeNotifyName' =>$NospaceName);
      $tobeNotifycontainer=(object)$tobeNotifycontainer;
      $job = (new SendMIRSNotification($tobeNotifycontainer))
                  ->delay(Carbon::now()->addSeconds(5));

      dispatch($job);
      if ($signableNames[0]->ApprovalReplacer!=null)
      {
        $nospaceName=str_replace(' ','',$signableNames[0]->ApprovalReplacer);
        $tobeNotifycontainer  = array('tobeNotifyName' =>$nospaceName);
        $tobeNotifycontainer=(object)$tobeNotifycontainer;
        $job = (new SendMIRSNotification($tobeNotifycontainer))
                    ->delay(Carbon::now()->addSeconds(5));

        dispatch($job);
      }
    }
    if ($signableNames[0]->Approvedby==Auth::user()->Fname .' '.Auth::user()->Lname)
    {
      MIRSMaster::where('MIRSNo',$id)->update(['ApproveSignature'=>Auth::user()->Signature,'ApprovalReplacerSignature'=>null]);
      $role=['role'=>'3and4'];
      $job=(new NewApprovedMIRSJob($role))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    $signaturesCheck=MIRSMaster::where('MIRSNo',$id)->get(['RecommendSignature','ApproveSignature','ManagerReplacerSignature']);
    if ((($signaturesCheck[0]->RecommendSignature!=null)||($signaturesCheck[0]->ManagerReplacerSignature!=null))&&($signaturesCheck[0]->ApproveSignature!=null))
    {
      $MIRSitems=MIRSDetail::where('MIRSNo',$id)->get(['MIRSNo','ItemCode','Particulars','Unit','Quantity','Remarks']);
      $forValidatortbl = array();
      foreach ($MIRSitems as $item)
      {
        $forValidatortbl[] = array('MIRSNo' =>$item->MIRSNo ,'ItemCode'=> $item->ItemCode,'Particulars'=>$item->Particulars,'Unit'=>$item->Unit,'Quantity'=>$item->Quantity,'Remarks'=>$item->Remarks);
      }
      MCTValidator::insert($forValidatortbl);
    }
  }
  public function mirsRequestcheck()
  {
    if (Auth::user()->Role==2)
    {
      $myrequestMIRS=MIRSMaster::orderBy('id','DESC')
                      ->whereNull('IfDeclined')->where('Approvedby',Auth::user()->Fname." ".Auth::user()->Lname)
                      ->whereNull('ApproveSignature')->whereNull('ApprovalReplacerSignature')->whereNotNull('PreparedSignature')->whereNotNull('RecommendSignature')
                      ->orWhereNull('IfDeclined')->where('Approvedby',Auth::user()->Fname." ".Auth::user()->Lname)
                      ->whereNull('ApproveSignature')->whereNull('ApprovalReplacerSignature')->whereNotNull('PreparedSignature')->whereNotNull('ManagerReplacerSignature')
                      ->paginate(10,['MIRSNo','Purpose','Preparedby','Approvedby','Recommendedby','MIRSDate','RecommendSignature','PreparedSignature','ApproveSignature','ManagerReplacerSignature']);
    }elseif(Auth::user()->Role==0)
    {
    $myrequestMIRS=MIRSMaster::orderBy('id','DESC')->where('Recommendedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('RecommendSignature')->whereNull('IfDeclined')->whereNull('ManagerReplacerSignature')
                    ->orWhere('ManagerReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ManagerReplacerSignature')->whereNull('RecommendSignature')
                    ->orWhere('ApprovalReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)
                    ->whereNotNull('ManagerReplacerSignature')->whereNull('ApprovalReplacerSignature')->whereNull('ApproveSignature')
                    ->orWhere('ApprovalReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ApprovalReplacerSignature')->whereNull('ApproveSignature')
                    ->whereNotNull('RecommendSignature')
                    ->paginate(10,['MIRSNo','Purpose','Preparedby','Approvedby','Recommendedby','MIRSDate','RecommendSignature','PreparedSignature','ApproveSignature','ManagerReplacerSignature']);
    }
    return view('Warehouse.MIRS.myMIRSrequest',compact('myrequestMIRS'));
  }
  public function readyForMCT()
  {
    $readyformct=MIRSMaster::orderBy('MIRSNo','DESC')->whereNotNull('RecommendSignature')->whereNotNull('ApproveSignature')->whereNotNull('PreparedSignature')->whereNull('WithMCT')
    ->orWhereNotNull('ApprovalReplacerSignature')->whereNotNull('RecommendSignature')->whereNull('WithMCT')
    ->orWhereNotNull('ManagerReplacerSignature')->whereNotNull('ApprovalReplacerSignature')->whereNull('WithMCT')
    ->orWhereNotNull('ManagerReplacerSignature')->whereNotNull('ApproveSignature')->whereNull('WithMCT')
    ->paginate(10,['MIRSNo','Purpose','Preparedby','Recommendedby','Approvedby','MIRSDate']);
  return view('Warehouse.MIRS.MIRSReadyList',compact('readyformct'));
  }
  public function CancelApproveMIRSinBehalf($id)
  {
    MIRSMaster::where('MIRSNo', $id)->update(['ApprovalReplacer'=>null]);
  }

  public function AcceptApprovalRequest($id)
  {
      $MIRSitems=MIRSDetail::where('MIRSNo',$id)->get(['MIRSNo','ItemCode','Particulars','Unit','Quantity','Remarks']);
      $forValidatortbl = array();
      foreach ($MIRSitems as $item)
      {
        $forValidatortbl[] = array('MIRSNo' =>$item->MIRSNo ,'ItemCode'=> $item->ItemCode,'Particulars'=>$item->Particulars,'Unit'=>$item->Unit,'Quantity'=>$item->Quantity,'Remarks'=>$item->Remarks);
      }
      MCTValidator::insert($forValidatortbl);
      MIRSMaster::where('MIRSNo',$id)->update(['ApprovalReplacerSignature'=>Auth::user()->Signature,'ApproveSignature'=>null]);

      $role=['role'=>'3and4'];
      $job=(new NewApprovedMIRSJob($role))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
  }
  public function fetchAllManager()
  {
    return User::whereNotNull('IsActive')->where('Role',0)->get(['Fname','Lname','id']);
  }
  public function SendRequestManagerReplacer($id,Request $request)
  {
    if (empty($request->ManagerReplacerID))
    {
      return ['error'=>'Required'];
    }
    $managerReplacer=User::where('id',$request->ManagerReplacerID)->get(['Fname','Lname']);
    MIRSMaster::where('MIRSNo',$id)->update(['ManagerReplacer'=>$managerReplacer[0]->Fname.' '.$managerReplacer[0]->Lname]);
    $Nospacename=str_replace(' ','',$managerReplacer[0]->Fname.$managerReplacer[0]->Lname);
    $notify = array('tobeNotifyName' =>$Nospacename);
    $notify=(object)$notify;
    $job = (new SendMIRSNotification($notify))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function CancelRequestManagerReplacer($id)
  {
    MIRSMaster::where('MIRSNo', $id)->update(['ManagerReplacer'=>null]);
  }
  public function SignatureManagerReplacer($id)
  {
    MIRSMaster::where('MIRSNo', $id)->update(['ManagerReplacerSignature'=>Auth::user()->Signature,'RecommendSignature'=>null]);
    $MIRSNotifynext=MIRSMaster::where('MIRSNo', $id)->get(['ApprovalReplacer','Approvedby']);
    if (!empty($MIRSNotifynext[0]->ApprovalReplacer)) {
      $GMreplacerName=str_replace(' ','',$MIRSNotifynext[0]->ApprovalReplacer);
      $notifyname = array('tobeNotifyName' =>$GMreplacerName);
      $notifyname=(object)$notifyname;
      $job = (new SendMIRSNotification($notifyname))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    $GMNospacename=str_replace(' ','',$MIRSNotifynext[0]->Approvedby);
    $GMname = array('tobeNotifyName' =>$GMNospacename);
    $GMname=(object)$GMname;
    $job = (new SendMIRSNotification($GMname))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function MIRSNotification()
  {
    $myrequestMIRS=0;
    if (Auth::user()->Role==2)
    {
      $myrequestMIRS=MIRSMaster::orderBy('id','DESC')
                      ->whereNull('IfDeclined')->where('Approvedby',Auth::user()->Fname." ".Auth::user()->Lname)
                      ->whereNull('ApproveSignature')->whereNull('ApprovalReplacerSignature')->whereNotNull('PreparedSignature')->whereNotNull('RecommendSignature')
                      ->orWhereNull('IfDeclined')->where('Approvedby',Auth::user()->Fname." ".Auth::user()->Lname)
                      ->whereNull('ApproveSignature')->whereNull('ApprovalReplacerSignature')->whereNotNull('PreparedSignature')->whereNotNull('ManagerReplacerSignature')
                      ->count();
    }elseif(Auth::user()->Role==0)
    {
    $myrequestMIRS=MIRSMaster::orderBy('id','DESC')->where('Recommendedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('RecommendSignature')->whereNull('IfDeclined')->whereNull('ManagerReplacerSignature')
                    ->orWhere('ManagerReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ManagerReplacerSignature')->whereNull('RecommendSignature')
                    ->orWhere('ApprovalReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)
                    ->whereNotNull('ManagerReplacerSignature')->whereNull('ApprovalReplacerSignature')->whereNull('ApproveSignature')
                    ->orWhere('ApprovalReplacer',Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('ApprovalReplacerSignature')->whereNull('ApproveSignature')
                    ->whereNotNull('RecommendSignature')
                    ->count();
    }
    $response = ['MIRSrequest' =>$myrequestMIRS];
    return response()->json($response);
  }
  public function newlyApprovedMIRSCount()
  {
    $readyformct=MIRSMaster::orderBy('MIRSNo','DESC')->whereNotNull('RecommendSignature')->whereNotNull('ApproveSignature')->whereNotNull('PreparedSignature')->whereNull('WithMCT')
    ->orWhereNotNull('ApprovalReplacerSignature')->whereNotNull('RecommendSignature')->whereNull('WithMCT')
    ->orWhereNotNull('ManagerReplacerSignature')->whereNotNull('ApprovalReplacerSignature')->whereNull('WithMCT')
    ->orWhereNotNull('ManagerReplacerSignature')->whereNotNull('ApproveSignature')->whereNull('WithMCT')
    ->count();
    $response=['NewlyApprovedMIRS'=>$readyformct];
    return response()->json($response);
  }
}
