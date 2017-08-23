@extends('layouts.master')
@section('title')
  Print preview| MIRS
@endsection
@section('body')
  <div class="printable-view-container">
    <div class="printable-paper">
      <div class="print-btn-container">
        @if (($MIRSMaster[0]->PreparedSignature!=null)&&($MIRSMaster[0]->RecommendSignature!=null)&&($MIRSMaster[0]->ApproveSignature!=null))
        <form class="download-form" action="{{route('mirs-download')}}" method="post">
          {{csrf_field()}}
          <input type="text" autocomplete="off" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
          <button type="submit">PDF <i class="fa fa-file-pdf-o"></i></button>
          unclaimed:<span class="color-blue">{{$unclaimed}}</span>
        </form>
        @elseif($MIRSMaster[0]->ApprovalReplacerSignature!=null)
          <form class="download-form" action="{{route('mirs-download')}}" method="post">
            {{csrf_field()}}
            <input type="text" autocomplete="off" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
            <button type="submit">PDF <i class="fa fa-file-pdf-o"></i></button>
            unclaimed:<span class="color-blue">{{$unclaimed}}</span>
          </form>
        @else
          <div class="empty-left-mirs">
          </div>
        @endif
        @if ($MIRSMaster[0]->ApprovalReplacerSignature==null)
          @if (($MIRSMaster[0]->PreparedSignature==null)||($MIRSMaster[0]->RecommendSignature==null)||($MIRSMaster[0]->ApproveSignature==null))
            @if (($MIRSMaster[0]->ApproveSignature!=Auth::user()->Signature)&&($MIRSMaster[0]->RecommendSignature != Auth::user()->Signature)&&($MIRSMaster[0]->PreparedSignature != Auth::user()->Signature)&&($MIRSMaster[0]->IfDeclined == null))
             @if (($MIRSMaster[0]->Preparedby == Auth::user()->Fname . ' '. Auth::user()->Lname)||($MIRSMaster[0]->Approvedby == Auth::user()->Fname . ' '. Auth::user()->Lname)||($MIRSMaster[0]->Recommendedby == Auth::user()->Fname . ' '. Auth::user()->Lname))
              <div class="middle-status">
                <form class="Accept" action="{{route('MIRSSign')}}" method="post">
                  {{ csrf_field() }}
                  <button type="submit" id="accepted" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}"><i class="fa fa-pencil"></i> Signature</button>
                </form>
                <form class="Deny" action="{{route('DeniedMIRS',[$MIRSMaster[0]->MIRSNo])}}" method="POST">
                  {{ csrf_field() }}
                  {{ csrf_field('PUT') }}
                  <button type="submit" id="not-accepted"><i class="fa fa-times"></i> Decline</button>
                </form>
              </div>
             @endif
           @endif
        @endif
        @endif
      @if (($MIRSMaster[0]->ApproveSignature==null)&&($MIRSMaster[0]->RecommendSignature!=null)&&(Auth::user()->Role==0)&&($MIRSMaster[0]->ApprovalReplacerFname==null)&&($MIRSMaster[0]->ApprovalReplacerLname==null))
        <form class="GMisAbsent-btn" action="{{route('GmIsAbsent',[$MIRSMaster[0]->MIRSNo])}}" method="post">
          <i class="fa fa-info-circle"></i> If the General Manager is N/A, you can click this
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit"><i class="fa fa-pencil"></i> Approve Signature</button>
        </form>
      @endif
      @if ((Auth::user()->Fname.' '.Auth::user()->Lname==$MIRSMaster[0]->ApprovalReplacerFname.' '.$MIRSMaster[0]->ApprovalReplacerLname)&&($MIRSMaster[0]->ApprovalReplacerSignature==null))
      <div class="gm-isnot-absent">
        <div class="request-to-admin-message">
          <label><i class=" fa fa-info-circle color-blue"> </i>  Please wait for the confimation from the <span class="color-blue"><i class="fa fa-user"> </i> Admin</span>.</label>
        </div>
        <form class="gm-isnot-absent-btn" action="{{route('cancel-request-toadmin',[$MIRSMaster[0]->MIRSNo])}}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit"><i class="fa fa-times"></i></button>
        </form>
      </div>
      @endif
  @if(((Auth::user()->Role==4)&&($MIRSMaster[0]->PreparedSignature!=null)&&($MIRSMaster[0]->RecommendSignature!=null)&&($MIRSMaster[0]->ApproveSignature!=null))||((Auth::user()->Role==4)&&($MIRSMaster[0]->PreparedSignature!=null)&&($MIRSMaster[0]->RecommendSignature!=null)&&($MIRSMaster[0]->ApprovalReplacerSignature!=null))||((Auth::user()->Role==3)&&($MIRSMaster[0]->PreparedSignature!=null)&&($MIRSMaster[0]->RecommendSignature!=null)&&($MIRSMaster[0]->ApproveSignature!=null))||((Auth::user()->Role==3)&&($MIRSMaster[0]->PreparedSignature!=null)&&($MIRSMaster[0]->RecommendSignature!=null)&&($MIRSMaster[0]->ApprovalReplacerSignature!=null)))
    <div class="mct-create-mct-list">
      <a href="{{route('toRecordingMCT.Page',[$MIRSMaster[0]->MIRSNo])}}"><button type="button" id="mct-modal-btn" name="button"><i class="fa fa-plus"></i> Record MCT</button></a>
      @if((Auth::user()->Role!=4)&&($MCTNumber[0]==null))
        <h1>No MCT generated yet</h1>
      @elseif(!empty($MCTNumber[0]))
        <a href="{{route('MCTofMIRS',[$MIRSMaster[0]->MIRSNo])}}"><button type="button" name="button"><i class="fa fa-table"></i> M.C.T. list</button></a>
      @endif
    </div>
  @endif{{-- end of isset --}}
      </div>
      <div class="bondpaper-size">
        @if (isset($MIRSMaster[0]->MIRSNo) && isset($MIRSDetail[0]->MIRSNo))
        <div class="top-part-box1">
          <h1>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h1>
            <h4>Cabulijan, Tubigon, Bohol</h4>
            <h2>MATERIALS ISSUANCE REQUISITION SLIP</h2>
              @if ($MIRSMaster[0]->IfDeclined==null)
                @if(($MIRSMaster[0]->ApprovalReplacerSignature!=null)&&($MIRSMaster[0]->RecommendSignature!=null)&&$MIRSMaster[0]->PreparedSignature!=null)
                  <div class="status-mirs approved">
                    <h1 class="approved-sign"><i class="fa fa-thumbs-up"></i> <br>Approved</h1>
                  </div>
                @else
                  @if (($MIRSMaster[0]->PreparedSignature!=null)&&($MIRSMaster[0]->ApproveSignature!=null)&&($MIRSMaster[0]->RecommendSignature!=null))
                  <div class="status-mirs approved">
                    <h1 class="approved-sign"><i class="fa fa-thumbs-up"></i> <br>Approved</h1>
                  </div>
                  @else
                  <div class="status-mirs">
                    <h1><i class="fa fa-clock-o"></i><br>Pending</h1>
                  </div>
                  @endif
                @endif
              @else
              <div class="status-mirs declined">
                <h1 class="deny-sign"><i class="fa fa-times"></i><br>Declined</h1>
              </div>
              @endif
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
                <h3>
                  @if (!empty($MIRSMaster[0]->PreparedSignature))
                    <img src="/storage/signatures/{{$MIRSMaster[0]->PreparedSignature}}">
                  @endif
                </h3>
                <h2>
                  {{$MIRSMaster[0]->Preparedby}}
                  @if ($MIRSMaster[0]->IfDeclined==$MIRSMaster[0]->Preparedby)
                    <i class="fa fa-times decliner"></i>
                  @endif <br>
                  {{$MIRSMaster[0]->PreparedPosition}}
                </h2>
              </div>
              <div class="recommend-sig">
              <h4>Recommended by:</h4>
              <h3>
                @if (!empty($MIRSMaster[0]->RecommendSignature))
                  <img src="/storage/signatures/{{$MIRSMaster[0]->RecommendSignature}}">
                @endif
              </h3>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Recommendedby}}
                @if ($MIRSMaster[0]->IfDeclined==$MIRSMaster[0]->Recommendedby)
                  <i class="fa fa-times decliner"></i>
                @endif
              </span><br>
                {{$MIRSMaster[0]->RecommendPosition}}
              </h2>
              </div>
            </div>
            <div class="president-sig">
              <h4>APPROVED:</h4>
              <h3>
                @if (!empty($MIRSMaster[0]->ApproveSignature))
                  <img src="/storage/signatures/{{$MIRSMaster[0]->ApproveSignature}}">
                @endif
              </h3>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Approvedby}}
                @if ($MIRSMaster[0]->IfDeclined==$MIRSMaster[0]->Approvedby)
                  <i class="fa fa-times decliner"></i>
                @endif
              </span><br>
                {{$MIRSMaster[0]->ApprovePosition}}
              </h2>
            </div>
          </div>
          @if ($MIRSMaster[0]->ApprovalReplacerSignature!=null)
            <div class="Signatured-in-behalf">
              <h1>Approved in behalf of the General Manager {{$MIRSMaster[0]->Approvedby}}:</h1>
              <div class="approving-manager-Mirs">
                <h2><img src="/storage/signatures/{{$MIRSMaster[0]->ApprovalReplacerSignature}}" alt=""></h2>
                <h5 class="bold">{{$MIRSMaster[0]->ApprovalReplacerFname.' '.$MIRSMaster[0]->ApprovalReplacerLname}}</h5>
                <h3>{{$MIRSMaster[0]->ApprovalReplacerPosition}}</h3>
              </div>
            </div>
          @endif
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
