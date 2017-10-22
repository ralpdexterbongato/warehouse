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
use App\MRTConfirmationDetail;
use App\MCTConfirmationDetail;
use App\MasterItem;
use App\Jobs\NewCreatedMRTJob;
class MRTController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    public function CreateMRT($id)
    {
      $MCTdata=MCTMaster::where('MCTNo',$id)->get(['Particulars','AddressTo','ReceivedbyPosition','Receivedby']);
      $MCTNumber = array('MCTNo' =>$id);
      $MCTNumber=json_encode($MCTNumber);
      return view('Warehouse.MRT.MRTCreate',compact('MCTNumber','MCTdata'));
    }
    public function CreateMRTFetchMCTdata($id)
    {
      $MTDetails=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $id)->paginate(5);
      $MTDetails->load('MasterItems');
      return response()->json(['MTDetails'=>$MTDetails]);
    }
    public function DisplaySessionMRT()
    {
      $SelectedSession=Session::get('MCTSelected');
      return response()->json(['SelectedSession'=>$SelectedSession]);
    }
    public function summaryMRT()
    {
      return view('Warehouse.MRT.MRT-summary');
    }

    public function StoreMRT(Request $request,$id)
    {
        if (empty(Session::get('MCTSelected')))
        {
          return ['error'=>'Selecting item is required'];
        }
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
        $MCTMaster=MCTMaster::where('MCTNo',$id)->get(['Receivedby','ReceivedbyPosition','MIRSNo']);
        $mrtDB=new MRTMaster;
        $mrtDB->MRTNo=$MRTincremented;
        $mrtDB->MCTNo =$id;
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

        $forMRTConfirmation = array();
        foreach (Session::get('MCTSelected') as $MRTitem)
        {
          $pricefromitsMCT=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode',$MRTitem->ItemCode)->where('MTType', 'MCT')->where('MTNo', $id)->take(1)->get(['UnitCost','AccountCode']);
          $amount=$MRTitem->Summary*$pricefromitsMCT[0]->UnitCost;
          $forMRTConfirmation[] = array('ItemCode' =>$MRTitem->ItemCode,'AccountCode'=>$pricefromitsMCT[0]->AccountCode,'MRTNo'=>$MRTincremented,'Unit'=>$MRTitem->Unit,'Description'=>$MRTitem->Description,'UnitCost' =>$pricefromitsMCT[0]->UnitCost,'Quantity' =>$MRTitem->Summary
          ,'Amount' =>$amount);
        }
        MRTConfirmationDetail::insert($forMRTConfirmation);
        Session::forget('MCTSelected');
        $nospacename=str_replace(' ','',$MCTMaster[0]->Receivedby);
        $notifythis = array('tobeNotify'=>$nospacename);
        $notifythis=(object)$notifythis;
        $job = (new NewCreatedMRTJob($notifythis))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
        return ['redirect'=>route('MRTpreviewPage',$MRTincremented)];
    }
    public function addToSession(Request $request)
    {
      $this->validate($request,[
        'Summary'=>'required|numeric|min:1|max:'.$request->Limit,
      ]);
      if (Session::has('MCTSelected'))
      {
        foreach (Session::get('MCTSelected') as $Alreadyhere)
        {
          if ($Alreadyhere->ItemCode === $request->ItemCode)
          {
            return ['error'=>'Sorry duplicate is not allowed'];
          }
        }
      }
      $MRTselected = array('ItemCode'=>$request->ItemCode ,'Description'=>$request->Description,'Unit'=>$request->Unit,'Summary'=>$request->Summary );
      $MRTselected = (object)$MRTselected;
      Session::push('MCTSelected',$MRTselected);
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
      }
    }

    public function MRTSearchdate(Request $request)
    {
      $this->datesearchValidator($request);
      $datesearch=$request->monthInput;
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode')->get();
      if (!empty($itemsummary[0]))
      {
        $detailMTNum =MaterialsTicketDetail::orderBy('id','DESC')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['MTNo']);
        $mrtmaster=MRTMaster::where('MRTNo',$detailMTNum[0]->MTNo)->get(['Receivedby','ReturnDate','ReceivedbyPosition']);
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
    public function toMRTpreviewPage($id)
    {
      $MRTNo=MRTMaster::where('MRTNo', $id)->get(['MRTNo']);
      return view('Warehouse.MRT.MRTfullPreview',compact('MRTNo'));
    }
    public function mrtviewing($id)
    {
      $mrtMaster=MRTMaster::where('MRTNo',$id)->get();
      $MRTConfirmationItems=MRTConfirmationDetail::where('MRTNo',$id)->get();
      $MRTbyAcntCode=MRTConfirmationDetail::orderBy('AccountCode')->where('MRTNo',$id)->groupBy('AccountCode')->selectRaw('sum(Amount) as totalAMT,AccountCode as AccountCode')->get();
      $totalsum=0;
      foreach ($MRTbyAcntCode as $AcntCode)
      {
        $totalsum= $totalsum + $AcntCode->totalAMT;
      }
      $response = array('MRTMaster' =>$mrtMaster ,'MRTbyAcntCode'=>$MRTbyAcntCode,'MRTConfirmationItems'=>$MRTConfirmationItems,'totalsum'=>$totalsum );
      return response()->json($response);
    }
    public function signatureMRT($id)
    {
      $datenow=MRTMaster::where('MRTNo',$id)->get(['ReturnDate']);
      $FromConfirmation=MRTConfirmationDetail::where('MRTNo',$id)->get();
      $forMRTtbl = array();
      foreach ($FromConfirmation as $fromConfirm)
      {
        $MTdetails=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $fromConfirm->ItemCode)->take(1)->get(['CurrentQuantity','CurrentAmount']);
        $MRTammount=$fromConfirm->Quantity* $fromConfirm->UnitCost;
        $currentQty=$fromConfirm->Quantity + $MTdetails[0]->CurrentQuantity;

        $totalofamt=$MTdetails[0]->CurrentAmount+ $MRTammount;
        $newcurrentcost=$totalofamt/$currentQty;
        $currentAmnt= $currentQty * $newcurrentcost;
        MasterItem::where('ItemCode',$fromConfirm->ItemCode)->update(['CurrentQuantity'=>$currentQty]);
        $forMRTtbl[] = array('ItemCode' =>$fromConfirm->ItemCode,'MTType'=>'MRT','MTNo' =>$fromConfirm->MRTNo ,'AccountCode' =>$fromConfirm->AccountCode,'UnitCost' =>$fromConfirm->UnitCost ,'Quantity' =>$fromConfirm->Quantity
        ,'Amount' =>$MRTammount ,'CurrentCost' =>$newcurrentcost ,'CurrentQuantity' =>$currentQty ,'CurrentAmount' =>$currentAmnt ,'MTDate' =>$datenow[0]->ReturnDate );
      }
      MaterialsTicketDetail::insert($forMRTtbl);
      MRTMaster::where('MRTNo',$id)->update(['ReturnedbySignature'=>Auth::user()->Signature]);
    }
    public function DeclineMRT($id)
    {
      MRTMaster::where('MRTNo',$id)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
    }
    public function updateQuantityMRT($id,Request $request)
    {
      $MRTConfirmdetail=MRTConfirmationDetail::where('MRTNo',$id)->get(['ItemCode','Quantity','UnitCost']);
      $MRTMaster=MRTMaster::where('MRTNo',$id)->get(['MCTNo']);
      foreach ($MRTConfirmdetail as $count=> $mrtconfirm)
      {
        $MCTitemQty=MCTConfirmationDetail::where('MCTNo',$MRTMaster[0]->MCTNo)->where('ItemCode',$mrtconfirm->ItemCode)->get(['Quantity']);
        if ($request->UpdatedQty[$count]>$MCTitemQty[0]->Quantity)
        {
          return ['error'=>$mrtconfirm->ItemCode.' quantity may not be greater than'.$MCTitemQty[0]->Quantity];
        }
      }
      foreach ($MRTConfirmdetail as $key=> $mrtconfirm)
      {
        $newAMT=$mrtconfirm->UnitCost*$request->UpdatedQty[$key];
        MRTConfirmationDetail::where('MRTNo',$id)->where('ItemCode',$mrtconfirm->ItemCode)->update(['Quantity'=>$request->UpdatedQty[$key],'Amount'=>$newAMT]);
      }
    }
    public function myMRTSignatureRequest()
    {
      return view('Warehouse.MRT.myMRTSignatureRequest');
    }
    public function myMRTSignatureFetchData()
    {
      return MRTMaster::orderBy('id','DESC')
      ->where('Returnedby',Auth::user()->Fname.' '.Auth::user()->Lname)
      ->whereNull('IfDeclined')
      ->whereNull('ReturnedbySignature')
      ->paginate(10,['MRTNo','ReturnDate','Particulars','AddressTo','Returnedby','Receivedby','Remarks']);
    }
    public function MRTSignatureRequestCount()
    {
      $NumberofRequest= MRTMaster::orderBy('id','DESC')->where('Returnedby',Auth::user()->Fname.' '.Auth::user()->Lname)->whereNull('IfDeclined')->whereNull('ReturnedbySignature')->count();
      $response = array('MRTRequestCount' => $NumberofRequest);
      return response()->json($response);
    }
    public function MRTindexPage()
    {
      return view('Warehouse.MRT.MRTindex');
    }
    public function MRTindexSearch(Request $request)
    {
      return MRTMaster::orderBy('id','DESC')->where('MRTNo','LIKE','%'.$request->MRTNo.'%')->paginate(10,['MRTNo','MCTNo','ReturnDate','Particulars','AddressTo','Receivedby','Returnedby','ReturnedbySignature','IfDeclined']);
    }
}
