@extends('layouts.master')
@section('title')
PO|Generated PO's
@endsection
@section('body')
  <div class="list-PO-generated-container">
    <div class="list-Po-container">
      @foreach ($POList as $POrder)
        <a href="{{route('POFullView',[$POrder->PONo])}}">
          <div class="PO-box">
            <h1>{{$POrder->Supplier}} purchase order</h1>
            @if($POrder->Status=='0')
              <h4><i class="material-icons">check</i></h4>
            @elseif ($POrder->Status=='1')
              <h2><i class="material-icons">close</i></h2>
            @else
              <h3><i class="material-icons">access_time</i></h3>
            @endif
          </div>
        </a>
      @endforeach
    </div>
  </div>
@endsection
