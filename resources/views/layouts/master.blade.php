<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/css/mystyle.css">
    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/icons/css/font-awesome.min.css">


  </head>
  <body>
    <header>
      <div class="top-nav-container">
        <div class="left-nav-content">
          <a href="/"><img src="/DesignIMG/logo.png" alt="logo"></a>
        </div>

        <div class="right-nav-content">
          <div class="title-top">
            <h1>Warehouse System</h1>
          </div>
        </div>
      </div>
      <div class="modal-for-all-notice">
        <div class="center-notice-div">
          <ul>
            <li class="notice-title"><h1>Notice</h1> <i class="fa fa-times"></i></li>

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
    <script type="text/javascript" src="/js/jquery.js">

    </script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('.add-new-item button').click(function(event) {
          $('.add-new-modal').addClass('active');
        });

        $('#cancel-btn').click(function(event) {
          $('.add-new-modal').removeClass('active');
        });

        @if (count($errors)>0)
          $('.modal-for-all-notice').addClass('active');
        @endif

        $('.notice-title i').click(function(event) {
          $('.modal-for-all-notice').removeClass('active');
        });

        $('.modal-find-button button').click(function(event) {
          $('.modal-search-item').addClass('active');
        });

        $('.middle-modal-search > h5').click(function(event) {
          $('.modal-search-item').removeClass('active');
        });

        @if (isset($itemMasters))
          $('.modal-search-item').addClass('active');
        @endif
      });
    </script>
  </body>
</html>
