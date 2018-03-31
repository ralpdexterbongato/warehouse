<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3367D6" />
    <link rel="icon" type="image/png" href="/DesignIMG/logo.png">
    <link rel="stylesheet" href="/css/mystyle.min.css">
    <link rel="stylesheet" href="/css/animate.min.css">
    <link rel="stylesheet" href="/css/bttn.min.css">
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
      <div class="top-nav-boxes empty-left-btn">
        <ul id="masterGN">
          <li class="dropping-parent">
            <a href="/">
              <h1 class="waves-effect waves-light">
                <i class="material-icons">home</i></i>
              </h1>
            </a>
          </li>
          @if ((Auth::user()->Role!=0)&&(Auth::user()->Role!=2))
          <li class="dropping-parent drop-add">
            <h1 class="waves-effect waves-light">
              <i class="material-icons">add</i></i>
            </h1>
            <ul class="dropping">
              <a href="{{route('mirs.add')}}"><li class="{{current_page('MIRS-add')?'active':''}}"><i class="material-icons">fiber_new</i> MIRS </li></a>
              <a href="{{route('Creating.RV')}}"><li class="{{current_page('RV-create')?'active':''}}"><i class="material-icons">fiber_new</i> RV</li></a>
            </ul>
          </li>
          @endif
          <li class="dropping-parent drop-index">
            <h1 class="waves-effect waves-light">
              <i class="material-icons">search</i>
            </h1>
            <ul class="dropping">
              <div class="dropping-scroller">
                <a href="{{route('MIRSgridview')}}"><li class="{{current_page('mirs-index-page')?'active':''}}"><i class="material-icons">content_paste</i> MIRS</li></a>
                <a href="{{route('indexMCT')}}"><li class="{{current_page('mct-index-page')?'active':''}}"><i class="material-icons">content_paste</i> MCT</li></a>
                <a href="{{route('MRTindexPageonly')}}"><li class="{{current_page('mrt-index-page')?'active':''}}"><i class="material-icons">content_paste</i> MRT</li></a>
                <a href="{{route('RVindexView')}}"><li class="{{current_page('RVindex')?'active':''}}"><i class="material-icons">content_paste</i> RV</li></a>
                <a href="{{route('RRindexview')}}"><li class="{{current_page('RR-index')?'active':''}}"><i class="material-icons">content_paste</i> RR</li></a>
                <a href="{{route('POIndexPage')}}"><li class="{{current_page('po-index-page')?'active':''}}"><i class="material-icons">content_paste</i> PO</li></a>
                <a href="{{route('MRIndexPage')}}"><li class="{{current_page('mr-index-page')?'active':''}}"><i class="material-icons">content_paste</i> MR</li></a>
              </div>
            </ul>
          </li>
          @if (Auth::user()->Role==1||Auth::user()->Role==3||Auth::user()->Role==4)
            <li class="dropping-parent drop-summary">
              <h1 class="waves-effect waves-light">
                <i class="material-icons">equalizer</i>
              </h1>
              <ul class="dropping">
                <a href="{{route('summary.mrt')}}"><li class="{{current_page('summary-mrt')?'active':''}}"><i class="material-icons">equalizer</i></i> MRT</li></a>
                <a href="{{route('mct-summary')}}"><li class="{{current_page('mct-summary')?'active':''}}"><i class="material-icons">equalizer</i></i> MCT</li></a>
              </ul>
            </li>
          @endif
          <globalnotification :user="{{Auth::user()}}"></globalnotification>
        </ul>
      </div>
      @endif
    </header>
        <div class="main-master-container" class="{{Auth::check()?'':'loggedout'}}">
          @if (Auth::check())
          <span id="master">
            <mynotification :user="{{Auth::user()}}">
            </mynotification>
          </span>
          @endif
          @section('body')
          @show
        </div>
    <footer>
      @Auth
      <div class="footer-container">
        <div class="simple-footer">
          <h1> &copy; Warehouse inventory control</h1>
        </div>
      </div>
      @endAuth
    </footer>
    @if (Auth::check())
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
    @endif
    <script type="text/javascript" src="/js/jquery.js">
    </script>
    <script type="text/javascript" src="/js/materialize.js">
    </script>
    @if (Auth::check())
      <script type="text/javascript" src="/js/master.js">
      </script>
      <script type="text/javascript" src="/js/masterGN.js">
      </script>
      <script type="text/javascript">
        $(document).ready(function()
        {
          $('.burger-button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.Account-modal').click(function(event)
          {
            $('body').removeClass('noScroll');
          });

          $('.modal-find-button button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.modal-search-item').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('#forstock-ItemRV').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.for-stock-Modal').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('#none-existing-itemRV').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.add-RV-item-modal').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('.button-find-item-container button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.mct-modal-ofItems').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('.pick-from-items button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.mrt-items-modal').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('.add-supplier-canvass button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.modal-canvass').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('.update-canvas-opener').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.modal-canvass').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('#opener-icon').click(function(event)
          {
            $('body').toggleClass('noScroll');
          });

          $('.btn-add-item-rrnopo button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.modal-rr-no-po').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('.add-item-rr-w-pobtn button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.rr-with-po-modal').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('.addfromrr-btn button').click(function(event)
          {
            $('body').addClass('noScroll');
          });
          $('.items-table-from-RR').click(function(event)
          {
            $('body').removeClass('noScroll');
          }).children().click(function(event) {
            return false;
          });

          $('.drop-add').click(function(event) {
            $('.drop-add .dropping').toggle();
            $('.drop-index .dropping').hide();
            $('.drop-summary .dropping').hide();
          });
          $('.drop-index').click(function(event) {
            $('.drop-index .dropping').toggle();
            $('.drop-summary .dropping').hide();
            $('.drop-add .dropping').hide();
          });

          $('.drop-summary').click(function(event) {
              $('.drop-summary .dropping').toggle();
              $('.drop-index .dropping').hide();
              $('.drop-add .dropping').hide();
          });
          $(window).scroll(function() {
            var scrollBottom = $(document).height() - $(window).height() - $(window).scrollTop();
            if (scrollBottom >= 36)
            {
              $('.sidebar-online ul').removeClass('active');
              $('.simple-footer').removeClass('fixed');
            }else
            {
              $('.simple-footer').addClass('fixed');
            }
          });

          $(window).scroll(function() {
            var spacetop =$(window).scrollTop();
            if (spacetop >= 500)
            {
              $('.side-user-stats').addClass('active');
              $('.big-user-center-wrap').addClass('active');
            }else
            {
              $('.side-user-stats').removeClass('active');
              $('.big-user-center-wrap').removeClass('active');

            }
          });
        });

      </script>
    @endif
  </body>
</html>
