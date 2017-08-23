<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MIRSMaster;
use App\RVMaster;
use App\MRMaster;
use App\POMaster;
class GMSignatureReplaceController extends Controller
{
  public function __construct()
  {
    $this->middleware('IsAdmin');
  }
  public function ViewAllReplaceSignatureRequest()
  {
    $MIRSrequestGMisAbsent=MIRSMaster::whereNotNull('ApprovalReplacerFname')->whereNotNull('ApprovalReplacerLname')->whereNull('ApprovalReplacerSignature')->paginate(5,['ApprovalReplacerLname','ApprovalReplacerFname','MIRSNo']);
    $RVrequestGMisAbsent=RVMaster::whereNotNull('ApprovalReplacerFname')->whereNotNull('ApprovalReplacerLname')->whereNull('ApprovalReplacerSignature')->paginate(5,['ApprovalReplacerLname','ApprovalReplacerFname','RVNo']);
    $POrequestGMisAbsent=POMaster::whereNotNull('ApprovalReplacerFname')->whereNotNull('ApprovalReplacerLname')->whereNull('ApprovalReplacerSignature')->paginate(5,['ApprovalReplacerLname','ApprovalReplacerFname','PONo']);
    $MRrequestGMisAbsent=MRMaster::whereNotNull('ApprovalReplacerFname')->whereNotNull('ApprovalReplacerLname')->whereNull('ApprovalReplacerSignature')->paginate(5,['ApprovalReplacerLname','ApprovalReplacerFname','MRNo']);
    return view('Warehouse.GMRequestSignaturesIfAbsent',compact('MIRSrequestGMisAbsent','RVrequestGMisAbsent','POrequestGMisAbsent','MRrequestGMisAbsent'));
  }
}
