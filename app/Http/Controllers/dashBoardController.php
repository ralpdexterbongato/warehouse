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
use App\User;
use App\RRMaster;
class dashBoardController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->middleware('IsWarehouseAndAdmin',['except'=>['UsersStatusCheck']]);
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

    }
    public function barChart()
    {
      $yearNow=Carbon::now()->format('Y');
      $MonthNow=Carbon::now()->format('m');

       $MCT = MCTMaster::select(DB::raw('month(MCTDate) as month'), DB::raw('count(*) as total'))
      ->whereYear('MCTDate', $yearNow)->whereMonth('MCTDate','<=',$MonthNow)
      ->where('Status','0')
      ->whereNull('IsRollBack')
      ->orWhere('IsRollBack','1')
      ->whereYear('MCTDate', $yearNow)->whereMonth('MCTDate','<=',$MonthNow)
      ->where('Status','0')
      ->groupBy(DB::raw('month(MCTDate)'))
      ->orderBy('month', 'asc')
      ->get();

      $MRT =MRTMaster::select(DB::raw('month(ReturnDate) as month'), DB::raw('count(*) as total'))
     ->whereYear('ReturnDate', $yearNow)->whereMonth('ReturnDate','<=',$MonthNow)
     ->where('Status','0')
     ->whereNull('IsRollBack')
     ->orWhere('IsRollBack','1')
     ->whereYear('ReturnDate', $yearNow)->whereMonth('ReturnDate','<=',$MonthNow)
     ->where('Status','0')
     ->groupBy(DB::raw('month(ReturnDate)'))
     ->orderBy('month', 'asc')
     ->get();

     $RR =RRMaster::select(DB::raw('month(RRDate) as month'), DB::raw('count(*) as total'))
     ->whereYear('RRDate', $yearNow)->whereMonth('RRDate','<=',$MonthNow)
     ->where('Status','0')
     ->whereNull('IsRollBack')
     ->orWhere('IsRollBack','1')
     ->whereYear('RRDate', $yearNow)->whereMonth('RRDate','<=',$MonthNow)
     ->where('Status','0')
     ->groupBy(DB::raw('month(RRDate)'))
     ->orderBy('month', 'asc')
     ->get();

     $MonthNow=$MonthNow+1;
      $MCTArray = array();
      for ($i=1; $i < $MonthNow ; $i++)
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
      for ($i=1; $i < $MonthNow ; $i++)
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
      for ($i=1; $i < $MonthNow ; $i++)
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
      $MonthNow = $MonthNow-1;
      $MonthsArray = array();
      switch ($MonthNow) {
        case '1':
          $MonthsArray[]=['Jan'];
          break;
        case '2':
          $MonthsArray[] = ['Jan','Feb'];
          break;
        case '3':
          $MonthsArray[]=['Jan','Feb','Mar'];
          break;
        case '4':
          $MonthsArray[]=['Jan','Feb','Mar','Apr'];
          break;
        case '5':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May'];
          break;
        case '6':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May','Jun'];
          break;
        case '7':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May','Jun','Jul'];
          break;
        case '8':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug'];
          break;
        case '9':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep'];
          break;
        case '10':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct'];
          break;
        case '11':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov'];
          break;
        case '12':
          $MonthsArray[]=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
          break;
        default:
          $MonthsArray[]=['Jan'];
          break;
      }
      $response = array('mct' => $MCTArray,'mrt'=>$MRTArray,'rr'=>$RRArray,'months'=>$MonthsArray);
      return response()->json($response);
    }
    public function UsersStatusCheck(Request $request)
    {
      return User::orderBy('LastOnline','DESC')->where('FullName','LIKE','%'.$request->search.'%')->paginate(18,['FullName','id','LastOnline as last_activity']);
    }

}
