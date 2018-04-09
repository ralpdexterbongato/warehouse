<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\MaterialsTicketDetail;
class excelController extends Controller
{
    public function __construct()
    {
     $this->middleware('auth');
    }
    public function exportExelMCT(Request $request)
    {

      $datesearch=$request->DateSearched;
      $MCTsummaryItems=MaterialsTicketDetail::where('MTType','MCT')->whereNull('IsRollBack')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalissued, ItemCode as ItemCode')->get();
      if (empty($MCTsummaryItems[0]))
      {
        return redirect('mct-summary');
      }
      $ForDisplay = array();
      foreach ($MCTsummaryItems as $key=> $items)
      {
        $ForDisplay[$key]=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('ItemCode',$items->ItemCode)->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->get(['AccountCode','ItemCode','CurrentQuantity','MTDate']);
        $UnitCost=MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('MTType','RR')->where('ItemCode',$items->ItemCode)->take(1)->get(['UnitCost']);
        $issued=(object)['totalissued'=>$items->totalissued,'UnitCost'=>$UnitCost[0]->UnitCost];
        $ForDisplay[$key]->push($issued);
      }
      // return $ForDisplay;

      Excel::create('MCT-Summary', function($excel) use($ForDisplay){

        // Set the spreadsheet title, creator, and description
        $excel->setTitle('MCT-Summary');
        $excel->setCreator('Laravel')->setCompany('BOHECO1');
        $excel->setDescription('Item charges summary exported');

        // Build the spreadsheet, passing in the payments array
        $excel->sheet('MCT', function($sheet) use($ForDisplay) {
           $sheet->row(1, array(
              'Summary of Charges(as of '.$ForDisplay[0][0]->MTDate->format('M Y').')'
           ));
            $sheet->row(2, array('AccountCode','ItemCode','Description','UnitCost','Unit','StockBalance','Issued'
            ));
            $sheet->row(2, function($row) {
             // call cell manipulation methods
             $row->setBackground('#C9D9ED');
             });
            $loop=3;
            foreach ($ForDisplay as $key => $summaryRow) {
              $sheet->row($loop, array(
                  $summaryRow[0]->accountcode, $summaryRow[0]->ItemCode, $summaryRow[0]->MasterItems->Description,$summaryRow[1]->UnitCost,$summaryRow[0]->MasterItems->Unit,$summaryRow[0]->CurrentQuantity,$summaryRow[1]->totalissued
              ));
              $loop=$loop+1;
            }

        });


    })->download('xlsx');
    }

    public function exportExelMRT(Request $request)
    {
      $datesearch=$request->monthInput;
      $itemsummary=MaterialsTicketDetail::orderBy('ItemCode')->whereNull('IsRollBack')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->groupBy('ItemCode')->selectRaw('sum(Quantity) as totalQty, ItemCode as ItemCode ')->get();
      if (!empty($itemsummary[0]))
      {
        // return $itemsummary;
        $MaterialDate =MaterialsTicketDetail::orderBy('id','DESC')->whereNull('IsRollBack')->where('MTType','MRT')->whereDate('MTDate','LIKE',date($datesearch).'%')->take(1)->value('MTDate');
        Excel::create('MRT-Summary', function($excel) use($itemsummary,$MaterialDate){

          // Set the spreadsheet title, creator, and description
          $excel->setTitle('MRT-Summary');
          $excel->setCreator('Laravel')->setCompany('BOHECO1');
          $excel->setDescription('Item returned summary exported');

          // Build the spreadsheet, passing in the payments array
          $excel->sheet('MRT', function($sheet) use($itemsummary,$MaterialDate) {
              $sheet->row(1, array(
                'Summary of Material return ticket(as of '.$MaterialDate->format('M Y').')'
              ));
              $sheet->row(2, array('Item Code','Description','Unit','Summary'));
              $sheet->row(2, function($row) {
               // call cell manipulation methods
               $row->setBackground('#C9D9ED');
               });
              $loop=3;
              foreach ($itemsummary as $key => $summaryRow) {
                $sheet->row($loop, array(
                   $summaryRow->ItemCode,$summaryRow->MasterItems->Description,$summaryRow->MasterItems->Unit, $summaryRow->totalQty
                ));
                $loop=$loop+1;
              }
            });
        })->download('xlsx');
      }else
      {
        return redirect('/summary-mrt');
      }
    }
}
