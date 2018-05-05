@extends('layouts.master')
@section('title')
warehouse | BOHECO 1
@endsection
@section('body')
  <div class="body-container">
    <div class="status-user-blade" id="accounts">
      <statususers></statususers>
    </div>
    <div id="items">
      <itemhistorytable :user="{{Auth::user()}}"></itemhistorytable>
    </div>
  </div>
  <script type="text/javascript" src="/js/Chart.bundle.min.js">
  </script>
  <script type="text/javascript" src="/js/item.js">
  </script>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
