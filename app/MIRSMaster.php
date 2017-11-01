<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MIRSMaster extends Model
{
  protected $dates = ['MIRSDate'];
  public $timestamps=false;
  protected $table='MIRSMaster';
  protected $dateFormat = 'M d, Y';

}
