<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialsTicketDetail extends Model
{
    protected $table = 'MaterialsTicketDetails';

    public function ItemMaster()
    {
      return $this->belongsTo('App\ItemMaster','id');
    }
}
