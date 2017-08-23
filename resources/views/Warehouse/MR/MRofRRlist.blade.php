@extends('layouts.master')
@section('title')
  List|MR of this RR
@endsection
@section('body')
  <div class="list-of-boxes-MR">
    <div class="MR-boxes-Container">
      @foreach ($MRmaster as $master)
      <a href="{{route('fullMR',[$master->MRNo])}}">
        <div class="mr-box">
          <h1>{{$master->MRNo}}</h1>
          @if ((($master->GeneralManagerSignature!=null)&&($master->RecommendedbySignature!=null))||(($master->ApprovalReplacerSignature!=null)&&($master->RecommendedbySignature!=null)))
            <h2><i class="fa fa-check"></i></h2>
          @elseif($master->IfDeclined)
            <h2><i class="fa fa-times big-x"></i></h2>
          @else
            <h2><i class="fa fa-clock-o big-clock"></i></h2>
          @endif
        </div>
      </a>
      @endforeach
    </div>
  </div>
@endsection
