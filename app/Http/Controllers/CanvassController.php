<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CanvassMaster;
use App\CanvassDetail;
use App\RVDetail;
use App\POMaster;
use App\RVMaster;
class CanvassController extends Controller
{
  public function __construct()
  {
    $this->middleware('IsWarehouse');
  }
  public function TocanvassPage($id)
  {
    $checkifpurchased=RVMaster::where('RVNo',$id)->get(['IfPurchased']);
    if ($checkifpurchased[0]->IfPurchased!=null)
    {
      return redirect('/');
    }
    $RVNumber = array('RVNo' =>$id);
    $RVNumber=json_encode($RVNumber);
    return view('Warehouse.CanvasCreate',compact('RVNumber'));
  }

  public function saveCanvass(Request $request)
  {
     $this->validate($request,[
       'RVNo'=>'required',
       'Supplier'=>'required',
       'Address'=>'required',
       'Telephone'=>'required|max:11',
       'Particulars.*'=>'required',
       'Price.*'=>'required',
       'Qty.*'=>'required|max:18',
       'Unit.*'=>'required|max:20',
     ]);
    $CanvassMasterDB = new CanvassMaster;
    $CanvassMasterDB->RVNo = $request->RVNo;
    $CanvassMasterDB->Supplier = $request->Supplier;
    $CanvassMasterDB->Address = $request->Address;
    $CanvassMasterDB->Telephone = $request->Telephone;
    $CanvassMasterDB->save();
    $insertCanvassDetails = array();
    foreach ($request->Particulars as $key => $item)
    {
      $noCommaPrice=str_replace(',','',$request->Price[$key]);
      $insertCanvassDetails[]= array('AccountCode'=>$request->AccountCode[$key],'ItemCode'=>$request->ItemCode[$key],'Article' => $item, 'Price'=>$noCommaPrice,'Unit'=>$request->Unit[$key],'Qty'=> $request->Qty[$key],'CanvassMasters_id'=>$CanvassMasterDB->id);
    }
    CanvassDetail::insert($insertCanvassDetails);
  }


  public function getSupplierRecords($id)
  {
    $FromRVDetail=RVDetail::where('RVNo', $id)->get();
     $SupplierRecords=CanvassMaster::where('RVNo',$id)->get(['Supplier','id']);
     $supplier= $SupplierRecords->load('CanvassDetail');
     $response=[
       'supplierdata'=>$supplier,
       'rvdata'=>$FromRVDetail,
     ];
     return response()->json($response);
  }
  public function searchSupplier(Request $request,$id)
  {
     $canvassMaster=CanvassMaster::where('RVNo',$id)->where('id',$request->canvassID)->get();
     return $canvassMaster->load('CanvassDetail');
  }
  public function canvassUpdate(Request $request,$id)
  {
    $this->validate($request,[
      'Supplier'=>'required|max:50',
      'Address'=>'required',
      'Telephone'=>'required|max:11',
      'Prices.*'=>'required',
    ]);
    $canvassMasterDB=CanvassMaster::find($id);
    $canvassMasterDB->Supplier=$request->Supplier;
    $canvassMasterDB->Address=$request->Address;
    $canvassMasterDB->Telephone=$request->Telephone;
    $canvassMasterDB->save();

    $itemdetails=$canvassMasterDB->CanvassDetail;
    foreach ($itemdetails as $key=>$item)
    {
      $noComma=str_replace(',','',$request->Prices[$key]);
      $item->Price=number_format($noComma,2);
      $item->save();
    }
  }
  public function deleteCanvassRecord($id)
  {
    $CanvassMaster=CanvassMaster::find($id);
    $CanvassMaster->delete();
  }
}
