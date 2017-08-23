<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RRMaster extends Model
{
  protected $table="RRMaster";
  public $timestamps =false;
  public $dates=['RRDate'];
  public $fillable=['RVNo'];
}
