<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterItem;
use App\MaterialsTicketDetail;
use App\MIRSDetail;
use App\RVDetail;
use Carbon\Carbon;
use DB;
use Session;
use Illuminate\Pagination\Paginator;
class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
     {
      $this->middleware('auth');
     }

    public function index()
    {
        return view('welcome');
    }

    public function FetchAndsearchItemData(Request $request)
    {
      $ItemCode=MasterItem::where('ItemCode',$request->SearchInput)->orWhere('Description',$request->SearchInput)->value('ItemCode');
      if (isset($ItemCode))
      {
        $historiesfound= MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode',$ItemCode)->whereNull('IsRollBack')->paginate(10);
        $latestFound=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode',$ItemCode)->whereNull('IsRollBack')->take(1)->get();
        $latestFound->load('MasterItems');
        $response = array('historiesfound'=>$historiesfound ,'latestFound'=>$latestFound);
      }else
      {
        $response = array('historiesfound'=>'' ,'latestFound'=>'');
      }
      return response()->json($response);

    }
    public function autocompleteSearch(Request $request)
    {
      return MasterItem::where('Description','LIKE','%'.$request->typed.'%')->take(10)->get();
    }
    public function searchItemMaster(Request $request)
    {
       return $itemMasters=MasterItem::where('ItemCode','LIKE','%'.$request->ItemCode.'%')->paginate(5);
    }
    public function ItemMasterSearch(Request $request)
    {
       return $itemMasters=MasterItem::where('Description','LIKE','%'.$request->search.'%')
       ->orWhere('ItemCode','LIKE','%'.$request->search.'%')->paginate(5);
    }

    public function addNonexistinwarehouseItem()
    {
      return view('Warehouse.AddItemToList');
    }
    public function ItemSaveValidator($request)
    {
      $this->validate($request,[
        'AccountCode'=>'required|max:20',
        'ItemCode'=>'required|max:20|unique:MasterItems',
        'CurrentQuantity'=>'required|numeric|min:0',
        'Unit'=>'required|max:10',
        'CurrentCost'=>'required|max:18',
        'Description'=>'required|max:100|unique:MasterItems',
        'AlertIfBelow'=>'required|numeric|min:1',
      ]);
    }
    public function ItemUpdateValidator($request)
    {
      $this->validate($request,[
        'AccountCode'=>'required|max:20',
        'ItemCode'=>'required|max:20',
        'CurrentQuantity'=>'required|numeric|min:0',
        'Unit'=>'required|max:10',
        'CurrentCost'=>'required|max:18',
        'Description'=>'required|max:100',
        'AlertIfBelow'=>'required|numeric|min:1',
      ]);
    }
    public function SaveNewItem(Request $request)
    {
      $this->ItemSaveValidator($request);
      if (($request->CurrentQuantity>0)&&($request->CurrentCost<=0))
      {
        return ['error'=>'The currentcost cannot be 0 if the quantity is not 0'];
      }
      if (($request->CurrentCost>0)&&($request->CurrentQuantity==0))
      {
        return ['error'=>'The currentcost must be 0 if the quantity is 0'];
      }
      $ItemMasterTable=new MasterItem;
      $ItemMasterTable->AccountCode=$request->AccountCode;
      $ItemMasterTable->ItemCode=$request->ItemCode;
      $ItemMasterTable->Description=$request->Description;
      $ItemMasterTable->Unit=$request->Unit;
      $ItemMasterTable->AlertIfBelow=$request->AlertIfBelow;
      $ItemMasterTable->CurrentQuantity=$request->CurrentQuantity;
      $ItemMasterTable->save();
      $nocommaCost=str_replace(',','',$request->CurrentCost);
      $AMT=$nocommaCost*$request->CurrentQuantity;
      $MTDtable=new MaterialsTicketDetail;
      $MTDtable->ItemCode=$request->ItemCode;
      $MTDtable->AccountCode=$request->AccountCode;
      $MTDtable->MTType='RR';
      $MTDtable->MTNo='Init';
      $MTDtable->Quantity=$request->CurrentQuantity;
      $MTDtable->CurrentQuantity=$request->CurrentQuantity;
      $MTDtable->UnitCost=$nocommaCost;
      $MTDtable->Amount=$AMT;
      $MTDtable->CurrentCost=$nocommaCost;
      $MTDtable->CurrentAmount=$AMT;
      $MTDtable->MTDate=Carbon::now();
      $MTDtable->save();
    }
    public function SearchDescriptionAndRecentAdded(Request $request)
    {
      if ($request->Search==null)
      {
        $MasterItem=MaterialsTicketDetail::where('MTNo', 'Init')->with('MasterItems')->orderBy('id','DESC')->paginate(10,['AccountCode','ItemCode','CurrentCost','CurrentQuantity','id']);
      }else
      {
        $MasterItem=MaterialsTicketDetail::where('ItemCode',$request->Search)->with('MasterItems')->where('MTNo', 'Init')->orderBy('id','DESC')->paginate(10,['AccountCode','ItemCode','CurrentCost','CurrentQuantity','id']);
      }
      $response = array('pagination'=>$MasterItem);
       return response()->json($response);
    }
    public function FetchItemToEdit($id)
    {
      $MaterialTD=MaterialsTicketDetail::where('id', $id)->get(['AccountCode','ItemCode','CurrentQuantity','CurrentCost','id']);
      $MaterialTD->load('MasterItems');
      return response()->json([
        'response'=>$MaterialTD,
      ]);
    }
    public function UpdateItem($id,Request $request)
    {
      $this->ItemUpdateValidator($request);
      $ItemCodeUniqueTest=MaterialsTicketDetail::where('ItemCode',$request->ItemCode)->where('MTNo','Init')->where('id','!=',$id)->get(['id']);
      if (!empty($ItemCodeUniqueTest[0]))
      {
        return ['error'=>'Item code has already been taken'];
      }
      if ($request->CurrentQuantity>0&&$request->CurrentCost<=0)
      {
        return ['error'=>'The currentcost cannot be 0 if the quantity is not 0'];
      }
      if ($request->CurrentCost>0&&$request->CurrentQuantity==0)
      {
        return ['error'=>'The currentcost must be 0 if the quantity is 0'];
      }
      $OldItemCode=MaterialsTicketDetail::where('id', $id)->get(['ItemCode']);

       if ($request->ItemCode!=$OldItemCode[0]->ItemCode)
       {
         $updateItemCodeValidatorMIRS = MIRSDetail::where('ItemCode', $OldItemCode[0]->ItemCode)->take(3)->count();
         $updateItemCodeValidatorRV = RVDetail::where('ItemCode', $OldItemCode[0]->ItemCode)->take(3)->count();
         if (($updateItemCodeValidatorMIRS!=0)||($updateItemCodeValidatorRV!=0))
         {
           return ['error'=> 'Not allowed to update item code'];
         }
       }
       $nocommaCost=str_replace(',','',$request->CurrentCost);
       $AMT=$nocommaCost*$request->CurrentQuantity;

       $checkifnew=MaterialsTicketDetail::where('ItemCode',$OldItemCode[0]->ItemCode)->count();
       if ($checkifnew>1)
       {
         $MTdetailsTBL=MaterialsTicketDetail::where('id',$id)->update(['AccountCode'=>$request->AccountCode,'Quantity'=>$request->CurrentQuantity,'UnitCost'=>$nocommaCost,'Amount'=>$AMT,'CurrentQuantity'=>$request->CurrentQuantity,'CurrentCost'=>$nocommaCost]);
         $affectedRows = MaterialsTicketDetail::where('ItemCode',$OldItemCode[0]->ItemCode)->whereNull('IsRollBack')->where('id','>',$id)
         ->chunk(5, function ($affectedRows) use ($OldItemCode)
         {
              foreach ($affectedRows as $affectedrow)
              {
                if ($affectedrow->MTType=='MCT')
                {
                 $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$OldItemCode[0]->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
                 $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $OldItemCode[0]->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
                 $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$OldItemCode[0]->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
                 $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $OldItemCode[0]->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
                 $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $OldItemCode[0]->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
          $CurrentQuantityOfItem=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode', $OldItemCode[0]->ItemCode)->take(1)->value('CurrentQuantity');
          MasterItem::where('ItemCode',$OldItemCode[0]->ItemCode)->update(['CurrentQuantity'=>$CurrentQuantityOfItem,'AccountCode'=>$request->AccountCode,'Description'=>$request->Description,'Unit'=>$request->Unit,'AlertIfBelow'=>$request->AlertIfBelow]);
       }else
       {
         MasterItem::where('ItemCode', $OldItemCode[0]->ItemCode)->update(['AccountCode'=>$request->AccountCode,'ItemCode'=>$request->ItemCode,'CurrentQuantity'=>$request->CurrentQuantity,'Description'=>$request->Description,'Unit'=>$request->Unit,'AlertIfBelow'=>$request->AlertIfBelow]);
         MaterialsTicketDetail::where('id',$id)->update(['AccountCode'=>$request->AccountCode,'Quantity'=>$request->CurrentQuantity,'UnitCost'=>$nocommaCost,'Amount'=>$AMT,'CurrentQuantity'=>$request->CurrentQuantity,'CurrentCost'=>$nocommaCost]);
       }

    }
}
