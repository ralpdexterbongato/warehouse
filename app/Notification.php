<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Notification extends Model
{
    public $timestamps=false;

    public function getTimeNotifiedAttribute($dateandtime)
    {
      return Carbon::createFromFormat('Y-m-d H:i:s.u', $dateandtime)->diffForHumans();
    }
}
