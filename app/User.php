<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Carbon\Carbon;
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

    public function MIRSSignatureTurn()
    {
        return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')->withPivot(['SignatureType','Signature'])
        ->wherePivot('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','RecommendedBy')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MIRSMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ManagerReplacer')->where('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MIRSMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','2')->wherePivot('SignatureType','ApprovalReplacer')->where('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MIRSMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','2')->wherePivot('SignatureType','ApprovedBy')->where('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MIRSMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','PreparedBy')->where('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MIRSMaster');
    }
    public function MCTSignatureTurn()
    {
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')->orderBy('MCTNo','DESC')->withPivot(['SignatureType','Signature'])
        ->wherePivot('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','IssuedBy')->wherePivot('user_id', Auth::user()->id)->whereNull('Status')
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ReceivedBy')->wherePivot('user_id', Auth::user()->id)->whereNull('Status');
    }
    public function MIRSHistory($date)
    {
      $dateArray = explode('-',$date);
      return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')
      ->withPivot(['SignatureType','Signature'])
      ->wherePivot('SignatureType', 'PreparedBy')
      ->whereYear("mirsdate", $dateArray[0])
      ->whereMonth("mirsdate", $dateArray[1]);
    }
    public function MCTHistory($date)
    {
        $dateArray = explode('-',$date);
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')
        ->orderBy('MCTNo','DESC')
        ->withPivot(['SignatureType','Signature'])
        ->wherePivot('SignatureType', 'ReceivedBy')
        ->whereYear("mctdate", $dateArray[0])
        ->whereMonth("mctdate", $dateArray[1]);
    }
    public function MRTHistory($date)
    {
        $dateArray = explode('-',$date);
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')
        ->orderBy('MRTNo','DESC')
        ->withPivot(['SignatureType','Signature'])
        ->wherePivot('SignatureType', 'ReturnedBy')
        ->whereYear("returndate", $dateArray[0])
        ->whereMonth("returndate", $dateArray[1]);
    }
    public function RVHistory($date)
    {
      $dateArray = explode('-',$date);
      return $this->morphedByMany('App\RVMaster', 'Signatureable')
      ->orderBy('RVNo','DESC')
      ->withPivot(['SignatureType','Signature'])
      ->wherePivot('SignatureType', 'Requisitioner')
      ->whereYear("RVDate", $dateArray[0])
      ->whereMonth("RVDate", $dateArray[1]);

    }
    public function RRHistory($date)
    {
      $dateArray = explode('-',$date);
      return $this->morphedByMany('App\RRMaster', 'Signatureable')
      ->orderBy('RRNo','DESC')->withPivot(['SignatureType','Signature'])
      ->wherePivot('SignatureType', 'ReceivedBy')
      ->whereYear("rrdate", $dateArray[0])
      ->whereMonth("rrdate", $dateArray[1]);
    }
    public function MRHistory($date)
    {
      $dateArray = explode('-',$date);
      return $this->morphedByMany('App\MRMaster', 'Signatureable')
      ->orderBy('MRNo','DESC')
      ->withPivot(['SignatureType','Signature'])
      ->wherePivot('SignatureType', 'ReceivedBy')
      ->whereYear("mrdate", $dateArray[0])
      ->whereMonth("mrdate", $dateArray[1]);
    }
    public function MRTSignatureTurn()
    {
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')->orderBy('MRTNo','DESC')->withPivot(['SignatureType','Signature'])
        ->wherePivot('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','ReceivedBy')->wherePivot('user_id', Auth::user()->id)
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ReturnedBy')->where('user_id', Auth::user()->id);
    }
    public function RVSignatureTurn()
    {
        return $this->morphedByMany('App\RVMaster','Signatureable')->orderBy('RVNo','DESC')->withPivot(['SignatureType','Signature'])
        ->wherePivot('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','Requisitioner')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\RVMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','1')->where('SignatureType','RecommendedBy')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\RVMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ManagerReplacer')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\RVMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','2')->wherePivot('SignatureType','BudgetOfficer')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\RVMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','3')->wherePivot('SignatureType','ApprovedBy')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\RVMaster')
        ->orWhere('Signature',null)->where('SignatureTurn','3')->wherePivot('SignatureType','ApprovalReplacer')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\RVMaster');
    }
    public function POSignatureTurn()
    {
      return $this->morphedByMany('App\POMaster','Signatureable')->orderBy('PONo','DESC')->withPivot(['SignatureType','Signature'])
      ->whereNull('Status')->wherePivot('Signature',null)->wherePivot('SignatureType', 'ApprovedBy')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\POMaster')
      ->orWhere('Status',null)->wherePivot('Signature',null)->where('SignatureType', 'ApprovalReplacer')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\POMaster');
    }
    public function RRSignatureTurn()
    {
      return $this->morphedByMany('App\RRMaster','Signatureable')->orderBy('RRNo','DESC')->withPivot(['SignatureType','Signature'])
      ->whereNull('Status')->wherePivot('Signature',null)->wherePivot('user_id', Auth::user()->id);
    }
    public function MRSignatureTurn()
    {
      return $this->morphedByMany('App\MRMaster', 'Signatureable')->orderBy('MRNo','DESC')->withPivot(['SignatureType','Signature'])
      ->where('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ApprovedBy')->where('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MRMaster')
      ->orWhere('Signature',null)->where('SignatureTurn','1')->wherePivot('SignatureType','ApprovalReplacer')->where('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MRMaster')
      ->orWhere('Signature',null)->where('SignatureTurn','0')->wherePivot('SignatureType','RecommendedBy')->wherePivot('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MRMaster')
      ->orWhere('Signature',null)->where('SignatureTurn','2')->wherePivot('SignatureType','ReceivedBy')->where('user_id', Auth::user()->id)->where('Signatureable_type', 'App\MRMaster');
    }

    public function getLastActivityAttribute($time)
    {
      $minAgo = Carbon::now()->subSeconds(300);
      if ($minAgo > $time)
      {
        $lastonline = Carbon::createFromFormat('Y-m-d H:i:s', $time)->diffForHumans();
        $lastonline = str_replace([' seconds', ' second'], ' sec', $lastonline);
        $lastonline = str_replace([' minutes', ' minute'], ' min', $lastonline);
        $lastonline = str_replace([' hours', ' hour'], ' h', $lastonline);
        $lastonline = str_replace([' months', ' month'], ' m', $lastonline);
        $lastonline = str_replace(' ago', '', $lastonline);

        if(preg_match('(years|year)', $lastonline)){
            $lastonline = $this->last_activity->toFormattedDateString();
        }
        return $lastonline;
      }else
      {
        return '0';
      }
    }
    //recent files
    public function mirsrecent()
    {
      return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')->withPivot(['SignatureType'])->take(1);
    }
    public function mctrecent()
    {
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')->orderBy('MCTNo','DESC')->withPivot(['SignatureType'])->take(1);
    }
    public function mrtrecent()
    {
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')->orderBy('MRTNo','DESC')->withPivot(['SignatureType'])->take(1);
    }
    public function rvrecent()
    {
      return $this->morphedByMany('App\RVMaster', 'Signatureable')->orderBy('RVNo','DESC')->withPivot(['SignatureType'])->take(1);
    }
    public function rrrecent()
    {
      return $this->morphedByMany('App\RRMaster', 'Signatureable')->orderBy('RRNo','DESC')->withPivot(['SignatureType'])->take(1);
    }
    public function mrrecent()
    {
      return $this->morphedByMany('App\MRMaster', 'Signatureable')->orderBy('MRNo','DESC')->withPivot(['SignatureType'])->take(1);
    }
    public function porecent()
    {
      return $this->morphedByMany('App\POMaster', 'Signatureable')->orderBy('PONo','DESC')->withPivot(['SignatureType'])->take(1);
    }

    // valid files
    public function mirsvalid()
    {
      return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')->withPivot(['SignatureType'])->where('Status','0');
    }
    public function mctvalid()
    {
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')->orderBy('MCTNo','DESC')->withPivot(['SignatureType'])->where('Status','0')->whereNull('IsRollBack')->orWhere('IsRollBack','1')->where('Status','0')->wherePivot('user_id',Auth::user()->id)->wherePivot('Signatureable_type','App\MCTMaster');
    }
    public function mrtvalid()
    {
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')->orderBy('MRTNo','DESC')->withPivot(['SignatureType'])->where('Status','0')->whereNull('IsRollBack')->orWhere('IsRollBack','1')->where('Status','0')->wherePivot('user_id',Auth::user()->id)->wherePivot('Signatureable_type','App\MRTMaster');
    }
    public function rvvalid()
    {
      return $this->morphedByMany('App\RVMaster', 'Signatureable')->orderBy('RVNo','DESC')->withPivot(['SignatureType'])->where('Status','0');
    }
    public function rrvalid()
    {
      return $this->morphedByMany('App\RRMaster', 'Signatureable')->orderBy('RRNo','DESC')->withPivot(['SignatureType'])->where('Status','0')->whereNull('IsRollBack')->orWhere('IsRollBack','1')->where('Status','0')->wherePivot('user_id',Auth::user()->id)->wherePivot('Signatureable_type','App\RRMaster');
    }
    public function mrvalid()
    {
      return $this->morphedByMany('App\MRMaster', 'Signatureable')->orderBy('MRNo','DESC')->withPivot(['SignatureType'])->where('Status','0');
    }
    public function povalid()
    {
      return $this->morphedByMany('App\POMaster', 'Signatureable')->orderBy('PONo','DESC')->withPivot(['SignatureType'])->where('Status','0');
    }

    // invalid files
    public function mirsinvalid()
    {
      return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')->withPivot(['SignatureType'])->where('Status','1');
    }
    public function mctinvalid()
    {
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')->orderBy('MCTNo','DESC')->withPivot(['SignatureType'])->where('Status','1')->orWhere('IsRollBack','0')->wherePivot('user_id',Auth::user()->id)->wherePivot('Signatureable_type','App\MCTMaster');
    }
    public function mrtinvalid()
    {
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')->orderBy('MRTNo','DESC')->withPivot(['SignatureType'])->where('Status','1')->orWhere('IsRollBack','0')->wherePivot('user_id',Auth::user()->id)->wherePivot('Signatureable_type','App\MRTMaster');
    }
    public function rvinvalid()
    {
      return $this->morphedByMany('App\RVMaster', 'Signatureable')->orderBy('RVNo','DESC')->withPivot(['SignatureType'])->where('Status','1');
    }
    public function rrinvalid()
    {
      return $this->morphedByMany('App\RRMaster', 'Signatureable')->orderBy('RRNo','DESC')->withPivot(['SignatureType'])->where('Status','1')->orWhere('IsRollBack','0')->wherePivot('user_id',Auth::user()->id)->wherePivot('Signatureable_type','App\RRMaster');
    }
    public function mrinvalid()
    {
      return $this->morphedByMany('App\MRMaster', 'Signatureable')->orderBy('MRNo','DESC')->withPivot(['SignatureType'])->where('Status','1');
    }
    public function poinvalid()
    {
      return $this->morphedByMany('App\POMaster', 'Signatureable')->orderBy('PONo','DESC')->withPivot(['SignatureType'])->where('Status','1');
    }

    // pending
    public function mirspending()
    {
      return $this->morphedByMany('App\MIRSMaster', 'Signatureable')->orderBy('MIRSNo','DESC')->withPivot(['SignatureType'])->whereNull('Status');
    }
    public function mctpending()
    {
        return $this->morphedByMany('App\MCTMaster', 'Signatureable')->orderBy('MCTNo','DESC')->withPivot(['SignatureType'])->whereNull('Status');
    }
    public function mrtpending()
    {
        return $this->morphedByMany('App\MRTMaster', 'Signatureable')->orderBy('MRTNo','DESC')->withPivot(['SignatureType'])->whereNull('Status');
    }
    public function rvpending()
    {
      return $this->morphedByMany('App\RVMaster', 'Signatureable')->orderBy('RVNo','DESC')->withPivot(['SignatureType'])->whereNull('Status');
    }
    public function rrpending()
    {
      return $this->morphedByMany('App\RRMaster', 'Signatureable')->orderBy('RRNo','DESC')->withPivot(['SignatureType'])->whereNull('Status');
    }
    public function mrpending()
    {
      return $this->morphedByMany('App\MRMaster', 'Signatureable')->orderBy('MRNo','DESC')->withPivot(['SignatureType'])->whereNull('Status');
    }
    public function popending()
    {
      return $this->morphedByMany('App\POMaster', 'Signatureable')->orderBy('PONo','DESC')->withPivot(['SignatureType'])->whereNull('Status');
    }
}
