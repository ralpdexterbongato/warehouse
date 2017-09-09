@extends('layouts.master')
@section('title')
  Printable| MIRS
@endsection
@section('body')
  <div class="printable-view-container">
    <div class="printable-paper">
      <div class="print-btn-container">
        @if (isset($MIRSMaster[0]->MIRSNo) && isset($MIRSDetail[0]->MIRSNo))
        <form class="download-form" action="{{route('mirs-download')}}" method="post">
          {{csrf_field()}}
          <input type="text" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
          <button type="submit">PDF <i class="fa fa-file-pdf-o"></i></button>
        </form>
      @else
        <h1>NO CURRENT MIRS RESULT</h1>
      @endif
      @if (!empty($MIRSMaster[0]))
        @if (empty($MIRSMaster[0]->Status))
          <div class="middle-status">
            <form class="Accept" action="index.html" method="post">
              {{ csrf_field() }}
              <button type="button" id="accepted" name="approved-button"><i class="fa fa-thumbs-up"></i>Approve</button>
            </form>
            <form class="Deny" action="denied" method="POST">
              {{ csrf_field() }}
              <input type="text" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
              <button type="submit" id="not-accepted"><i class="fa fa-thumbs-down"></i>Disapprove</button>
            </form>
          </div>
        @else
          <div class="MCT-link-btn">
            <form action="{{route('previewMCT')}}" method="GET">
              <input type="text" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
              <button type="submit"><i class="fa fa-eye"></i> VIEW MCT</button>
            </form>
          </div>
        @endif
      @endif
      </div>
      <div class="bondpaper-size">
        @if (isset($MIRSMaster[0]->MIRSNo) && isset($MIRSDetail[0]->MIRSNo))
        <div class="top-part-box1">
          <h1>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h1>
            <h4>Cabulijan, Tubigon, Bohol</h4>
            <h2>MATERIALS ISSUANCE REQUISITION SLIP</h2>
            <div class="status-mirs">
              @if (empty($MIRSMaster[0]->Status))
              <h1><i class="fa fa-clock-o"></i><br>Pending</h1>
              @else
              <h1 class="approved-sign"><i class="fa fa-thumbs-up"></i> <br>Approved</h1>
              @endif
            </div>
        </div>
        <div class="top-part-box2">
          <div class="top-box2-left">
          </div>
          <div class="top-box2-right">
            <div class="top-box2-right-data">
              <label>MIRS #:</label><h1>{{$MIRSMaster[0]->MIRSNo}}</h1>
            </div>
            <div class="top-box2-right-data">
              <label>Date:</label><h1>{{$MIRSMaster[0]->MIRSDate->format('M d, Y')}}</h1>
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
              @foreach ($MIRSDetail as $details)
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
                <h3><img src="/DesignIMG/signature1.png"></h3>
                <h2>
                  {{$MIRSMaster[0]->Preparedby}} <br>
                  Requisitioner
                </h2>
              </div>
              <div class="recommend-sig">
              <h4>Recommended by:</h4>
              <h3><img src="/DesignIMG/signature4.png"></h3>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Recommendedby}}</span><br>
                Department Manager
              </h2>
              </div>
            </div>
            <div class="president-sig">
              <h4>APPROVED:</h4>
              <h3><img src="/DesignIMG/signature5.png"></h3>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Approvedby}}</span><br>
                General Manager
              </h2>
            </div>
          </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="MCT-modal">
    <div class="MCT-form" style="background:white">
      <div class="title-mct-form">
        <h1>MCT Form</h1>
      </div>
      <div class="form-content-mct">
        @if (!empty($MIRSMaster[0]))
        <form class="" action="{{route('Storing.MCT')}}" method="post">
          {{ csrf_field() }}
          <input type="text" name="AddressTo" placeholder="Addressed to" required><br>
          <input type="text" name="Issuedby" placeholder="Issued by" required><br>
          <input type="text" name="Recievedby" placeholder="Recieved by" required>
          <input type="text" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
          <input type="text" name="MIRSDate" value="{{$MIRSMaster[0]->MIRSDate}}" style="display:none">
          <input type="text" name="Particulars" value="{{$MIRSMaster[0]->Purpose}}"style=" display:none">
          <div class="mct-form-buttons">
            <button type="button" id="cancel-mct" name="button">Cancel</button>
            <button type="submit">Submit</button>
          </div>
        </form>
        @endif
      </div>
    </div>
  </div>

@endsection
