@extends('layouts.master')
@section('title')
  PO|Full Preview
@endsection
@section('body')
<div class="full-PO-container" id="po">
    <pofullpreview :pono="{{$PONumber}}" :user="{{Auth::user()}}"></pofullpreview>
</div>
<script type="text/javascript" src="/js/po.js">
</script>
@endsection
