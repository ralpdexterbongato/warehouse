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
use App\Jobs\NewCreatedPOJob;
use App\RVDetail;
class POController extends Controller
{
    public function GeneratePOfromCanvass(Request $request)
    {
    $date=Carbon::now();
    $year=Carbon::now()->format('y');
    $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role','2')->take(1)->get(['FullName']);
    $RVMasterDB=RVMaster::where('RVNo',$request->RVNo)->get(['RVDate','Purpose']);
    $collected=collect($request->SupplierChoice);
    $SupplierGrouped=$collected->unique();
    $POid=POMaster::orderBy('PONo','DESC')->take(1)->value('PONo');
    $incremented='';
    $ApprovalReplacer=User::whereNotNull('IfApproveReplacer')->get(['FullName']);
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
      if (!empty($ApprovalReplacer[0]))
      {
        $toDBMaster[]=array('PONo'=>$incremented,'RVNo' => $CanvasMaster[0]->RVNo,
        'Supplier' =>$CanvasMaster[0]->Supplier ,'Address'=>$CanvasMaster[0]->Address,
        'Telephone'=>$CanvasMaster[0]->Telephone,'Purpose'=>$RVMasterDB[0]->Purpose,'GeneralManager'=>$GM[0]->FullName,
        'RVDate'=>$RVMasterDB[0]->RVDate,'PODate'=>$date,'ApprovalReplacer'=>$ApprovalReplacer[0]->FullName);
      }else
      {
        $toDBMaster[]=array('PONo'=>$incremented,'RVNo' => $CanvasMaster[0]->RVNo,
        'Supplier' =>$CanvasMaster[0]->Supplier ,'Address'=>$CanvasMaster[0]->Address,
        'Telephone'=>$CanvasMaster[0]->Telephone,'Purpose'=>$RVMasterDB[0]->Purpose,'GeneralManager'=>$GM[0]->FullName,
        'RVDate'=>$RVMasterDB[0]->RVDate,'PODate'=>$date);
      }

      $FromRVDetail=RVDetail::where('RVNo',$request->RVNo)->get(['Particulars','QuantityValidator']);//use to minus qty for every po generation,so we can validate po items cant be overOrder.
      foreach ($request->SupplierChoice as $key => $supplierpick)
      {
        if (($SupplierG==$supplierpick)&&($supplierpick!=null))
        {
          $price=$CanvasMaster[0]->CanvassDetail[$key]->Price;
          $quantity=$CanvasMaster[0]->CanvassDetail[$key]->Qty;
          $Amt=$price*$quantity;
          $toDBDetails[] = array('AccountCode'=>$CanvasMaster[0]->CanvassDetail[$key]->AccountCode,'ItemCode'=>$CanvasMaster[0]->CanvassDetail[$key]->ItemCode,'Price' =>$price ,'Unit'=>$CanvasMaster[0]->CanvassDetail[$key]->Unit,'Description'=>$CanvasMaster[0]->CanvassDetail[$key]->Article
          ,'Qty'=>$quantity,'QtyValidator'=>$quantity,'Amount'=>$Amt,'PONo'=>$incremented);
           foreach ($FromRVDetail as $overorderpreventor)
           {
             if (($overorderpreventor->Particulars==$CanvasMaster[0]->CanvassDetail[$key]->Article))
             {
               if ($overorderpreventor->QuantityValidator==0)
               {
                 return response()->json(['error'=>$overorderpreventor->Particulars.' already have purchase order.']);
               }
               RVDetail::where('RVNo',$request->RVNo)->where('Particulars', $overorderpreventor->Particulars)->update(['QuantityValidator'=>0]);
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
    $GMName=str_replace(' ','',$GM[0]->FullName);
    $NotifyName = array('NotifyName' =>$GMName);
    $NotifyName=(object)$NotifyName;
    $job=(new NewCreatedPOJob($NotifyName))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
    if (!empty($ApprovalReplacer[0]))
    {
      $ApproveReplacerName=str_replace(' ','',$ApprovalReplacer[0]->FullName);
      $NotifyName = array('NotifyName' =>$ApproveReplacerName);
      $NotifyName=(object)$NotifyName;
      $job=(new NewCreatedPOJob($NotifyName))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
  }
    return ['redirect'=>route('POListView',[$request->RVNo])];
  }

  public function POListView($id)
  {
    $POList=POMaster::orderBy('PONo','DESC')->where('RVNo', $id)->get(['PONo','Supplier','GeneralManagerSignature','IfDeclined','ApprovalReplacerSignature']);
    return view('Warehouse.PO.POlistView',compact('POList'));
  }
  public function POFullpreview($id)
  {
    $PONumber = array('PONo' =>$id);
    $PONumber=json_encode($PONumber);
    return view('Warehouse.PO.POfullpreview',compact('PONumber'));
  }
  public function POFullPreviewFetch($id)
  {
    $FromPODetail=PODetail::where('PONo',$id)->get(['QtyValidator']);
    $remainingUnreceived=$FromPODetail->sum('QtyValidator');
    $OrderMaster=POMaster::where('PONo', $id)->get();
    $OrderMaster->load('PODetails');
    $totalAmt=PODetail::where('PONo', $id)->get(['Amount'])->sum('Amount');
    $response = array('OrderMaster' =>$OrderMaster ,'remainingUnreceived'=>$remainingUnreceived,'totalAmt'=>$totalAmt);
    return response()->json($response);
  }
  public function GMSignaturePO($id)
  {
    POMaster::where('PONo',$id)->update(['GeneralManagerSignature'=>Auth::user()->Signature,'ApprovalReplacer'=>null,'ApprovalReplacerSignature'=>null]);
  }
  public function GMDeclined($id)
  {
    POMaster::where('PONo',$id)->update(['IfDeclined'=>Auth::user()->FullName,'ApprovalReplacer'=>null,'ApprovalReplacerSignature'=>null]);
    $PODetails=PODetail::where('PONo',$id)->get(['Qty','Description']);
    $RVNo=POMaster::where('PONo',$id)->value('RVNo');
    $FROMRVdetail=RVDetail::where('RVNo',$RVNo)->get(['Particulars']);
    foreach ($PODetails as $podetail)
    {
      foreach ($FROMRVdetail as $canvassvalidator)
      {
        if ($canvassvalidator->Particulars==$podetail->Description)
        {
          RVDetail::where('RVNo',$RVNo)->where('Particulars',$canvassvalidator->Particulars)->update(['QuantityValidator'=>$podetail->Qty]);
        }
      }
    }
  }
  public function MyPOrequestlist()
  {
    if (Auth::user()->Role==2)
    {
      $myPOlist=POMaster::orderBy('PONo','DESC')->where('GeneralManager',Auth::user()->FullName)->whereNull('GeneralManagerSignature')->whereNull('ApprovalReplacerSignature')->where('IfDeclined',null)->paginate(10,['PONo','RVNo','Supplier','Address','Telephone','Purpose','PODate']);
    }elseif(Auth::user()->Role==0)
    {
      $myPOlist=POMaster::orderBy('PONo','DESC')->where('ApprovalReplacer',Auth::user()->FullName)->whereNull('GeneralManagerSignature')->whereNull('ApprovalReplacerSignature')->where('IfDeclined',null)->paginate(10,['PONo','RVNo','Supplier','Address','Telephone','Purpose','PODate']);
    }
    return view('Warehouse.PO.myPOrequest',compact('myPOlist'));
  }
  public function RefuseAuthorizeInBehalf($id)
  {
      POMaster::where('PONo', $id)->update(['ApprovalReplacer'=>null]);
  }
  public function AuthorizeInBehalfconfirmed($id)
  {
    POMaster::where('PONo',$id)->update(['ApprovalReplacerSignature'=>Auth::user()->Signature]);
  }
  public function MyPORequestCount()
  {
    $myPOcount=0;
    if (Auth::user()->Role==2)
    {
      $myPOcount=POMaster::orderBy('PONo','DESC')->where('GeneralManager',Auth::user()->FullName)->whereNull('GeneralManagerSignature')->whereNull('ApprovalReplacerSignature')->where('IfDeclined',null)->count();
    }elseif(Auth::user()->Role==0)
    {
      $myPOcount=POMaster::orderBy('PONo','DESC')->where('ApprovalReplacer',Auth::user()->FullName)->whereNull('GeneralManagerSignature')->whereNull('ApprovalReplacerSignature')->where('IfDeclined',null)->count();
    }
    $response = array('PONotifCount' =>$myPOcount);
    return response()->json($response);
  }
  public function indexPoPage()
  {
    return view('Warehouse.PO.POindex');
  }
  public function fetchAndSearchPOindex(Request $request)
  {
    return POMaster::where('PONo','LIKE','%'.$request->PONo.'%')->orderBy('PONo','DESC')->paginate(10,['PONo','PODate','RVNo','RVDate','Supplier','Purpose','GeneralManager','GeneralManagerSignature','IfDeclined','ApprovalReplacerSignature']);
  }
}
