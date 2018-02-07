@extends('layouts.Master')
@section('title')
  MRT | Signature request
@endsection
@section('body')
  <div class="myMRTSignatureRequest">
      <h1><i class="material-icons">mode_edit</i> Signature request MRT</h1>
      <div id="mrt">
        <mrtrequesttable></mrtrequesttable>
      </div>
  </div>
  <script type="text/javascript" src="/js/mrt.js">
  </script>
@endsection
