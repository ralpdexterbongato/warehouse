<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MRTMaster extends Model
{
    protected $table="MRTMaster";
    public $dates=['ReturnDate'];
    public $timestamps =false;
    protected $dateFormat = 'M d, Y';
    protected $primaryKey='MRTNo';
    public $incrementing = false;

    public function users()
    {
      return $this->morphToMany('App\User', 'signatureable')->withPivot('Signature','SignatureType');
    }
}
