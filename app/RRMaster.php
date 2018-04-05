<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class RRMaster extends Model
{
  protected $table="RRMaster";
  public $timestamps =false;
  public $dates=['RRDate'];
  public $fillable=['RVNo'];
  public $incrementing=false;
  public $primaryKey='RRNo';

  public function users()
  {
    return $this->morphToMany('App\User', 'Signatureable')->withPivot('Signature','SignatureType')->orderBy('id');
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
