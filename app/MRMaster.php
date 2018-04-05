<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class MRMaster extends Model
{
  protected $table='MRMaster';
  public $timestamps=false;
  protected $dates=['RRDate','RVDate','MRDate'];
  protected $primaryKey='MRNo';
  public $incrementing= false;
  public function MRDetail()
  {
    return $this->hasMany('App\MRDetail','MRNo','MRNo');
  }
  public function users()
  {
    return $this->morphToMany('App\User', 'Signatureable')->withPivot('Signature','SignatureType','id')->orderBy('pivot_id');
  }
  public function getNotificationDateTimeAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
  }
  public function warehouseman()
  {
    return $this->belongsTo('App\User','CreatorID','id');
  }
}
