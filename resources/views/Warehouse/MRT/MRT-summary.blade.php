@extends('layouts.master')
@section('title')
  MRT | SUMMARY
@endsection
@section('body')
<div class="Summary-MRT-Container">
  <div class="MRT-summary-body">
    <div class="search-mrt-summary">
      <h1><i class="fa fa-calendar"></i> Summary of materials returned</h1>
      <form class="searchbox-summary-mrt" action="{{route('mrt.summary.find')}}" method="get">
        <input type="text" autocomplete="off" name="monthInput" placeholder="Year-Month (yyyy-mm)"><button type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
    <div class="results-summary-mrt">
      @if (!empty($mrtmaster[0]))
      <div class="bondpaper-sample-mrt">
          <form class="print-mrt-summary" action="{{route('mrt-summary-print')}}" method="get">
            <button type="submit" name="monthInput" value="{{$mrtmaster[0]->ReturnDate->format('Y-m')}}"><i class="fa fa-file-pdf-o"></i> Print</button>
          </form>
        <div class="header-summary">
          <div class="header-summary-content">
            <img src="DesignIMG/logo.png" alt="logo">
            <h2>BOHOL I ELECTRIC COOPERATIVE, INC</h2>
            <h3>Cabulijan, Tubigon, Bohol</h3>
            <h4>SUMMARY OF MATERIAL RETURN TICKET</h4>
            @if (isset($mrtmaster[0]))
              <h5>(MONTH OF {{$mrtmaster[0]->ReturnDate->format('M, Y')}})</h5>
            @endif
          </div>
        </div>
        <div class="body-summary-mrt">
          <table>
            <tr>
              <th>Item Code</th>
              <th>Description</th>
              <th>Unit</th>
              <th>Summary</th>
            </tr>
            @if (!empty($itemsummary[0]))
              @foreach ($itemsummary as $item)
              <tr>
                <td>{{$item->ItemCode}}</td>
                <td>{{$item->MasterItems->Description}}</td>
                <td>{{$item->MasterItems->Unit}}</td>
                <td class="align-right">{{$item->totalQty}}</td>
              </tr>
              @endforeach
              @endif
          </table>
        </div>
        <div class="reciever-signature">
          <h3>Received By:</h3>
          @if (!empty($mrtmaster[0]))
            <div class="recievers-name">
              <h5><img src="DesignIMG/signature1.png" alt="signature"></h5>
              <h4>{{$mrtmaster[0]->Receivedby}}</h4>
              <p>{{$mrtmaster[0]->ReceivedbyPosition}}</p>
            </div>
          @endif
        </div>
      </div>
      @else
        <h1 class="no-MRT-summary">No Current Result <i class="fa fa-search"></i></h1>
      @endif
    </div>
  </div>
</div>
@endsection
