@extends('layouts.Master')
@section('title')
  Settings|Managers
@endsection
@section('body')
  <div class="ManagerAccount-container">
    <div class="title-account-manager">
      <h1>List of Managers <i class="fa fa-group color-blue"></i></h1>
    </div>
    <div class="top-right-menu-accounts">
      <ul>
        <a href="{{route('AccountsListGM')}}"><li>General Managers</li></a>
        <a href="#"><li class="ActiveList">Managers</li></a>
        <a href="{{route('Admin-list')}}"><li>Administrators</li></a>
        <a href="{{route('otherAccounts')}}"><li>Other...</li></a>
      </ul>
    </div>
    <div id="accounts">
      <managers></managers>
    </div>
  </div>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
