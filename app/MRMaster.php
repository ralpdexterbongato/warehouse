<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MRMaster extends Model
{
  protected $table='MRMaster';
  public $timestamps=false;
  protected $dates=['RRDate','RVDate','MRDate'];
  public function MRDetail()
  {
    return $this->hasMany('App\MRDetail','MRNo','MRNo');
  }
}
