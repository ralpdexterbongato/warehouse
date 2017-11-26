@extends('layouts.master')
@section('title')
RR|Create
@endsection
@section('body')
  <div class="CreateRR-No-PO">
    <div id="rr">
      <createrrnopo :fromrvdetail="{{$fromRVDetail}}" :managers="{{$Managers}}" :auditors="{{$Auditors}}" :clerks="{{$Clerks}}" :allusers="{{$AllActiveUsers}}"></createrrnopo>
    </div>
  </div>
  <script type="text/javascript" src="/js/rr.js">
  </script>
@endsection
