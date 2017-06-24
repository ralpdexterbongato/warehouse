<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
class MCTController extends Controller
{
  public function MCTIndex()
  {
    return view('Warehouse.MCTviews');
  }
  public function addingSessionItem(Request $request)
  {
    $this->SessionValidator($request);
    $itemselected =[
      'id'=>$request->id,'ItemCode' => $request->ItemCode,'Particulars' => $request->Particulars,'Unit' => $request->Unit,'Quantity' => $request->Quantity,'Remarks' => $request->Remarks
    ];

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
    return view('Warehouse.MIRSprintable');
  }

}
