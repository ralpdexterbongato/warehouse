@extends('layouts.master')
@section('title')
  MRT Viewer
@endsection
@section('body')
  <div class="MRT-viewer-container" id="mrt">
    <mrtpreview :mrtno="{{$MRTNo}}" :user="{{Auth::user()}}"></mrtpreview>
  </div>
  <script type="text/javascript" src="/js/mrt.js">

  </script>
@endsection
