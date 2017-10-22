<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialsTicketDetail extends Model
{
    protected $dates=['MTDate'];
    protected $table = 'MaterialsTicketDetails';
    public $timestamps = false;
    protected $primaryKey=['ItemCode'];
    public $incrementing = false;
    // protected $dateFormat = 'M-Y';
    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         $model->MTDate = $model->freshTimestamp();
    //     });
    // }
    public function MasterItems()
    {
      return $this->belongsTo('App\MasterItem','ItemCode','ItemCode');
    }
    // public function getMTDateAttribute($value)
    // {
    //   return $dateFormat='M Y';
    // }
    // public function getFnameAttribute($value)
    // {
    //   return ucfirst($value);
    // }
}
