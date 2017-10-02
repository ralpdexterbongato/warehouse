@extends('layouts.master')
@section('title')
  Print preview| MIRS
@endsection
@section('body')
  <div class="printable-view-container" id="mirs">
    <mirspreview :mirsno="{{$MIRSNumber}}" :user="{{Auth::user()}}"></mirspreview>
  </div>
  <script type="text/javascript" src="/js/mirs.js">
  </script>
@endsection
