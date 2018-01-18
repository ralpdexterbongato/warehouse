<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\POMaster;
use App\RRMaster;
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
       return $total = $poCount + $rrCount + $mirsCount +$rvCount;
     }else
     {
       return $total =$mirsCount + $rvCount;
     }
    }
}
