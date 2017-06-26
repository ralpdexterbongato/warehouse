<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\MIRSDetail;
use App\MIRSMaster;
class MCTController extends Controller
{
  public function MIRSIndex()
  {
    return view('Warehouse.MIRSviews');
  }
  public function addingSessionItem(Request $request)
  {
    $this->SessionValidator($request);
    $itemselected =[
    'ItemCode' => $request->ItemCode,'Particulars' => $request->Particulars,'Unit' => $request->Unit,'Remarks'=>$request->Remarks,'Quantity' => $request->Quantity,];

    if (Session::has('ItemSelected'))
    {
      foreach (Session::get('ItemSelected') as $selected)
      {
        if ($selected->ItemCode == $request->ItemCode) {
          return redirect('/MCT-add');
        }

      }
    }
      $itemselected = (object)$itemselected;
      Session::push('ItemSelected',$itemselected);
      return redirect('/MCT-add');


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
      'Quantity' => 'Integer|min:1',
    ]);
  }

  public function StoringMIRS(Request $request)
  {

    $date=Carbon::today();
    $incremented = '2017-40';
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

      $MIRSDetails=MIRSDetail::where('MIRSNo',$incremented)->get();
      $MIRSMaster=MIRSMaster::where('MIRSNo',$incremented)->get();
      return view('Warehouse.MIRSpreview',compact('MIRSDetails','MIRSMaster'));
  }

}
