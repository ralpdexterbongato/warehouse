@extends('layouts.master')
@section('title')
  MRT | SUMMARY
@endsection
@section('body')
<div class="Summary-MRT-Container">
  <div class="MRT-summary-body">
    <div class="search-mrt-summary">
      <h1><i class="fa fa-list-alt"></i> MRT MONTHLY SUMMARY</h1>
      <form class="searchbox-summary-mrt" action="{{route('mrt.summary.find')}}" method="get">
        <input type="text" name="monthInput" placeholder="Year-Month (yyyy-mm)"><button type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
    <div class="results-summar-mrt">
      <div class="bondpaper-sample-mrt">
        <div class="header-summary">
          <div class="header-summary-content">
            <img src="DesignIMG/logo.png" alt="logo">
            <h2>BOHOL I ELECTRIC COOPERATIVE, INC</h2>
            <h3>Cabulijan, Tubigon, Bohol</h3>
            <h4>SUMMARY OF MATERIAL RETURN TICKET</h4>
            <h5>(MONTH OF FEBRUARY 2017)</h5>
          </div>
        </div>
        <div class="body-summary-mrt">
          <table>
            <tr>
              <th>MCT No.</th>
              <th>Item Code</th>
              <th>Description</th>
              <th>Unit</th>
              <th>Summary</th>
            </tr>
            @foreach ($itemsummary as $item)
            <tr>
              <td>{{$item->MTNo}}</td>
              <td>{{$item->ItemCode}}</td>
              <td>{{$item->MasterItems->Description}}</td>
              <td>{{$item->Unit}}</td>
              <td class="align-right">{{$item->Quantity}}</td>
            </tr>
            @endforeach
          </table>
        </div>
        <div class="reciever-signature">
          <h3>Received By:</h3>
          <div class="recievers-name">
            <h5><img src="DesignIMG/signature1.png" alt="signature"></h5>
            <h4>FELICISIMO M. CANONES</h4>
            <p>Assistant Warehouseman</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
