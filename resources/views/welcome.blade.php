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
                <a href="{{route('mirs.add')}}"><li><i class="fa fa-print"></i> Create MIRS</li></a>
                <a href="{{route('MIRSgridview')}}"><li><i class="fa fa-search"></i> Search MIRS</li></a>
              </ul>
            </li>
            <li class="dropping-parent">
              <h1>SUMMARY <i class="fa fa-angle-down"></i></h1>
              <ul class="dropping">
                <a href="{{route('summary.mrt')}}"><li><i class="fa fa-list-alt"></i> MRT(summary)</li></a>
                <a href="{{route('mct-summary')}}"><li><i class="fa fa-list-alt"></i> MCT(summary)</li></a>
              </ul>
            </li>
            <li class="dropping-parent">
              <h1>RR <i class="fa fa-angle-down"></i></h1>
              <ul class="dropping create-rr-btn">
                @if ((Auth::check())&&(Auth::user()->Role == 4))
                  <a href="{{route('MakeRR')}}"><li><i class="fa fa-print"></i> Create RR</li></a>
                @endif
                <a href="{{route('RRindexview')}}"><li><i class="fa fa-search"></i> Search RR</li></a>
              </ul>
            </li>
            <li class="dropping-parent">
              <h1>RV <i class="fa fa-angle-down"></i></h1>
              <ul class="dropping create-rv-btn">
                <a href="{{route('Creating.RV')}}"><li><i class="fa fa-print"></i> Create RV</li></a>
                <a href="{{route('RVindexView')}}"><li><i class="fa fa-search"></i> Search RV</li></a>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      <div class="search-box">
        <form action="{{route('search.code')}}" method="get">
          <input id="search-code-input" type="text" name="ItemCode" placeholder="Item code here..." required>
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
              <td>{{$latestFound[0]->MTDate->format('M')}}</td>
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
              <td>{{$history->MTDate->format('M')}}</td>
            </tr>
            @endforeach
          @else
            <div class="background-pic animated slideInRight">
            </div>
          @endif
        </table>
        @if (!empty($historiesfound[0]))
          {{$historiesfound->links()}}
        @endif
      </div>
    </div>
  </div>
@endsection
