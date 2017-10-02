<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>MR Printing</title>
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
  /*CSS start*/
  body
  {
    font-family: sans-serif;
    padding:60px 30px 10px 30px;
    font-size: 14px;
  }
    .top-title-content
    {
      text-align:center;
    }
    .mr-long-title
    {
      margin-top: 25px;
      text-decoration: underline;
    }
    .numbers-date-container ul li
    {
      display: inline-block;
      width:35%;
      text-align: center;
    }
    .numbers-date-container
    {
      margin-top: 40px;
    }
    .number
    {
      text-decoration: underline;
      margin-bottom: 3px;
    }
    .AknowlegeParag p
    {
      text-indent: 40px;
    }
    .table-container
    {
      margin-top: 30px;
    }
    .table-container table
    {
      width:100%;
      text-align: center;
      border: 1px solid black;
    }
    .table-container th
    {
      border-top: 2px solid black;
      border-bottom: 2px solid black;
      border-left: 1px solid black;
      border-right: 1px solid black;
      font-size: 13px;
      padding:5px;
    }
    .table-container td
    {
      padding:5px;
      border-left: 1px solid black;
      border-right: 1px solid black;
    }
    .bold
    {
      font-weight: 600;
    }
    .note-container
    {
      margin-top: 10px;
      margin-bottom: 15px;
      height: 40px;
    }
    .left-box
    {
      width: 45%;
      margin-top: 30px;
      font-size: 14px;
      display: inline-block;
    }
    .left-box h5
    {
      margin-top: 25px;
      margin-bottom: 5px;
    }
    .instruction-parag
    {
      text-indent: 40px;
    }
    .signatures-container
    {
      page-break-inside:avoid;
      margin-top: 20px;
    }
    .right-box
    {
      display: inline-block;
      width: 38%;
      float:right;
    }
    .signature-box
    {
      margin-top: 20px;
    }
    .signature-names p
    {
      text-decoration: underline;
    }
    .signature-names
    {
      position: relative;
      text-align: center;
      margin-top: 50px;
    }
    .signature-names h1 img
    {
      width: 100%;
    }
    .signature-names h1
    {
      width: 150px;
      height: 50px;
      position: absolute;
      top:-50px;
      left: 60px;
    }
    .underline
    {
      text-decoration: underline;
    }
    .uppercase
    {
      text-transform:uppercase;
    }
    .for
    {
      position: absolute;
      left: 16px;
      top: -20px;
    }
  </style>
  <body>
    <div class="paper-container">
      <div class="top-title-content">
        <h4>BOHOL I ELECTRIC COOPERATIVE, INC.</h4>
        <p>Cabulijan, Tubigon, Bohol</p>
        <p class="mr-long-title">MEMORANDUM RECEIPT FOR EQUIPMENT . SEMI-EXPENDABLE AND NON EXPENDABLE PROPERTY</p>
      </div>
      <div class="numbers-date-container">
        <ul>
          <li>
            <p class="number">RR No. {{$MRMaster[0]->RRNo}}</p>
            <p>{{$MRMaster[0]->RRDate->format('M d, Y')}}</p>
          </li>
          <li>
            <p class="number">RV No. {{$MRMaster[0]->RVNo}}</p>
            <p>{{$MRMaster[0]->RVDate->format('M d, Y')}}</p>
          </li>
          <li>
            <p class="number">MR No. {{$MRMaster[0]->MRNo}}</p>
            <p>{{$MRMaster[0]->MRDate->format('M d, Y')}}</p>
          </li>
        </ul>
      </div>
      <div class="AknowlegeParag">
        <p>I HEREBY ACKNOWLEDGE to have received from <span class="bold">{{$MRMaster[0]->WarehouseMan}}</span> Warehouseman the following<br>
property for which I am responsible, subject to the provision of the usual accounting and auditing rules and regulations<br> and which will be used for General Services.</p>
      </div>
      <div class="table-container">
        <table>
          <tr>
            <th>QUANTITY</th>
            <th>UNIT</th>
            <th>NAME AND DESCRIPTION</th>
            <th>PROPERTY NUMBER</th>
            <th>UNIT VALUE</th>
            <th>TOTAL VALUE</th>
            <th>REMARKS</th>
          </tr>
          @foreach ($MRMaster[0]->MRDetail as $item)
          <tr>
            <td>{{$item->Quantity}}</td>
            <td>{{$item->Unit}}</td>
            <td>{{$item->NameDescription}}</td>
            <td></td>
            <td>{{$item->UnitValue}}</td>
            <td>{{$item->TotalValue}}</td>
            <td>{{$item->Remarks}}</td>
          </tr>
          @endforeach
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>.</td>
          </tr>
        </table>
      </div>
      <div class="note-container">
        <p>Note: {{$MRMaster[0]->Note}}</p>
      </div>
      <div class="signatures-container">
        <div class="left-box">
          <h5>REFERENCE:</h5>
          <p>Purchase from: {{$MRMaster[0]->Supplier}}</p>
          <p>Invoice number: {{$MRMaster[0]->InvoiceNo}}</p>
          <h5>INSTRUCTION</h5>
          <p class="instruction-parag">This form shall be prepared in FOUR (4)
          LEGIBLE COPIES,DISTRIBUTION:(1) ORIGINAL<br>
          should be KEPT by the Accountable Officer<br>
          (2) DUPLICATE must be FILED in the Personal<br>
          file of the Employee Concerned. (3) TRIPLICATE<br>
          should be FILED in the OFFICE OF THE<br>
          Accounting Section.(4) QUADRUPLICATE<br>
          must be KEPT by the Responsible Employee.
        </p>

        </div>
        <div class="right-box">
          <p>P.O. Number: {{$MRMaster[0]->PONo}}</p>
          <div class="signature-box">
            <label>RECOMMENDING APPROVAL:</label>
            <div class="signature-names">
              @if ($MRMaster[0]->RecommendedbySignature)
                <h1><img src="c:/xampp/htdocs/warehouse/public/storage/signatures/{{$MRMaster[0]->RecommendedbySignature}}" alt="signatures"></h1>
              @endif
              <p>{{$MRMaster[0]->Recommendedby}}</p>
              <label>{{$MRMaster[0]->RecommendedbyPosition}}</label>
            </div>
          </div>
          <div class="signature-box">
            <label>APPROVED:</label>
            <div class="signature-names">
              @if ($MRMaster[0]->GeneralManagerSignature)
                <h1><img src="c:/xampp/htdocs/warehouse/public/storage/signatures/{{$MRMaster[0]->GeneralManagerSignature}}" alt="signatures"></h1>
              @elseif($MRMaster[0]->ApprovalReplacerSignature)
                <medium class="for">For :</medium> <h1><img src="c:/xampp/htdocs/warehouse/public/storage/signatures/{{$MRMaster[0]->ApprovalReplacerSignature}}" alt="signatures"></h1>
              @endif
              <p>{{$MRMaster[0]->GeneralManager}}</p>
              <label>General Manager</label>
            </div>
          </div>
          <div class="signature-box">
            <label>RECEIVED:</label>
            <div class="signature-names">
              @if ($MRMaster[0]->ReceivedbySignature)
                <h1><img src="c:/xampp/htdocs/warehouse/public/storage/signatures/{{$MRMaster[0]->ReceivedbySignature}}" alt="signatures"></h1>
              @endif
              <p>{{$MRMaster[0]->Receivedby}}</p>
              <label>{{$MRMaster[0]->ReceivedbyPosition}}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
