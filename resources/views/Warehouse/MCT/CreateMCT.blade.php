@extends('layouts.master')
@section('title')
  MCT | recording
@endsection
@section('body')
  <div class="create-mct-container" id="mct">
    <createmct :mirsno="{{$MIRSNumber}}" :purpose="{{$MIRSMasterPurpose[0]}}"></createmct>
  </div>
  <script type="text/javascript" src="/js/mct.js">
  </script>
@endsection
