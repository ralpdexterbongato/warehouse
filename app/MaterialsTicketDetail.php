<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialsTicketDetail extends Model
{
    protected $dates=['created_at'];
    protected $table = 'MaterialsTicketDetails';
    public $timestamps = false;
}
