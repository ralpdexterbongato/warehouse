<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MRTMaster extends Model
{
    protected $table="MRTMaster";
    public $dates=['ReturnDate'];
    public $timestamps =false;
}
