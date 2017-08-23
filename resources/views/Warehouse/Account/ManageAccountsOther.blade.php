@extends('layouts.master')
@section('title')
  Settings|Admin
@endsection
@section('body')
  <div class="ManagerAccount-container">
    <div class="title-account-manager">
      <h1>Other accounts <i class="fa fa-group color-blue"></i></h1>
    </div>
    <div class="top-right-menu-accounts">
      <ul>
        <a href="{{route('AccountsListGM')}}"><li>General Managers</li></a>
        <a href="{{route('AccountlistManagers')}}"><li>Managers</li></a>
        <a href="{{route('Admin-list')}}"><li>Administrators</li></a>
        <a href="#"><li class="ActiveList">Other...</li></a>
      </ul>
    </div>
    <div id="accounts">
      <other></other>
    </div>
  </div>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
