<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterItem;
use Carbon\Carbon;
use DB;
use App\MCTMaster;
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
    public function lineChart()
    {
      $MonthNow=Carbon::now()->format('m');
      $yearNow=Carbon::now()->format('Y');

      return $activeUsers = DB::table('MIRSMaster')
      ->select(DB::raw('month(MIRSDate) as month'), DB::raw('count(*) as total'))
      ->whereYear('MIRSDate', '=', $yearNow)->whereMonth('MIRSDate','<=',$MonthNow)
      ->groupBy(DB::raw('month(MIRSDate)'))
      ->orderBy('total', 'desc')
      ->get();
    }
}
