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
    return $this->morphToMany('App\User', 'Signatureable')->withPivot('Signature','SignatureType','id')->orderBy('pivot_id');
  }
  public function getmonthAttribute($monthNumber)
  {
    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('M');
  }
  public function getNotificationDateTimeAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
  }

}
