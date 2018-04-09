<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class AccountHistoryController extends Controller
{
  public function  __construct()
  {
    $this->middleware('auth');
  }
  public function ShowMyHistoryPage(Request $request)
  {
    $ActiveUser=User::whereNotNull('IsActive')->get(['id','FullName']);
    return view('Warehouse.Account.MyHistory',compact('ActiveUser'));
  }
  public function MyMIRSHistoryandSearch(Request $request)
  {
      $date=date('Y-m',strtotime($request->YearMonth));
     $user = User::find($request->PreparedById);
     return $mirshistory = $user->MIRSHistory($date)->paginate(5);

  }
  public function MyMCTHistoryandSearch(Request $request)
  {
    $user = User::find($request->ReceivedById);
    return $mcthistory = $user->MCTHistory($request->YearMonth)->paginate(5);
  }
  public function MyMRTHistoryandSearch(Request $request)
  {
    $user = User::find($request->ReturnedById);
    return $mrthistory = $user->MRTHistory($request->YearMonth)->paginate(5);
  }
  public function MyMRHistoryandSearch(Request $request)
  {
    $user = User::find($request->ReceivedById);
    return $mrhistory=$user->MRHistory($request->YearMonth)->paginate(5);
  }
  public function MyRVHistoryandSearch(Request $request)
  {
    $user = User::find($request->Requisitioner);
    return $rvhistory=$user->RVHistory($request->YearMonth)->paginate(5);
  }
  public function MyRRHistoryandSearch(Request $request)
  {
    $user = User::find($request->ReceivedById);
    return $rrhistory=$user->RRHistory($request->YearMonth)->paginate(5);
  }
}
