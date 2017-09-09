@extends('layouts.master')
@section('title')
  Registration
@endsection
@section('body')
  <div class="Registration-container">
    <div class="registration-form">
      <form class="form-user-info" action="{{route('StoreRegister')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <h1>New user</h1>
        <div class="straight-form">
          <input type="text" name="Fname" placeholder="First Name">
          <input type="text" name="Lname" placeholder="Last Name">
          <select name="Position">
            <option value="">Choose position</option>
            <option value="Admin">Admin</option>
            <option value="ISD Manager">ISD Manager</option>
            <option value="DSD Manager">DSD Manager</option>
            <option value="ESD Manager">ESD Manager</option>
            <option value="SEEAD Manager">SEEAD Manager</option>
            <option value="SEEAD Manager">PGD Manager</option>
            <option value="CHEIF/IT CORPLAN">CHEIF/IT CORPLAN</option>
            <option value="General Manager">General Manager</option>
            <option value="Senior Auditor">Senior Auditor</option>
            <option value="HEAD-Warehouse Section">HEAD-Warehouse Section</option>
          </select>
          <select name="Role">
            <option value="">Choose role</option>
            <option value="0">Manager</option>
            <option value="1">Admin</option>
            <option value="2">GM</option>
            <option value="4">WarehouseHead</option>
            <option value="5">Auditor</option>
          </select>
          <input type="text" name="Username"placeholder="Username">
          <input type="password" name="Password" placeholder="Password">
          <input type="password" name="Password_confirmation" placeholder="Confirm-password">
          <input type="file" name="Signature" accept="image/PNG">
          <button type="submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
@endsection
