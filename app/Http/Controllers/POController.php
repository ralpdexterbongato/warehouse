<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CanvassMaster;
use App\POMaster;
use Carbon\Carbon;
use App\PODetail;
use App\RVMaster;
use App\User;
use Auth;
use App\RRValidatorNoPO;
use App\RRValidatorWithPO;
class POController extends Controller
{
    public function GeneratePOfromCanvass(Request $request)
    {

    $date=Carbon::now();
    $year=Carbon::now()->format('y');
    $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role','2')->take(1)->get(['Fname','Lname']);
    $RVMasterDB=RVMaster::where('RVNo',$request->RVNo)->get(['RVDate','Purpose']);
    $collected=collect($request->SupplierChoice);
    $SupplierGrouped=$collected->unique();
    $POid=POMaster::orderBy('PONo','DESC')->take(1)->value('PONo');
    $incremented='';
    $toDBMaster = array();
    $toDBDetails = array();
    foreach ($SupplierGrouped as $key => $SupplierG)
    {
    if ($SupplierG!=null)
    {
      if (($POid!=null)&&($incremented==null))
      {
        $numonly=substr($POid,'3');
        $numonlyint=(int)$numonly;
        $newID=$numonlyint + 1;
        $incremented=$year .'-'. sprintf("%04d",$newID);
      }elseif($incremented!=null)
      {
        $numonly=substr($incremented,'3');
        $numonlyint=(int)$numonly;
        $newID=$numonlyint + 1;
        $incremented=$year .'-'. sprintf("%04d",$newID);
      }
      else
      {
        $incremented= $year.'-'. sprintf("%04d",'1');
      }
      $CanvasMaster=CanvassMaster::where('RVNo',$request->RVNo)->where('Supplier', $SupplierG)->get();
      $toDBMaster[]=array('PONo'=>$incremented,'RVNo' => $CanvasMaster[0]->RVNo,
      'Supplier' =>$CanvasMaster[0]->Supplier ,'Address'=>$CanvasMaster[0]->Address,
      'Telephone'=>$CanvasMaster[0]->Telephone,'Purpose'=>$RVMasterDB[0]->Purpose,'GeneralManager'=>$GM[0]->Fname.' '.$GM[0]->Lname,
      'RVDate'=>$RVMasterDB[0]->RVDate,'PODate'=>$date);

      $RRValidNoPO=RRValidatorNoPO::where('RVNo',$request->RVNo)->get(['Particulars','Quantity']);//use to minus qty for every po generation,so we can validate po items cant be overOrder.
      foreach ($request->SupplierChoice as $key => $supplierpick)
      {
        if (($SupplierG==$supplierpick)&&($supplierpick!=null))
        {
          $price=$CanvasMaster[0]->CanvassDetail[$key]->Price;
          $quantity=$CanvasMaster[0]->CanvassDetail[$key]->Qty;
          $Amt=$price*$quantity;
          $toDBDetails[] = array('AccountCode'=>$CanvasMaster[0]->CanvassDetail[$key]->AccountCode,'ItemCode'=>$CanvasMaster[0]->CanvassDetail[$key]->ItemCode,'Price' =>$price ,'Unit'=>$CanvasMaster[0]->CanvassDetail[$key]->Unit,'Description'=>$CanvasMaster[0]->CanvassDetail[$key]->Article
          ,'Qty'=>$quantity,'Amount'=>$Amt,'PONo'=>$incremented);
           foreach ($RRValidNoPO as $overorderpreventor)
           {
             if ($overorderpreventor->Particulars==$CanvasMaster[0]->CanvassDetail[$key]->Article)
             {
               RRValidatorNoPO::where('RVNo',$request->RVNo)->where('Particulars', $overorderpreventor->Particulars)->update(['Quantity'=>0]);
             }
           }
        }
      }
    }
  }
  if (!empty($toDBMaster[0]))
  {
    POMaster::insert($toDBMaster);
    PODetail::insert($toDBDetails);
  }
    return ['redirect'=>route('POListView',[$request->RVNo])];
  }

