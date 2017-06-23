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
    $itemselected =[
      'ItemCode' => $request->ItemCode,'Particulars' => $request->Particulars,'Unit' => $request->Unit,'Quantity' => $request->Quantity,'Remarks' => $request->Remarks
    ];
      foreach (Session::get('ItemSelected') as $selected)
      {
        if ($selected->ItemCode == $request->ItemCode) {
          return redirect('/MCT-add');
        }

      }
      $itemselected = (object)$itemselected;
      Session::push('ItemSelected',$itemselected);
      return redirect('/MCT-add');


  }
}
