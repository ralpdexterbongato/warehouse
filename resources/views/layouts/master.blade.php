<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        @if (isset($itemMasters))
          $('.modal-search-item').addClass('active');
        @endif

        @if (!empty($MIRSMaster[0]->Status))
          $('.status-mirs').addClass('approved');
        @endif
      });
    </script>
  </body>
</html>
