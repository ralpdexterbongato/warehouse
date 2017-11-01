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

    public function MasterItems()
    {
      return $this->belongsTo('App\MasterItem','ItemCode','ItemCode');
    }

}
