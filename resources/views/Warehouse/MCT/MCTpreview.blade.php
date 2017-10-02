@extends('layouts.master')
@section('title')
  MCT|Preview
@endsection
@section('body')
  <div class="Preview-MCT-container" id="mct">
    <mctpreview :mctno="{{$MCTNo}}" :user="{{Auth::user()}}"></mctpreview>
  </div>
<script type="text/javascript" src="/js/mct.js">

</script>
@endsection
