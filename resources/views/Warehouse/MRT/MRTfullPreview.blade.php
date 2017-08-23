@extends('layouts.master')
@section('title')
  MRT Viewer
@endsection
@section('body')
  <div class="MRT-viewer-container">
    <div class="Bondpaper-mrt-preview">
      @if (!empty($mrtMaster[0]))
      <div class="header-mrt-center">
        <h4>BOHOL I ELECTRIC COOPERATIVE, INC.</h4>
        <p class="address-mrt">Cabulijan, Tubigon, Bohol</p>
        <h3>MATERIALS RETURN TICKET</h3>
      </div>
      <div class="left-right-mrt">
        <div class="left-mrt">
          <span class="mrt-info"><label>Particulars:</label><h3>{{$mrtMaster[0]->Particulars}}</h3></span>
          <span class="mrt-info"><label>Address:</label><h3>{{$mrtMaster[0]->AddressTo}}</h3></span>
        </div>
        <div class="right-mrt">
          <span class="mrt-info"><label>MCT No:</label><h3>{{$mrtMaster[0]->MCTNo}}</h3></span>
          <span class="mrt-info"><label>MRT No:</label><h3>{{$mrtMaster[0]->MRTNo}}</h3></span>
          <span class="mrt-info"><label>Returned Date:</label><h3>{{$mrtMaster[0]->ReturnDate->format('m/d/Y')}}</h3></span>
        </div>
      </div>
      <div class="mrt-table-viewer">
        <table>
          <tr>
            <th>Acct. Code</th>
            <th>Item Code</th>
            <th>Description</th>
            <th>Unit Cost</th>
            <th>Amount</th>
            <th>Unit</th>
            <th>Returned</th>
          </tr>
          @foreach ($MTDitems as $items)
          <tr>
            <td>{{$items->AccountCode}}</td>
            <td>{{$items->ItemCode}}</td>
            <td>{{$items->MasterItems->Description}}</td>
            <td>{{number_format($items->UnitCost,'2','.',',')}}</td>
            <td>{{number_format($items->Amount,'2','.',',')}}</td>
            <td>{{$items->Unit}}</td>
            <td class="align-right">{{$items->Quantity}}</td>
          </tr>
          @endforeach
        </table>
      </div>
      <div class="groupbyAccount">
        @foreach ($MRTbyAcntCode as $totalamtbyacc)
          <span class="mrt-info"><label>{{$totalamtbyacc->AccountCode}}</label><h5>{{number_format($totalamtbyacc->totalAMT,'2','.',',')}}</h5></span>
        @endforeach
      </div>
      <div class="total-mrt-info">
        <span class="total-mrt-flex">
          <h3>Total ammount Returned</h3>
          <h4>{{number_format($totalsum,'2','.',',')}}</h4>
        </span>
      </div>
      <div class="mrt-returnby-receivedby-container">
        <div class="mrt-returnby-container">
            <p>Returned by:</p>
            <div class="mrt-bottom-data">
              <p>{{$mrtMaster[0]->Returnedby}}</p>
              <p>{{$mrtMaster[0]->ReturnedbyPosition}}</p>
            </div>
        </div>
        <div class="mrt-received-container">
          <p>Recieved by:</p>
          <div class="mrt-bottom-data">
            <h3><img src="/storage/signatures/{{$mrtMaster[0]->ReceivedbySignature}}" alt="signature"></h3>
            <p>{{$mrtMaster[0]->Receivedby}}</p>
            <p>{{$mrtMaster[0]->ReceivedbyPosition}}</p>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
@endsection
