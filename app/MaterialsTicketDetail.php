<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialsTicketDetail extends Model
{
    protected $dates=['MTDate'];
    protected $table = 'MaterialsTicketDetails';
    public $timestamps = false;
    public $dateFormat = 'M d, Y';

    public function MasterItems()
    {
      return $this->belongsTo('App\MasterItem','ItemCode','ItemCode');
    }

}
