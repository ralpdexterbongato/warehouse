<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Printing </title>
  </head>
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
  /*Start RR css*/
  .RRbondpaper-Container
  {

  }

  body
  {
    font-family: sans-serif;
    padding:60px 30px 10px 30px;
  }
  .RRtitle
  {
    text-align: center;
    margin-top: 20px;
    border: 1px solid black;
    font-size: 15px;
    font-weight: 600;
    padding: 5px;
  }
  .header-content
  {
    font-size: 15px;
    text-align:center;
  }
  .NoAndDate
  {
 text-align: right;
 font-size: 14px;
 margin-top: 20px;
 margin-bottom: 20px;
  }
  .NoAndDate ul
  {
    float: right;
  }
  .NoAndDate .empty
  {
    height: 40px;

  }
  .NoAndDate ul li label
  {
    display:inline-block;
    width: 110px;
  }
  .NoAndDate ul li p
  {
    width: 110px;
    display: inline-block;
  }
  .RRMasterDetails
  {
    font-size: 14px;
  }
  .right-details
  {
    float: right;
    display: inline-block;
  }
  .right-details ul li label
  {
    width: 140px;
    display: inline-block;
  }
  .right-details ul li p
  {
    display: inline-block;
    width: 150px;
  }
  .left-details
  {
    display: inline-block;
    margin-top: 10px;
  }
  .left-details ul li,.right-details ul li
  {
    margin-bottom: 2px;
  }
  .left-details ul li label
  {
    display: inline-block;
    width: 100px;
  }
  .left-details ul li p
  {
    display: inline-block;
  }
  .table-container
  {
    margin-top: 30px;
  }
  .table-container table
  {
    width: 100%;
    font-family: sans-serif;
    font-size: 14px;
  }
  .table-container th
  {
    text-align: center;
    font-weight: 600;
    border-top:1px solid black;
    border-bottom:1px solid black;
    padding:3px;
  }
  .bordering-left
  {
    border-left:1px solid black;
  }
  .bordering-right
  {
    border-right:1px solid black;
  }
  .table-container td
  {
    text-align: center;
    padding:5px 10px;
  }
  .content-right-totals
  {
    display: inline-block;
    float: right;
    width: 45%;
  }
  .content-right-totals ul li label
  {
    display:inline-block;
    width: 100px;
    border:1px solid black;
    padding:2px 4px;
    width: 120px;
  }
  .content-right-totals ul h5
  {
    display: inline-block;
    padding-left:20px;
    font-size: 13px;
    padding-top: 10px;
    padding-bottom: 5px;
    text-align: right;
    width: 45px;
  }
  .content-right-totals ul h6
  {
    display: inline-block;
    padding-left:20px;
    font-size: 13px;
    padding-top: 10px;
    padding-bottom: 5px;
    text-align: right;
    width: 120px;
  }
  .content-right-totals ul li p
  {
    display: inline-block;
    text-align: right;
    border:1px solid black;
    padding:2px 4px;
    width: 130px;
  }
  .note-container
  {
    page-break-inside:avoid;
    margin-bottom: 25px;
  }
  .note-container label
  {
    display: inline-block;
  }
  .note-container p
  {
    display: inline-block;
  }
  .netsales-totals-container
  {
    margin-top: 40px;
    margin-bottom: 100px;
    page-break-inside:avoid;
  }
  .left-name-sign,.right-name-sign
  {
    list-style: none;
  }
  .left-name-sign li,.right-name-sign li
  {
    margin-left: 100px;
    margin-top: 50px;
    position: relative;
  }
  .left-name-sign
  {
    width: 45%;
    float:left;
  }
  .right-name-sign
  {
    float:right;
    width: 45%;
  }
