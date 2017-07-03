<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>print mct</title>
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
    /*MCT pdf css*/
    .mct-titles
    {
      margin:0 auto;
      text-align: center;
      font-family: sans-serif;
      position: relative;
    }
    .mct-titles h2
    {
      font-size: 13px;
    }
    .mct-titles h1
    {
      font-size: 15px;
      margin-top: 20px;
    }
    .mct-print-container
    {
      padding:90px 20px 5px 20px;
    }
    .mct-titles img
    {
        position: absolute;
        left: 180px;
        top: -20px;
        width: 70px;
    }
    .title-container
    {
      height: 90px;
    }
    .mctmaster-left
    {
      display: inline-block;
      float: left;
    }
    .mctmaster-right
    {
      display: inline-block;
      float: right;
    }
    .mctmaster-left ul li label,.mctmaster-right ul li label
    {
      width: 100px;
      display: inline-block;
      font-family: sans-serif;
      font-size: 14px;
      padding-top: 3px;
    }
    .mctmaster-left ul li h3,.mctmaster-right ul li h3
    {
      display: inline-block;
      font-family: sans-serif;
      font-size: 14px;
      font-weight: 400;
      padding-top: 3px;
    }
    .mctmaster-top-container
    {
      height: 60px;
    }
    .mct-table table
    {
      width: 100%;
    }
    .mct-table table th
    {
     font-size: 15px;
     font-family:helvetica;
     border: 1px solid black;
     text-align: center;
     font-weight: 400;
    }
    .mct-table table td
    {
      text-align: center;
      padding-top: 4px;
      font-family: sans-serif;
      font-size: 13px;
    }
    .align-left
    {
      text-align: left!important;
    }
    .mct-table-container
    {
      height: 400px;
    }
    .account-codes-present
    {
      width:100%;
      height: 175px;
    }
    .account-codes-present ul li label,.account-codes-present ul li h5
    {
      display: inline-block;
      font-weight: 400;
      font-family: sans-serif;
      font-size: 14px;
    }
    .account-codes-present ul li h5
    {
      margin-left: 30px;
    }
    .acc-code-container
    {
      height:170px;
    }
    .signature-left
    {
      display: inline-block;
      float: left;
      width: 40%;
    }
    .signature-right
    {
      display: inline-block;
      float: right;
      width: 40%;
    }
    .signatures-wrapper
    {
      height: 120px;
      width: 100%;
    }
    .recieve-name,.issued-name
    {
      text-align: center;
      font-size: 14px;
      font-weight: 400;
      font-family: sans-serif;
      margin-top: 40px;
      position: relative;
    }
    .issued-label,.recieve-label
    {
      font-family: sans-serif;
      font-size: 14px;
      font-weight: 200;
      position: relative;
    }
    .signature-reciever img,.signature-issued img
    {
      width: 100%;
    }
    .signature-reciever,.signature-issued
    {
      width: 150px;
      height: 50px;
      position: absolute;
      top:-49px;
      left:90px;
    }
    .total-amt
    {
      position: absolute;
      top:-30px;
    }
    .total-amt h3
    {
      float: left;
      border: 1px solid black;
      display: inline-block;
      font-size: 14px;
      height: 20px;
      width: 100px;
      font-weight: 200;
      text-align: center;
      padding-top: 4px;
    }
    .total-amt h4
    {
      padding-top: 4px;
      float:left;
      border:1px solid black;
      display: inline-block;
      font-size: 14px;
      font-weight: 200;
      height:20px;
      width: 125px;
      text-align: right;
      margin-left: 100px;
      padding-right: 5px;
    }
    </style>
  </head>
  <body>
    <div class="mct-print-container">
      <div class="title-container">
        <div class="mct-titles">
          <img src="c:/xampp/htdocs/warehouse/public/DesignIMG/logo.png" alt="logo">
          <h2>BOHOL I ELECTRIC COOPERATIVE, INC.</h2>
          <h5>Cabulijan, Tubigon, Bohol</h5>
          <h1>MATERIALS CHARGE TICKET</h1>
        </div>
      </div>
      <div class="mctmaster-top-container">
        <div class="mctmaster-top">
          <div class="mctmaster-left">
            <ul>
              <li><label>Particulars:</label><h3>{{$MCTMast[0]->Particulars}}</h3></li>
              <li><label>Address:</label><h3>{{$MCTMast[0]->AddressTo}}</h3></li>
            </ul>
          </div>
          <div class="mctmaster-right">
            <ul>
              <li><label>MIRS No.:</label><h3>{{$MCTMast[0]->MIRSNo}}</h3></li>
              <li><label>Date:</label><h3>{{$MCTMast[0]->MIRSDate}}</h3></li>
              <li><label>MCT No.</label><h3>{{$MCTMast[0]->MCTNo}}</h3></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="mct-table-container">
        <div class="mct-table">
           <table>
             <tr>
               <th>Acct. Code</th>
               <th>Item Code</th>
               <th>Description</th>
               <th>Unit Cost</th>
               <th>Amount</th>
               <th>Unit</th>
               <th>Quantity</th>
             </tr>
             @foreach ($MTDetails as $MTDetail)
             <tr>
               <td>{{$MTDetail->AccountCode}}</td>
               <td>{{$MTDetail->ItemCode}}</td>
               <td class="align-left">{{$MTDetail->MasterItems->Description}}</td>
               <td>{{number_format($MTDetail->UnitCost,'2','.',',')}}</td>
               <td>{{number_format($MTDetail->Amount,'2','.',',')}}</td>
               <td>{{$MTDetail->Unit}}</td>
               <td>{{$MTDetail->Quantity}}</td>
             </tr>
             @endforeach
           </table>
        </div>
      </div>
      <div class="acc-code-container">
        <div class="account-codes-present">
          <ul>
            @foreach ($AccountCodeGroup as $Account)
            <li><label>{{$Account->AccountCode}}</label><h5>{{number_format($totalsum,'2','.',',')}}</h5></li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="signatures">
        <div class="signatures-wrapper">
          <div class="signature-left">
            <div class="issued-label">
              Issued by:
            </div>
            <div class="issued-name">
              <div class="signature-issued">
                <img src="c:/xampp/htdocs/warehouse/public/DesignIMG/signature1.png" alt="signature">
              </div>
              {{$MCTMast[0]->Issuedby}}
              <p>HEAD-Warehouse Section</p>
            </div>
          </div>
          <div class="signature-right">
            <div class="recieve-label">
              <div class="total-amt">
                <h3>TOTAL</h3>
                <h4>{{number_format($totalsum,'2','.',',')}}</h4>
              </div>
              Recieved by:
            </div>
            <div class="recieve-name">
              <div class="signature-reciever">
                <img src="c:/xampp/htdocs/warehouse/public/DesignIMG/signature5.png" alt="signature">
              </div>
                {{$MCTMast[0]->Recievedby}}
                <p>Leadman</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
