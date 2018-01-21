<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\POMaster;
use App\RRMaster;
use App\RVMaster;
use App\MRMaster;
use App\MCTMaster;
use App\MRTMaster;
class globalNotificationController extends Controller
{
    public function fetchMIRS()
    {
      $currentUser=User::find(Auth::user()->id);
      $notifications=$currentUser->MIRSGlobalNotif()->paginate(8);
      $currentUser->MIRSGlobalNotif()->update(['UnreadNotification'=>NULL]);
      return $notifications;
    }
    public function countInfoNotification()
    {
      $currentUser=User::find(Auth::user()->id);
      $mirsCount = $currentUser->countMIRSGlobalNotif()->count();
      $rvCount = $currentUser->countRVGlobalNotif()->count();

     if ((Auth::user()->Role=='4')||(Auth::user()->Role=='3'))
     {
       $poCount=POMaster::where('UnreadNotification','!=',NULL)->count();
       $rrCount=RRMaster::where('UnreadNotification','!=',NULL)->count();
       $mrCount=MRMaster::where('UnreadNotification','!=',NULL)->count();
       $mctCount=MCTMaster::where('UnreadNotification','!=',NULL)->count();
       $mrtCount=MRTMaster::where('UnreadNotification','!=',NULL)->count();
       $response = array('unreadPO' =>$poCount,'unreadRR'=>$rrCount,'unreadMIRS'=>$mirsCount,'unreadRV'=>$rvCount,'unreadMR'=>$mrCount,'unreadMCT'=>$mctCount,'unreadMRT'=>$mrtCount);
     }else
     {
       $response = array('unreadMIRS'=>$mirsCount,'unreadRV'=>$rvCount);
     }
     return response()->json($response);
    }
    public function fetchRV()
    {
      $currentUser=User::find(Auth::user()->id);

      $rv = $currentUser->RVGlobalNotif()->paginate(8);
      $currentUser->RVGlobalNotif()->update(['UnreadNotification'=>NULL]);
      return $rv;
    }
    public function fetchPO()
    {
      $po = POMaster::where('Status','!=',NULL)->orderBy('notification_date_time','DESC')->paginate(8);
      POMaster::where('Status','!=',NULL)->update(['UnreadNotification'=>NULL]);
      return $po;
    }
    public function fetchRR()
    {
      $rr = RRMaster::where('Status','!=',NULL)->orderBy('notification_date_time','DESC')->paginate(8);
      RRMaster::where('Status','!=',NULL)->update(['UnreadNotification'=>NULL]);
      return $rr;
    }
    public function fetchMR()
    {
      $mr = MRMaster::where('Status','!=',NULL)->orderBy('notification_date_time','DESC')->paginate(8);
      MRMaster::where('Status','!=',NULL)->update(['UnreadNotification'=>NULL]);
      return $mr;
    }
    public function fetchMCT()
    {
      $mct = MCTMaster::where('Status','!=',NULL)->orderBy('notification_date_time','DESC')->paginate(8);
      MCTMaster::where('Status','!=',NULL)->update(['UnreadNotification'=>NULL]);
      return $mct;
    }
    public function fetchMRT()
    {
      $mrt = MRTMaster::where('Status','!=',NULL)->orderBy('notification_date_time','DESC')->paginate(8);
      MRTMaster::where('Status','!=',NULL)->update(['UnreadNotification'=>NULL]);
      return $mrt;
    }

}
