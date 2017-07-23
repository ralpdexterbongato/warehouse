<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\MasterItem;
use App\MaterialsTicketDetail;
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
      $this->middleware('IsWarehouse',['except'=>['index','ItemMasterbyDescription','searchByItemCode','searchItemMaster']]);
     }

    public function index()
    {
        $allhistory=MaterialsTicketDetail::all();
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $month=Carbon::today()->format('M');
      $this->NewItemValidation($request);
      $amount = $request->UnitCost * $request->Quantity;
      $itemHistory=new MaterialsTicketDetail;
      $itemHistory->ItemCode = $request->ItemCode;
      $itemHistory->MTType = 'NEW';
      $itemHistory->MTNo = 'NEW';
      $itemHistory->AccountCode = $request->AccountCode;
      $itemHistory->UnitCost = $request->UnitCost;
      $itemHistory->Quantity = $request->Quantity;
      $itemHistory->Amount = $amount;
      $itemHistory->Unit = $request->Unit;
      $itemHistory->CurrentCost=  $request->UnitCost;
      $itemHistory->CurrentQuantity = $request->Quantity;
      $itemHistory->CurrentAmount = $amount;
      $itemHistory->save();

      $itemMaster=new MasterItem;
      $itemMaster->AccountCode = $request->AccountCode;
      $itemMaster->Description = $request->Description;
      $itemMaster->Unit = $request->Unit;
      $itemMaster->UnitCost = $request->UnitCost;
      $itemMaster->Quantity = $request->Quantity;
      $itemMaster->Month = $month;
      $itemMaster->ItemCode_id=$request->ItemCode;
      $itemMaster->save();

      return redirect()->back()->with('message', 'Successfully Maked');
    }
    public function NewItemValidation($request)
    {
      return $this->validate($request,[
      'ItemCode'=>'required|unique:MaterialsTicketDetails',
      'AccountCode'=>'required',
      'Description'=>'required|max:50',
      'Unit'=>'required',
      'UnitCost'=>'required',
      'Quantity'=>'required',
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function searchByItemCode(Request $request)
    {
      $historiesfound= MaterialsTicketDetail::where('ItemCode',$request->ItemCode)->orderBy('MTDate','DESC')->paginate(10);
      $latestFound=MaterialsTicketDetail::orderBy('MTDate','DESC')->where('ItemCode',$request->ItemCode)->take(1)->get();
      $historiesfound->withPath('http://localhost:8000/SearchByItemCode?_token=HqnLPVtyAoKBH4OD1wFgcuf4CvMiVrreO9mHP48t&ItemCode=L-001');
      return view('welcome',compact('historiesfound','latestFound'));
    }

    public function searchItemMaster(Request $request)
    {
      Session::forget('itemMasters');
      $itemMasters=MasterItem::where('ItemCode_id',$request->ItemCode)->paginate(5);
      if (!empty($itemMasters[0]))
      {
        Session::put('itemMasters',$itemMasters);
      }else {
        return redirect()->back()->with('message', 'No Results found');
      }
      return redirect()->back();
    }
    public function ItemMasterbyDescription(Request $request)
    {
      Session::forget('itemMasters');
       $itemMasters=MasterItem::where('Description','LIKE','%'.$request->Description.'%')->paginate(5);
       if (!empty($itemMasters[0]))
       {
        Session::put('itemMasters',$itemMasters);
        return redirect()->back();
      }
       return redirect()->back()->with('message','No results found');
    }

}
