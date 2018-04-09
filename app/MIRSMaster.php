<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DateTime;
class MIRSMaster extends Model
{
  protected $dates = ['MIRSDate'];
  protected $primaryKey='MIRSNo';
  public $incrementing = false;
  public $timestamps=false;
  protected $table='MIRSMaster';

  public function users()
  {
    return $this->morphToMany('App\User', 'Signatureable')->withPivot('Signature','SignatureType','id')->orderBy('pivot_id');
  }
  public function getMonthAttribute($monthNumber)
  {
    $dateObj   = DateTime::createFromFormat('!m', $monthNumber);
    return $monthName = $dateObj->format('F');
  }
  public function getNotificationDateTimeAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
  }

}
