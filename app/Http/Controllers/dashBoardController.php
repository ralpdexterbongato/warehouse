<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterItem;
use Carbon\Carbon;
use DB;
use App\RVMaster;
use App\MIRSMaster;
use App\MCTMaster;
use App\MRTMaster;
use App\RRMaster;
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

      return $MIRS = MIRSMaster::select(DB::raw('month(MIRSDate) as month'), DB::raw('count(*) as total'))
      ->whereYear('MIRSDate', '=', $yearNow)->whereMonth('MIRSDate','<=',$MonthNow)
      ->where('Status','0')
      ->groupBy(DB::raw('month(MIRSDate)'))
      ->orderBy('month', 'asc')
      ->get();

      //  $RV = RVMaster::select(DB::raw('month(RVDate) as month'), DB::raw('count(*) as total'))
      // ->whereYear('RVDate', '=', $yearNow)->whereMonth('RVDate','<=',$MonthNow)
      // ->groupBy(DB::raw('month(RVDate)'))
      // ->orderBy('month', 'asc')
      // ->get();

    }
    public function barChart()
    {
      $yearNow=Carbon::now()->format('Y');
      $MonthNow=Carbon::now()->format('m');

       $MCT = MCTMaster::select(DB::raw('month(MCTDate) as month'), DB::raw('count(*) as total'))
      ->whereYear('MCTDate', $yearNow)->whereMonth('MCTDate','<=',$MonthNow)
      ->where('Status','0')
      ->whereNull('IsRollBack')
      ->groupBy(DB::raw('month(MCTDate)'))
      ->orderBy('month', 'asc')
      ->get();

      $MRT =MRTMaster::select(DB::raw('month(ReturnDate) as month'), DB::raw('count(*) as total'))
     ->whereYear('ReturnDate', $yearNow)->whereMonth('ReturnDate','<=',$MonthNow)
     ->where('Status','0')
     ->whereNull('IsRollBack')
     ->groupBy(DB::raw('month(ReturnDate)'))
     ->orderBy('month', 'asc')
     ->get();

     $RR =RRMaster::select(DB::raw('month(RRDate) as month'), DB::raw('count(*) as total'))
     ->whereYear('RRDate', $yearNow)->whereMonth('RRDate','<=',$MonthNow)
     ->where('Status','0')
     ->whereNull('IsRollBack')
     ->groupBy(DB::raw('month(RRDate)'))
     ->orderBy('month', 'asc')
     ->get();

      $MCTArray = array();
      for ($i=1; $i < 13 ; $i++)
      {
        $matchMCT=false;
        $keymatchMCT=null;
        foreach ($MCT as $key => $month)
        {
          if ($month->month==$i)
          {
            $matchMCT=true;
            $keymatchMCT=$key;
          }
        }
        if ($matchMCT==true)
        {
          $MCTArray[] =$MCT[$keymatchMCT]->total;
        }else
        {
          $MCTArray[]='0';
        }
      }
      $MRTArray = array();
      for ($i=1; $i < 13 ; $i++)
      {
        $matchMRT=false;
        $keymatchMRT=null;
        foreach ($MRT as $key => $month)
        {
          if ($month->month==$i)
          {
            $matchMRT=true;
            $keymatchMRT=$key;
          }
        }
        if ($matchMRT==true)
        {
          $MRTArray[] =$MRT[$keymatchMRT]->total;
        }else
        {
          $MRTArray[]='0';
        }
      }

      $RRArray = array();
      for ($i=1; $i < 13 ; $i++)
      {
        $matchRR=false;
        $keymatchRR=null;
        foreach ($RR as $key => $month)
        {
          if ($month->month==$i)
          {
            $matchRR=true;
            $keymatchRR=$key;
          }
        }
        if ($matchRR==true)
        {
          $RRArray[] =$RR[$keymatchRR]->total;
        }else
        {
          $RRArray[]='0';
        }
      }

      $response = array('mct' => $MCTArray,'mrt'=>$MRTArray,'rr'=>$RRArray);
      return response()->json($response);
    }
    public function DoughnutData()
    {
      $MonthNow=Carbon::now()->format('m');
      $yearNow=Carbon::now()->format('Y');

      $MCT = MCTMaster::whereYear('MCTDate', $yearNow)->whereMonth('MCTDate',$MonthNow)
      ->where('Status','0')
      ->whereNull('IsRollBack')
      ->count();

      $MRT = MRTMaster::whereYear('ReturnDate', $yearNow)->whereMonth('ReturnDate',$MonthNow)
      ->where('Status','0')
      ->whereNull('IsRollBack')
      ->count();

      $RR = RRMaster::whereYear('RRDate', $yearNow)->whereMonth('RRDate',$MonthNow)
      ->where('Status','0')
      ->whereNull('IsRollBack')
      ->count();

      $response = array('mct' =>$MCT ,'mrt'=>$MRT,'rr'=>$RR);
      return response()->json($response);
    }

}
