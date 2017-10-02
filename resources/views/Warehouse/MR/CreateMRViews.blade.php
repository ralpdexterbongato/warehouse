@extends('layouts.master')
@section('title')
M.R. create
@endsection
@section('body')
  <span id="mr">
    <mrcreate :rritems="{{$RRItemsdetail}}" :allmanager="{{$allmanager}}" :allactive="{{$AllActiveUsers}}"></mrcreate>
  </span>
  <script type="text/javascript" src="/js/MR.js">
  </script>
@endsection
