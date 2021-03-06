<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class MRMaster extends Model
{
  protected $table='MRMaster';
  public $timestamps=false;
  protected $dates=['RRDate','RVDate','MRDate'];
  protected $dateFormat='M d, Y';
  protected $primaryKey='MRNo';
  public $incrementing= false;
  public function MRDetail()
  {
    return $this->hasMany('App\MRDetail','MRNo','MRNo');
  }
  public function users()
  {
    return $this->morphToMany('App\User', 'signatureable')->withPivot('Signature','SignatureType');
  }
  public function getNotificationDateTimeAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s.u', $date)->diffForHumans();
  }
  public function warehouseman()
  {
    return $this->belongsTo('App\User','CreatorID','id');
  }
}
