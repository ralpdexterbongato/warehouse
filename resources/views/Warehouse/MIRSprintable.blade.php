<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Printing</title>
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


    /*PRINTABLE MIRS CSS*/
    .bold
    {
      font-weight: 600;
    }
    .bondpaper-size
    {
      margin:0 auto;
      background: #fff;
    }
    body
    {
      padding:70px 30px 0px 30px;
    }
    .top-part-titles
    {
      text-align: center;

    }

    .top-part-titles h2
    {
      margin-top: 25px;
      font-size: 16px;
      text-decoration: underline;
        font-family:cursive;
        font-weight: 200;

    }
    .top-part-titles h1
    {
      font-family:sans-serif;
      font-weight: bold;
      font-size: 15px;
    }
    .top-part-titles h4
    {
      font-family: sans-serif;
      font-size: 13px;
      margin-top: 5px;
      font-weight: 200;
    }
    .date-mirs-blank
    {
      display: inline-block;
      width:45%;
      height: 50px;
    }
    .date-mirs-content
    {
      display: inline-block;
      width: 45%;
      float: right;
      height: 80px;
    }
    .top-part-date
    {
      overflow:auto;
      margin-top: 25px;

    }
    .MIRS-number label,.MIRS-date label
    {
      text-align: right;
      width:120px;
      display:inline-flex;
    }
    .MIRS-number h1,.MIRS-date h1
    {
      border-bottom: 1px solid black;
      font-size: 13px;
      text-align: left;
      width: 150px;
      display: inline-block;
    }
    .MIRS-number h1,.MIRS-date h1,.MIRS-number label,.MIRS-date label
    {
      font-size: 13px;
      font-family: sans-serif;
      font-weight: 200;
    }
    .for-gm p
    {
      margin: 0;
    }
    .purpose-for,.for-gm
    {
      display: inline-block;
    }

  .purpose-for
  {
    margin-left: 56px;
    border-bottom: 1px solid black;
    width: 218px;
  }
  .left-purpose
  {
    display: inline-block;
    width: 45%;
    height:50px;
    padding-top: 10px;
    font-family: sans-serif;
    font-size: 13px;
  }
  .right-purpose
  {
    display: inline-block;
    width: 45%;
    height:50px;
    float: right;
    font-family: sans-serif;
    font-size: 13px;
  }
  .table-print-wrap
  {
    border-bottom:1px solid black;
  }
  .ruller-wrap
  {
    border:1px solid black;
  }
  .table-print-wrap table
  {
    width: 100%;

  }
  .table-print-wrap th
  {
    border-bottom: 1px solid black;
    border-left: 1px solid black;
    border-right: 1px solid black;
    text-align:center;
    font-family: sans-serif;
    font-weight: 600;
    font-size: 14px;
    padding:20px 10px 0px 10px;
  }
  .table-print-wrap td
  {
    text-align: center;
    border-left: 1px solid black;
    border-right: 1px solid black;
    padding:7px 14px;
    font-family: sans-serif;
    font-size: 13px;
  }
  .statement-container p
  {
    font-family: sans-serif;
    font-size: 13px;
  }
  .statement-container
  {
    border-left: 1px solid black;
    border-right: 1px solid black;
    padding:5px 10px 0px 10px;
  }
  .prepared-recommend
  {
    height: 120px;
    width: 100%;
  }
  .approved
  {
    height: 125px;
    width: 100%;
    text-align:center;
    position: relative;

  }
  .approved img
  {
    width: 100%;
  }
  .approved h2
  {
    width: 150px;
    height: 80px;
    position: absolute;
    left:300px;
    top: 20px;
  }
  .approved h3
  {
    font-size: 13px;
    font-weight: 200;
  }
  .approved h4
  {
    font-size: 13px;
    margin-top: 40px;
  }
  .approved h5
  {
    margin-top: 4px;
    font-weight: 200;
  }
  .recommended h2 img
  {
    width: 100%;
  }
  .recommended h2
  {
    width: 150px;
    height: 50px;
    position: absolute;
    right: 90px;
    top: 30px;
  }
  .recommended
  {
    position: react
    display: inline-block;
    height: 120px;
    width: 45%;
    text-align: center;
    float:right;
    position: relative;
  }
  .recommended h3
  {
    font-size: 13px;
    margin-top: 20px;
    font-weight: 200;
  }
  .recommended h4
  {
    font-size: 13px;
    margin-top: 40px;
  }
  .recommended h5
  {
    margin-top: 4px;
    font-weight: 200;
  }
  .prepared h2 img
  {
    width: 100%;
  }
  .prepared h2
  {
    position:absolute;
    left: 95px;
    top: 30px;
    width: 150px;
    height: 50px;
  }
  .prepared
  {
    display: inline-block;
    width: 45%;
    height: 120px;
    text-align: center;
    float: left;
    position: relative;
  }
  .prepared h3
  {
    font-size: 13px;
    margin-top: 20px;
    font-weight: 200;
  }
  .prepared h4
  {
    font-size: 13px;
    margin-top: 40px;
    font-weight: 300;
  }
  .prepared h5
  {
    margin-top: 4px;
    font-weight: 200;
  }
  .signatures-container
  {
    border: 1px solid black;
    font-family:sans-serif;
    page-break-inside:avoid;
  }
    </style>
  </head>
  <body>
        <div class="bondpaper-size">
          <div class="top-part-titles">
            <h1>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h1>
              <h4>Cabulijan, Tubigon, Bohol</h4>
              <h2>MATERIALS ISSUANCE REQUISITION SLIP</h2>
          </div>
          <div class="top-part-date">
            <div class="date-mirs-blank">

            </div>
            <div class="date-mirs-content">
              <div class="MIRS-number">
                <label>MIRS # :</label>
                <h1>{{$master[0]->MIRSNo}}</h1>
              </div>
              <div class="MIRS-date">
                <label>Date :</label>
                <h1>{{$master[0]->MIRSDate->format('M d, Y')}}</h1>
              </div>
            </div>
          </div>
          <div class="purpose-container">
            <div class="left-purpose">
              <div class="for-gm">
                <p>TO: The General Manager <br> Please furnish the following materials for:</p>
              </div>
            </div>
            <div class="right-purpose">
              <div class="purpose-for">
                <p class="bold">{{$master[0]->Purpose}}</p>
              </div>
            </div>
          </div>
          <div class="ruller-wrap">
            <div class="table-print-wrap">
              <table>
                <tr>
                  <th>CODE</td>
                  <th>PARTICULARS</td>
                  <th>UNIT</td>
                  <th>QUANTITY</td>
                  <th>REMARKS</td>
                </tr>
                @foreach ($details as $detail)
                <tr>
                  <td>{{$detail->ItemCode}}</td>
                  <td>{{$detail->Particulars}}</td>
                  <td>{{$detail->Unit}}</td>
                  <td>{{$detail->Quantity}}</td>
                  <td>{{$detail->Remarks}}</td>
                </tr>
                @endforeach
              </table>
            </div>
          </div>
          <div class="statement-container">
            <p>I hereby certify that the materials / supplies requested above are <br> necessary and with purpose stated above</p>
          </div>
          <div class="signatures-container">
            <div class="prepared-recommend">
              <div class="prepared">
                <h3>Prepared by:</h3>
                <h2 class="signature">
                  @if (!empty($master[0]->PreparedSignature))
                    <img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$master[0]->PreparedSignature}}">
                  @endif
                </h2>
                <h4>{{$master[0]->Preparedby}}</h4>
                <h5>{{$master[0]->PreparedPosition}}</h5>
              </div>
              <div class="recommended">
                <h3>Recommended by:</h3>
                <h2>
                  @if (!empty($master[0]->RecommendSignature))
                    <img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$master[0]->RecommendSignature}}">
                  @endif
                </h2>
                <h4>{{$master[0]->Recommendedby}}</h4>
                <h5>{{$master[0]->RecommendPosition}}</h5>
              </div>
            </div>
            <div class="approved">
                <h3>APPROVED:</h3>
                <h2 class="signature">
                  @if (!empty($master[0]->ApproveSignature))
                    <img src="c:/xampp/htdocs/Warehouse/public/storage/signatures/{{$master[0]->ApproveSignature}}">
                  @endif
                </h2>
                <h4>{{$master[0]->Approvedby}}</h4>
                <h5>{{$master[0]->ApprovePosition}}</h5>
            </div>
          </div>
        </div>
  </body>
</html>
