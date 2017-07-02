<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\MIRSDetail;
use App\MIRSMaster;
use App\MaterialsTicketDetail;
use DB;
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
    'ItemCode' => $request->ItemCode,'Particulars' => $request->Particulars,'Unit' => $request->Unit,'Remarks'=>$request->Remarks,'Quantity' => $request->Quantity,
    ];


    if (Session::has('ItemSelected'))
    {
      foreach (Session::get('ItemSelected') as $selected)
      {
        if ($selected->ItemCode == $request->ItemCode) {
          return redirect('/MIRS-add')->with('message', 'This Item has been added already');
        }
      }
    }
    if (count(Session::get('ItemSelected'))==10)
    {
      return redirect('/MIRS-add');
    }else
    {
      $itemselected = (object)$itemselected;
      Session::push('ItemSelected',$itemselected);
      return redirect('/MIRS-add');
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
      $master->Preparedby =$request->Preparedby;
      $master->Recommendedby =$request->Recommendedby;
      $master->Approvedby = $request->Approvedby;
      $master->MIRSDate = $date;
      $master->save();
      foreach ($selectedITEMS as $items)
      {
        $details=new MIRSDetail;
        $details->MIRSNo = $incremented;
        $details->ItemCode= $items->ItemCode;
        $details->Particulars = $items->Particulars;
        $details->Unit= $items->Unit;
        $details->Remarks=$items->Remarks;
        $details->Quantity= $items->Quantity;
        $details->save();
      }
        Session::flush();
        Session::put('LastMIRSid',$master->MIRSNo);
        return redirect()->route('PreviewMIRS');
    }else
    {
      return redirect()->back()->with('message', 'Items cannot be empty');
    }
  }
  public function MIRSpreview()
  {
    return view('Warehouse.MIRSpreview');
  }
  public function searchMIRSNo(Request $request)
  {
    $MIRSDetail=MIRSDetail::where('MIRSNo', $request->MIRSNo)->get();
    $MIRSMaster=MIRSMaster::where('MIRSNo', $request->MIRSNo)->get();
    return view('Warehouse.MIRSpreview',compact('MIRSDetail','MIRSMaster'));

  }
  public function DeleteDenied(Request $request)
  {
    $mirsnum=$request->MIRSNo;
    $mirsMaster= DB::table('MIRSMaster')->where('MIRSNo',$mirsnum)->delete();
    $mirsDetail= DB::table('MIRSDetails')->where('MIRSNo',$mirsnum)->delete();
    if ($mirsnum==Session::get('LastMIRSid')) {
      Session::forget('LastMIRSid');
    }
    return redirect()->route('PreviewMIRS');
  }

}
