<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialsTicketDetail extends Model
{
    protected $dates=['MTDate'];
    protected $table = 'MaterialsTicketDetails';
    public $timestamps = false;
    protected $fillable = ['Quantity','UnitCost','Amount','CurrentCost','CurrentQuantity','CurrentAmount','IsRollBack'];
    public function MasterItems()
    {
      return $this->belongsTo('App\MasterItem','ItemCode','ItemCode');
    }

}
