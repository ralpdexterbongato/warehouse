<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Developer's" content="Ralp Dexter Bongato & Zeshrou Añuber">
    <link rel="icon" type="image/png" href="/DesignIMG/logo.png">
    <link rel="stylesheet" href="/css/mystyle.css">
    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/css/animate.css">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/bttn.css">
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = '/icons/css/font-awesome.min.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <title>@yield('title')</title>
    <script>
      window.Laravel = <?php echo json_encode([
          'csrfToken' => csrf_token()
      ]); ?>;
    </script>
  </head>
  <body>
    <header>
      <div class="top-nav-container">
        <div class="left-nav-content">
          @if (Auth::check())
            <button type="button" class="burger-button" name="button"><i class="fa fa-navicon"></i></button>
          @endif
          <h1><a href="/"><img src="/DesignIMG/logo.png" alt="logo"></a></h1>
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
              <li class="userinfo"><i class="fa fa-times"></i>
                <h3 class="account-image"></h3>
                <p class="name-of-user bold">{{Auth::user()->Fname}} {{Auth::user()->Lname}}</p>
                <p class="position">{{Auth::user()->Position}}</p>
                <a href="{{route('ViewMyHistory')}}"><h5><p>history</p></h5></a>
              </li>
              <a href="{{route('checkmyMIRSrequest')}}"><li><i class="fa fa-pencil"></i> MIRS signature</li></a>
              <a href="{{route('checkmyMCTrequest')}}"><li><i class="fa fa-pencil"></i> MCT signature</li></a>
              <a href="{{route('checkmyRRrequest')}}"><li><i class="fa fa-pencil"></i> RR signature</li></a>
              <a href="{{route('MyRVrequestlist')}}"><li><i class="fa fa-pencil"></i> RV signature</li></a>
              @if ((Auth::user()->Role==2))
                <a href="{{route('viewPOrequest')}}"><li><i class="fa fa-pencil"></i> PO signature</li></a>
              @endif
              @if ((Auth::user()->Role==2)||(Auth::user()->Role==0))
                <a href="{{route('myMR.signature.Request')}}"><li><i class="fa fa-pencil"></i> M.R. signature</li></a>
              @endif
              @if ((Auth::user()->Role==4)||(Auth::user()->Role==3))
                <a href="{{route('mirs-ready')}}"><li><i class="fa fa-check"></i> Ready for MCT list</li></a>
                <a href="{{route('pending-purchase-rv')}}"><li><i class="fa fa-hourglass"></i> Unpurchased RV list</li></a>
              @endif
              @if (Auth::user()->Role==1)
                <a href="{{route('Registration')}}"><li><i class="fa fa-user-plus"></i> Create Account</li></a>
                <a href="{{route('AccountsListGM')}}"><li><i class="fa fa-medium"></i> Manage accounts</li></a>
                <a href="{{route('view.request.gm-signature.replace')}}"><li><i class="fa fa-user-times"></i>Signatures in behalf</li></a>
              @endif
              <a><li onclick="$('.logoutform').submit()"><i class="fa fa-plug"> </i> Logout</li></a>
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
        @if (Session::has('SessionForStock'))
          $('.for-stock-Modal').addClass('active');
        @endif
      });
    </script>
  </body>
</html>
