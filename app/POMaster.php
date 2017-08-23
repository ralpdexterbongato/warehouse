<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class POMaster extends Model
{
  protected $table='PurchaseOrderMasters';
  public $timestamps=false;
  public $incrementing=false;
  public $dates=['PODate','RVDate'];
  public function PODetails()
  {
    return $this->hasMany('App\PODetail','PurchaseOrderMasters_PONo','PONo');
  }
}
