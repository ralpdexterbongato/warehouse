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
use App\MRTConfirmationDetail;
use App\MCTConfirmationDetail;
use App\MasterItem;
use App\Jobs\NewCreatedMRTJob;
use App\User;
use App\Signatureable;
use App\Notification;
use App\Jobs\GlobalNotifJob;
class MRTController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    public function CreateMRT($id)
    {
      Session::forget('MCTSelected');
      $MCTdata=MCTMaster::with('ReceiverMCT')->where('MCTNo',$id)->get(['Particulars','AddressTo','MCTNo']);
      $MCTNumber = array('MCTNo' =>$id);
      $MCTNumber=json_encode($MCTNumber);
      return view('Warehouse.MRT.MRTCreate',compact('MCTNumber','MCTdata'));
    }
    public function CreateMRTFetchMCTdata($id)
    {
      $MTDetails=MCTConfirmationDetail::where('MCTNo', $id)->paginate(5);
      return response()->json(['MTDetails'=>$MTDetails]);
    }
    public function DisplaySessionMRT()
    {
      $SelectedSession=Session::get('MCTSelected');
      if (isset($SelectedSession))
      {
        $SelectedSession=array_reverse($SelectedSession);
      }
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
        $MRTNum=MRTMaster::orderBy('MRTNo','DESC')->take(1)->value('MRTNo');
        $explodedMRTNum = explode('-',$MRTNum);
        if (count($MRTNum)>0 && $explodedMRTNum[0]==$year)
        {
          $numOnly=substr($MRTNum,'3');
          $numOnly = (int)$numOnly;
          $newID=$numOnly + 1;
          $MRTincremented= $year . '-' . sprintf("%04d",$newID);
        }else
        {
         $MRTincremented=  $year . '-' . sprintf("%04d",'1');
        }
        $ReturnerID=Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MCTMaster')->where('SignatureType', 'ReceivedBy')->get(['user_id']);
        $mrtDB=new MRTMaster;
        $mrtDB->MRTNo=$MRTincremented;
        $mrtDB->MCTNo =$id;
        $mrtDB->ReturnDate =$datenow;
        $mrtDB->Particulars =$request->Particulars;
        $mrtDB->AddressTo= $request->AddressTo;
        $mrtDB->Remarks = $request->Remarks;
        $mrtDB->CreatorID = Auth::user()->id;
        $mrtDB->save();

        $forMRTSignatureTbl = array(
           array('user_id' =>Auth::user()->id, 'Signatureable_id'=>$MRTincremented, 'signatureable_type'=>'App\MRTMaster', 'SignatureType'=>'ReceivedBy'),
           array('user_id' =>$ReturnerID[0]->user_id, 'Signatureable_id'=>$MRTincremented, 'signatureable_type'=>'App\MRTMaster', 'SignatureType'=>'ReturnedBy')
        );
        $forMRTConfirmation = array();
        foreach (Session::get('MCTSelected') as $MRTitem)
        {
          $pricefromitsMCT=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode',$MRTitem->ItemCode)->where('MTType', 'MCT')->where('MTNo', $id)->take(1)->get(['UnitCost','AccountCode']);
          $amount=$MRTitem->Summary*$pricefromitsMCT[0]->UnitCost;
          $forMRTConfirmation[] = array('ItemCode' =>$MRTitem->ItemCode,'AccountCode'=>$pricefromitsMCT[0]->AccountCode,'MRTNo'=>$MRTincremented,'Unit'=>$MRTitem->Unit,'Description'=>$MRTitem->Description,'UnitCost' =>$pricefromitsMCT[0]->UnitCost,'Quantity' =>$MRTitem->Summary
          ,'Amount' =>$amount);
        }
        Signatureable::insert($forMRTSignatureTbl);
        MRTConfirmationDetail::insert($forMRTConfirmation);
        Session::forget('MCTSelected');
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
            return ['error'=>'Item is already added'];
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
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->where('MTType','MRT')->whereNull('IsRollBack')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode')->get();
      if (!empty($itemsummary[0]))
      {
        $MaterialDate =MaterialsTicketDetail::orderBy('id','DESC')->where('MTType','MRT')->whereNull('IsRollBack')->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->value('MTDate');
        $WarehouseMan=User::where('isActive', '0')->where('Role', '4')->orderBy('id','DESC')->take(1)->get(['FullName','Position','Signature']);
        return view('Warehouse.MRT.MRT-summary',compact('itemsummary','MaterialDate','WarehouseMan'));
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
      $MRTNo = array('MRTNo' => $id );
      $MRTNo=json_encode($MRTNo);
      return view('Warehouse.MRT.MRTfullPreview',compact('MRTNo'));
    }
    public function mrtviewing($id)
    {
      $mrtMaster=MRTMaster::with('users')->where('MRTNo',$id)->get();
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
      $SignatureTurn=MRTMaster::where('MRTNo', $id)->value('SignatureTurn');
      //receiver \/
      $ReceiverID=Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MRTMaster')->where('SignatureType', 'ReceivedBy')->get(['user_id']);
      //returner \/
      $ReturnerID=Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MRTMaster')->where('SignatureType', 'ReturnedBy')->get(['user_id']);
      if ((Auth::user()->id==$ReceiverID[0]->user_id)&&($SignatureTurn=='0'))
      {
        Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MRTMaster')->where('SignatureType', 'ReceivedBy')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
        MRTMaster::where('MRTNo', $id)->update(['SignatureTurn'=>'1']);
        if (Auth::user()->id!=$ReturnerID[0]->user_id)
        {
          $notifythis = array('tobeNotify'=>$ReturnerID[0]->user_id);
          $notifythis=(object)$notifythis;
          $job = (new NewCreatedMRTJob($notifythis))->delay(Carbon::now()->addSeconds(5));
          dispatch($job);

          $NotificationTbl = new Notification;
          $NotificationTbl->user_id = $ReturnerID[0]->user_id;
          $NotificationTbl->NotificationType = 'Request';
          $NotificationTbl->FileType = 'MRT';
          $NotificationTbl->FileNo = $id;
          $NotificationTbl->TimeNotified = Carbon::now();
          $NotificationTbl->save();

          // global notif trigger
          $ReceiverID = array('id' =>$ReturnerID[0]->user_id);
          $ReceiverID = (object)$ReceiverID;
          $job = (new GlobalNotifJob($ReceiverID))
          ->delay(Carbon::now()->addSeconds(5));
          dispatch($job);
        }
      }elseif((Auth::user()->id==$ReturnerID[0]->user_id)&&($SignatureTurn=='1'))
      {
        $MRTMaster=MRTMaster::where('MRTNo',$id)->get(['ReturnDate','CreatorID']);
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
          ,'Amount' =>$MRTammount ,'CurrentCost' =>$newcurrentcost ,'CurrentQuantity' =>$currentQty ,'CurrentAmount' =>$currentAmnt ,'MTDate' =>$MRTMaster[0]->ReturnDate );
        }
        MaterialsTicketDetail::insert($forMRTtbl);
        Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MRTMaster')->where('SignatureType', 'ReturnedBy')->where('user_id', Auth::user()->id)->update(['Signature'=>'0']);
        MRTMaster::where('MRTNo', $id)->update(['Status'=>'0']);

        $NotificationTbl = new Notification;
        $NotificationTbl->user_id = $MRTMaster[0]->CreatorID;
        $NotificationTbl->NotificationType = 'Approved';
        $NotificationTbl->FileType = 'MRT';
        $NotificationTbl->FileNo = $id;
        $NotificationTbl->TimeNotified = Carbon::now();
        $NotificationTbl->save();

        // global notif trigger
        $ReceiverID = array('id' =>$MRTMaster[0]->CreatorID);
        $ReceiverID = (object)$ReceiverID;
        $job = (new GlobalNotifJob($ReceiverID))
        ->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
    }
    public function DeclineMRT($id)
    {
      Signatureable::where('Signatureable_id',$id)->where('signatureable_type','App\MRTMaster')->where('user_id', Auth::user()->id)->update(['Signature'=>'1']);
      MRTMaster::where('MRTNo', $id)->update(['Status'=>'1']);

      $MRTMaster=MRTMaster::where('MRTNo',$id)->get(['CreatorID']);

      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $MRTMaster[0]->CreatorID;
      $NotificationTbl->NotificationType = 'Declined';
      $NotificationTbl->FileType = 'MRT';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$MRTMaster[0]->CreatorID);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    public function updateQuantityMRT($id,Request $request)
    {
      $MRTConfirmdetail=MRTConfirmationDetail::where('MRTNo',$id)->get(['ItemCode','Quantity','UnitCost']);
      $MRTMaster=MRTMaster::where('MRTNo',$id)->get(['MCTNo','Status']);
      if ($MRTMaster[0]->Status != null)
      {
        return ['error'=> 'Refreshed'];
      }
      foreach ($MRTConfirmdetail as $count=> $mrtconfirm)
      {
        $MCTitemQty=MCTConfirmationDetail::where('MCTNo',$MRTMaster[0]->MCTNo)->where('ItemCode',$mrtconfirm->ItemCode)->get(['Quantity']);
        if ($request->UpdatedQty[$count] > $MCTitemQty[0]->Quantity)
        {
          return ['error'=>$mrtconfirm->ItemCode.' quantity may not be greater than'.$MCTitemQty[0]->Quantity];
        }elseif($request->UpdatedQty[$count] < 1)
        {
          return ['error'=>$mrtconfirm->ItemCode.' quantity must be atleast 1'];
        }
      }
      foreach ($MRTConfirmdetail as $key=> $mrtconfirm)
      {
        $newAMT=$mrtconfirm->UnitCost*$request->UpdatedQty[$key];
        MRTConfirmationDetail::where('MRTNo',$id)->where('ItemCode',$mrtconfirm->ItemCode)->update(['Quantity'=>$request->UpdatedQty[$key],'Amount'=>$newAMT]);
      }

      $returnerID=Signatureable::where('Signatureable_id',$id)->where('signatureable_type', 'App\MRTMaster')->where('SignatureType','ReturnedBy')->value('user_id');
      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $returnerID;
      $NotificationTbl->NotificationType = 'Updated';
      $NotificationTbl->FileType = 'MRT';
      $NotificationTbl->FileNo = $id;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$returnerID);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    public function myMRTSignatureRequest()
    {
      return view('Warehouse.MRT.myMRTSignatureRequest');
    }
    public function myMRTSignatureFetchData()
    {
      $user=User::find(Auth::user()->id);
      return $NumberofRequest=$user->MRTSignatureTurn()->paginate(10);
    }
    public function MRTSignatureRequestCount()
    {
      $user=User::find(Auth::user()->id);
      $NumberofRequest=$user->MRTSignatureTurn()->count();
      $response = array('MRTRequestCount' => $NumberofRequest);
      return response()->json($response);
    }
    public function MRTindexPage()
    {
      return view('Warehouse.MRT.MRTindex');
    }
    public function MRTindexSearch(Request $request)
    {
      return MRTMaster::with('users')->orderBy('MRTNo','DESC')->where('MRTNo','LIKE','%'.$request->MRTNo.'%')->paginate(10,['MRTNo','MCTNo','ReturnDate','Particulars','AddressTo','Status','IsRollBack']);
    }
    public function RollBack($mrtNo)
    {
      $dataToRollBack=MaterialsTicketDetail::where('MTType', 'MRT')->where('MTNo', $mrtNo)->whereNull('IsRollBack')->get();
      MRTMaster::where('MRTNo',$mrtNo)->update(['IsRollBack'=>'0']);
      foreach ($dataToRollBack as $data)
      {
        $idOfMRTHistory = MaterialsTicketDetail::where('MTType', 'MRT')->where('MTNo', $mrtNo)->where('ItemCode',$data->ItemCode)->value('id');
        MaterialsTicketDetail::where('id', $idOfMRTHistory)->update(['IsRollBack'=>'0']);
        $affectedRows = MaterialsTicketDetail::where('ItemCode',$data->ItemCode)->whereNull('IsRollBack')->where('id','>',$idOfMRTHistory)
        ->chunk(5, function ($affectedRows) use ($data)
        {
             foreach ($affectedRows as $affectedrow)
             {
               if ($affectedrow->MTType=='MCT' || $affectedrow->MTType=='DMG')
               {
                $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
                $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
                $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
                $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
                $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
         $CurrentQuantityOfItem=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode', $data->ItemCode)->take(1)->value('CurrentQuantity');
         MasterItem::where('ItemCode',$data->ItemCode)->update(['CurrentQuantity' => $CurrentQuantityOfItem]);
       }
       $MRTMaster=MRTMaster::where('MRTNo',$mrtNo)->get(['CreatorID']);
       $NotificationTbl = new Notification;
       $NotificationTbl->user_id = $MRTMaster[0]->CreatorID;
       $NotificationTbl->NotificationType = 'Invalid';
       $NotificationTbl->FileType = 'MRT';
       $NotificationTbl->FileNo =$mrtNo;
       $NotificationTbl->TimeNotified = Carbon::now();
       $NotificationTbl->save();

       // global notif trigger
       $ReceiverID = array('id' =>$MRTMaster[0]->CreatorID);
       $ReceiverID = (object)$ReceiverID;
       $job = (new GlobalNotifJob($ReceiverID))
       ->delay(Carbon::now()->addSeconds(5));
       dispatch($job);
    }
    public function UndoRollBack($mrtNo)
    {
      $dataToUndoRollBack=MaterialsTicketDetail::where('MTType', 'MRT')->where('MTNo', $mrtNo)->get();
      MRTMaster::where('MRTNo',$mrtNo)->update(['IsRollBack'=>'1']);
      foreach ($dataToUndoRollBack as $data)
      {
        MaterialsTicketDetail::where('MTType', 'MRT')->where('MTNo', $mrtNo)->where('ItemCode',$data->ItemCode)->update(['IsRollBack'=>NULL]);
        $idOfMRTHistory = MaterialsTicketDetail::where('MTType', 'MRT')->where('MTNo', $mrtNo)->where('ItemCode',$data->ItemCode)->value('id');
        $affectedRows = MaterialsTicketDetail::where('ItemCode',$data->ItemCode)->whereNull('IsRollBack')->where('id','>',$idOfMRTHistory)
        ->chunk(5, function ($affectedRows) use ($data)
        {
             foreach ($affectedRows as $affectedrow)
             {
               if ($affectedrow->MTType=='MCT' || $affectedrow->MTType=='DMG')
               {
                $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
                $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
                $uCostLatestRR=MaterialsTicketDetail::orderBy('id','DESC')->where('MTType', 'RR')->where('ItemCode',$data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->value('UnitCost');
                $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
                $dataBelowTheRow=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode', $data->ItemCode)->where('id','<',$affectedrow->id)->whereNull('IsRollBack')->take(1)->get();
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
         $CurrentQuantityOfItem=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode', $data->ItemCode)->take(1)->value('CurrentQuantity');
         MasterItem::where('ItemCode',$data->ItemCode)->update(['CurrentQuantity' => $CurrentQuantityOfItem]);
      }
      $MRTMaster=MRTMaster::where('MRTNo',$mrtNo)->get(['CreatorID']);
      $NotificationTbl = new Notification;
      $NotificationTbl->user_id = $MRTMaster[0]->CreatorID;
      $NotificationTbl->NotificationType = 'UndoInvalid';
      $NotificationTbl->FileType = 'MRT';
      $NotificationTbl->FileNo =$mrtNo;
      $NotificationTbl->TimeNotified = Carbon::now();
      $NotificationTbl->save();

      // global notif trigger
      $ReceiverID = array('id' =>$MRTMaster[0]->CreatorID);
      $ReceiverID = (object)$ReceiverID;
      $job = (new GlobalNotifJob($ReceiverID))
      ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
}
