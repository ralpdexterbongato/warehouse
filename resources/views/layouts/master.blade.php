<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="Developers" content="Group 2 IT MDC 2018">
    <link rel="icon" type="image/png" href="/DesignIMG/logo.png">
    <link rel="stylesheet" href="/css/mystyle.css">
    <link rel="stylesheet" href="/css/animate.min.css">
    <link rel="stylesheet" href="/css/bttn.min.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
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
      @else
        <div class="top-nav-container">
          <div class="left-nav-content">
            <h1><a href="/"><img src="/DesignIMG/logo.png" alt="logo"></a></h1>
          </div>
          <div class="right-nav-content">
            <div class="title-top">
              <p> Warehouse Inventory</p>
            </div>
          </div>
        </div>
      @endif
      @if (Auth::check())
      <div class="top-nav-boxes empty-left-btn">
        <ul>
          @if ((Auth::user()->Role!=0)&&(Auth::user()->Role!=2))
          <li class="dropping-parent">
            <h1>
              <span><i class="fa fa-plus"></i></span></i>
              <ul class="dropping">
                <a href="{{route('mirs.add')}}"><li class="{{current_page('MIRS-add')?'active':''}}"><i class="fa fa-leaf"></i> MIRS </li></a>
                <a href="{{route('Creating.RV')}}"><li class="{{current_page('RV-create')?'active':''}}"><i class="fa fa-leaf"></i> RV</li></a>
              </ul>
            </h1>
          </li>
          @endif
          <li class="dropping-parent">
            <h1>
              <span><i class="fa fa-search"></i></span>
              <ul class="dropping">
                <a href="{{route('MIRSgridview')}}"><li class="{{current_page('mirs-index-page')?'active':''}}"><i class="fa fa-th-large"></i> MIRS</li></a>
                <a href="{{route('indexMCT')}}"><li class="{{current_page('mct-index-page')?'active':''}}"><i class="fa fa-th-large"></i> MCT</li></a>
                <a href="{{route('MRTindexPageonly')}}"><li class="{{current_page('mrt-index-page')?'active':''}}"><i class="fa fa-th-large"></i> MRT</li></a>
                <a href="{{route('RVindexView')}}"><li class="{{current_page('RVindex')?'active':''}}"><i class="fa fa-th-large"></i> RV</li></a>
                <a href="{{route('RRindexview')}}"><li class="{{current_page('RR-index')?'active':''}}"><i class="fa fa-th-large"></i> RR</li></a>
                <a href="{{route('POIndexPage')}}"><li class="{{current_page('po-index-page')?'active':''}}"><i class="fa fa-th-large"></i> PO</li></a>
                <a href="{{route('MRIndexPage')}}"><li class="{{current_page('mr-index-page')?'active':''}}"><i class="fa fa-th-large"></i> MR</li></a>
              </ul>
            </h1>
          </li>
          @if (Auth::user()->Role==1||Auth::user()->Role==3||Auth::user()->Role==4)
            <li class="dropping-parent">
              <h1>
                <i class="fa fa-bar-chart"></i>
                <ul class="dropping">
                  <a href="{{route('summary.mrt')}}"><li class="{{current_page('summary-mrt')?'active':''}}"><i class="fa fa-bar-chart"></i> MRT</li></a>
                  <a href="{{route('mct-summary')}}"><li class="{{current_page('mct-summary')?'active':''}}"><i class="fa fa-bar-chart"></i> MCT</li></a>
                </ul>
              </h1>
            </li>
          @endif
        </ul>
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
