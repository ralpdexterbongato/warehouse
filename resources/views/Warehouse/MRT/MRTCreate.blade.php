@extends('layouts.master')
@section('title')
 MRT |form
@endsection
@section('body')
<div class="MRT-form-bigcontainer" id="mrt">
  <mrtcreate :mctdata="{{$MCTdata}}" :mctno="{{$MCTNumber}}"></mrtcreate>
</div>
<script type="text/javascript" src="/js/mrt.js">
</script>
@endsection
