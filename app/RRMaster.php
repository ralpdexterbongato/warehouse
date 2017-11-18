<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RRMaster extends Model
{
  protected $table="RRMaster";
  public $timestamps =false;
  public $dates=['RRDate'];
  public $fillable=['RVNo'];
  protected $dateFormat='M d, Y';
  public $incrementing=false;
  public $primaryKey='RRNo';

  public function users()
  {
    return $this->morphToMany('App\User', 'signatureable')->withPivot('Signature','SignatureType');
  }
}
