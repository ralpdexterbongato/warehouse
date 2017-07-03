<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialsTicketDetail extends Model
{
    protected $dates=['created_at'];
    protected $table = 'MaterialsTicketDetails';
    public $timestamps = false;
    protected $primaryKey=['ItemCode'];
    public $incrementing = false;
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
    public function MasterItems()
    {
      return $this->belongsTo('App\MasterItem','ItemCode','ItemCode_id');
    }
    public function MRTMaster()
    {
      return $this->belongsTo('App\MRTMaster','ItemCode','ItemCode_id');
    }
}
