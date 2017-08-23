@extends('layouts.master')
@section('title')
  MIRS | Create
@endsection

@section('body')
  <div class="MIRS-CONTAINER">
    <div id="mirs">
      <mirscreate :managers="{{$allManager}}" :gm="{{$GenMan}}"></mirscreate>
    </div>
  </div>
  <script type="text/javascript" src="/js/mirs.js">
  </script>
@endsection
