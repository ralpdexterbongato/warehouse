@extends('layouts.master')
@section('title')
  RV|Full preview
@endsection
@section('body')
  <div class="fullRV-container" id="rv">
    <rvpreview :rvno="{{$RVNumber}}" :user="{{Auth::user()}}"></rvpreview>
  </div>
  <script src="/js/rv.js">
  </script>
@endsection
