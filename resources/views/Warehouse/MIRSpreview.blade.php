@extends('layouts.master')
@section('title')
  Printable| MIRS
@endsection
@section('body')
  <div class="printable-view-container">
    <div class="printable-paper">
      <div class="print-btn-container">
        <form action="{{route('mirs-download')}}" method="post">
          {{ csrf_field() }}
          <input type="text" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
          <button type="submit">PDF <i class="fa fa-file-pdf-o"></i></button>
        </form>
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
              <label>MIRS #:</label><h1>{{$MIRSMaster[0]->MIRSNo}}</h1>
            </div>
            <div class="top-box2-right-data">
              <label>Date:</label><h1>{{$MIRSMaster[0]->MIRSDate}}</h1>
            </div>
          </div>
        </div>
        <div class="top-part-box3">
          <p>
            TO: The General Manager <br>
            Please furnish the following materials for :
          </p>
          <div class="purpose-container">
            <h2>{{$MIRSMaster[0]->Purpose}}</h2>
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
              @foreach ($MIRSDetails as $details)
              <tr>
                <td class="noborder-left">{{$details->ItemCode}}</td>
                <td class="particular">{{$details->Particulars}}</td>
                <td>{{$details->Unit}}</td>
                <td>{{$details->Quantity}}</td>
                <td>{{$details->Remarks}}</td>
              </tr>
              @endforeach
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
                  {{$MIRSMaster[0]->Preparedby}} <br>
                  Requisitioner
                </h2>
              </div>
              <div class="recommend-sig">
              <h4>Recommended by:</h4>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Recommendedby}}</span><br>
                Department Manager
              </h2>
              </div>
            </div>
            <div class="president-sig">
              <h4>APPROVED:</h4>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Approvedby}}</span><br>
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
