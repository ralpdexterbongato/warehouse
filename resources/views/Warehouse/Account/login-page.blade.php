@extends('layouts.master')
@section('title')
  Login|User or Admin
@endsection
@section('body')
  <div class="login-container">
    <div class="body-login-container">
      <div class="box-form-login">
        <h1>Login <i class="fa fa-lock"></i></h1>
        <form class="login-form" action="{{route('login-submit')}}" method="post">
          {{ csrf_field() }}
          <input type="text" autocomplete="off" name="Username" placeholder="Username">
          <input type="password" name="Password" placeholder="Password">
          <button type="submit" name="button">Login </button>
        </form>
      </div>
    </div>
  </div>
@endsection
