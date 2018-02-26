@extends('layouts.master')
@section('title')
warehouse | BOHECO 1
@endsection
@section('body')
  <div class="body-container">
    <div class="sidebar-online" id="accounts">
      <statususers></statususers>
    </div>
    <div id="items">
      <itemhistorytable :user="{{Auth::user()}}"></itemhistorytable>
    </div>
  </div>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.3/Chart.min.js">
  </script>
  <script type="text/javascript" src="/js/item.js">
  </script>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
