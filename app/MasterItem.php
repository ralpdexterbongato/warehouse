<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    protected $table = 'MasterItems';
    public $timestamps = false;
    public $increments=false;
    public $primaryKey='ItemCode';
    public function MaterialsTicketDetails()
    {
      return $this->hasMany('App\MaterialsTicketDetail','ItemCode','ItemCode');
    }
}
