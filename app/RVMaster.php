<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RVMaster extends Model
{
  protected $table="RVMasters";
  public $timestamps =false;
  public $dates=['RVDate'];
}
