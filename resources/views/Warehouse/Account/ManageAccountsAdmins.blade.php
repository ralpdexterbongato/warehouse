@extends('layouts.master')
@section('title')
  Settings|Admin
@endsection
@section('body')
  <div class="ManagerAccount-container">
    <div class="title-account-manager">
      <h1>List of Admin accounts <i class="fa fa-group color-blue"></i></h1>
    </div>
    <div class="top-right-menu-accounts">
      <ul>
        <a href="{{route('AccountsListGM')}}"><li>General Managers</li></a>
        <a href="{{route('AccountlistManagers')}}"><li>Managers</li></a>
        <a href="#"><li class="ActiveList">Administrators</li></a>
        <a href="{{route('otherAccounts')}}"><li>Other...</li></a>
      </ul>
    </div>
    <div id="accounts">
      <admin></admin>
    </div>
  </div>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
