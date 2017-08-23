@extends('layouts.master')
@section('title')
   Settings|GM
@endsection
@section('body')
  <div class="ManagerAccount-container">
    <div class="title-account-manager">
      <h1>List of General Managers <i class="fa fa-group color-blue"></i></h1>
    </div>
    <div class="top-right-menu-accounts">
      <ul>
        <a href="#"><li class="ActiveList">General Managers</li></a>
        <a href="{{route('AccountlistManagers')}}"><li>Managers</li></a>
        <a href="{{route('Admin-list')}}"><li>Administrators</li></a>
        <a href="{{route('otherAccounts')}}"><li>Other...</li></a>
      </ul>
    </div>
    <div id="accounts">
      <generalmanagers></generalmanagers>
    </div>
  </div>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
