<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MaterialsTicketDetail;
use App\MasterItem;
use Carbon\Carbon;
class DamagedItemController extends Controller
{
    public function __construct()
    {
     $this->middleware('auth');
    }
    public function store(Request $request, $itemcode)
    {
      //quantity param
      $latest=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $itemcode)->take(1)->get();
      $this->handleStoreValidation($latest,$request);
      $results = $this->handleComputation($itemcode,$request,$latest);
      $results = (object)$results;
      $MTDetailTble = new MaterialsTicketDetail;
      $MTDetailTble->MTType = 'DMG';
      $MTDetailTble->MTNo = 'DMG';
      $MTDetailTble->AccountCode = $latest[0]->AccountCode;
      $MTDetailTble->ItemCode =$latest[0]->ItemCode;
      $MTDetailTble->UnitCost =$results->latestUnitCost;
      $MTDetailTble->Quantity = $request->quantity;
      $MTDetailTble->Amount = $results->amt;
      $MTDetailTble->CurrentCost=$results->newCurrentCost;
      $MTDetailTble->CurrentQuantity=$results->newQty;
      $MTDetailTble->CurrentAmount=$results->newAMT;
      $MTDetailTble->MTDate = Carbon::now();
      $MTDetailTble->save();
      $this->handleUpdateMasterQty($latest,$results->newQty);
    }
    public function handleComputation($itemcode,$request,$latest)
    {
      $latestUnitCost=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $itemcode)->where('MTType', 'RR')->whereNull('IsRollBack')->take(1)->value('UnitCost');
      $amt = $request->quantity * $latestUnitCost;
      $newQty=$latest[0]->CurrentQuantity - $request->quantity;
      $newAMT = $latest[0]->CurrentAmount - $amt;
      if ($newQty==0)
      {
        $newCurrentCost = 0;
      }else
      {
        $newCurrentCost = $newAMT / $newQty;
      }
      return $result = array('latestUnitCost' =>$latestUnitCost ,'amt'=>$amt,'newQty'=>$newQty,'newAMT'=>$newAMT,'newCurrentCost'=>$newCurrentCost);
    }
    public function handleStoreValidation($latest,$request)
    {
      $this->validate($request,[
        'quantity'=>'required|numeric|max:'.$latest[0]->CurrentQuantity.'|min:1',
      ]);
    }
    public function handleUpdateMasterQty($latest,$newQty)
    {
      MasterItem::where('ItemCode',$latest[0]->ItemCode)->update(['CurrentQuantity'=>$newQty]);
    }

    public function delete($id,$itemcode)
    {
      MaterialsTicketDetail::where('id', $id)->delete();
      $affectedRows = MaterialsTicketDetail::where('ItemCode',$itemcode)->whereNull('IsRollBack')->where('id','>',$id)
      ->chunk(5, function ($affectedRows) use ($itemcode)
      {
           foreach ($affectedRows as $affectedrow)
           {
             if ($affectedrow->MTType=='MCT' || $affectedrow->MTType=='DMG')
             {
               $this->handleMCTandDMG($affectedrow,$itemcode);
             }
             if ($affectedrow->MTType=='MRT')
             {
               $this->handleMRT($affectedrow,$itemcode);
             }
             if ($affectedrow->MTType=='RR')
             {
               $this->handleRR($affectedrow,$itemcode);
             }
           }
       });
       $CurrentQuantityOfItem=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode', $itemcode)->take(1)->value('CurrentQuantity');
       MasterItem::where('ItemCode',$itemcode)->update(['CurrentQuantity' => $CurrentQuantityOfItem]);
    }

    // delete function handlers start
    public function handleMCTandDMG($affectedrow,$itemcode)
    {
      $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$itemcode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
      $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $itemcode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
      $newAmt = $affectedrow->Quantity * $uCostLatestRR;
      $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity - $affectedrow->Quantity;
      $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount - $newAmt;
      if ($newCurrentQty!=0)
      {
        $newCurrentCost= $newCurrentAmount / $newCurrentQty;
      }else
      {
        $newCurrentCost = 0;
      }
      $affectedrow->update(['UnitCost'=>$uCostLatestRR,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
    }
    public function handleMRT($affectedrow,$itemcode)
    {
      $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$itemcode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
      $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $itemcode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
      $newAmt = $affectedrow->Quantity * $uCostLatestRR;
      $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity + $affectedrow->Quantity;
      $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount + $newAmt;
      if ($newCurrentQty!=0)
      {
        $newCurrentCost= $newCurrentAmount / $newCurrentQty;
      }else
      {
        $newCurrentCost = 0;
      }
      $affectedrow->update(['UnitCost'=>$uCostLatestRR,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
    }
    public function handleRR($affectedrow,$itemcode)
    {
      $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $itemcode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
      $newAmt = $affectedrow->Quantity * $affectedrow->UnitCost;
      $newCurrentQty = $dataBelowTheRow[0]->CurrentQuantity + $affectedrow->Quantity;
      $newCurrentAmount= $dataBelowTheRow[0]->CurrentAmount + $newAmt;
      if ($newCurrentQty!=0)
      {
        $newCurrentCost= $newCurrentAmount / $newCurrentQty;
      }else
      {
        $newCurrentCost = 0;
      }
      $affectedrow->update(['UnitCost'=>$affectedrow->UnitCost,'Amount'=>$newAmt,'CurrentQuantity'=>$newCurrentQty,'CurrentAmount'=>$newCurrentAmount,'CurrentCost'=>$newCurrentCost]);
    }
    // delete function handlers end
}
