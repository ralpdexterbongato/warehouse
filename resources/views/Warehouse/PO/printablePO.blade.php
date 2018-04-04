<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Print Purchase Order</title>
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
    /*CSS Start*/
    body
    {
      font-family: sans-serif;
      padding:60px 30px 10px 30px;
    }
    .top-titles
    {
      text-align: center;
      font-size: 17px;
    }
    .top-titles h5
    {
      margin-bottom: 2px;
    }
    .top-titles p
    {
      font-size: 15px;
    }
    .PO-big-title
    {
      margin-top: 20px;
    }
    .left-data
    {
      float: left;
      width: 45%;
      font-size: 14px;
    }
    .right-data
    {
      float: right;
      width: 45%;
      font-size: 14px;
    }
    .right-data ul
    {
      display: inline-block;
      float:right;
    }
    .left-data label,.right-data label
    {
      display: inline-block;
      width: 20%;
    }
    .left-data p
    {
      display: inline-block;
      border-bottom: 1px solid black;
      padding-top: 2px;
      width: 70%;
    }
    .right-data p
    {
      display: inline-block;
      border-bottom: 1px solid black;
      padding-top: 2px;
      width: 40%;
    }
    .invisible
    {
      color: #fff;
    }
    .PO-master-data
    {
      margin-top: 40px;
    }
    .data-wrap
    {
      height: 80px;
    }
    .po-statement-container
    {
     padding:3px;
     border:1px solid black;
     width: 100%;
     font-size: 14px;
    }
    .table-wrap
    {
      page-break-inside: avoid;
    }
    .table-po-details table
    {
      border:1px solid black;
      border-top: none;
      width: 100%;
      text-align: center;
      font-size: 14px;
    }
    .table-po-details th
    {
      padding:5px;
      border:1px solid black;
      border-top: none;
      font-size: 13px;
    }
    .table-po-details td
    {
      border-left:1px solid black;
      border-right: 1px solid black;
      padding:6px;
    }
    .total-amt-po td
    {
      border:1px solid black;
    }
    .RV-data
    {
      width: 500px;
      margin:0 auto;
      border:1px solid black;
      height: 15px;
      padding:3px;
      font-size: 15px;
      border-top: none;
    }
    .rvdate,.rvnum
    {
      display: inline-block;
    }
    .rvdate
    {
      float: right;
    }
    .rvnum
    {
      float:left;
    }
    .signatures
    {
      font-size: 14px;
      margin-top: 40px;
      height: 200px;
    }
    .signature-wrapper
    {
      page-break-inside:avoid;
    }
    .left-content-seller
    {
      width: 45%;
      float:left;
    }
    .right-content-gm
    {
      width: 45%;
      float:right;
    }
    .right-gm-wrap
    {
      float:right;
      display: inline-block;
    }
    .gm-signature
    {
      margin-top: 120px;
      text-align: center;
      width: 200px;
      position: relative;
      font-size: 12px;
    }
    .gm-signature h1 img
    {
      width: 100%;
    }
    .gm-signature h1
    {
      position: absolute;
      height: 50px;
      width: 150px;
      top: -60px;
      left: 25px;
    }
    .left-content-seller p
    {
      text-align: center;
      width: 200px;
    }
    .left-content-seller h4
    {
      width: 200px;
      border-bottom: 1px solid black;
      margin-top: 30px;
    }
    .left-content-seller h3
    {
      margin-top: 50px;
      width: 200px;
      border-bottom: 1px solid black;
    }
    .for
    {
      position:absolute;
      left: -10px;
      top:-40px;
    }
    </style>
  </head>
  <body>
    <div class="bondpaper-container">
      <div class="top-titles">
        <h5>BOHOL 1 ELECTRIC COOPERATIVE INC.</h5>
        <h5>CABULIJAN, TUBIGON, BOHOL</h5>
        <p>Tel# 508-9741 / 508-9731</p>
        <h5 class="PO-big-title">PURCHASE ORDER</h5>
      </div>
      <div class="PO-master-data">
        <div class="data-wrap">
          <div class="left-data">
            <ul>
              <li><label>TO:</label><p>{{$MasterPO[0]->Supplier}}</p></li>
              <li><label class="invisible">.</label><p>{{$MasterPO[0]->Address}}</p></li>
              <li><label class="invisible">.</label><p>Tel# {{$MasterPO[0]->Telephone}}</p></li>
            </ul>
          </div>
          <div class="right-data">
            <ul>
              <li><label>P.O. No.</label><p>{{$MasterPO[0]->PONo}}</p></li>
              <li><label>DATE:</label><p>{{$MasterPO[0]->PODate->format('M d,Y')}}</p></li>
              <li><label>TERMS:</label><p class="invisible">.</p></li>
              <li>( {{$MasterPO[0]->Purpose}} )</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="table-wrap">
        <div class="po-statement-container">
          Please furnish the following articles subject to all terms and conditions stated here and in accordance with the<br> quotation.
        </div>
        <div class="table-po-details">
          <table>
            <tr>
              <th>ITEM NO.</th>
              <th>QTY</th>
              <th>UNIT</th>
              <th>DESCRIPTION</th>
              <th>Unit Price</th>
              <th>AMOUNT</th>
            </tr>
            @foreach ($MasterPO[0]->PODetails as $key => $poDetails)
              <tr>
                <td>{{$key+1}}</td>
                <td>{{$poDetails->Qty}}</td>
                <td>{{$poDetails->Unit}}</td>
                <td>{{$poDetails->Description}}</td>
                <td>{{number_format($poDetails->Price,'2','.',',')}}</td>
                <td>{{number_format($poDetails->Amount,'2','.',',')}}</td>
              </tr>
            @endforeach
            <tr class="total-amt-po">
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>Total AMT</td>
              <td>{{number_format($Totalamt,'2','.',',')}}</td>
            </tr>
          </table>
        </div>
        <div class="rvdata-wrapper">
          <div class="RV-data">
            <p class="rvnum">APPROVED RV No. {{$MasterPO[0]->RVNo}}</p> <p class="rvdate">Dated: {{$MasterPO[0]->RVDate->format('M d, Y')}}</p>
          </div>
        </div>
      </div>
      <div class="signature-wrapper">
      <div class="signatures">
        <div class="left-content-seller">
          <label>ACCEPTED ORDER AND RECEIVED<br> ORIGINAL COPY OF THIS PURCHASE<br> ORDER:</label>
          <h3 class="invisible">,</h3>
          <p>( Seller )</p>
          <h4 class="invisible">,</h>
        </div>
        <div class="right-content-gm">
          <div class="right-gm-wrap">
            <label>ORDER ISSUED AND AUTHORIZED <br> BY:</label>
            <div class="gm-signature">
              @if ($MasterPO[0]->users[0]->pivot->Signature=='0')
                <h1><img src="c:/xampp/htdocs/warehouse/public/ForHerokuOnly/{{$MasterPO[0]->users[0]->Signature}}" alt="signature"></h1>
              @elseif (isset($MasterPO[0]->users[1])&&($MasterPO[0]->users[1]->pivot->Signature=='0'))
                <medium class="for">For :</medium><h1><img src="c:/xampp/htdocs/warehouse/public/ForHerokuOnly/{{$MasterPO[0]->users[1]->Signature}}" alt="signature"></h1>
              @endif
              <h4>{{$MasterPO[0]->users[0]->FullName}}</h4>
              <p>{{$MasterPO[0]->users[0]->Position}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </body>
</html>
