@extends('layouts.master')
@section('title')
  Printable| MIRS
@endsection
@section('body')
  <div class="printable-view-container">
    <div class="printable-paper">
      <div class="print-btn-container">
        <button type="button" name="button">PDF <i class="fa fa-file-pdf-o"></i></button>
      </div>
      <div class="bondpaper-size">
        <div class="top-part-box1">
          <h1>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h1>
            <h4>Cabulijan, Tubigon, Bohol</h4>
            <h2>MATERIALS ISSUANCE REQUISITION SLIP</h2>
        </div>
        <div class="top-part-box2">
          <div class="top-box2-left">
          </div>
          <div class="top-box2-right">
            <div class="top-box2-right-data">
              <label>MIRS #:</label><h1>2016-1527</h1>
            </div>
            <div class="top-box2-right-data">
              <label>Date:</label><h1>October 22, 2017</h1>
            </div>
          </div>
        </div>
        <div class="top-part-box3">
          <p>
            TO: The General Manager <br>
            Please furnish the following materials for :
          </p>
          <div class="purpose-container">
            <h2>Tubigon Parish Church, Tubigon Meter Replacement</h2>
          </div>
        </div>
        <div class="mirs-details-container">
          <div class="table-mirs-container">
            <table>
              <tr>
                <th class="noborder-left">CODE</th>
                <th>PARTICULARS</th>
                <th>UNIT</th>
                <th>QUANTITY</th>
                <th class="noborder-right">REMARKS</th>
              </tr>
              <tr>
                <td class="noborder-left">2898</td>
                <td class="particular">Particulars is equal to description</td>
                <td>PC</td>
                <td>7</td>
                <td>remarks</td>
              </tr>
              <tr>
                <td class="noborder-left">2898</td>
                <td class="particular">Particulars is equal to description</td>
                <td>PC</td>
                <td>7</td>
                <td>remarks</td>
              </tr>
              <tr>
                <td class="noborder-left">2898</td>
                <td class="particular">Particulars is equal to description</td>
                <td>PC</td>
                <td>7</td>
                <td>remarks</td>
              </tr>
              <tr>
                <td class="noborder-left">2898</td>
                <td class="particular">Particulars is equal to description</td>
                <td>PC</td>
                <td>7</td>
                <td>remarks</td>
              </tr>
              <tr>
                <td class="noborder-left">2898</td>
                <td class="particular">Particulars is equal to descriptionParticulars is equal to descriptionParticulars is equal to description</td>
                <td>PC</td>
                <td>7</td>
                <td>remarks</td>
              </tr>
            </table>
          </div>
          <div class="statement-container">
            <p>I hereby certify that the materials/supplies requested above are <br>necessary and with purpose stated above</p>
          </div>
          <div class="bottom-mirs-part">
            <div class="request-recommend-sig">
              <div class="request-sig">
                <h4>Prepared by:</h4>
                <h2>
                  BENITO MAGLAWAY <br>
                  Requisitioner
                </h2>
              </div>
              <div class="recommend-sig">
              <h4>Recommended by:</h4>
              <h2>
              <span class="bold">RIENERIO A. TUMABANG</span><br>
                Department Manager
              </h2>
              </div>
            </div>
            <div class="president-sig">
              <h4>APPROVED:</h4>
              <h2>
              <span class="bold">DINO NICOLAS T. ROXAS</span><br>
                General Manager
              </h2>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
