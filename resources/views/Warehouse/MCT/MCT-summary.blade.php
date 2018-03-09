@extends('layouts.master')
@section('title')
  MCT Summary
@endsection
@section('body')
  <div class="summary-mct-container">
    <div class="MCTSummaryForm">
      <div class="title-summary-mct">
        <i class="material-icons">today</i>  Summary of Charges
      </div>
      <form class="mct-sum-search" action="{{route('mct-search-date')}}" method="get">
        <input type="month" autocomplete="off" name="monthInput" placeholder="Year-Month(yyyy-mm)"><button type="submit"><i class="material-icons">search</i></button>
      </form>
    </div>
    @if (isset($ForDisplay[0]))
      @if (Auth::user()->Role==4)
        <div class="print-mct-summary">
          <form class="print-summary-mct-btn" action="{{route('mct-summary-print')}}" method="post">
            {{ csrf_field() }}
            <input type="text" name="DateSearched" value="{{$datesearch}}" style="display:none">
            <button type="submit">PDF</button>
          </form>
        </div>
      @endif
    <div class="bondpapercontainer-mct">
      <div class="landscape-bondpaper-mct">
        <div class="top-titles-mctSum">
          <img src="/DesignIMG/logo.png" alt="logo">
          <div class="titles-mct-content">
            <h4>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h4>
            <h4 class="address-mct-summary">Cabulijan, Tubigon, Bohol</h4>
            @if (isset($ForDisplay[0][0]))
            <h3>Summary of Charges(as of {{$ForDisplay[0][0]->MTDate->format('M Y')}})</h3>
            @endif
          </div>
        </div>
        <div class="mct-summary-table">
          <table>
            <tr>
              <th>Account</th>
              <th>ItemCode</th>
              <th>Description</th>
              <th>UnitCost</th>
              <th>Unit</th>
              <th>Stock</th>
              @if (isset($ForDisplay[0][0]))
                <th class="monthof">Month of {{$ForDisplay[0][0]->MTDate->format('M')}}</th>
              @endif
            </tr>
            <tr class="not-for-mobile">
              <th>Code</th>
              <th></th>
              <th></th>
              @if (isset($ForDisplay[0][0]))
                <th>({{$ForDisplay[0][0]->MTDate->format('M')}})</th>
              @endif
              <th></th>
              <th>Balance</th>
              <th>Issued</th>
            </tr>
            @if (!empty($ForDisplay[0]))
              @foreach ($ForDisplay as $item)
              <tr>
                <td>{{$item[0]->AccountCode}}</td>
                <td>{{$item[0]->ItemCode}}</td>
                <td>{{$item[0]->MasterItems->Description}}</td>
                <td>{{number_format($item[1]->UnitCost,'2','.',',')}}</td>
                <td>{{$item[0]->MasterItems->Unit}}</td>
                <td>{{$item[0]->CurrentQuantity}}</td>
                <td>{{$item[1]->totalissued}}</td>
              </tr>
              @endforeach
            @endif
          </table>
        </div>
      </div>
    </div>
    @else
      <h4 class="no-mct-summary"> No results found  <i class="material-icons">search</i></h4>
    @endif
  </div>
@endsection
