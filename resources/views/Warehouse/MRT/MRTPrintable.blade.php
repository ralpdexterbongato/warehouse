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

  /*custom*/
  .head-data-container
  {
    text-align: center;
  }
  .bondpaper-container
  {
    padding:60px 30px;
    font-family: sans-serif;
  }
  .head-data-container
  {
    font-size: 15px;
  }
  .head-data-container h4
  {
    margin-top: 10px;
  }
  .head-second-data-container
  {
    margin-top: 25px;
  }
  .head-left-container
  {
    display: inline-block;
    float: left;
  }
  .head-right-container
  {
    display: inline-block;
    float: right;
  }
  .head-left-container > li,.head-right-container > li
  {
    list-style: none;
  }
  .head-left-container > li > label,.head-right-container > li > label
  {
    width: 100px;
    display: inline-block;
    font-family: sans-serif;
    font-size: 14px;
    padding-top: 3px;
  }
  .head-left-container > li > h3,.head-right-container > li > h3
  {
    display: inline-block;
    font-family: sans-serif;
    font-size: 14px;
    font-weight: 400;
    padding-top: 3px;
  }
  .second-data-wrap
  {
    height:90px;
  }
  .table-container
  {
    page-break-inside:avoid;
  }
  .table-container table td
  {
    padding-top: 4px;
    font-family: sans-serif;
    font-size: 13px;
    text-align: left;
  }
  .table-container table th
  {
    font-size: 14px;
    font-family:helvetica;
    border: 1px solid black;
    font-weight:600;
    text-align: center;
  }
  .right
  {
    text-align: right;
  }
  .table-container table
  {
    width: 100%;
  }
  .account-code-summarized-container
  {
    margin-top: 50px;
  }
  .left-account-code-group-box
  {
    display: inline-block;
    float:left;
    padding-top: 40px;
  }
  .left-account-code-group-box li
  {
    list-style: none;
    font-size: 13px;
  }
  .left-account-code-group-box li label
  {
    display: inline-block;
    width: 100px;
  }
  .left-account-code-group-box li p
  {
    display: inline-block;
  }
  .right-total-amt
  {
    float: right;
    display: inline-block;
    padding-top:100px;
    page-break-inside:avoid;
  }
  .right-total-amt p
  {
    display: inline-block;
    font-size: 14px;
    border:1px solid black;
  }
  .total-label
  {
    text-align: center;
    padding:5px 15px;
    width: 166px;
  }
  .totalnum
  {
    text-align: right;
    padding:5px 10px;
    width: 140px;
  }
  .signatures-container
  {
    padding-top: 200px;
    width: 100%;
    page-break-inside:avoid;
  }
  .signatures-boxes
  {
    width: 100%;
  }
  .signatures-left-box
  {
    display: inline-block;
    float:left;
    width: 40%;
  }
  .signatures-right-box
  {
    display: inline-block;
    float:right;
    width: 40%;
  }
  .signature-label
  {
    font-family: sans-serif;
    font-size: 14px;
    font-weight: 200;
    position: relative;
  }
  .signaturer-name
  {
    text-align: center;
    font-size: 14px;
    font-weight: 400;
    font-family: sans-serif;
    margin-top: 40px;
    position: relative;
  }
  .signature-image
  {
    width: 150px;
    height: 50px;
    position: absolute;
    top:-70px;
    left:90px;
  }
  .signature-image img
  {
    width: 100%;
  }
  .account-code-group-container
  {
    page-break-inside:avoid;
  }
</style>


<div class="bondpaper-container">
  <div class="head-data-container">
    <p>BOHOL I ELECTRIC COOPERATIVE, INC.</p>
    <p>Cabulijan, Tubigon, Bohol</p>
    <h4>MATERIALS RETURN TICKET</h4>
  </div>
  <div class="head-second-data-container">
    <div class="second-data-wrap">
      <div class="head-left-container">
        <li><label>Particulars:</label> <h3>{{$mrtMaster[0]->Particulars}}</h3></li>
        <li><label>Address:</label> <h3>{{$mrtMaster[0]->AddressTo}}</h3> </li>
      </div>
      <div class="head-right-container">
        <li><label>MCTNo:</label> <h3>{{$mrtMaster[0]->MCTNo}}</h3></li>
        <li><label>MRTNo:</label> <h3>{{$mrtMaster[0]->MRTNo}}</h3> </li>
        <li><label>Returned Date:</label> <h3>{{$mrtMaster[0]->ReturnDate->Format('M d, Y')}}</h3></li>
        <li><label>Remarks:</label> <h3>{{$mrtMaster[0]->Remarks}}</h3> </li>
      </div>
    </div>
  </div>
  <div class="table-container">
    <div class="table-wrapper">
      <table>
        <tr>
          <th>Acct. code</th>
          <th>ItemCode</th>
          <th>Description</th>
          <th>UnitCost</th>
          <th>Amount</th>
          <th>Unit</th>
          <th>Returned</th>
        </tr>
        @foreach ($MRTConfirmationItems as $key => $item)
        <tr>
          <td>{{$item->AccountCode}}</td>
          <td>{{$item->ItemCode}}</td>
          <td>{{$item->Description}}</td>
          <td>{{$item->UnitCost}}</td>
          <td>{{$item->Amount}}</td>
          <td>{{$item->Unit}}</td>
          <td style="text-align:right">{{$item->Quantity}}</td>
        </tr>
        @endforeach
      </table>
    </div>
  </div>
  <div class="account-code-group-container">
    <div class="left-account-code-group-box">
      @foreach ($MRTbyAcntCode as $key => $groupitem)
        <li>
          <label>{{$groupitem->AccountCode}}</label><p>{{number_format($groupitem->totalAMT,'2','.',',')}}</p>
        </li>
      @endforeach
    </div>
  </div>
  <div class="right-total-amt">
    <div class="right-total-box">
      <p class="total-label">Total ammount Returned</p><p class="totalnum">{{number_format($totalsum,'2','.',',')}}</p>
    </div>
  </div>
  <div class="signatures-container">
    <div class="signatures-boxes">
      <div class="signatures-left-box">
        <div class="signature-label">
          Returned by:
        </div>
        <div class="signaturer-name">
          <div class="signature-image">
            @if ($mrtMaster[0]->users[1]->pivot->Signature=='0')
              <img src="c:/xampp/htdocs/warehouse/public/storage/signatures/{{$mrtMaster[0]->users[1]->Signature}}" alt="signature">
            @endif
          </div>
          {{$mrtMaster[0]->users[1]->FullName}}
          <p>{{$mrtMaster[0]->users[1]->Position}}</p>
        </div>
      </div>
      <div class="signatures-right-box">
        <div class="signature-label">
          Received by:
        </div>
        <div class="signaturer-name">
          <div class="signature-image">
            @if ($mrtMaster[0]->users[0]->pivot->Signature=='0')
              <img src="c:/xampp/htdocs/warehouse/public/storage/signatures/{{$mrtMaster[0]->users[0]->Signature}}" alt="signature">
            @endif
          </div>
          {{$mrtMaster[0]->users[0]->FullName}}
          <p>{{$mrtMaster[0]->users[0]->Position}}</p>
        </div>
      </div>
    </div>
  </div>
</div>
