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
class RRController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware('IsWarehouse',['except'=>['RRindex','previewRR','signatureRR','declineRR','RRindexSearchbyRRNo','RRsignatureRequest','displayRRcurrentSession']]);
  }
  public function CreateRR()
  {
   $Auditors=User::where('Role', '5')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
   $Managers=User::where('Role','0')->whereNotNull('IsActive')->get(['id','Lname','Fname']);
   $Clerks=User::where('Role','6')->whereNotNull('IsActive')->get(['id','Lname','Fname']);

   return view('Warehouse.RR.RRCreateView',compact('Auditors','Managers','Clerks'));
  }

  public function StoreSessionRRnonExisting(Request $request)
  {
      $this->storeRRSessionValidator($request);
      if ($request->QuantityAccepted> $request->QuantityDelivered)
      {
        return redirect()->back()->with('message', 'Quantity Accepted cannot be higher than Delivered');
      }
      if (Session::has('RR-Items-Added'))
      {
        foreach (Session::get('RR-Items-Added') as $addedAlready)
        {
          if ($addedAlready->ItemCode == $request->ItemCode)
          {
            return redirect()->back()->with('message','This item is already added');
          }
        }
      }
      $nocommaUCost=str_replace(',','',$request->UnitCost);
      $toSessionArray = array('AccountCode' =>$request->AccountCode,'ItemCode'=>$request->ItemCode,'Description'=>$request->Description,'UnitCost'=>$nocommaUCost,'Unit'=>$request->Unit,'QuantityDelivered'=>$request->QuantityDelivered,'QuantityAccepted'=>$request->QuantityAccepted );
      $toSessionArray=(object)$toSessionArray;
      Session::push('RR-Items-Added',$toSessionArray);
      return redirect()->back();

  }
  public function storeRRSessionValidator($request)
  {
    $this->validate($request,[
      'AccountCode'=>'required',
      'ItemCode'=>'required|unique:MaterialsTicketDetails',
      'UnitCost'=>'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
      'Description'=>'required|unique:MasterItems',
      'Unit'=>'required',
      'QuantityDelivered'=>'required',
      'QuantityAccepted'=>'required',
    ]);
  }
  public function storeRRExistSessionValidator($request)
  {
    $maximum=$request->QuantityDelivered;
    $this->validate($request,[
      'AccountCode'=>'required',
      'ItemCode'=>'required',
      'UnitCost'=>'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
      'Description'=>'required',
      'Unit'=>'required',
      'QuantityDelivered'=>'required',
      'QuantityAccepted'=>'required|numeric|max:'.$maximum,
    ]);
  }
  public function deleteSessionStored($id)
  {
    if (Session::has('RR-Items-Added'))
    {
      $SelectedRRitems=(array)Session::get('RR-Items-Added');
      foreach ($SelectedRRitems as $key => $item)
      {
         if ($item->ItemCode==$id)
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

  public function StoreSessionItemExist(Request $request)
  {
    $this->storeRRExistSessionValidator($request);
      if (Session::has('RR-Items-Added'))
      {
        foreach (Session::get('RR-Items-Added') as $items)
        {
          if ($items->ItemCode==$request->ItemCode)
          {
            return;
          }
        }
      }
      $nocommaUCost=str_replace(',','',$request->UnitCost);
        $DataFromUserToArray = array('ItemCode'=>$request->ItemCode,'AccountCode'=>$request->AccountCode,'Description'=>$request->Description,'UnitCost'=>$nocommaUCost,'Unit'=>$request->Unit,'QuantityDelivered'=>$request->QuantityDelivered,'QuantityAccepted'=>$request->QuantityAccepted );
        $DataFromUserToArray=(object)$DataFromUserToArray;
        Session::push('RR-Items-Added',$DataFromUserToArray);
        return redirect()->back();
  }

  public function StoringRRtoTable(Request $request)
  {
    $this->StoringRRTableValidator($request);
    $RVValidation=RVDetail::where('RVNo',$request->RVNo)->take(1)->get(['Particulars']);
    if (empty($RVValidation[0]))
    {
      return redirect()->back()->with('message', 'The R.V number entered does not exist');
    }else
    {
      foreach (Session::get('RR-Items-Added') as $itemsfromRequest)
      {
        $checkifDescriptionExist=RVDetail::where('RVNo',$request->RVNo)->where('Particulars', $itemsfromRequest->Description)->get(['RVNo']);
        if(empty($checkifDescriptionExist[0]->RVNo))
        {
          return redirect()->back()->with('message', $itemsfromRequest->Description.' did not match any item from RV No.'.$request->RVNo);
        }
      }
    }
    if (!empty($request->PONo))
    {
      $POValidation=POMaster::where('PONo',$request->PONo)->whereNotNull('GeneralManagerSignature')->get(['GeneralManagerSignature','Supplier']);
      if (empty($POValidation[0]))
      {
        return redirect()->back()->with('message', 'The P.O. number entered does not exist or rejected by the GM');
      }
      if ($POValidation[0]->Supplier!=$request->Supplier)
      {
        return redirect()->back()->with('message','The Supplier in P.O.'.$request->PONo.' is '.$POValidation[0]->Supplier);
      }
    }
    if (empty(Session::get('RR-Items-Added')))
    {
     return redirect()->back()->with('message', 'Selecting items is required');
    }
    $year=Carbon::now()->format('y');
    $date=Carbon::now();
    $latestID=RRMaster::orderBy('id','DESC')->take(1)->value('RRNo');
    if (count($latestID)>0)
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
    $RRconfirmDB=new RRMaster;
    $RRconfirmDB->RRNo =$incremented;
    $RRconfirmDB->RRDate=$date;
    $RRconfirmDB->Supplier=$request->Supplier;
    $RRconfirmDB->Address=$request->Address;
    $RRconfirmDB->InvoiceNo=$request->InvoiceNo;
    $RRconfirmDB->RVNo=$request->RVNo;
    $RRconfirmDB->Carrier=$request->Carrier;
    $RRconfirmDB->DeliveryReceiptNo=$request->DeliveryReceiptNo;
    $RRconfirmDB->PONo=$request->PONo;
    $RRconfirmDB->Note=$request->Note;
    $RRconfirmDB->Receivedby=Auth::user()->Fname.' '.Auth::user()->Lname;
    $RRconfirmDB->ReceivedbyPosition=Auth::user()->Position;
    $RRconfirmDB->ReceivedbySignature=Auth::user()->Signature;
    if ($verifiedUser[0]->Fname.' '.$verifiedUser[0]->Lname== Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRconfirmDB->VerifiedbySignature=Auth::user()->Signature;
    }
    if ($originalReceiver[0]->Fname.' '.$originalReceiver[0]->Lname== Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRconfirmDB->ReceivedOriginalbySignature=Auth::user()->Signature;
    }
    if ($BINPoster[0]->Fname.' '.$BINPoster[0]->Lname == Auth::user()->Fname.' '.Auth::user()->Lname)
    {
      $RRconfirmDB->PostedtoBINbySignature=Auth::user()->Signature;
    }
    $RRconfirmDB->Verifiedby=$verifiedUser[0]->Fname.' '.$verifiedUser[0]->Lname;
    $RRconfirmDB->VerifiedbyPosition=$verifiedUser[0]->Position;
    $RRconfirmDB->ReceivedOriginalby=$originalReceiver[0]->Fname.' '.$originalReceiver[0]->Lname;
    $RRconfirmDB->ReceivedOriginalbyPosition=$originalReceiver[0]->Position;
    $RRconfirmDB->PostedToBINby=$BINPoster[0]->Fname.' '.$BINPoster[0]->Lname;
    $RRconfirmDB->PostedToBINbyPosition=$BINPoster[0]->Position;
    $RRconfirmDB->save();
    $ForRRDB = array();
    foreach (Session::get('RR-Items-Added') as $confirmedDetail)
    {
        $AMT=$confirmedDetail->UnitCost*$confirmedDetail->QuantityAccepted;
        $ForRRDB[] = array('ItemCode' =>$confirmedDetail->ItemCode ,'RRNo' =>$incremented ,
        'AccountCode' =>$confirmedDetail->AccountCode ,'Description' =>$confirmedDetail->Description ,'UnitCost' =>$confirmedDetail->UnitCost ,'RRQuantityDelivered' =>$confirmedDetail->QuantityDelivered,
        'QuantityAccepted' =>$confirmedDetail->QuantityAccepted ,'Unit' =>$confirmedDetail->Unit ,'Amount' =>$AMT);
    }
    RRconfirmationDetails::insert($ForRRDB);
    Session::forget('RR-Items-Added');
    Session::forget('itemMastersRR');
    return redirect()->route('RRindexview')->with('message', 'Success!');
  }
  public function StoringRRTableValidator($request)
  {
    $this->validate($request,[
      'Supplier'=>'required',
      'Address'=>'required',
      'InvoiceNo'=>'required',
      'RVNo'=>'required',
      'Carrier'=>'required',
      'DeliveryReceiptNo'=>'required',
      'PONo'=>'max:7',
      'Note'=>'max:50',
      'Verifiedby'=>'required',
      'ReceivedOriginalby'=>'required',
      'PostedtoBINby'=>'required',
    ]);
  }
  public function RRindex()
  {
    $RRmasters=RRMaster::orderBy('RRNo','DESC')->paginate(10,['RRNo','Supplier','Address','RVNo','Carrier','Receivedby','ReceivedbySignature','ReceivedOriginalby','ReceivedOriginalbySignature','Verifiedby','VerifiedbySignature','PostedtoBINby','PostedtoBINbySignature','IfDeclined']);
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
    $RRMaster=RRMaster::where('RRNo',$request->RRNo)->get(['ReceivedOriginalby','Verifiedby','PostedtoBINby']);
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
      $RRconfirmDetails=RRconfirmationDetails::where('RRNo',$request->RRNo)->get();
      foreach ($RRconfirmDetails as $confirmedDetail)
      {
        $MTLatestDetail=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('ItemCode', $confirmedDetail->ItemCode)->take(1)->get();
        if (!empty($MTLatestDetail[0]))
        {

          $Amount=$confirmedDetail->UnitCost*$confirmedDetail->QuantityAccepted;
          $newQuantity=$MTLatestDetail[0]->CurrentQuantity + $confirmedDetail->QuantityAccepted;
            $addedAMT=$MTLatestDetail[0]->CurrentAmount + $Amount;
            $newCost=$addedAMT/$newQuantity;
          $currentAMT=$newQuantity*$newCost;
          $MTDdb=new MaterialsTicketDetail;
          $MTDdb->ItemCode=$confirmedDetail->ItemCode;
          $MTDdb->MTType='RR';
          $MTDdb->MTNo=$confirmedDetail->RRNo;
          $MTDdb->AccountCode=$MTLatestDetail[0]->AccountCode;
          $MTDdb->UnitCost=$confirmedDetail->UnitCost;
          $MTDdb->RRQuantityDelivered=$confirmedDetail->QuantityDelivered;
          $MTDdb->Quantity=$confirmedDetail->QuantityAccepted;
          $MTDdb->Unit=$MTLatestDetail[0]->Unit;
          $MTDdb->Amount=$Amount;
          $MTDdb->CurrentCost=$newCost;
          $MTDdb->CurrentQuantity=$newQuantity;
          $MTDdb->CurrentAmount=$currentAMT;
          $MTDdb->MTDate=Carbon::now();
          $MTDdb->save();
        }else
        {
          $AMT=$confirmedDetail->UnitCost*$confirmedDetail->QuantityAccepted;
          $MTDdb=new MaterialsTicketDetail;
          $MTDdb->ItemCode=$confirmedDetail->ItemCode;
          $MTDdb->MTType='RR';
          $MTDdb->MTNo=$confirmedDetail->RRNo;
          $MTDdb->AccountCode=$confirmedDetail->AccountCode;
          $MTDdb->UnitCost=$confirmedDetail->UnitCost;
          $MTDdb->RRQuantityDelivered=$confirmedDetail->RRQuantityDelivered;
          $MTDdb->Quantity=$confirmedDetail->QuantityAccepted;
          $MTDdb->Unit=$confirmedDetail->Unit;
          $MTDdb->Amount=$AMT;
          $MTDdb->CurrentCost=$confirmedDetail->UnitCost;
          $MTDdb->CurrentQuantity=$confirmedDetail->QuantityAccepted;
          $MTDdb->CurrentAmount=$AMT;
          $MTDdb->save();
          $MasterItemDB=new MasterItem;
          $MasterItemDB->AccountCode=$confirmedDetail->AccountCode;
          $MasterItemDB->Description=$confirmedDetail->Description;
          $MasterItemDB->Unit=$confirmedDetail->Unit;
          $MasterItemDB->UnitCost=$confirmedDetail->UnitCost;
          $MasterItemDB->Quantity=$confirmedDetail->QuantityAccepted;
          $MasterItemDB->Month=Carbon::now()->format('M');
          $MasterItemDB->ItemCode_id=$confirmedDetail->ItemCode;
          $MasterItemDB->save();
        }
      }
    }
    return redirect()->back();
  }
  public function RRindexSearchbyRRNo(Request $request)
  {
    $RRMasterResults=RRMaster::where('RRNo',$request->RRNo)->paginate(10,['RRNo','Supplier','Address','RVNo','Carrier','Receivedby','ReceivedbySignature','ReceivedOriginalby','ReceivedbySignature','Verifiedby','VerifiedbySignature','PostedtoBINby','PostedtoBINbySignature','IfDeclined']);
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
    ->paginate(10,['RRNo','Supplier','Address','RVNo','Carrier','Receivedby','ReceivedbySignature','ReceivedOriginalby','ReceivedOriginalbySignature','Verifiedby','VerifiedbySignature','PostedtoBINby','PostedtoBINbySignature']);
    return view('Warehouse.RR.myRRrequest',compact('requestRR'));
  }
  public function declineRR(Request $request)
  {
    RRMaster::where('RRNo',$request->RRNo)->update(['IfDeclined'=>Auth::user()->Fname.' '.Auth::user()->Lname]);
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
}
