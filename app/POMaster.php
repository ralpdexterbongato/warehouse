<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class POMaster extends Model
{
  protected $table='POMasters';
  public $timestamps=false;
  public $incrementing=false;
  public $dates=['PODate','RVDate'];
  public $primaryKey='PONo';

  public function PODetails()
  {
    return $this->hasMany('App\PODetail','PONo','PONo');
  }
  public function users()
  {
    return $this->morphToMany('App\User','signatureable')->withPivot('Signature','SignatureType');
  }
  public function getNotificationDateTimeAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s.u', $date)->diffForHumans();
  }
  public function creator()
  {
    return $this->belongsTo('App\User','CreatorID','id');
  }
}
