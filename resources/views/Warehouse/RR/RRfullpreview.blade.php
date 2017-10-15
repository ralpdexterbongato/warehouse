@extends('layouts.master')
@section('title')
  RR Preview
@endsection
@section('body')
  <div class="previewRR-Container" id="rr">
   <rrpreview :user="{{Auth::user()}}" :rrno="{{$RRNumber}}"></rrpreview>
  </div>
  <script type="text/javascript" src="/js/rr.js">
  </script>
@endsection
