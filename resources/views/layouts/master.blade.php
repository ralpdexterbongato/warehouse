<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Developer's" content="Ralp Dexter Bongato & Zeshrou Añuber">
    <link rel="icon" type="image/png" href="DesignIMG/logo.png">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/css/mystyle.css">
    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/icons/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/animate.css">
  </head>
  <body>
    <header>
      <div class="top-nav-container">
        <div class="left-nav-content">
          @if (Auth::check())
            <button type="button" class="burger-button" name="button"><i class="fa fa-bars"></i></button>
          @endif
          <a href="/"><img class="animated rollIn" src="/DesignIMG/logo.png" alt="logo"></a>
        </div>
        <div class="right-nav-content">
          <div class="title-top">
            <h1 class="animated pulse">Warehouse System</h1>
          </div>
        </div>
      </div>
      <div class="modal-for-all-notice">
        <div class="center-notice-div">
          <ul>
            <li class="notice-title"><h1>Notice</h1> <i class="fa fa-times"></i></li>
            <li>{{Session::get('message')}}</li>
            @foreach ($errors->all() as $error)
              <li>{{$error}}</li>
            @endforeach
          </ul>
        </div>
      </div>
      @if (Auth::check())
        <div class="Account-modal">
          <div class="middle-account-modal">
            <ul>
              <li class="userinfo">{{Auth::user()->Fname}} {{Auth::user()->Lname}}<i class="fa fa-times"></i></li>
              <a href="{{route('checkmyMIRSrequest')}}"><li><i class="fa fa-pencil"></i> MIRS signature request</li></a>
              <a href="{{route('checkmyMCTrequest')}}"><li><i class="fa fa-pencil"></i> MCT signature request</li></a>
              <a href="{{route('checkmyRRrequest')}}"><li><i class="fa fa-pencil"></i> RR signature request</li></a>
              <a href="{{route('MyRVrequestlist')}}"><li><i class="fa fa-pencil"></i> RV signature request</li></a>
              @if (Auth::user()->Role==4)
                <a href="{{route('mirs-ready')}}"><li><i class="fa fa-check"></i> Ready for MCT list</li></a>
              @endif
              @if (Auth::user()->Role==1)
                <a href="{{route('Registration')}}"><li><i class="fa fa-user"></i> Create Account</li></a>
              @endif
              <a><li onclick="$('.logoutform').submit()"><i class="fa fa-sign-out"> </i> Logout</li></a>
              <form class="logoutform" action="{{route('Logging.out')}}" method="post">
                {{ csrf_field() }}
              </form>
            </ul>
          </div>
        </div>
      @endif
    </header>
      @section('body')
      @show
    <footer>
      <div class="footer-container">
        <div class="simple-footer">
          <h1>&copy; 2017 All rights reserved BOHECO 1</h1>
        </div>
      </div>
    </footer>
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/myjquery.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        @if ((count($errors)>0)||(Session::has('message')))
          $('.modal-for-all-notice').addClass('active');
        @endif
        @if (Session::has('itemMasters'))
          $('.modal-search-item').addClass('active');
        @endif
        @if (Session::has('itemMastersRR'))
          $('.search-itemRR-Container').addClass('active');
        @endif

      });
    </script>
  </body>
</html>
