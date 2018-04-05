<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class MRTMaster extends Model
{
    protected $table="MRTMaster";
    public $dates=['ReturnDate'];
    public $timestamps =false;
    protected $primaryKey='MRTNo';
    public $incrementing = false;

    public function users()
    {
      return $this->morphToMany('App\User', 'Signatureable')->withPivot('Signature','SignatureType','id')->orderBy('pivot_id');
    }
    public function getNotificationDateTimeAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
    }
}
