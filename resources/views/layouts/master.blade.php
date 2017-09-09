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
              <li>Hello {{Auth::user()->Fname}} <i class="fa fa-times"></i></li>
              <a href="#"><li>MCT pending <span class="color-blue">4</span></li></a>
              <a href="#"><li>MRT pending <span class="color-blue">8</span> </li></a>
              <a href="#"><li>MIRS pending <span class="color-blue">1</span></li></a>
              @if (Auth::user()->Role==1)
                <a href="{{route('Registration')}}"><li>Create Account</li></a>
              @endif
              @if ((Auth::user()->Role==1)||(Auth::user()->Role==4))
                <a><li class="add-noexist">Add none existing item</li></a>
              @endif
              <a><li onclick="$('.logoutform').submit()">Logout</li></a>
              <form class="logoutform" action="{{route('Logging.out')}}" method="post">
                {{ csrf_field() }}
              </form>
            </ul>
          </div>
        </div>
      @endif
      <div class="add-new-modal">
        <div class="new-modal-box">
            <div class="new-modal-title">
              <h1>Create new item</h1>
            </div>
            @if (Auth::check())
              @if ((Auth::user()->Role==1)||(Auth::user()->Role==4))
                <div class="add-new-item-form">
                  <form class="form-new-item" action="{{route('store')}}" method="post">
                    {{ csrf_field() }}
                    <table>
                      <tr>
                        <th>Account code</th>
                        <td><input type="text" name="AccountCode" value="{{old('AccountCode')}}"></td>
                      </tr>
                      <tr>
                        <th>Item code</th>
                        <td><input type="text" name="ItemCode" value="{{old('ItemCode')}}"></td>
                      </tr>
                      <tr>
                        <th>Description</th>
                        <td><textarea name="Description" value="{{old('Description')}}"></textarea></td>
                      </tr>
                      <tr>
                        <th>Unit cost</th>
                        <td><input type="text" name="UnitCost" value="{{old('UnitCost')}}"></td>
                      </tr>
                      <tr>
                        <th>Unit</th>
                        <td><select name="Unit" value="{{old('Unit')}}">
                          <option value="PC">PC</option>
                          <option value="BOX">BOX</option>
                          <option value="DOZ">DOZ</option>
                          <option value="REAM">REAM</option>
                        </select></td>
                      </tr>
                      <tr>
                        <th>Quantity</th>
                        <td><input type="text" name="Quantity" value="{{old('Quantity')}}"></td>
                      </tr>
                    </table>
                    <div class="submit-bottons-newitem-container">
                      <div class="empty-submit">

                      </div>
                      <div class="submit-bottons-new">
                        <button id="cancel-btn" type="button">Cancel</button>
                        <button id="go-create" type="submit">Go</button>
                      </div>
                    </div>
                  </form>
                </div>
              @endif
            @endif
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
