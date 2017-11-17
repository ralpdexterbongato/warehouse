<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RVMaster extends Model
{
  protected $table="RVMasters";
  public $timestamps =false;
  public $dates=['RVDate'];
  protected $dateFormat = 'M d, Y';
  protected $primaryKey='RVNo';
  public $incrementing = false;

  public function users()
  {
    return $this->morphToMany('App\User','signatureable')->withPivot('Signature','SignatureType');
  }
}
