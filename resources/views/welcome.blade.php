@extends('layouts.master')
@section('title')
warehouse | BOHECO 1
@endsection
@section('body')
  <div class="body-container">
    <div id="items">
      <itemhistorytable></itemhistorytable>
    </div>
  </div>
  <script type="text/javascript" src="/js/item.js">
  </script>
@endsection
