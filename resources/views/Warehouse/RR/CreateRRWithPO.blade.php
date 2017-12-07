@extends('layouts.master')
@section('title')
Create RR|with PO
@endsection
@section('body')
  <div class="rr-with-po-container" id="rr">
    <h1 class="rr-create-w-po-title"><i class="material-icons">add</i> Create R.R. with P.O.</h1>
    <createrrwithpo :pomasters="{{$POMaster}}" :rrvalidatorwpo="{{$fromPODetail}}" :auditors="{{$Auditors}}" :managers="{{$Managers}}" :clerks="{{$Clerks}}" :allusers="{{$AllActiveUsers}}">
    </createrrwithpo>
  </div>
  <script type="text/javascript" src="/js/rr.js">
  </script>
@endsection
