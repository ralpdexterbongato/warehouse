@extends('layouts.Master')
@section('title')
  MRT | Signature request
@endsection
@section('body')
  <div class="myMRTSignatureRequest">
      <h1>My MRT <i class="fa fa-pencil"></i> Signature request</h1>
      <div id="mrt">
        <mrtrequesttable></mrtrequesttable>
      </div>
  </div>
  <script type="text/javascript" src="/js/mrt.js">
  </script>
@endsection