.signature-top-wrap
{
  height: 100px;
  margin-bottom:20px;
}
.signatures
{
  page-break-inside:avoid;
}
.top-signatures,.bottom-signatures
{
  text-align: center;
}
.top-signatures h2 img,.bottom-signatures h2 img
{
  width: 100%;
}
.top-signatures h2,.bottom-signatures h2
{
  width: 150px;
  height: 50px;
  position: absolute;
  top: -50px;
  left:50px;
}
  </style>
  <body>
    <div class="RRbondpaper-Container">
      <div class="header-content">
        <p>BOHOL I ELECTRIC COOPERATIVE,INC.</p>
        <p>Cabulijan, Tubigon, Bohol</p>
      </div>
      <div class="RRtitle">
        RECEIVING REPORT
      </div>
      <div class="NoAndDate">
        <ul>
          <li><label>RR No. :</label><p>{{$RRconfirmMasterResult[0]->RRNo}}</p></li>
          <li><label>Date :</label><p>{{$RRconfirmMasterResult[0]->RRDate->format('M d, Y')}}</p></li>
        </ul>
        <div class="empty">

        </div>
      </div>
      <div class="RRMasterDetails">
        <div class="left-details">
          <ul>
            <li><label>Supplier:</label><p>{{$RRconfirmMasterResult[0]->Supplier}}</p></li>
            <li><label>Address:</label><p>{{$RRconfirmMasterResult[0]->Address}}</p></li>
            <li><label>Invoice No.:</label><p>{{$RRconfirmMasterResult[0]->InvoiceNo}}</p></li>
            <li><label>R.V. No.:</label><p>{{$RRconfirmMasterResult[0]->RVNo}}</p></li>
          </ul>
        </div>
        <div class="right-details">
          <ul>
            <li><label>Carrier:</label><p>{{$RRconfirmMasterResult[0]->Carrier}}</p></li>
            <li><label>Delivery Receipt No:</label><p>{{$RRconfirmMasterResult[0]->DeliveryReceiptNo}}</p></li>
            <li><label>P.O No.:</label>
              @if ($RRconfirmMasterResult[0]->PONo)
              <p>{{$RRconfirmMasterResult[0]->PONo}}</p>
              @endif
            </li>
          </ul>
        </div>
        <div class="table-container">
          <table>
            <tr>
              <th class="bordering-left">Code No.</th>
              <th>Article</th>
              <th>Unit</th>
              <th>Quantity Delivered</th>
              <th>Quantity Accepted</th>
              <th>U-Cost</th>
              <th class="bordering-right">Amount</th>
            </tr>
            @foreach ($RRconfirmDetails as $mtdetail)
              <tr>
                <td>{{$mtdetail->ItemCode}}</td>
                <td>{{$mtdetail->Description}}</td>
                <td>{{$mtdetail->Unit}}</td>
                <td>{{$mtdetail->RRQuantityDelivered}}</td>
                <td>{{$mtdetail->Quantity}}</td>
                <td>{{number_format($mtdetail->UnitCost,'2','.',',')}}</td>
                <td>{{number_format($mtdetail->Amount,'2','.',',')}}</td>
              </tr>
            @endforeach
          </table>
        </div>
        <div class="netsales-totals-container">
          <div class="empty-left">
          </div>
            <div class="content-right-totals">
              <ul>
                <li><label>Net Sales</label><p>{{number_format($netsales,'2','.',',')}}</p></li>
                <li><h5>Add:VAT</h5><h5>12%</h5><h6>{{number_format($VAT,'2','.',',')}}</h6></li>
                <li><label>TOTAL AMOUNT</label><p>{{number_format($totalAmmount,'2','.',',')}}</p></li>
              </ul>
            </div>
        </div>
      </div>
      <div class="note-container">
        <label>Note:</label><p>{{$RRconfirmMasterResult[0]->Note}}</p>
      </div>
      <div class="signatures">
        <div class="signature-top-wrap">
          <div class="top-signatures">
            <div class="left-name-sign">
              <h5>Received by</h5>
              <li>
                @if ($RRconfirmMasterResult[0]->ReceivedbySignature)
                    <h2><img src="storage/signatures/{{$RRconfirmMasterResult[0]->ReceivedbySignature}}" alt="signature"></h2>
                @endif
                <p>{{$RRconfirmMasterResult[0]->Receivedby}}</p>
                <label>{{$RRconfirmMasterResult[0]->ReceivedbyPosition}}</label>
              </li>
            </div>
            <div class="right-name-sign">
              <h5>Received Original by</h5>
              <li>
                @if ($RRconfirmMasterResult[0]->ReceivedOriginalbySignature)
                  <h2><img src="storage/signatures/{{$RRconfirmMasterResult[0]->ReceivedOriginalbySignature}}" alt="signature"></h2>
                @endif
                <p>{{$RRconfirmMasterResult[0]->ReceivedOriginalby}}</p>
                <label>{{$RRconfirmMasterResult[0]->ReceivedOriginalbyPosition}}</label>
              </li>
            </div>
          </div>
        </div>
        <div class="bottom-signatures">
          <div class="left-name-sign">
            <h5>Verified by</h5>
            <li>
              @if ($RRconfirmMasterResult[0]->VerifiedbySignature)
                <h2><img src="storage/signatures/{{$RRconfirmMasterResult[0]->VerifiedbySignature}}" alt="signature"></h2>
              @endif
              <p>{{$RRconfirmMasterResult[0]->Verifiedby}}</p>
              <label>{{$RRconfirmMasterResult[0]->VerifiedbyPosition}}</label>
            </li>
          </div>
          <div class="right-name-sign">
            <h5>Posted to BIN card by</h5>
            <li>
              @if ($RRconfirmMasterResult[0]->PostedtoBINbySignature)
                <h2><img src="storage/signatures/{{$RRconfirmMasterResult[0]->PostedtoBINbySignature}}" alt="signature"></h2>
              @endif
              <p>{{$RRconfirmMasterResult[0]->PostedtoBINby}}</p>
              <label>{{$RRconfirmMasterResult[0]->PostedtoBINbyPosition}}</label>
            </li>
          </div>
        </div>
        </div>
      </div>
    </div>
  </body>
</html>
