@extends('layouts.master')
@section('title')
  M.R.|full Preview
@endsection
@section('body')
<div class="MR-full-container" id="mr">
<mrpreview :mrno="{{$MRNumber}}" :user="{{Auth::user()}}"></mrpreview>
</div>
<script type="text/javascript" src="/js/mr.js">

</script>
@endsection
