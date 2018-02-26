@extends('layouts.master')
@section('title')
warehouse | BOHECO 1
@endsection
@section('body')
  <div class="body-container">
    <div class="sidebar-online">
      <div class="list-wrapper">
        <ul>
          <li class="online-list-header"><i class="material-icons">account_circle</i><p>{{Auth::user()->FullName}}</p></li>
          <div class="search-user-container">
            <i class="material-icons">search</i>
            <input type="text" placeholder="Search">
          </div>
          <div class="online-scroll-container">
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">D</p><p class="userfullname">Anuber Zeshyrou</p></li>
            <li><p class="letter-avatar">Z</p><p class="userfullname">Ben Donald Walohan</p></li>
            <li><p class="letter-avatar">D</p><p class="userfullname">Kim Aljon Alqueza</p></li>
            <li><p class="letter-avatar">B</p><p class="userfullname">Bravo john linbert</p></li>
            <li><p class="letter-avatar">C</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">D</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">Z</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">D</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">B</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">C</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">D</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">Z</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">D</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">B</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">C</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
            <li><p class="letter-avatar">A</p><p class="userfullname">Ralp Dexter Bongato</p></li>
          </div>
        </ul>
      </div>
    </div>
    <div id="items">
      <itemhistorytable :user="{{Auth::user()}}"></itemhistorytable>
    </div>
  </div>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.3/Chart.min.js">
  </script>
  <script type="text/javascript" src="/js/item.js">
  </script>
@endsection
