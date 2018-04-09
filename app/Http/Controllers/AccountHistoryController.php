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
    if (empty($request->YearMonth))
    {
      $date=Carbon::now()->format('Y-m');
    }else
    {
      $date=date('Y-m',strtotime($request->YearMonth));
    }
     $user = User::find($request->PreparedById);
     return $mirshistory = $user->MIRSHistory($date)->paginate(5);

  }
  public function MyMCTHistoryandSearch(Request $request)
  {
    if (empty($request->YearMonth))
    {
      $date=Carbon::now()->format('Y-m');
    }else
    {
      $date=date('Y-m',strtotime($request->YearMonth));
    }
    $user = User::find($request->ReceivedById);
    return $mcthistory = $user->MCTHistory($date)->paginate(5);
  }
  public function MyMRTHistoryandSearch(Request $request)
  {
    if (empty($request->YearMonth))
    {
      $date=Carbon::now()->format('Y-m');
    }else
    {
      $date=date('Y-m',strtotime($request->YearMonth));
    }
    $user = User::find($request->ReturnedById);
    return $mrthistory = $user->MRTHistory($date)->paginate(5);
  }
  public function MyMRHistoryandSearch(Request $request)
  {
    if (empty($request->YearMonth))
    {
      $date=Carbon::now()->format('Y-m');
    }else
    {
      $date=date('Y-m',strtotime($request->YearMonth));
    }
    $user = User::find($request->ReceivedById);
    return $mrhistory=$user->MRHistory($date)->paginate(5);
  }
  public function MyRVHistoryandSearch(Request $request)
  {
    if (empty($request->YearMonth))
    {
      $date=Carbon::now()->format('Y-m');
    }else
    {
      $date=date('Y-m',strtotime($request->YearMonth));
    }
    $user = User::find($request->Requisitioner);
    return $rvhistory=$user->RVHistory($date)->paginate(5);
  }
  public function MyRRHistoryandSearch(Request $request)
  {
    if (empty($request->YearMonth))
    {
      $date=Carbon::now()->format('Y-m');
    }else
    {
      $date=date('Y-m',strtotime($request->YearMonth));
    }
    $user = User::find($request->ReceivedById);
    return $rrhistory=$user->RRHistory($date)->paginate(5);
  }
}