  public function POListView($id)
  {
    $POList=POMaster::where('RVNo', $id)->get(['PONo','Supplier','GeneralManagerSignature','IfDeclined','ApprovalReplacerSignature']);
    return view('Warehouse.PO.POlistView',compact('POList'));
  }
  public function POFullpreview($id)
  {
    $RRValidatorWithPO=RRValidatorWithPO::where('PONo',$id)->get(['Qty']);
    $remainingUnreceived=$RRValidatorWithPO->sum('Qty');
    $OrderMaster=POMaster::where('PONo', $id)->get();
    $totalAmt=PODetail::where('PONo', $id)->get(['Amount'])->sum('Amount');
    return view('Warehouse.PO.POfullpreview',compact('remainingUnreceived','OrderMaster','totalAmt'));
  }
  public function GMSignaturePO(Request $request)
  {
    POMaster::where('PONo',$request->PONo)->update(['GeneralManagerSignature'=>Auth::user()->Signature,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
     $PODetails=PODetail::where('PONo',$request->PONo)->get();
     $forRRValidatorWithPO = array();
     foreach ($PODetails as $podata)
     {
       $forRRValidatorWithPO[] = array('Price' =>$podata->Price ,'Unit'=>$podata->Unit,'Description'=>$podata->Description,'Qty'=>$podata->Qty,'Amount'=>$podata->Amount,'PONo'=>$podata->PONo,'ItemCode'=>$podata->ItemCode,'AccountCode'=>$podata->AccountCode);
     }
     RRValidatorWithPO::insert($forRRValidatorWithPO);
    return redirect()->back();
  }
  public function GMDeclined(Request $request)
  {
    POMaster::where('PONo',$request->PONo)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname,'ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null,'ApprovalReplacerSignature'=>null,'ApprovalReplacerPosition'=>null]);
    $PODetails=PODetail::where('PONo',$request->PONo)->get(['Qty','Description']);
    $RVNo=POMaster::where('PONo',$request->PONo)->value('RVNo');
    $RRValidatorNoPO=RRValidatorNoPO::where('RVNo',$RVNo)->get(['Particulars']);
    foreach ($PODetails as $podetail)
    {
      foreach ($RRValidatorNoPO as $canvassvalidator)
      {
        if ($canvassvalidator->Particulars==$podetail->Description)
        {
          RRValidatorNoPO::where('RVNo',$RVNo)->where('Particulars',$canvassvalidator->Particulars)->update(['Quantity'=>$podetail->Qty]);
        }
      }
    }
    return redirect()->back();
  }
  public function MyPOrequestlist()
  {
    $myPOlist=POMaster::orderBy('PONo','DESC')->where('GeneralManager',Auth::user()->Fname.' '.Auth::user()->Lname)->where('GeneralManagerSignature',null)->where('IfDeclined',null)->paginate(10,['PONo','RVNo','Supplier','Address','Telephone','Purpose','PODate']);
    return view('Warehouse.PO.myPOrequest',compact('myPOlist'));
  }
  public function POAuthorizeInBehalf($id)
  {
    if (Auth::user()->Role==0)
    {
        POMaster::where('PONo',$id)->update(['ApprovalReplacerFname'=>Auth::user()->Fname,'ApprovalReplacerLname'=>Auth::user()->Lname]);
    }
    return redirect()->back();
  }
  public function CancelAuthorizeInBehalf($id)
  {
    $POMaster=POMaster::where('PONo', $id)->get(['ApprovalReplacerFname','ApprovalReplacerLname','ApprovalReplacerSignature']);
    if (($POMaster[0]->ApprovalReplacerFname.' '.$POMaster[0]->ApprovalReplacerLname==Auth::user()->Fname.' '.Auth::user()->Lname)&&($POMaster[0]->ApprovalReplacerSignature==null))
    {
      POMaster::where('PONo', $id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null]);
    }
    return redirect()->back();
  }
  public function AuthorizeInBehalfdeclined($id)
  {
      POMaster::where('PONo', $id)->update(['ApprovalReplacerFname'=>null,'ApprovalReplacerLname'=>null]);
      return redirect()->back();
  }
  public function AuthorizeInBehalfconfirmed($id)
  {
    $authorizeReplacer=POMaster::where('PONo',$id)->get(['ApprovalReplacerFname','ApprovalReplacerLname']);
    $replacerSignaturePosition=User::whereNotNull('IsActive')->where('Fname',$authorizeReplacer[0]->ApprovalReplacerFname)->where('Lname', $authorizeReplacer[0]->ApprovalReplacerLname)->get(['Signature','Position']);
    POMaster::where('PONo',$id)->update(['ApprovalReplacerSignature'=>$replacerSignaturePosition[0]->Signature,'ApprovalReplacerPosition'=>$replacerSignaturePosition[0]->Position]);
    $PODetails=PODetail::where('PONo',$id)->get();
    $forRRValidatorWithPO = array();
    foreach ($PODetails as $podata)
    {
      $forRRValidatorWithPO[] = array('Price' =>$podata->Price ,'Unit'=>$podata->Unit,'Description'=>$podata->Description,'Qty'=>$podata->Qty,'Amount'=>$podata->Amount,'PONo'=>$podata->PONo,'ItemCode'=>$podata->ItemCode,'AccountCode'=>$podata->AccountCode);
    }
    RRValidatorWithPO::insert($forRRValidatorWithPO);
    return redirect()->back();
  }
}
