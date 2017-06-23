<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemMaster extends Model
{
    protected $table = 'ItemMasters';
    public function MaterialsTicketDetail()
    {
      return $this->hasMany('App\MaterialsTicketDetail','id','MaterialsTicketDetails_id');
    }
}
