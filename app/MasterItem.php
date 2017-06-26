<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    protected $table = 'MasterItems';
    public $timestamps = false;
    public function MaterialsTicketDetail()
    {
      return $this->hasMany('App\MaterialsTicketDetail','id','MaterialsTicketDetails_id');
    }
}
