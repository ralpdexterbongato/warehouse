<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>MCT Summary print</title>
  </head>
  <style media="all">
   /*CSSRESTART*/
   html, body, div, span, applet, object, iframe,
   h1, h2, h3, h4, h5, h6, p, blockquote, pre,
   a, abbr, acronym, address, big, cite, code,
   del, dfn, em, img, ins, kbd, q, s, samp,
   small, strike, strong, sub, sup, tt, var,
   b, u, i, center,
   dl, dt, dd, ol, ul, li,
   fieldset, form, label, legend,
   table, caption, tbody, tfoot, thead, tr, th, td,
   article, aside, canvas, details, embed,
   figure, figcaption, footer, header, hgroup,
   menu, nav, output, ruby, section, summary,
   time, mark, audio, video {
     margin: 0;
     padding: 0;
     border: 0;
     vertical-align: baseline;
   }
   /* HTML5 display-role reset for older browsers */
   article, aside, details, figcaption, figure,
   footer, header, hgroup, menu, nav, section {
     display: block;
   }
   body {
     line-height: 1;
     padding:30px;
   }
   ol, ul {
     list-style: none;
   }
   blockquote, q {
     quotes: none;
   }
   blockquote:before, blockquote:after,
   q:before, q:after {
     content: '';
     content: none;
   }
   table
   {
     border-collapse: collapse;
   }
  /* CSS Start*/
  .bondpaper-container
  {
    font-family: sans-serif;
    font-size: 15px;
  }
  .bondpaper-container h1
  {
    width: 59px;
    display: inline-block;
    float: left;
  }
  .bondpaper-container img
  {
    width: 100%;
  }
  .bondpaper-container .head-data
  {
    display: inline-block;
  }
  .SCtitle
  {
    margin-top:20px;
  }
  .table-container
  {
    margin-top: 20px;
  }
  .table-container table
  {
    width: 100%;
  }
  .table-container th
  {
    font-weight: 100;
    border:1px solid black;
    text-align: center;
  }
  .table-container td
  {
    text-align: center;
    padding:3px;
    font-size: 13px;
  }
  .align-left
  {
    text-align: left;
  }
  </style>
  <body>
    <div class="bondpaper-container">
      <h1><img src="c:/xampp/htdocs/warehouse/DesignIMG/logo.png" alt="logo"></h1>
      <div class="head-data">
        <p>BOHOL 1 ELECTRIC COOPERATIVE, INC.</p>
        <p>Cabulijan, Tubigon, Bohol</p>
        <p class="SCtitle">Summary of charges (as of {{$ForDisplay[0][0]->MTDate->format('M Y')}})</p>
      </div>
      <div class="table-container">
        <table>
          <tr>
            <th>Account</th>
            <th>ItemCode</th>
            <th>Description</th>
            <th>UnitCost</th>
            <th>Unit</th>
            <th>Stock</th>
            <th>Month of {{$ForDisplay[0][0]->MTDate->format('M')}}</th>
          </tr>
          <tr>
            <th>Code</th>
            <th></th>
            <th></th>
            <th>({{$ForDisplay[0][0]->MTDate->format('M')}})</th>
            <th></th>
            <th>Balance</th>
            <th>Issued</th>
          </tr>
          @foreach ($ForDisplay as $summary)
          <tr>
            <td>{{$summary[0]->AccountCode}}</td>
            <td>{{$summary[0]->ItemCode}}</td>
            <td class="align-left">{{$summary[0]->MasterItems->Description}}</td>
            <td>{{number_format($summary[1]->UnitCost,'2','.',',')}}</td>
            <td>{{$summary[0]->MasterItems->Unit}}</td>
            <td>{{$summary[0]->CurrentQuantity}}</td>
            <td>{{$summary[1]->totalissued}}</td>
          </tr>
          @endforeach

        </table>
      </div>
    </div>
  </body>
</html>
