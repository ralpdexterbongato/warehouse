<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\MIRSDetail;
use App\MIRSMaster;
use App\MaterialsTicketDetail;
use DB;
use Auth;
class MIRSController extends Controller
{
  public function MIRScreate()
  {
    return view('Warehouse.MIRSviews');
  }
  public function addingSessionItem(Request $request)
  {
    $this->SessionValidator($request);
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
      $master=new MIRSMaster;
      $master->MIRSNo = $incremented;
      $master->Purpose =$request->Purpose;
      $master->Preparedby =Auth::user()->Fname . ' ' .Auth::user()->Lname;
      $master->Recommendedby =$request->Recommendedby;
      $master->Approvedby = $request->Approvedby;
      $master->MIRSDate = $date;
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
        return redirect()->route('MIRSgridview');
    }else
    {
      return redirect()->back()->with('message', 'Items cannot be empty');
    }
  }
  public function fullMIRSNo(Request $request)
  {
    $MIRSDetail=MIRSDetail::where('MIRSNo', $request->MIRSNo)->get();
    $MIRSMaster=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get();
    return view('Warehouse.MIRSpreview',compact('MIRSDetail','MIRSMaster'));
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
  public function DeleteDenied(Request $request)
  {
    $mirsnum=$request->MIRSNo;
    $mirsMaster= DB::table('MIRSMaster')->where('MIRSNo',$mirsnum)->delete();
    $mirsDetail= DB::table('MIRSDetails')->where('MIRSNo',$mirsnum)->delete();
    if ($mirsnum==Session::get('LastMIRSid')) {
      Session::forget('LastMIRSid');
    }
    return redirect()->route('MIRSgridview');
  }

  public function Indexgrid()
  {
    $AllmasterMIRS=MIRSMaster::orderBy('id','DESC')->paginate(10);
    return view('Warehouse.MIRS-index',compact('AllmasterMIRS'));
  }

}
