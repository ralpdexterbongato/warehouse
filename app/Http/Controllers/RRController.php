<?php

namespace App\Http\Controllers;
use Session;
use App\MasterItem;
use Carbon\Carbon;
use App\RRMaster;
use App\MaterialsTicketDetail;
use App\User;
use App\RRconfirmationDetails;
use Auth;
use Illuminate\Http\Request;
use App\MRMaster;
use App\POMaster;
use App\RVDetail;
use App\RRValidatorNoPO;
use App\RRValidatorWithPO;
use App\RRDetailsNotForStock;
use App\RVMaster;

class RRController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware('IsWarehouse',['except'=>['RRindex','previewRR','signatureRR','declineRR','RRindexSearchbyRRNo','RRsignatureRequest','displayRRcurrentSession']]);
  }
  public function storeRRSessionValidatorNoPO($request)
  {
    $maxdelivered=$request->MaxQty;
    $maximumaccept=$request->QuantityDelivered;
    $this->validate($request,[
      'UnitCost'=>'required|min:0.1|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
      'Description'=>'required',
      'Unit'=>'required',
      'QuantityDelivered'=>'required|numeric|min:1|max:'.$maxdelivered,
      'QuantityAccepted'=>'required|numeric|min:0|max:'.$maximumaccept,
    ]);
  }
  public function storeRRSessionValidatorWithPO($request)
  {
    $maxdelivered=$request->MaxQty;
    $maximumaccept=$request->QuantityDelivered;
    $this->validate($request,[
      'UnitCost'=>'required|min:0.1',
      'Description'=>'required',
      'Unit'=>'required',
      'QuantityDelivered'=>'required|numeric|min:1|max:'.$maxdelivered,
      'QuantityAccepted'=>'required|numeric|min:0|max:'.$maximumaccept,
    ]);
  }
  public function deleteSessionStored($id)
  {
    if (Session::has('RR-Items-Added'))
    {
      $SelectedRRitems=Session::get('RR-Items-Added');
      foreach ($SelectedRRitems as $key => $item)
      {
         if ($key==$id)
         {
           unset($SelectedRRitems[$key]);
         }
      }
      Session::put('RR-Items-Added',$SelectedRRitems);
    }
  }
  public function searchbyItemMasterCode(Request $request)
  {
    $itemMasters=MasterItem::where('ItemCode_id','LIKE','%'.$request->searchcode.'%')->paginate(5,['AccountCode','ItemCode_id','Description','Unit']);
    $response=[
      'pagination'=>[
        'total'=> $itemMasters->total(),
        'per_page'=>$itemMasters->perPage(),
        'current_page'=>$itemMasters->currentPage(),
        'last_page'=>$itemMasters->lastPage(),
        'from'=>$itemMasters->firstitem(),
        'to'=>$itemMasters->lastitem(),
      ],
      'model'=> $itemMasters
    ];
    return response()->json($response);
  }
  public function ItemMasterbyDescription(Request $request)
  {
    Session::forget('itemMastersRR');
     $itemMasters=MasterItem::where('Description','LIKE','%'.$request->Description.'%')->paginate(5);
     if (!empty($itemMasters[0]))
     {
      Session::put('itemMastersRR',$itemMasters);
      return redirect()->back();
    }
     return redirect()->back()->with('message','No results found');
  }
  public function StoreSessionRRNoPO(Request $request)
  {
    $this->storeRRSessionValidatorNoPO($request);
      if ($request->UnitCost<=0)
      {
        return response()->json(['error'=>'UnitCost must be atleast 0.1']);
      }
      if (Session::has('RR-Items-Added'))
      {
        foreach (Session::get('RR-Items-Added') as $items)
        {
          if (($items->ItemCode==$request->ItemCode)||($items->Description==$request->Description))
          {
            return response()->json(['error'=>'Oops! cannot duplicate items']);
          }
        }
      }
      $nocommaUCost=str_replace(',','',$request->UnitCost);
      $AMT=$nocommaUCost*$request->QuantityAccepted;
        $DataFromUserToArray = array('ItemCode'=>$request->ItemCode,'AccountCode'=>$request->AccountCode,'Description'=>$request->Description,'UnitCost'=>$nocommaUCost,'Unit'=>$request->Unit,'QuantityDelivered'=>$request->QuantityDelivered,'QuantityAccepted'=>$request->QuantityAccepted,'Amount'=>$AMT);
        $DataFromUserToArray=(object)$DataFromUserToArray;
        Session::push('RR-Items-Added',$DataFromUserToArray);
  }
  public function StoreSessionRRWithPO(Request $request)
  {
    $this->storeRRSessionValidatorWithPO($request);
      if ($request->UnitCost<=0)
      {
        return response()->json(['error'=>'UnitCost must be atleast 0.1']);
      }
      if (Session::has('RR-Items-Added'))
      {
        foreach (Session::get('RR-Items-Added') as $items)
        {
          if (($items->ItemCode==$request->ItemCode)||($items->Description==$request->Description))
          {
            return response()->json(['error'=>'Oops! cannot duplicate items']);
          }
        }
      }
      $nocommaUCost=str_replace(',','',$request->UnitCost);
      $AMT=$nocommaUCost*$request->QuantityAccepted;
        $DataFromUserToArray = array('ItemCode'=>$request->ItemCode,'AccountCode'=>$request->AccountCode,'Description'=>$request->Description,'UnitCost'=>$nocommaUCost,'Unit'=>$request->Unit,'QuantityDelivered'=>$request->QuantityDelivered,'QuantityAccepted'=>$request->QuantityAccepted,'Amount'=>$AMT);
        $DataFromUserToArray=(object)$DataFromUserToArray;
        Session::push('RR-Items-Added',$DataFromUserToArray);
  }
  public function showSessionRRData()
  {
    return Session::get('RR-Items-Added');
  }
  public function StoreRRtoTableNoPO(Request $request)
  {
    $this->StoringRRTableNoPOValidator($request);
    if (empty(Session::get('RR-Items-Added')))
    {
     return response()->json(['error'=>'Selecting items is required']);
    }
    $year=Carbon::now()->format('y');
    $date=Carbon::now();
    $latestID=RRMaster::orderBy('id','DESC')->take(1)->value('RRNo');
    if (isset($latestID[0]))
    {
      $numOnly=substr($latestID,'3');
      $numOnly=(int)$numOnly;
      $newID=$numOnly + 1;
      $incremented=$year.'-'.sprintf("%04d",$newID);
    }else
    {
      $incremented=$year.'-'.sprintf("%04d",'1');
    }
    $verifiedUser=User::whereNotNull('IsActive')->where('id',$request->Verifiedby)->get(['Fname','Lname','Position']);
    $originalReceiver=User::whereNotNull('IsActive')->where('id',$request->ReceivedOriginalby)->get(['Fname','Lname','Position']);
    $BINPoster=User::whereNotNull('IsActive')->where('id', $request->PostedtoBINby)->get(['Fname','Lname','Position']);
    $RRMasterDB=new RRMaster;
    $RRMasterDB->RRNo =$incremented;
    $RRMasterDB->RRDate=$date;
    $RRMasterDB->Supplier=$request->Supplier;
    $RRMasterDB->Address=$request->Address;
    $RRMasterDB->InvoiceNo=$request->InvoiceNo;
    $RRMasterDB->RVNo=$request->RVNo;
    $RRMasterDB->Carrier=$request->Carrier;
    $RRMasterDB->DeliveryReceiptNo=$request->DeliveryReceiptNo;
    $RRMasterDB->Note=$request->Note;
    $RRMasterDB->Receivedby=Auth::user()->Fname.' '.Auth::user()->Lname;
    $RRMasterDB->ReceivedbyPosition=Auth::user()->Position;
    $RRMasterDB->ReceivedbySignature=Auth::user()->Signature;
    if ($verifiedUser[0]->Fname.' '.$verifiedUser[0]->Lname== Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRMasterDB->VerifiedbySignature=Auth::user()->Signature;
    }
    if ($originalReceiver[0]->Fname.' '.$originalReceiver[0]->Lname== Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRMasterDB->ReceivedOriginalbySignature=Auth::user()->Signature;
    }
    if ($BINPoster[0]->Fname.' '.$BINPoster[0]->Lname == Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRMasterDB->PostedtoBINbySignature=Auth::user()->Signature;
    }
    $RRMasterDB->Verifiedby=$verifiedUser[0]->Fname.' '.$verifiedUser[0]->Lname;
    $RRMasterDB->VerifiedbyPosition=$verifiedUser[0]->Position;
    $RRMasterDB->ReceivedOriginalby=$originalReceiver[0]->Fname.' '.$originalReceiver[0]->Lname;
    $RRMasterDB->ReceivedOriginalbyPosition=$originalReceiver[0]->Position;
    $RRMasterDB->PostedToBINby=$BINPoster[0]->Fname.' '.$BINPoster[0]->Lname;
    $RRMasterDB->PostedToBINbyPosition=$BINPoster[0]->Position;
    $RRMasterDB->save();
    $ForRRconfirmItemsDB = array();
    $RRValidatorNoPO=RRValidatorNoPO::where('RVNo',$request->RVNo)->get(['Particulars','Quantity']);
    foreach (Session::get('RR-Items-Added') as $forconfirmDetail)
    {
      $ForRRconfirmItemsDB[] = array('ItemCode' =>$forconfirmDetail->ItemCode ,'RRNo' =>$incremented ,
      'AccountCode' =>$forconfirmDetail->AccountCode ,'Description' =>$forconfirmDetail->Description ,'UnitCost' =>$forconfirmDetail->UnitCost ,'RRQuantityDelivered' =>$forconfirmDetail->QuantityDelivered,
      'QuantityAccepted' =>$forconfirmDetail->QuantityAccepted ,'Unit' =>$forconfirmDetail->Unit ,'Amount' =>$forconfirmDetail->Amount);
      foreach ($RRValidatorNoPO as $validatornopo)
      {
        if ($validatornopo->Particulars==$forconfirmDetail->Description)
        {
          $newValidatorQty=$validatornopo->Quantity-$forconfirmDetail->QuantityAccepted;
          RRValidatorNoPO::where('RVNo',$request->RVNo)->where('Particulars',$validatornopo->Particulars)->update(['Quantity'=>$newValidatorQty]);
        }
      }
    }
    RRconfirmationDetails::insert($ForRRconfirmItemsDB);
    Session::forget('RR-Items-Added');
    return ['redirect'=>route('RRfullpreview',[$incremented])];
  }
  public function StoreRRtoTableWithPO(Request $request)
  {
    $this->StoringRRTableWithPOValidator($request);
    if (empty(Session::get('RR-Items-Added')))
    {
     return response()->json(['error'=>'Selecting items is required']);
    }
    $year=Carbon::now()->format('y');
    $date=Carbon::now();
    $latestID=RRMaster::orderBy('id','DESC')->take(1)->value('RRNo');
    if (isset($latestID[0]))
    {
      $numOnly=substr($latestID,'3');
      $numOnly=(int)$numOnly;
      $newID=$numOnly + 1;
      $incremented=$year.'-'.sprintf("%04d",$newID);
    }else
    {
      $incremented=$year.'-'.sprintf("%04d",'1');
    }
    $verifiedUser=User::whereNotNull('IsActive')->where('id',$request->Verifiedby)->get(['Fname','Lname','Position']);
    $originalReceiver=User::whereNotNull('IsActive')->where('id',$request->ReceivedOriginalby)->get(['Fname','Lname','Position']);
    $BINPoster=User::whereNotNull('IsActive')->where('id', $request->PostedtoBINby)->get(['Fname','Lname','Position']);
    $POMaster=POMaster::where('PONo',$request->PONo)->get(['Supplier','Address','RVNo']);
    $RRMasterDB=new RRMaster;
    $RRMasterDB->RRNo =$incremented;
    $RRMasterDB->RRDate=$date;
    $RRMasterDB->Supplier=$POMaster[0]->Supplier;
    $RRMasterDB->Address=$POMaster[0]->Address;
    $RRMasterDB->PONo=$request->PONo;
    $RRMasterDB->InvoiceNo=$request->InvoiceNo;
    $RRMasterDB->RVNo=$POMaster[0]->RVNo;
    $RRMasterDB->Carrier=$request->Carrier;
    $RRMasterDB->DeliveryReceiptNo=$request->DeliveryReceiptNo;
    $RRMasterDB->Note=$request->Note;
    $RRMasterDB->Receivedby=Auth::user()->Fname.' '.Auth::user()->Lname;
    $RRMasterDB->ReceivedbyPosition=Auth::user()->Position;
    $RRMasterDB->ReceivedbySignature=Auth::user()->Signature;
    if ($verifiedUser[0]->Fname.' '.$verifiedUser[0]->Lname== Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRMasterDB->VerifiedbySignature=Auth::user()->Signature;
    }
    if ($originalReceiver[0]->Fname.' '.$originalReceiver[0]->Lname== Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRMasterDB->ReceivedOriginalbySignature=Auth::user()->Signature;
    }
    if ($BINPoster[0]->Fname.' '.$BINPoster[0]->Lname == Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRMasterDB->PostedtoBINbySignature=Auth::user()->Signature;
    }
    $RRMasterDB->Verifiedby=$verifiedUser[0]->Fname.' '.$verifiedUser[0]->Lname;
    $RRMasterDB->VerifiedbyPosition=$verifiedUser[0]->Position;
    $RRMasterDB->ReceivedOriginalby=$originalReceiver[0]->Fname.' '.$originalReceiver[0]->Lname;
    $RRMasterDB->ReceivedOriginalbyPosition=$originalReceiver[0]->Position;
    $RRMasterDB->PostedToBINby=$BINPoster[0]->Fname.' '.$BINPoster[0]->Lname;
    $RRMasterDB->PostedToBINbyPosition=$BINPoster[0]->Position;
    $RRMasterDB->save();
    $ForRRconfirmItemsDB = array();
    $RRValidatorWithPO=RRValidatorWithPO::where('PONo',$request->PONo)->get(['Qty','Description']);
    foreach (Session::get('RR-Items-Added') as $forconfirmDetail)
    {
      $ForRRconfirmItemsDB[] = array('ItemCode' =>$forconfirmDetail->ItemCode ,'RRNo' =>$incremented ,
      'AccountCode' =>$forconfirmDetail->AccountCode ,'Description' =>$forconfirmDetail->Description ,'UnitCost' =>$forconfirmDetail->UnitCost ,'RRQuantityDelivered' =>$forconfirmDetail->QuantityDelivered,
      'QuantityAccepted' =>$forconfirmDetail->QuantityAccepted ,'Unit' =>$forconfirmDetail->Unit ,'Amount' =>$forconfirmDetail->Amount);
      foreach ($RRValidatorWithPO as $validatorwithpo)
      {
        if ($validatorwithpo->Description==$forconfirmDetail->Description)
        {
          $newValidatorQty=$validatorwithpo->Qty-$forconfirmDetail->QuantityAccepted;
          RRValidatorWithPO::where('PONo',$request->PONo)->where('Description',$validatorwithpo->Description)->update(['Qty'=>$newValidatorQty]);
        }
      }
    }
    RRconfirmationDetails::insert($ForRRconfirmItemsDB);
    Session::forget('RR-Items-Added');
    return ['redirect'=>route('RRfullpreview',[$incremented])];
  }
  public function StoringRRTableNoPOValidator($request)
  {
    $this->validate($request,[
      'Supplier'=>'required|max:50',
      'Address'=>'required|max:30',
      'InvoiceNo'=>'max:12',
      'RVNo'=>'required',
      'Carrier'=>'max:30',
      'DeliveryReceiptNo'=>'max:12',
      'Note'=>'max:50',
      'Verifiedby'=>'required',
      'ReceivedOriginalby'=>'required',
      'PostedtoBINby'=>'required',
    ]);
  }
  public function StoringRRTableWithPOValidator($request)
  {
    $this->validate($request,[
      'InvoiceNo'=>'max:12|required',
      'Carrier'=>'max:30',
      'DeliveryReceiptNo'=>'max:12|required',
      'Note'=>'max:50',
      'Verifiedby'=>'required',
      'ReceivedOriginalby'=>'required',
      'PostedtoBINby'=>'required',
    ]);
  }
  public function RRindex()
  {
    $RRmasters=RRMaster::orderBy('RRNo','DESC')->paginate(10,['RRNo','Supplier','Address','RVNo','Receivedby','ReceivedbySignature','ReceivedOriginalby','ReceivedOriginalbySignature','Verifiedby','VerifiedbySignature','PostedtoBINby','PostedtoBINbySignature','IfDeclined']);
    return view('Warehouse.RR.RRindex',compact('RRmasters'));
  }
  public function previewRR($id)
  {
    $RRconfirmationDetails=RRconfirmationDetails::where('RRNo',$id)->get(['ItemCode','Unit','Description','RRQuantityDelivered','QuantityAccepted','UnitCost','Amount']);
    $Netsales=$RRconfirmationDetails->sum('Amount');
    $VAT=$Netsales*.12;
    $TOTALamt=$Netsales+$VAT;
    $RRMaster=RRMaster::where('RRNo',$id)->get();
    $checkMR=MRMaster::orderBy('RRNo','DESC')->where('RRNo',$id)->take(1)->get(['MRNo']);
    return view('Warehouse.RR.RRfullpreview',compact('allmanager','TOTALamt','VAT','checkMR','Netsales','RRMaster','RRconfirmationDetails'));
  }
  public function signatureRR(Request $request)
  {
    $RRMaster=RRMaster::where('RRNo',$request->RRNo)->get(['ReceivedOriginalby','Verifiedby','PostedtoBINby','PONo','RVNo']);
    if ($RRMaster[0]->ReceivedOriginalby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
        RRMaster::where('RRNo',$request->RRNo)->update(['ReceivedOriginalbySignature'=>Auth::user()->Signature]);
    }
    if ($RRMaster[0]->Verifiedby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      RRMaster::where('RRNo',$request->RRNo)->update(['VerifiedbySignature'=>Auth::user()->Signature]);
    }
    if ($RRMaster[0]->PostedtoBINby==Auth::user()->Fname.' '.Auth::user()->Lname)
    {
        RRMaster::where('RRNo',$request->RRNo)->update(['PostedtoBINbySignature'=>Auth::user()->Signature]);
    }
    $RRMasterUpdated=RRMaster::where('RRNo',$request->RRNo)->get(['ReceivedbySignature','ReceivedOriginalbySignature','VerifiedbySignature','PostedtoBINbySignature']);
    if (($RRMasterUpdated[0]->ReceivedOriginalbySignature)&&($RRMasterUpdated[0]->VerifiedbySignature)&&($RRMasterUpdated[0]->PostedtoBINbySignature)&&($RRMasterUpdated[0]->ReceivedbySignature))
    {
      $date=Carbon::now();
      $RRconfirmDetails=RRconfirmationDetails::where('RRNo',$request->RRNo)->get();
      $forMTDtable = array();
      $NotforMTDtable = array();
      foreach ($RRconfirmDetails as $forconfirmDetail)
      {
        if ($forconfirmDetail->ItemCode)
        {
          $MTLatestDetail=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('ItemCode', $forconfirmDetail->ItemCode)->take(1)->get();
          $Amount=$forconfirmDetail->UnitCost*$forconfirmDetail->QuantityAccepted;
          $newQuantity=$MTLatestDetail[0]->CurrentQuantity + $forconfirmDetail->QuantityAccepted;
          $addedAMT=$MTLatestDetail[0]->CurrentAmount + $Amount;
          $newCost=$addedAMT/$newQuantity;
          $currentAMT=$newQuantity*$newCost;
          $forMTDtable[] = array('ItemCode' =>$forconfirmDetail->ItemCode ,'MTType' =>'RR' ,'MTNo' =>$forconfirmDetail->RRNo ,
          'AccountCode' =>$MTLatestDetail[0]->AccountCode ,
          'UnitCost' =>$forconfirmDetail->UnitCost,'RRQuantityDelivered' =>$forconfirmDetail->QuantityDelivered ,'Quantity' =>$forconfirmDetail->QuantityAccepted,
          'Unit' =>$MTLatestDetail[0]->Unit ,'Amount' =>$Amount ,'CurrentCost' =>$newCost ,'CurrentQuantity'=>$newQuantity,'CurrentAmount'=>$currentAMT,'MTDate'=>$date);
        }else
        {
          $NotforMTDtable[] = array('RRNo' =>$forconfirmDetail->RRNo ,'Description' =>$forconfirmDetail->Description ,'UnitCost' =>$forconfirmDetail->UnitCost ,'RRQuantityDelivered' =>$forconfirmDetail->RRQuantityDelivered ,'QuantityAccepted' =>$forconfirmDetail->QuantityAccepted ,'Unit' =>$forconfirmDetail->Unit ,'Amount' =>$forconfirmDetail->Amount);
        }
      }
      MaterialsTicketDetail::insert($forMTDtable);
      RRDetailsNotForStock::insert($NotforMTDtable);
      if ($RRMaster[0]->PONo!=null)
      {
        $POofRV=POMaster::where('RVNo',$RRMaster[0]->RVNo)->get(['PONo']);
        $unpurchasedtotal=0;
        foreach ($POofRV as $POofrv)
        {
          $Qtyleft=RRValidatorWithPO::where('PONo', $POofrv->PONo)->get(['Qty']);
          $unpurchasedtotal=$unpurchasedtotal + $Qtyleft[0]->Qty;
        }
        if ($unpurchasedtotal==0)
        {
          RVMaster::where('RVNo',$RRMaster[0]->RVNo)->update(['IfPurchased'=>'0']);
        }
      }else
      {
        $remainingQuantity=RRValidatorNoPO::where('RVNo',$RRMaster[0]->RVNo)->sum('Quantity');
        if ($remainingQuantity==0)
        {
          RVMaster::where('RVNo',$RRMaster[0]->RVNo)->update(['IfPurchased'=>'0']);
        }
      }
    }

    return redirect()->back();
  }
  public function RRindexSearchbyRRNo(Request $request)
  {
    $RRMasterResults=RRMaster::where('RRNo',$request->RRNo)->paginate(10,['RRNo','Supplier','Address','RVNo','Receivedby','ReceivedbySignature','ReceivedOriginalby','ReceivedbySignature','Verifiedby','VerifiedbySignature','PostedtoBINby','PostedtoBINbySignature','IfDeclined']);
    return view('Warehouse.RR.RRindex',compact('RRMasterResults'));
  }

  public function RRsignatureRequest()
  {
    $requestRR=RRMaster::orderBy('RRNo','DESC')->where('ReceivedOriginalby',Auth::user()->Fname.' '.Auth::user()->Lname)
    ->whereNull('ReceivedOriginalbySignature')
    ->whereNull('IfDeclined')
    ->orWhere('Verifiedby',Auth::user()->Fname.' '.Auth::user()->Lname)
    ->whereNull('VerifiedbySignature')
    ->whereNull('IfDeclined')
    ->orWhere('PostedtoBINby',Auth::user()->Fname.' '.Auth::user()->Lname)
    ->whereNull('PostedtoBINbySignature')
    ->whereNull('IfDeclined')
    ->paginate(10,['RRNo','Supplier','Address','RVNo','Receivedby','ReceivedbySignature','ReceivedOriginalby','ReceivedOriginalbySignature','Verifiedby','VerifiedbySignature','PostedtoBINby','PostedtoBINbySignature']);
    return view('Warehouse.RR.myRRrequest',compact('requestRR'));
  }
  public function declineRR(Request $request)
  {
    RRMaster::where('RRNo',$request->RRNo)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
    $RRMaster=RRMaster::where('RRNo',$request->RRNo)->get(['PONo','RVNo']);
    if ($RRMaster[0]->PONo!=null)
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$request->RRNo)->get(['Description','QuantityAccepted']);
      $RRValidatorWithPO=RRValidatorWithPO::where('PONo', $RRMaster[0]->PONo)->get(['Description','Qty']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($RRValidatorWithPO as $rrvalidatorwithpo)
        {
          if ($rrvalidatorwithpo->Description==$canceldata->Description)
          {
            $NewQty=$rrvalidatorwithpo->Qty + $canceldata->QuantityAccepted;
            RRValidatorWithPO::where('PONo', $RRMaster[0]->PONo)->where('Description', $rrvalidatorwithpo->Description)->update(['Qty'=>$NewQty]);
          }
        }
      }
    }else
    {
      $Detailscanceled=RRconfirmationDetails::where('RRNo',$request->RRNo)->get(['Description','QuantityAccepted']);
      $RRValidatorNoPO=RRValidatorNoPO::where('RVNo', $RRMaster[0]->RVNo)->get(['Particulars','Quantity']);
      foreach ($Detailscanceled as $canceldata)
      {
        foreach ($RRValidatorNoPO as $rrvalidatorNopo)
        {
          if ($rrvalidatorNopo->Particulars==$canceldata->Description)
          {
            $NewQty=$rrvalidatorNopo->Quantity + $canceldata->QuantityAccepted;
            RRValidatorNoPO::where('RVNo',$RRMaster[0]->RVNo)->where('Particulars', $rrvalidatorNopo->Particulars)->update(['Quantity'=>$NewQty]);
          }
        }
      }
    }
    return redirect()->back();
  }
  public function displayRRcurrentSession()
  {
    $fromsession=Session::get('RR-Items-Added');
    $response=[
      'sessions'=> $fromsession
    ];
    return response()->json($response);
  }
  public function CreateRRNoPO($id)
  {
    $Auditors=User::where('Role', '5')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
    $Managers=User::where('Role','0')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
    $Clerks=User::where('Role','6')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
    $fromRRValidatorNoPO=RRValidatorNoPO::where('RVNo',$id)->get();
    return view('Warehouse.RR.CreateRRNoPO',compact('fromRRValidatorNoPO','Auditors','Managers','Clerks'));
  }
  public function CreateRRWithPO($id)
  {
    $Auditors=User::where('Role', '5')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
    $Managers=User::where('Role','0')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
    $Clerks=User::where('Role','6')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
    $POMaster=POMaster::where('PONo',$id)->get(['Supplier','Address','RVNo','PONo']);
    $RRValidatorWithPO=RRValidatorWithPO::where('PONo',$id)->get();
    return view('Warehouse.RR.CreateRRWithPO',compact('POMaster','RRValidatorWithPO','Auditors','Managers','Clerks'));
  }
  public function RRofRVlist($id)
  {
    $RRofRV=RRMaster::where('RVNo',$id)->paginate(9,['RRNo','RVNo','RRDate','Supplier','Address','ReceivedOriginalbySignature','VerifiedbySignature','PostedtoBINbySignature','IfDeclined']);
    return view('Warehouse.RR.RRlistOfRV',compact('RRofRV'));
  }
}
