@extends('layouts.master')
@section('title')
warehouse | BOHECO 1
@endsection
@section('body')
  <div class="body-container">
    <div id="items">
      <itemhistorytable :user="{{Auth::user()}}"></itemhistorytable>
    </div>
  </div>
  <script type="text/javascript" src="/js/item.js">
  </script>
@endsection
