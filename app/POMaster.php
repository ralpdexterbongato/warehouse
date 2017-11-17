<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class POMaster extends Model
{
  protected $table='POMasters';
  public $timestamps=false;
  public $incrementing=false;
  public $dates=['PODate','RVDate'];
  protected $dateFormat = 'M d, Y';
  public $primaryKey='PONo';

  public function PODetails()
  {
    return $this->hasMany('App\PODetail','PONo','PONo');
  }
  public function users()
  {
    return $this->morphToMany('App\User','signatureable')->withPivot('Signature','SignatureType');
  }
}
