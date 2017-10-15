@extends('layouts.master')
@section('title')
RR|Create
@endsection
@section('body')
  <div class="CreateRR-No-PO">
    <div id="rr">
      <createrrnopo :fromrrvalidator="{{$fromRRValidatorNoPO}}" :managers="{{$Managers}}" :auditors="{{$Auditors}}" :clerks="{{$Clerks}}"></createrrnopo>
    </div>
  </div>
  <script type="text/javascript" src="/js/rr.js">
  </script>
@endsection