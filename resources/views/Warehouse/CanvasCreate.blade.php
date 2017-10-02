@extends('layouts.master')
@section('title')
  Canvass |Create
@endsection
@section('body')
  <div id="canvass">
      <canvasscreate :rvno="{{$checkifpurchased[0]}}">
      </canvasscreate>
  </div>
  <script type="text/javascript" src="/js/canvass.js">
  </script>
@endsection
