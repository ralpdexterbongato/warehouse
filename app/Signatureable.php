<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Signatureable extends Model
{
    protected $table="Signatureables";
    public $timestamps =false;

    public function user()
    {
      return $this->belongsTo('App\User','user_id','id');
    }
}
