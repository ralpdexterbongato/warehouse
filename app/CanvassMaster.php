<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanvassMaster extends Model
{
  protected $table='CanvassMasters';
  public $timestamps=false;

  public function CanvassDetail()
  {
    return $this->hasMany('App\CanvassDetail','CanvassMasters_id','id');
  }
  protected static function boot() {
       parent::boot();

       static::deleting(function($CanvassMaster) { // before delete() method call this
            $CanvassMaster->CanvassDetail()->delete();
            // do the rest of the cleanup...
       });
   }
}
