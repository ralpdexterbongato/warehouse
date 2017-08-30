@extends('layouts.master')
@section('title')
warehouse | BOHECO 1
@endsection
@section('body')
  <div class="body-container">
    <div class="new-search-container">
      <div class="empty-left">
        <div class="empty-left-btn">
          <ul>
            <li class="dropping-parent">
              <h1>MIRS <i class="fa fa-angle-down"></i></h1>
              <ul class="dropping">
                <a href="{{route('mirs.add')}}"><li><i class="fa fa-plus"></i> Create</li></a>
                <a href="{{route('MIRSgridview')}}"><li><i class="fa fa-search"></i> Search</li></a>
              </ul>
            </li>
            <li class="dropping-parent">
              <h1>SUMMARY <i class="fa fa-angle-down"></i></h1>
              <ul class="dropping">
                <a href="{{route('summary.mrt')}}"><li><i class="fa fa-list-alt"></i> MRT</li></a>
                <a href="{{route('mct-summary')}}"><li><i class="fa fa-list-alt"></i> MCT</li></a>
              </ul>
            </li>
            <li class="dropping-parent">
              <h1>RV <i class="fa fa-angle-down"></i></h1>
              <ul class="dropping create-rv-btn">
                <a href="{{route('Creating.RV')}}"><li><i class="fa fa-plus"></i> Create</li></a>
                <a href="{{route('RVindexView')}}"><li><i class="fa fa-search"></i> Search</li></a>
              </ul>
            </li>
            <li class="dropping-parent">
              <a href="{{route('RRindexview')}}"><h1><p>Search RR</p><i class="fa fa-search"></i></h1></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="search-box">
        <form action="{{route('search.code')}}" method="get">
          <input id="search-code-input" autocomplete="off" type="text" name="ItemCode" placeholder="Item code here..." required>
          <button id="search-go" type="submit"><i class="fa fa-search"></i></button>
        </form>
      </div>
    </div>
    <div class="data-results-container">
    @if(!empty($historiesfound[0]))
      <div class="search-welcome-title">
        <div class="Current-title">
          <h1>Latest Data</h1>
        </div>
      </div>
      <div class="latest-data">
        <table>
          <tr>
            <th>MT type</th>
            <th>MT No.</th>
            <th>Account code</th>
            <th>Item code</th>
            <th>Description</th>
            <th>Unit cost</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Amount</th>
            <th>Current cost</th>
            <th>Current quantity</th>
            <th>Current amount</th>
            <th>Date</th>
          </tr>
            <tr>
              <td>{{$latestFound[0]->MTType}}</td>
              <td>{{$latestFound[0]->MTNo}}</td>
              <td>{{$latestFound[0]->AccountCode}}</td>
              <td>{{$latestFound[0]->ItemCode}}</td>
              <td>{{$latestFound[0]->MasterItems->Description}}</td>
              <td>{{number_format($latestFound[0]->UnitCost,'2','.',',')}}</td>
              <td>{{$latestFound[0]->Quantity}}</td>
              <td>{{$latestFound[0]->Unit}}</td>
              <td>{{number_format($latestFound[0]->Amount,'2','.',',')}}</td>
              <td>{{number_format($latestFound[0]->CurrentCost,'2','.',',')}}</td>
              <td>{{$latestFound[0]->CurrentQuantity}}</td>
              <td>{{number_format($latestFound[0]->CurrentAmount,'2','.',',')}}</td>
              <td>{{$latestFound[0]->MTDate->format('M, Y')}}</td>
            </tr>
        </table>

      </div>
      <div class="history-found">
        <h1>Recent History</h1>
        <table>
          <tr>
            <th>MT type</th>
            <th>MT No.</th>
            <th>Account code</th>
            <th>Item code</th>
            <th>Description</th>
            <th>Unit cost</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Amount</th>
            <th>Current cost</th>
            <th>Current quantity</th>
            <th>Current amount</th>
            <th>Month</th>
          </tr>
            @foreach ($historiesfound as $history)
            <tr>
              <td>{{$history->MTType}}</td>
              <td>{{$history->MTNo}}</td>
              <td>{{$history->AccountCode}}</td>
              <td>{{$history->ItemCode}}</td>
              <td>{{$history->MasterItems->Description}}</td>
              <td>{{number_format($history->UnitCost,'2','.',',')}}</td>
              <td>{{$history->Quantity}}</td>
              <td>{{$history->Unit}}</td>
              <td>{{number_format($history->Amount,'2','.',',')}}</td>
              <td>{{number_format($history->CurrentCost,'2','.',',')}}</td>
              <td>{{$history->CurrentQuantity}}</td>
              <td>{{number_format($history->CurrentAmount,'2','.',',')}}</td>
              <td>{{$history->MTDate->format('M, Y')}}</td>
            </tr>
            @endforeach
          @else
            <div class="background-pic">
            </div>
          @endif
        </table>
        @if (!empty($historiesfound[0]))
          <div class="paginate-container">
            {{$historiesfound->links()}}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
