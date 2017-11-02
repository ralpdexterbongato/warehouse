@extends('layouts.master')
@section('title')
History|{{Auth::user()->FullName}}
@endsection
@section('body')
  <div class="histories-container" id="accounts">
  <history :user="{{ json_encode(Auth::user()) }}" :activenames="{{$ActiveNames}}"></history>
  </div>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
