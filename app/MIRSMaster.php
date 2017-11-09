<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MIRSMaster extends Model
{
  protected $dates = ['MIRSDate'];
  protected $primaryKey='MIRSNo';
  public $incrementing = false;
  public $timestamps=false;
  protected $table='MIRSMaster';
  protected $dateFormat = 'M d, Y';

  public function users()
  {
    return $this->morphToMany('App\User', 'signatureable')->withPivot('Signature','SignatureType');
  }
}
