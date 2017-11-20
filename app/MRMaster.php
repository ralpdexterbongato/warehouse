<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
