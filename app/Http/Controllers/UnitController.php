<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unit;
class UnitController extends Controller
{
  public function SaveUnit(Request $request)
  {
   $this->validate($request,[
     'NewUnit'=>'required|max:20',
   ]);
   $UnitDB=new Unit;
   $UnitDB->UnitName=$request->NewUnit;
   $UnitDB->save();
  }
  public function fetchUnits()
  {
    return Unit::all();
  }
  public function DeleteUnit($id)
  {
    if ($id==null)
    {
      return [error=>'Selecting unit is required'];
    }
    Unit::find($id)->delete();
  }
}
