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

      $this->validate($request,[
        'quantity'=>'required|numeric|max:'.$latest[0]->CurrentQuantity.'|min:1',
      ]);

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
      $MTDetailTble = new MaterialsTicketDetail;
      $MTDetailTble->MTType = 'DMG';
      $MTDetailTble->MTNo = 'DMG';
      $MTDetailTble->accountcode = $latest[0]->accountcode;
      $MTDetailTble->ItemCode =$latest[0]->ItemCode;
      $MTDetailTble->UnitCost =$latestUnitCost;
      $MTDetailTble->Quantity = $request->quantity;
      $MTDetailTble->Amount =$amt;
      $MTDetailTble->CurrentCost=$newCurrentCost;
      $MTDetailTble->CurrentQuantity=$newQty;
      $MTDetailTble->CurrentAmount=$newAMT;
      $MTDetailTble->MTDate = Carbon::now();
      $MTDetailTble->save();
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
             if ($affectedrow->MTType=='MRT')
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
             if ($affectedrow->MTType=='RR')
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
           }
       });
       $CurrentQuantityOfItem=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode', $itemcode)->take(1)->value('CurrentQuantity');
       MasterItem::where('ItemCode',$itemcode)->update(['CurrentQuantity' => $CurrentQuantityOfItem]);
    }
}
