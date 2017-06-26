<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialsTicketDetail extends Model
{
    protected $table = 'MaterialsTicketDetails';
    public $timestamps = false;
    public function MasterItem()
    {
      return $this->belongsTo('App\MasterItem','id');
    }
}
