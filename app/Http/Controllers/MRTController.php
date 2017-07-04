<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MRTMaster;
use App\MCTMaster;
use Carbon\Carbon;
use App\MaterialsTicketDetail;
use Session;
class MRTController extends Controller
{
    public function CreateMRT(Request $request)
    {
      $MTDetails=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $request->MCTNo)->get();
      $MCTdata=MCTMaster::where('MCTNo',$request->MCTNo)->get(['Particulars','AddressTo']);
      return view('Warehouse.MRTformView',compact('MTDetails','MCTdata'));
    }

    public function StoreMRT(Request $request)
    {
      $year=Carbon::now()->format('y');
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


      $mrtDB=new MRTMaster;
      $mrtDB->MRTNo=$MRTincremented;
      $mrtDB->MCTNo =$request->MCTNo;
      $mrtDB->ReturnDate = Carbon::now();
      $mrtDB->Particulars =$request->Particulars;
      $mrtDB->AddressTo= $request->AddressTo;
      $mrtDB->Returnedby = $request->Returnedby;
      $mrtDB->Receivedby = $request->Receivedby;
      $mrtDB->Remarks = $request->Remarks;
      $mrtDB->save();
      return redirect()->back();
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
}
