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
use App\Notification;
class globalNotificationController extends Controller
{
  public function __construct()
  {
   $this->middleware('auth');
  }
  public function fetchNotifications()
  {
    return Notification::orderBy('TimeNotified','DESC')->where('user_id', Auth::user()->id)->select('TimeNotified as time_notified','id','user_id','NotificationType','FileType','FileNo','Seen')->paginate(10);
  }
  public function countInfoNotification()
  {
    return Notification::where('user_id', Auth::user()->id)->whereNull('Seen')->count();
  }
  public function markSeen($id)
  {
    Notification::where('id',$id)->update(['Seen'=>'0']);
    return ['success'=>'success'];
  }
  public function markAllSeen()
  {
    Notification::where('user_id',Auth::user()->id)->update(['Seen'=>'0']);
    return ['success'=>'success'];
  }
}
