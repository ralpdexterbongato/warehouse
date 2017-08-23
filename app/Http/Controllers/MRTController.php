<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MRTMaster;
use App\MCTMaster;
use Carbon\Carbon;
use App\MaterialsTicketDetail;
use Session;
use DB;
use Auth;
use App\MCTValidator;
class MRTController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    public function CreateMRT(Request $request)
    {
      $this->validate($request,[
        'MCTNo' => 'unique:MRTMaster',
      ]);
      $MTDetails=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $request->MCTNo)->get();
      $MCTdata=MCTMaster::where('MCTNo',$request->MCTNo)->get(['Particulars','AddressTo','ReceivedbyPosition','Receivedby']);
      return view('Warehouse.MRT.MRTformView',compact('MTDetails','MCTdata'));
    }
    public function summaryMRT()
    {
      return view('Warehouse.MRT.MRT-summary');
    }

    public function StoreMRT(Request $request)
    {
      $this->MRTValidator($request);
      if (!empty(Session::get('MCTSelected')))
      {
        $year=Carbon::now()->format('y');
        $datenow=Carbon::now();
        $MRTNum=MRTMaster::orderBy('id','DESC')->take(1)->value('MRTNo');
        if (count($MRTNum)>0)
        {
          $numOnly=substr($MRTNum,'3');
          $numOnly = (int)$numOnly;
          $newID=$numOnly + 1;
          $MRTincremented= $year . '-' . sprintf("%04d",$newID);
        }else
        {
         $MRTincremented=  $year . '-' . sprintf("%04d",'1');
        }
        $MCTMaster=MCTMaster::where('MCTNo',$request->MCTNo)->get(['Receivedby','ReceivedbyPosition','MIRSNo']);
        $mrtDB=new MRTMaster;
        $mrtDB->MRTNo=$MRTincremented;
        $mrtDB->MCTNo =$request->MCTNo;
        $mrtDB->ReturnDate =$datenow;
        $mrtDB->Particulars =$request->Particulars;
        $mrtDB->AddressTo= $request->AddressTo;
        $mrtDB->Returnedby = $MCTMaster[0]->Receivedby ;
        $mrtDB->ReturnedbyPosition =$MCTMaster[0]->ReceivedbyPosition ;
        $mrtDB->Receivedby = Auth::user()->Fname.' '.Auth::user()->Lname;
        $mrtDB->ReceivedbySignature=Auth::user()->Signature;
        $mrtDB->ReceivedbyPosition=Auth::user()->Position;
        $mrtDB->Remarks = $request->Remarks;
        $mrtDB->save();

        $forMRTtbl = array();
        foreach (Session::get('MCTSelected') as $MRTitem)
        {
          $validatorItemQTY=MCTValidator::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$MRTitem->ItemCode)->get(['Quantity']);
          $qtyValidatorleft=$validatorItemQTY[0]->Quantity + $MRTitem->Summary;
          MCTValidator::where('MIRSNo',$MCTMaster[0]->MIRSNo)->where('ItemCode',$MRTitem->ItemCode)->Update(['Quantity'=>$qtyValidatorleft]);

          $MTdetails=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('ItemCode', $MRTitem->ItemCode)->take(1)->get();
          $pricefromitsMCT=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('MTType', 'MCT')->where('MTNo', $request->MCTNo)->take(1)->get(['UnitCost']);
          $qty=(float)$MRTitem->Summary;
          $MRTammount=$qty * $pricefromitsMCT[0]->UnitCost;
          $currentQty=$qty + $MTdetails[0]->CurrentQuantity;

          $totalofamt=$MTdetails[0]->CurrentAmount+ $MRTammount;
          $newcurrentcost=$totalofamt/$currentQty;
          $currentAmnt= $currentQty * $newcurrentcost;
          $forMRTtbl[] = array('ItemCode' =>$MRTitem->ItemCode,'MTType'=>'MRT','MTNo' =>$MRTincremented ,'AccountCode' =>$MTdetails[0]->AccountCode ,'UnitCost' =>$pricefromitsMCT[0]->UnitCost ,'Quantity' =>$MRTitem->Summary
          ,'Unit' =>$MTdetails[0]->Unit ,'Amount' =>$MRTammount ,'CurrentCost' =>$newcurrentcost ,'CurrentQuantity' =>$currentQty ,'CurrentAmount' =>$currentAmnt ,'MTDate' =>$datenow );

        }
        MaterialsTicketDetail::insert($forMRTtbl);
        Session::forget('MCTSelected');
        return redirect('/mrt-viewer/'.$MRTincremented);
      }else
      {
        return redirect()->back()->with('message', 'item is required');
      }
    }
    public function addToSession(Request $request)
    {
      if (Session::has('MCTSelected'))
      {
        foreach (Session::get('MCTSelected') as $Alreadyhere)
        {
          if ($Alreadyhere->ItemCode === $request->ItemCode)
          {
            return redirect()->back()->with('message', 'Sorry this item is already added');
          }
        }
      }
      $MRTselected = array('ItemCode'=>$request->ItemCode ,'Description'=>$request->Description,'Unit'=>$request->Unit,'Summary'=>$request->Summary );
      $MRTselected = (object)$MRTselected;
      Session::push('MCTSelected',$MRTselected);
      return redirect()->back();
    }
    public function deletePartSession($id)
    {
      if(Session::has('MCTSelected'))
      {
        $items=(array)Session::get('MCTSelected');
        foreach ($items as $key=>$item)
        {
          if ($item->ItemCode == $id)
          {
            unset($items[$key]);
          }
        }
        Session::put('MCTSelected',$items);
        return redirect()->back();
      }
    }
    public function MRTValidator($request)
    {
      return $this->validate($request,[
       'MCTNo'=>'required|unique:MRTMaster',
      ]);
    }
    public function MRTSearchdate(Request $request)
    {
      $this->datesearchValidator($request);
      $datesearch=$request->monthInput;
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode','Unit')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode , Unit as Unit ')->get();
      if (!empty($itemsummary[0]))
      {
        $detailMTNum =MaterialsTicketDetail::orderBy('MTDate','DESC')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['MTNo']);
        $mrtmaster=MRTMaster::where('MRTNo',$detailMTNum[0]->MTNo)->get(['Receivedby','ReturnDate']);
        return view('Warehouse.MRT.MRT-summary',compact('itemsummary','mrtmaster'));
      }else
      {
        return redirect('/summary-mrt');
      }
    }
    public function datesearchValidator($request)
    {
      return $this->validate($request,[
        'monthInput'=> 'required|min:7|max:7',
      ]);
    }

    public function mrtviewing($id)
    {
      $mrtMaster=MRTMaster::where('MRTNo',$id)->get();
      $MTDitems=MaterialsTicketDetail::where('MTType', 'MRT')->where('MTNo',$id)->get();
      $MRTbyAcntCode=MaterialsTicketDetail::orderBy('AccountCode')->where('MTType','MRT')->where('MTNo',$id)->groupBy('AccountCode')->selectRaw('sum(Amount) as totalAMT,AccountCode as AccountCode')->get();
      $totalsum=0;
      foreach ($MRTbyAcntCode as $AcntCode)
      {
        $totalsum= $totalsum + $AcntCode->totalAMT;
      }
      return view('Warehouse.MRT.MRTfullPreview',compact('mrtMaster','MRTbyAcntCode','MTDitems','totalsum'));
    }
}
