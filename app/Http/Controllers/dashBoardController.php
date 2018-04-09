<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterItem;
use Carbon\Carbon;
use DB;
use App\RVMaster;
use App\MIRSMaster;
use App\MCTMaster;
use Auth;
use App\MRTMaster;
use App\User;
use App\RRMaster;
use App\Signatureable;
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

      return $MIRS = MIRSMaster::select(DB::raw('mirsdate as month'), DB::raw('count(*) as total'))
      ->whereYear('mirsdate', '=', $yearNow)->whereMonth('mirsdate','<=',$MonthNow)
      ->where('Status','0')
      ->groupBy(DB::raw('mirsdate'))
      ->orderBy('month', 'asc')
      ->get();

    }
    public function barChart()
    {
      $yearNow=Carbon::now()->format('Y');
      $MonthNow=Carbon::now()->format('m');

       $MCT = MCTMaster::select(DB::raw('mctdate as month'), DB::raw('count(*) as total'))
      ->whereYear('mctdate', $yearNow)->whereMonth('mctdate','<=',$MonthNow)
      ->where('Status','0')
      ->whereNull('isrollback')
      ->orWhere('isrollback','1')
      ->whereYear('mctdate', $yearNow)->whereMonth('mctdate','<=',$MonthNow)
      ->where('Status','0')
      ->groupBy(DB::raw('mctdate'))
      ->orderBy('month', 'asc')
      ->get();

      $MRT =MRTMaster::select(DB::raw('returndate as month'), DB::raw('count(*) as total'))
     ->whereYear('returndate', $yearNow)->whereMonth('returndate','<=',$MonthNow)
     ->where('Status','0')
     ->whereNull('isrollback')
     ->orWhere('isrollback','1')
     ->whereYear('returndate', $yearNow)->whereMonth('returndate','<=',$MonthNow)
     ->where('Status','0')
     ->groupBy(DB::raw('returndate'))
     ->orderBy('month', 'asc')
     ->get();

     $RR =RRMaster::select(DB::raw('rrdate as month'), DB::raw('count(*) as total'))
     ->whereYear('rrdate', $yearNow)->whereMonth('rrdate','<=',$MonthNow)
     ->where('Status','0')
     ->whereNull('isrollback')
     ->orWhere('isrollback','1')
     ->whereYear('rrdate', $yearNow)->whereMonth('rrdate','<=',$MonthNow)
     ->where('Status','0')
     ->groupBy(DB::raw('rrdate'))
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
      return User::orderBy('LastOnline','DESC')->where('IsActive','0')->where('FullName','LIKE','%'.$request->search.'%')->paginate(18,['FullName','id','LastOnline as last_activity']);
    }

    // get recent files
    public function getRecentTransactions()
    {
      $recent = User::where('id', Auth::user()->id)->with(array('mirsrecent'=>function($query){
      $query->select('MIRSNo','Status');
              }))->with(array('mctrecent'=>function($query){
      $query->select('MCTNo','Status','isrollback');
              }))->with(array('mrtrecent'=>function($query){
              $query->select('MRTNo','Status','isrollback');
                      }))->with(array('rvrecent'=>function($query){
              $query->select('RVNo','Status');
                      }))->with(array('mrrecent'=>function($query){
                      $query->select('MRNo','Status');
                              }))->with(array('rrrecent'=>function($query){
                              $query->select('RRNo','Status','isrollback');
                                      }))->with(array('porecent'=>function($query){
                                      $query->select('PONo','Status');
                                              }))->get(['id']);
      $RecentMIRS = [];
      if (isset($recent) && isset($recent[0]->mirsrecent[0]))
      {
        $RecentMIRS=Signatureable::where('Signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->where('Signatureable_id', $recent[0]->mirsrecent[0]->MIRSNo)->with(array('user'=>function($query){
        $query->select('id','FullName');
                }))->get(['user_id']);
      }
      $RecentMCT = [];
      if (isset($recent) && isset($recent[0]->mctrecent[0]))
      {
        $RecentMCT=Signatureable::where('Signatureable_type', 'App\MCTMaster')->where('SignatureType', 'ReceivedBy')->where('Signatureable_id', $recent[0]->mctrecent[0]->MCTNo)->with(array('user'=>function($query){
        $query->select('id','FullName');
                }))->get(['user_id']);
      }
      $RecentMRT = [];
      if (isset($recent) && isset($recent[0]->mrtrecent[0]))
      {
        $RecentMRT=Signatureable::where('Signatureable_type', 'App\MRTMaster')->where('SignatureType', 'ReturnedBy')->where('Signatureable_id', $recent[0]->mrtrecent[0]->MRTNo)->with(array('user'=>function($query){
        $query->select('id','FullName');
                }))->get(['user_id']);
      }
      $RecentRV = [];
      if (isset($recent) && isset($recent[0]->rvrecent[0]))
      {
        $RecentRV=Signatureable::where('Signatureable_type', 'App\RVMaster')->where('SignatureType', 'Requisitioner')->where('Signatureable_id', $recent[0]->rvrecent[0]->RVNo)->with(array('user'=>function($query){
        $query->select('id','FullName');
                }))->get(['user_id','id']);
      }
      $RecentMR = [];
      if (isset($recent) && isset($recent[0]->mrrecent[0]))
      {
        $RecentMR=Signatureable::where('Signatureable_type', 'App\MRMaster')->where('SignatureType', 'ReceivedBy')->where('Signatureable_id', $recent[0]->mrrecent[0]->MRNo)->with(array('user'=>function($query){
        $query->select('id','FullName');
                }))->get(['user_id','id']);
      }
      $RecentRR = [];
      if (isset($recent) && isset($recent[0]->rrrecent[0]))
      {
        $RecentRR=Signatureable::where('Signatureable_type', 'App\RRMaster')->where('SignatureType', 'ReceivedBy')->where('Signatureable_id', $recent[0]->rrrecent[0]->RRNo)->with(array('user'=>function($query){
        $query->select('id','FullName');
                }))->get(['user_id','id']);
      }
      $RecentPO = [];
      if (isset($recent) && isset($recent[0]->porecent[0]))
      {
        $RecentPO=Signatureable::where('Signatureable_type', 'App\POMaster')->where('Signatureable_id', $recent[0]->porecent[0]->PONo)->with(array('user'=>function($query){
        $query->select('id','FullName');
                }))->get(['user_id','id']);
      }
      $response = array('recent' =>$recent ,'MCT'=>$RecentMCT,'MRT'=>$RecentMRT,'MIRS'=>$RecentMIRS,'RV'=>$RecentRV,'RR'=>$RecentRR,'MR'=>$RecentMR,'PO'=>$RecentPO);
      return response()->json($response);
    }
    public function countUserTransactions()
    {
       $user=User::find(Auth::user()->id);
       $totalvalid=0;
       $validMct=$user->mctvalid()->count();
       $validMrt=$user->mrtvalid()->count();
       $validMirs=$user->mirsvalid()->count();
       $validRv=$user->rvvalid()->count();
       $validRr=$user->rrvalid()->count();
       $validMr=$user->mrvalid()->count();
       $validPo=$user->povalid()->count();
       $totalvalid = $validMct + $validMrt + $validMirs + $validRv + $validRr + $validMr + $validPo;
      //  invalid
       $totalinvalid=0;
       $invalidMct=$user->mctinvalid()->count();
       $invalidMrt=$user->mrtinvalid()->count();
       $invalidMirs=$user->mirsinvalid()->count();
       $invalidRv=$user->rvinvalid()->count();
       $invalidRr=$user->rrinvalid()->count();
       $invalidMr=$user->mrinvalid()->count();
       $invalidPo=$user->poinvalid()->count();
       $totalinvalid = $invalidMct + $invalidMrt + $invalidMirs + $invalidRv + $invalidRr + $invalidMr + $invalidPo;
      //  pending
       $totalpending=0;
       $pendingMct=$user->mctpending()->count();
       $pendingMrt=$user->mrtpending()->count();
       $pendingMirs=$user->mirspending()->count();
       $pendingRv=$user->rvpending()->count();
       $pendingRr=$user->rrpending()->count();
       $pendingMr=$user->mrpending()->count();
       $pendingPo=$user->popending()->count();
       $totalpending = $pendingMct + $pendingMrt + $pendingMirs + $pendingRv + $pendingRr + $pendingMr + $pendingPo;
       $totaltransactions= $totalvalid + $totalinvalid + $totalpending;
       $response = array('validtotal' =>$totalvalid,'invalidtotal'=>$totalinvalid, 'pendingtotal'=>$totalpending,'overall'=>$totaltransactions);
       return response()->json($response);
    }

}
