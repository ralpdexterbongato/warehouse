<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MCTMaster extends Model
{
  protected $table='MCTMaster';
  public $timestamps=false;
  public $dates=['MCTDate'];
  protected $dateFormat = 'M d, Y';
}
