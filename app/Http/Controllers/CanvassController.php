<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CanvassMaster;
use App\CanvassDetail;
use App\RVDetail;
use App\POMaster;
use App\RVMaster;
use App\RRValidatorNoPO;
class CanvassController extends Controller
{
  public function __construct()
  {
    $this->middleware('IsWarehouse');
  }
  public function TocanvassPage($id)
  {
    $checkifpurchased=RVMaster::where('RVNo',$id)->get(['IfPurchased','RVNo']);
    if ($checkifpurchased[0]->IfPurchased!=null)
    {
      return redirect()->back();
    }
    return view('Warehouse.CanvasCreate',compact('checkifpurchased'));
  }

  public function saveCanvass(Request $request)
  {
     $this->validate($request,[
       'RVNo'=>'required',
       'Supplier'=>'required',
       'Address'=>'required',
       'Telephone'=>'required|numeric|max:99999999999',
       'Particulars.*'=>'required',
       'Price.*'=>'required|numeric|min:0',
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
    $detailsRRValidator=RRValidatorNoPO::where('RVNo', $id)->get(); //i am using rrvalidatorNoPO for this so we can use it validate if the item already have PO.
    $integ =[];
    foreach ($detailsRRValidator as $key => $rvdetail) {
      $integ[] = array('price' =>0);
    }
     $SupplierRecords=CanvassMaster::where('RVNo',$id)->get(['Supplier','id']);
     $supplier= $SupplierRecords->load('CanvassDetail');
     $response=[
       'supplierdata'=>$supplier,
       'rvdata'=>$detailsRRValidator,
       'integ'=>$integ,
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
      'Prices.*'=>'numeric|required|min:0',
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
      $item->Price=$noComma;
      $item->save();
    }
  }
  public function deleteCanvassRecord($id)
  {
    $CanvassMaster=CanvassMaster::find($id);
    $CanvassMaster->delete();
  }
}
