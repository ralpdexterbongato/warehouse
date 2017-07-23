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
class MIRSController extends Controller
{
  public function MIRScreate()
  {
    $GenMan=User::orderBy('id','DESC')->where('Role','2')->take(1)->get(['Fname','Lname','id']);
    $allManager=User::where('Role', '0')->get(['id','Fname','Lname','Position']);
    return view('Warehouse.MIRSviews',compact('allManager','GenMan'));
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
            return redirect('/MIRS-add')->with('message', 'This Item has been added already');
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
      'Remarks' => 'Required|max:50',
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
      $selectedITEMS=Session::get('ItemSelected');
      $selectedITEMS = (array)$selectedITEMS;

      $recommend=User::where('id',$request->Recommendedby)->get(['Position','Fname','Lname']);
      $approve=User::where('id',$request->Approvedby)->get(['Position','Fname','Lname']);
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
      foreach ($selectedITEMS as $items)
      {
        $details=new MIRSDetail;
        $details->MIRSNo = $incremented;
        $details->ItemCode= $items->ItemCode_id;
        $details->Particulars = $items->Particulars;
        $details->Unit= $items->Unit;
        $details->Remarks=$items->Remarks;
        $details->Quantity= $items->Quantity;
        $details->save();
      }
        Session::forget('ItemSelected');
        Session::forget('itemMasters');
        return redirect()->route('MIRSgridview');
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
  public function fullMIRSNo(Request $request)
  {
    $MIRSDetail=MIRSDetail::where('MIRSNo', $request->MIRSNo)->get();
    $MIRSMaster=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get();
    $MCTNumber=MCTMaster::where('MIRSNo', $request->MIRSNo)->value('MCTNo');
    return view('Warehouse.MIRSpreview',compact('MIRSDetail','MIRSMaster','MCTNumber'));
  }
  public function searchMIRSNo(Request $request)
  {
    $mirsResult=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get();
    if (empty($mirsResult[0]->MIRSNo))
    {
      return redirect()->route('MIRSgridview');
    }
    return view('Warehouse.MIRS-index',compact('mirsResult'));
  }
  public function DeniedMIRS(Request $request)
  {
    MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['IfDenied'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
    return redirect()->route('MIRSgridview');
  }

  public function Indexgrid()
  {
    $AllmasterMIRS=MIRSMaster::orderBy('id','DESC')->paginate(10);
    return view('Warehouse.MIRS-index',compact('AllmasterMIRS'));
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
      MIRSMaster::where('MIRSNo',$request->MIRSNo)->update(['ApproveSignature'=>Auth::user()->Signature]);
    }
    return redirect()->back();

  }
  public function mirsRequestcheck()
  {
    $myrequestMIRS=MIRSMaster::orderBy('id','DESC')->whereNull('IfDenied')->where('Preparedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('PreparedSignature')
                    ->orWhere('Recommendedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('RecommendSignature')->whereNull('IfDenied')
                    ->orWhere('Approvedby',Auth::user()->Fname." ".Auth::user()->Lname)
                    ->whereNull('ApproveSignature')->whereNull('IfDenied')
                    ->paginate(10,['MIRSNo','Purpose','Preparedby','Approvedby','Recommendedby','MIRSDate','RecommendSignature','PreparedSignature','ApproveSignature']);
    return view('Warehouse.myMIRSrequest',compact('myrequestMIRS'));
  }
  public function readyForMCT()
  {
    $readyformct=MIRSMaster::orderBy('MIRSNo','DESC')->whereNull('WithMCT')->where('RecommendSignature','!=','')->where('ApproveSignature','!=','')->where('PreparedSignature','!=','')
    ->paginate(10,['MIRSNo','Purpose','Preparedby','Recommendedby','Approvedby','MIRSDate']);
  return view('Warehouse.MIRSReadyList',compact('readyformct'));
  }
}
