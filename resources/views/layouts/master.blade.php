<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="Developers" content="Group 2 IT MDC 2018">
    <meta name="theme-color" content="#3367D6" />
    <link rel="icon" type="image/png" href="/DesignIMG/logo.png">
    <link rel="stylesheet" href="/css/mystyle.css">
    <link rel="stylesheet" href="/css/animate.min.css">
    <link rel="stylesheet" href="/css/bttn.min.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Comfortaa|Kaushan+Script" rel="stylesheet">
    <title>@yield('title')</title>
    <script>
      window.Laravel = <?php echo json_encode([
          'csrfToken' => csrf_token()
      ]); ?>;
    </script>
  </head>
  <body>
    @php
      function current_page($uri)
      {
        return strstr(request()->path(),$uri);
      }
    @endphp
    <header>
      @if (Auth::check())
        <span id="master">
          <mynotification :user="{{Auth::user()}}">
          </mynotification>
        </span>
      @endif
      @if (Auth::check())
      <div class="top-nav-boxes empty-left-btn">
        <ul>
          @if ((Auth::user()->Role!=0)&&(Auth::user()->Role!=2))
          <li class="dropping-parent">
            <h1>
              <span><i class="material-icons">add</i></span></i>
              <ul class="dropping">
                <a href="{{route('mirs.add')}}"><li class="{{current_page('MIRS-add')?'active':''}}"><i class="material-icons">fiber_new</i> MIRS </li></a>
                <a href="{{route('Creating.RV')}}"><li class="{{current_page('RV-create')?'active':''}}"><i class="material-icons">fiber_new</i> RV</li></a>
              </ul>
            </h1>
          </li>
          @endif
          <li class="dropping-parent">
            <h1>
              <span><i class="material-icons">search</i></span>
              <ul class="dropping">
                <a href="{{route('MIRSgridview')}}"><li class="{{current_page('mirs-index-page')?'active':''}}"><i class="material-icons">show_chart</i> MIRS</li></a>
                <a href="{{route('indexMCT')}}"><li class="{{current_page('mct-index-page')?'active':''}}"><i class="material-icons">show_chart</i> MCT</li></a>
                <a href="{{route('MRTindexPageonly')}}"><li class="{{current_page('mrt-index-page')?'active':''}}"><i class="material-icons">show_chart</i> MRT</li></a>
                <a href="{{route('RVindexView')}}"><li class="{{current_page('RVindex')?'active':''}}"><i class="material-icons">show_chart</i> RV</li></a>
                <a href="{{route('RRindexview')}}"><li class="{{current_page('RR-index')?'active':''}}"><i class="material-icons">show_chart</i> RR</li></a>
                <a href="{{route('POIndexPage')}}"><li class="{{current_page('po-index-page')?'active':''}}"><i class="material-icons">show_chart</i> PO</li></a>
                <a href="{{route('MRIndexPage')}}"><li class="{{current_page('mr-index-page')?'active':''}}"><i class="material-icons">show_chart</i> MR</li></a>
              </ul>
            </h1>
          </li>
          @if (Auth::user()->Role==1||Auth::user()->Role==3||Auth::user()->Role==4)
            <li class="dropping-parent">
              <h1>
                <i class="material-icons">equalizer</i>
                <ul class="dropping">
                  <a href="{{route('summary.mrt')}}"><li class="{{current_page('summary-mrt')?'active':''}}"><i class="material-icons">equalizer</i></i> MRT</li></a>
                  <a href="{{route('mct-summary')}}"><li class="{{current_page('mct-summary')?'active':''}}"><i class="material-icons">equalizer</i></i> MCT</li></a>
                </ul>
              </h1>
            </li>
          @endif
        </ul>
      </div>
      @endif
    </header>
        <div class="main-master-container">
          @section('body')
          @show
        </div>
    <footer>
      @Auth
      <div class="footer-container">
        <div class="simple-footer">
          <h1>&copy; 2017 All rights reserved BOHECO 1</h1>
        </div>
      </div>
      @endAuth
    </footer>
    @if (Auth::check())
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
    @endif
    <script type="text/javascript" src="/js/myjquery.js"></script>
    <script type="text/javascript">
    </script>
    @if (Auth::check())
      <script type="text/javascript" src="/js/master.js">
      </script>
    @endif
  </body>
</html>
