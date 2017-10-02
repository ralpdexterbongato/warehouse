@extends('layouts.master')
@section('title')
  Requisition Voucher
@endsection
@section('body')
  <div class="RV-container" id="rv">
    <rvcreate :user="{{Auth::user()}}" :mymanager="{{$mymanager[0]}}" :budgetofficer="{{$currentBudgetOfficer}}" :gm="{{$GM}}"></rvcreate>
  </div>
  <script type="text/javascript" src="/js/rv.js">
  </script>
@endsection
