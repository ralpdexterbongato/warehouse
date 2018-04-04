<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>MRT SUMMARY</title>
    <style media="all">
    /*RESTART CSS*/

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

    /*CSS*/
    .bondpaper-container
    {
      font-family:sans-serif;
    }
    body
    {
      padding: 100px 60px 10px 60px;
    }
    .header-mrt-pdf
    {
      width: 100%;
    }
    .header-mrt-content
    {
      text-align: center;
      font-size: 14px;
      margin-bottom: 35px;
    }
    .header-mrt-content h4
    {
      font-weight: 200;
    }
    .header-mrt-content h4 img
    {
      position: absolute;
      width: 70px;
      height: 70px;
      left: 130px;
      top: -20px;
    }
    .recieverName
    {
      font-size: 14px;
      font-weight: 200;
      text-align: center;
      margin-left: 70px;
      width:30%;
      margin-top: 20px;
      position: relative;
    }
    .address-mrt
    {
        font-weight:100;
      margin-bottom: 10px;
    }
    .boheco-big-title
    {
      position:relative;
      margin-bottom: 2px;
    }
    .table-mrt-pdf
    {
      margin-bottom: 50px;
    }
    .table-mrt-pdf table
    {
      width: 100%;
    }
    .table-mrt-pdf th
    {
      text-align:center;
      padding:3px 12px;
      border:1px solid black;
      font-size: 13px;
    }
    .table-mrt-pdf td
    {
      text-align: left;
      padding:3px 6px;
      font-size: 13px;
    }
    .align-right
    {
      text-align: right!important;
    }
      .recieverName h1 img
      {
        width: 100%;
      }
    .recieverName h1
    {
      width: 150px;
      height: 50px;
      position: absolute;
      top: -70px;
      left: 30px;
    }
    .name
    {
      text-decoration: underline;
      margin-bottom: 3px;
    }
    .receiver-container label
    {
      font-size: 14px;
    }
    .receiver-container
    {
      page-break-inside:avoid;
      margin-top: 100px;
    }
    </style>
  </head>
  <body>
    <div class="bondpaper-container">
      <div class="header-mrt-pdf">
        <div class="header-mrt-content">
          <h4 class="boheco-big-title"><img src="c:/xampp/htdocs/warehouse/public/DesignIMG/logo.png" alt="logo"> BOHOL I ELECTRIC COOPERATIVE, INC.</h4>
          <h4 class="address-mrt">Cabulijan, Tubigon, Bohol</h4>
          <h4 class="boheco-big-title">SUMMARY OF MATERIAL RETURN TICKET</h4>
          <h4>(MONTH OF {{$MaterialDate->format('M, Y')}})</h4>
        </div>
      </div>
      <div class="table-mrt-pdf">
        <table>
          <tr>
            <th>Item Code</th>
            <th>Description</th>
            <th>Unit</th>
            <th>Summary</th>
          </tr>
          @if (!empty($itemsummary[0]))
            @foreach ($itemsummary as $item)
              <tr>
                <td>{{$item->ItemCode}}</td>
                <td>{{$item->MasterItems->Description}}</td>
                <td>{{$item->MasterItems->Unit}}</td>
                <td class="align-right">{{$item->totalQty}}</td>
              </tr>
            @endforeach
          @endif
        </table>
      </div>
      <div class="receiver-container">
        <label>Received By:</label>
        <div class="recieverName">
          <h1><img src="c:/xampp/htdocs/warehouse/public/ForHerokuOnly/{{$WarehouseMan[0]->Signature}}" alt="signature"></h1>
          @if (isset($WarehouseMan[0]))
          <p class="name">
              {{$WarehouseMan[0]->FullName}}
          </p>
          <p>{{$WarehouseMan[0]->Position}}</p>
          @endif
        </div>
      </div>
    </div>
  </body>
</html>
