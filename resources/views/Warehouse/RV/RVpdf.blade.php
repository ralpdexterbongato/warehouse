<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>RV printing</title>
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

  .top-box-titles
  {
    text-align: center;
  }
  .rvtitle
  {
    margin-top:6px;
    font-size: 18px;
  }
  body
  {
    padding:70px 30px 20px 30px;
    font-family: sans-serif;
  }
  .RV-DateAndNo ul
  {
    float: right;
    margin-top: 20px;
    font-size: 14px;
  }
  .RV-DateAndNo label
  {
    display: inline-block;
    width: 80px;
  }
  .RV-DateAndNo p
  {
    border-bottom: 1px solid black;
    display: inline-block;
    width: 110px;
  }
  .ToGM
  {
    font-size:14px;
  }
  .ToGM p
  {
    display: inline-block;
  }
  .ToGM .reason
  {
    display: inline-block;
    float: right;
    width: 380px;
    text-align:center;
    margin-top: 7px;
    border-bottom: .5px solid black;
  }
  .reason
  {
    text-align: left!important;
  }
  .RV-DATENOWrapper
  {
    height: 55px;
  }
  .RV-table-wrapper
  {
    margin-top: 30px;
  }
  .RV-table-wrapper table
  {
    width: 100%;
    border:1px solid black;
    font-size: 14px;
  }
  .RV-table-wrapper th
  {
    border:1px solid black;
    padding:4px 8px;
    text-align:center;
    font-size: 14px;
    font-weight: 600;
  }
  .RV-table-wrapper td
  {
    text-align:center;
    padding:3px 6px;
    border-left: 1px solid black;
    border-right: 1px solid black;
  }
  .signature-label
  {
    border-left: 1px solid black;
    border-right: 1px solid black;
    padding:2px;
    font-size: 14px;
  }
  .bottom-left-sign li,.bottom-right-sign li,.left-budget li
  {
    list-style: none;
    margin-left: 40px;
    text-align: center;
    margin-top: 80px;
    position: relative;
  }
  .left-budget li
  {
    margin-left: 22px!important;
  }
  .bottom-left-sign h1,.bottom-right-sign h1,.left-budget h1
  {
    position: absolute;
    top: -70px;
    left:65px;
    height: 50px;
    width: 150px;
  }
  .bottom-left-sign h1 img,.bottom-right-sign h1 img,.left-budget h1 img
  {
    width: 100%;
  }
  .bottom-left-sign
  {
    display: inline-block;
    float: left;
    width: 45%;
  }
  .bottom-right-sign
  {
    display: inline-block;
    float: right;
    width: 45%;
  }
  .bottom-right-sign.bottom
  {
    margin-top: 20px;
  }
  .bottom-signatures-container.bottom
  {
    border-top:0px;
  }
  .bottom-signatures-container
  {
    border:1px solid black;
    height: 150px;
    padding:10px;

  }
  .left-budget
  {
    width: 45%;
    display: inline-block;
    float: left;
    text-align:center;
    margin-left: 15px;
  }
  .left-budget .availableAmmount
  {
    border-bottom: 1px solid black;
    width: 80%;
    margin:0 auto;
  }
  .left-budget li
  {
    list-style: none;
    margin-top: 80px;
    text-align: center;
  }
  .signatures
  {
    page-break-inside:avoid;
    font-size: 15px;
  }
  .for
  {
    position: absolute;
    left:30px;
    top: -35px;
  }
  </style>
  <body>
    <div class="rv-pdf-container">
      <div class="top-box-titles">
        <p>BOHOL I ELECTRIC COOPERATIVE</p>
        <p>Cabulijan, Tubigon, Bohol</p>
        <p class="rvtitle">REQUISITION VOUCHER</p>
      </div>
      <div class="RV-DateAndNo">
        <div class="RV-DATENOWrapper">
          <div class="empty-left">
          </div>
          <ul>
            <li><label>RV No.</label><p>{{$RVMaster[0]->RVNo}}</p></li>
            <li><label>DATE:</label><p>{{$RVMaster[0]->RVDate->format('m/d/Y')}}</p></li>
          </ul>
        </div>
      </div>
      <div class="ToGM">
        <div class="GMNote-wrap">
          <p>TO: The General Manager<br>
          Please furnish the following Materials/Supplies for</p>
          <p class="reason">{{$RVMaster[0]->Purpose}}</p>
        </div>
      </div>
      <div class="RV-table-container">
        <div class="RV-table-wrapper">
          <table>
            <tr>
              <th>Articles</th>
              <th>Unit</th>
              <th>Qty</th>
              <th>Remarks</th>
            </tr>
            @foreach ($RVDetails as $rvdetail)
              <tr>
                <td>{{$rvdetail->Particulars}}</td>
                <td>{{$rvdetail->Unit}}</td>
                <td>{{$rvdetail->Quantity}}</td>
                <td>{{$rvdetail->Remarks}}</td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      <div class="signatures">
        <div class="signature-label">
          <p>I hereby certify that the above requested materials/supplies are necessary and will be used solely for the<br> purpose stated above.</p>
        </div>
        <div class="bottom-signatures-container">
          <div class="bottom-left-sign">
            <h5>Requested by</h5>
            <li>
              @if ($RVMaster[0]->RequisitionerSignature)
              <h1><img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$RVMaster[0]->RequisitionerSignature}}" alt="signature"></h1>
              @endif
              <p>{{$RVMaster[0]->Requisitioner}}</p>
              <p>{{$RVMaster[0]->RequisitionerPosition}}</p>
            </li>
          </div>
          <div class="bottom-right-sign">
            <h5>Recommended by</h5>
            <li>
              @if ($RVMaster[0]->RecommendedbySignature)
                <h1><img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$RVMaster[0]->RecommendedbySignature}}" alt="signature"></h1>
              @elseif($RVMaster[0]->ManagerReplacerSignature)
                <medium class="for">For :</medium><h1><img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$RVMaster[0]->ManagerReplacerSignature}}" alt="signature"></h1>
              @endif
              <p>{{$RVMaster[0]->Recommendedby}}</p>
              <p>{{$RVMaster[0]->RecommendedbyPosition}}</p>
            </li>
          </div>
        </div>
        <div class="bottom-signatures-container bottom">
          <div class="left-budget">
            <label>BUDGET AVAILABLE ON THIS REQUEST</label>
            <p class="availableAmmount">{{$RVMaster[0]->BudgetAvailable,'2','.',','}}</p>
            <li>
              @if ($RVMaster[0]->BudgetOfficerSignature)
                <h1><img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$RVMaster[0]->BudgetOfficerSignature}}" alt="signature"></h1>
              @endif
              <p>{{$RVMaster[0]->BudgetOfficer}}</p>
              <p>Budget Officer</p>
            </li>
          </div>
          <div class="bottom-right-sign bottom">
            <h5>Approved:</h5>
            <li>
              @if ($RVMaster[0]->GeneralManagerSignature)
                <h1><img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$RVMaster[0]->GeneralManagerSignature}}" alt="signature"></h1>
              @elseif($RVMaster[0]->ApprovalReplacerSignature!=null)
                <medium class="for">For :</medium><h1><img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$RVMaster[0]->ApprovalReplacerSignature}}" alt="signature"></h1>
              @endif
              <p>{{$RVMaster[0]->GeneralManager}}</p>
              <p>General Manager</p>
            </li>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
