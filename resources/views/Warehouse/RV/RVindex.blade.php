@extends('layouts.master')
@section('title')
  Requisition Voucher|index
@endsection
@section('body')
  <div class="RV-index-container">
    <div id="rv">
      <rvtable></rvtable>
    </div>
  </div>
  <script type="text/javascript" src="/js/rv.js">
  </script>
@endsection
