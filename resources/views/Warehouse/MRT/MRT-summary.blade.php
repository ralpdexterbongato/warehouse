@extends('layouts.master')
@section('title')
  MRT | SUMMARY
@endsection
@section('body')
<div class="Summary-MRT-Container">
  <div class="MRT-summary-body">
    <div class="search-mrt-summary">
      <h1><i class="material-icons">today</i> Summary of materials returned</h1>
      <form class="searchbox-summary-mrt" action="{{route('mrt.summary.find')}}" method="get">
        <input type="month" autocomplete="off" name="monthInput"  placeholder="Year-Month (yyyy-mm)"><button type="submit"><i class="material-icons">search</i></button>
      </form>
    </div>
    <div class="results-summary-mrt">
      @if (!empty($MaterialDate))
        @if (Auth::user()->Role==4)
          <div class="print-summary-mrt-container">
            <a class="print-mrt-summary" href="/MRTSUMMARY.pdf?monthInput={{$MaterialDate->format('Y-m')}}">
              <button type="submit">PDF</button>
            </a>
            <a class="print-mrt-summary" href="export-excel-mrt-summary?monthInput={{$MaterialDate->format('Y-m')}}">
              <button type="button" name="button"><i class="material-icons">file_download</i> Excel</button>
            </a>
          </div>
        @endif
      <div class="bondpaper-sample-mrt">
        <div class="header-summary">
          <div class="header-summary-content">
            <img src="DesignIMG/logo.png" alt="logo">
            <h2>BOHOL I ELECTRIC COOPERATIVE, INC</h2>
            <h3>Cabulijan, Tubigon, Bohol</h3>
            <h4>SUMMARY OF MATERIAL RETURN TICKET</h4>
            @if (isset($MaterialDate))
              <h5>(MONTH OF {{$MaterialDate->format('M, Y')}})</h5>
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
          @if (!empty($WarehouseMan[0]))
            <div class="recievers-name">
              <h5><img src="/ForHerokuOnly/{{$WarehouseMan[0]->Signature}}" alt="signature"></h5>
              <h4>{{$WarehouseMan[0]->FullName}}</h4>
              <p>{{$WarehouseMan[0]->Position}}</p>
            </div>
          @endif
        </div>
      </div>
      @else
        <h1 class="no-MRT-summary">No Current Result <i class="material-icons">search</i></h1>
      @endif
    </div>
  </div>
</div>
@endsection
