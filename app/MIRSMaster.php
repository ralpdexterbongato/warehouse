<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class MIRSMaster extends Model
{
  protected $dates = ['MIRSDate'];
  protected $primaryKey='MIRSNo';
  public $incrementing = false;
  public $timestamps=false;
  protected $table='MIRSMaster';

  public function users()
  {
    return $this->morphToMany('App\User', 'signatureable')->withPivot('Signature','SignatureType');
  }
  public function getMonthAttribute($monthNumber)
  {
    return date("M", mktime(0, 0, 0, $monthNumber, 1));
  }
  public function getNotificationDateTimeAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s.u', $date)->diffForHumans();
  }

}
