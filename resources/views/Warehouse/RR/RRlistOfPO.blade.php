@extends('layouts.master')
@section('title')
  RR list of PO
@endsection
@section('body')
  <div class="RR-list-of-po-container">
    <h5><i class="material-icons">list</i> List of RR that belongs to PO # {{$PONum->PONumber}}</h5>
    <div class="RR-box-container">
      @foreach ($RRofPO as $rr)
      <a href="/RR-fullpreview/{{$rr->RRNo}}">
        <div class="RR-box">
          <div class="rr-box-head">
            <h5>RR # <span class="bold">{{$rr->RRNo}}</span></h5>
          </div>
          <div class="rr-box-content">
            <div class="rr-box-top-content">
              <p>Date: {{$rr->RRDate->format('M d Y')}}</p>
              <p>Supplier: {{$rr->Supplier}}</p>
              <p>Address: {{$rr->Address}}</p>
            </div>
            <div class="rr-box-status">
              @if ($rr->Status=='0')
                <h3><i class="material-icons color-green">check</i></h3>
              @elseif ($rr->Status=='1')
                <h3><i class="material-icons color-red">close</i></h3>
              @else
                <h3><i class="material-icons darker-blue">access_time</i></h3>
              @endif
            </div>
          </div>
        </div>
      </a>
      @endforeach
    </div>
    <div class="pagination-container">
      {{$RRofPO->links()}}
    </div>
  </div>
@endsection
