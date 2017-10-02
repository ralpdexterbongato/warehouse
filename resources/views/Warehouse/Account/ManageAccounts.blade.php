@extends('layouts.master')
@section('title')
   Settings | account
@endsection
@section('body')
  <div class="ManagerAccount-container">
    <div id="accounts">
      <accountsettings></accountsettings>
    </div>
  </div>
  <script type="text/javascript" src="/js/AccountManagement.js">
  </script>
@endsection
