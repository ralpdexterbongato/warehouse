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
}
