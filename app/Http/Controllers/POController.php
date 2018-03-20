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
use App\Jobs\POApprovalReplacer;
use App\RVDetail;
use App\Signatureable;
use App\Jobs\GlobalNotifJob;
class POController extends Controller
{
    public function GeneratePOfromCanvass(Request $request)
    {
    $date=Carbon::now();
    $year=Carbon::now()->format('y');
    $GM=User::orderBy('id','DESC')->whereNotNull('IsActive')->where('Role','2')->take(1)->get(['id']);
    $RVMasterDB=RVMaster::where('RVNo',$request->RVNo)->get(['RVDate','Purpose']);
    $collected=collect($request->SupplierChoice);
    $SupplierGrouped=$collected->unique();
    $POid=POMaster::orderBy('PONo','DESC')->take(1)->value('PONo');
    $incremented='';
    $ApprovalReplacer=User::whereNotNull('IfApproveReplacer')->get(['id']);
    $toDBMaster = array();
    $toDBSignatures = array();
    $toDBDetails = array();
    $explodedPOid = explode('-',$POid);
    foreach ($SupplierGrouped as $key => $SupplierG)
    {
    if ($SupplierG!=null)
    {
      if (($POid!=null)&&($incremented==null)&&($explodedPOid[0] == $year))
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
        'Telephone'=>$CanvasMaster[0]->Telephone,'Purpose'=>$RVMasterDB[0]->Purpose,
        'RVDate'=>$RVMasterDB[0]->RVDate,'PODate'=>$date,'CreatorID'=>Auth::user()->id);
        $toDBSignatures[] = array('user_id' =>$GM[0]->id,'signatureable_id'=>$incremented,'signatureable_type' =>'App\POMaster','SignatureType'=>'ApprovedBy');
        $toDBSignatures[] = array('user_id' =>$ApprovalReplacer[0]->id,'signatureable_id'=>$incremented,'signatureable_type' =>'App\POMaster','SignatureType'=>'ApprovalReplacer');
      }else
      {
        $toDBMaster[]=array('PONo'=>$incremented,'RVNo' => $CanvasMaster[0]->RVNo,
        'Supplier' =>$CanvasMaster[0]->Supplier ,'Address'=>$CanvasMaster[0]->Address,
        'Telephone'=>$CanvasMaster[0]->Telephone,'Purpose'=>$RVMasterDB[0]->Purpose,
        'RVDate'=>$RVMasterDB[0]->RVDate,'PODate'=>$date,'CreatorID'=>Auth::user()->id);
        $toDBSignatures[] = array('user_id' =>$GM[0]->id,'signatureable_id'=>$incremented,'signatureable_type' =>'App\POMaster','SignatureType'=>'ApprovedBy');
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
    Signatureable::insert($toDBSignatures);
    PODetail::insert($toDBDetails);
    $NotifyId = array('NotifyId' =>$GM[0]->id);
    $NotifyId=(object)$NotifyId;
    $job=(new NewCreatedPOJob($NotifyId))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
    if (!empty($ApprovalReplacer[0]))
    {
      $NotifyId = array('NotifyId' =>$ApprovalReplacer[0]->id);
      $NotifyId=(object)$NotifyId;
      $job=(new NewCreatedPOJob($NotifyId))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
  }
    return ['redirect'=>route('POListView',[$request->RVNo])];
  }

  public function POListView($id)
  {
    $POList=POMaster::orderBy('PONo','DESC')->where('RVNo', $id)->get(['PONo','Supplier','Status']);
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
    $OrderMaster=POMaster::with('users')->where('PONo', $id)->get();
    $OrderMaster->load('PODetails');
    $totalAmt=PODetail::where('PONo', $id)->get(['Amount'])->sum('Amount');
    $response = array('OrderMaster' =>$OrderMaster ,'remainingUnreceived'=>$remainingUnreceived,'totalAmt'=>$totalAmt);
    return response()->json($response);
  }
  public function GMSignaturePO($id)
  {
    POMaster::where('PONo',$id)->update(['Status'=>'0']);
    Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id', $id)->where('signatureable_type','App\POMaster')->where('SignatureType','ApprovedBy')->update(['Signature'=>'0']);
    Signatureable::where('signatureable_id', $id)->where('signatureable_type','App\POMaster')->where('SignatureType','ApprovalReplacer')->delete();
    // notify warehouseman the creator
    $POMaster=POMaster::where('PONo',$id)->get(['CreatorID']);
    $ReceiverID = array('id' =>$POMaster[0]->CreatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function GMDeclined($id)
  {
    POMaster::where('PONo',$id)->update(['Status'=>'1']);
    Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id', $id)->where('signatureable_type','App\POMaster')->where('SignatureType','ApprovedBy')->update(['Signature'=>'1']);
    Signatureable::where('signatureable_id', $id)->where('signatureable_type','App\POMaster')->where('SignatureType','ApprovalReplacer')->delete();
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
    // notify warehouseman the creator
    $POMaster=POMaster::where('PONo',$id)->get(['CreatorID']);
    $ReceiverID = array('id' =>$POMaster[0]->CreatorID);
    $ReceiverID = (object)$ReceiverID;
    $job = (new GlobalNotifJob($ReceiverID))
    ->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function MyPOrequestlist()
  {
    $myPOlist=Auth::user()->POSignatureTurn()->paginate(10);
    return view('Warehouse.PO.myPOrequest',compact('myPOlist'));
  }
  public function RefuseAuthorizeInBehalf($id)
  {
    Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id', $id)->where('signatureable_type', 'App\POMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
  }
  public function AuthorizeInBehalfconfirmed($id)
  {
    POMaster::where('PONo',$id)->update(['Status'=>'0']);
    Signatureable::where('user_id', Auth::user()->id)->where('signatureable_id', $id)->where('signatureable_type', 'App\POMaster')->where('SignatureType', 'ApprovalReplacer')->update(['Signature'=>'0']);

    //smsAlert
    $GMId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\POMaster')->where('SignatureType', 'ApprovedBy')->value('user_id');
    $GMMobile=User::where('id', $GMId)->value('Mobile');
    $data = array('Mobile' =>$GMMobile, 'PONo'=>$id,'Replacer'=>Auth::user()->FullName);
    $data=(object)$data;
    $job = (new POApprovalReplacer($data))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function MyPORequestCount()
  {
    $myPOcount=Auth::user()->POSignatureTurn()->count();
    $response = array('PONotifCount' =>$myPOcount);
    return response()->json($response);
  }
  public function indexPoPage()
  {
    return view('Warehouse.PO.POindex');
  }
  public function fetchAndSearchPOindex(Request $request)
  {
    return POMaster::with('users')->where('PONo','LIKE','%'.$request->PONo.'%')->orderBy('PONo','DESC')->paginate(10,['PONo','PODate','RVNo','RVDate','Supplier','Purpose','Status']);
  }
}
