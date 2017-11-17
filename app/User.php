<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
class User extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'FullName', 'Password',
    ];

    public $timestamps=false;
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function MIRSHistory($date)
    {
      return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')->withPivot(['SignatureType','Signature'])->wherePivot('SignatureType', 'PreparedBy')->where('MIRSDate','LIKE',$date.'%');
    }
    public function MIRSSignatureTurn()
    {
        return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')->withPivot(['SignatureType','Signature'])->wherePivot('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','RecommendedBy')->wherePivot('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ManagerReplacer')->where('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','2')->wherePivot('SignatureType','ApprovalReplacer')->where('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','2')->wherePivot('SignatureType','ApprovedBy')->where('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','PreparedBy')->where('user_id', Auth::user()->id);
    }
    public function MCTSignatureTurn()
    {
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')->orderBy('MCTNo','DESC')->withPivot(['SignatureType','Signature'])
        ->wherePivot('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','IssuedBy')->wherePivot('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ReceivedBy')->where('user_id', Auth::user()->id);
    }
    public function MCTHistory($date)
    {
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')->orderBy('MCTNo','DESC')->withPivot(['SignatureType','Signature'])->wherePivot('SignatureType', 'ReceivedBy')->where('MCTDate','LIKE',$date.'%');
    }
    public function MRTSignatureTurn()
    {
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')->orderBy('MRTNo','DESC')->withPivot(['SignatureType','Signature'])
        ->wherePivot('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','ReceivedBy')->wherePivot('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ReturnedBy')->where('user_id', Auth::user()->id);
    }
    public function MRTHistory($date)
    {
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')->orderBy('MRTNo','DESC')->withPivot(['SignatureType','Signature'])->wherePivot('SignatureType', 'ReturnedBy')->where('ReturnDate','LIKE',$date.'%');
    }
    public function RVSignatureTurn()
    {
        return $this->morphedByMany('App\RVMaster','Signatureable')->orderBy('RVNo','DESC')->withPivot(['SignatureType','Signature'])
        ->wherePivot('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','Requisitioner')->wherePivot('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','RecommendedBy')->where('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ManagerReplacer')->where('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','2')->wherePivot('SignatureType','BudgetOfficer')->where('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','3')->wherePivot('SignatureType','ApprovedBy')->where('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','3')->wherePivot('SignatureType','ApprovalReplacer')->where('user_id', Auth::user()->id);
    }
    public function RVHistory($date)
    {
      return $this->morphedByMany('App\RVMaster', 'Signatureable')->orderBy('RVNo','DESC')->withPivot(['SignatureType','Signature'])->wherePivot('SignatureType', 'Requisitioner')->where('RVDate','LIKE',$date.'%');
    }
    public function POSignatureTurn()
    {
      return $this->morphedByMany('App\POMaster','Signatureable')->orderBy('PONo','DESC')->withPivot(['SignatureType','Signature'])
      ->whereNull('Status')->wherePivot('Signature',null)->wherePivot('SignatureType', 'ApprovedBy')
      ->orWhere('Status',null)->wherePivot('Signature',null)->where('SignatureType', 'ApprovalReplacer');
    }
}
