<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterItem;
class dashBoardController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->middleware('IsWarehouseAndAdmin');
    }
    public function show()
    {
      $good=MasterItem::whereColumn('CurrentQuantity','>=','AlertIfBelow')->count();
      $warning=MasterItem::whereColumn('CurrentQuantity','<','AlertIfBelow')->where('CurrentQuantity','!=',0)->count();
      $empty=MasterItem::where('CurrentQuantity',0)->count();
      $response = array('good'=>$good,'warn'=>$warning,'empty'=>$empty);
      return response()->json($response);
    }
}
