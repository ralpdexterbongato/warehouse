@extends('layouts.master')
@section('title')
  RV|Full preview
@endsection
@section('body')
  <div class="fullRV-container">
    <div class="RV-signature-print-container">
      @if (($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->GeneralManagerSignature!=null))
        <button type="button" name="button"><i class="fa fa-print"></i> Print</button>
      @else
        <div class="empty-left">
        </div>
      @endif
      <div class="declineOrSignatureBtn">
        @if (($RVMaster[0]->Requisitioner==Auth::user()->Fname.' '.Auth::user()->Lname)||($RVMaster[0]->BudgetOfficer==Auth::user()->Fname.' '.Auth::user()->Lname)||($RVMaster[0]->Recommendedby==Auth::user()->Fname.' '.Auth::user()->Lname)||($RVMaster[0]->GeneralManager==Auth::user()->Fname.' '.Auth::user()->Lname))
          @if (($RVMaster[0]->RequisitionerSignature!=Auth::user()->Signature)&&($RVMaster[0]->BudgetOfficerSignature!=Auth::user()->Signature)&&($RVMaster[0]->RecommendedbySignature!=Auth::user()->Signature)&&($RVMaster[0]->GeneralManagerSignature!=Auth::user()->Signature)&&($RVMaster[0]->IfDeclined==null))
            <form class="RVapprove" action="{{route('SignatureThisRV')}}" method="post">
              {{ csrf_field() }}
              <button type="submit" name="RVNo" value="{{$RVMaster[0]->RVNo}}"><i class="fa fa-pencil"></i> Signature</button>
            </form>
            <form class="RVdecline" action="{{route('DeclineRV')}}" method="post">
              {{ csrf_field() }}
              <button type="submit" name="RVNo" value="{{$RVMaster[0]->RVNo}}"><i class="fa fa-times"></i> Decline</button>
            </form>
          @endif
        @endif
      </div>
    </div>
    <div class="bondpaper-RV-container">
      <div class="bondpaper-RV">
        @if ($RVMaster[0]->IfDeclined==null)
          @if (($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->GeneralManagerSignature!=null))
            <div class="status-rv approved">
              <i class="fa fa-thumbs-up"></i>
              <h1>Approved</h1>
            </div>
          @else
            <div class="status-rv">
              <i class="fa fa-clock-o"></i>
              <h1>Pending</h1>
            </div>
          @endif
        @else
          <div class="status-rv declined">
            <i class="fa fa-times"></i>
            <h1>Declined</h1>
          </div>
        @endif
        <div class="top-rv-contents">
          <h5>BOHOL I ELECTRIC COOPERATIVE</h5>
          <h6>Cabulijan, Tubigon, Bohol</h6>
          <h4>REQUISITION VOUCHER</h4>
        </div>
        <div class="RVdate-RVNo-container">
          <ul>
            <li><label>RV No.</label><p> {{$RVMaster[0]->RVNo}}</p></li>
            <li><label>DATE:</label><p> {{$RVMaster[0]->RVDate->format('m/d/Y')}}</p></li>
          </ul>
        </div>
        <div class="to-gm-container">
          <p>TO: The General Manager</p>
          <div class="toGM-parag">
            <p>Please furnish the following Materials/Supplies for</p><h3>{{$RVMaster[0]->Purpose}}</h3>
          </div>
        </div>
        <div class="full-RVtable">
          <table>
            <tr>
              <th>Articles</th>
              <th>Unit</th>
              <th>Qty</th>
              <th>Remarks</th>
            </tr>
            @foreach ($RVDetails as $RVDetail)
              <tr>
                <td>{{$RVDetail->Particulars}}</td>
                <td>{{$RVDetail->Unit}}</td>
                <td>{{$RVDetail->Quantity}}</td>
                <td>{{$RVDetail->Remarks}}</td>
              </tr>
            @endforeach
          </table>
          <div class="certify-RV">
            <h3>I hereby certify that the above requested materials/supplies are necessary and will be used solely for the purpose stated above.</h3>
          </div>
          <div class="RVsignatures-container">
            <div class="top-signature-RV">
              <div class="RV-top-leftSignature">
                <h5>Requested by:</h5>
                  <div class="requestRV-content">
                    @if ($RVMaster[0]->RequisitionerSignature)
                      <h6><img src="/storage/signatures/{{$RVMaster[0]->RequisitionerSignature}}" alt="signature"></h6>
                    @endif
                    <p>
                      {{$RVMaster[0]->Requisitioner}}
                      @if ($RVMaster[0]->Requisitioner==$RVMaster[0]->IfDeclined)
                        <i class="fa fa-times decliner"></i>
                      @endif
                    </p>
                    <label>{{$RVMaster[0]->RequisitionerPosition}}</label>
                  </div>
              </div>
              <div class="RV-top-RightSignature">
                <h5>Recommended by</h5>
                <div class="requestRV-content">
                  @if ($RVMaster[0]->RecommendedbySignature)
                    <h6><img src="/storage/signatures/{{$RVMaster[0]->RecommendedbySignature}}" alt="signature"></h6>
                  @endif
                  <p>
                    {{$RVMaster[0]->Recommendedby}}
                    @if ($RVMaster[0]->Recommendedby==$RVMaster[0]->IfDeclined)
                      <i class="fa fa-times decliner"></i>
                    @endif
                  </p>
                  <label>{{$RVMaster[0]->RecommendedbyPosition}}</label>
                </div>
              </div>
            </div>
            <div class="bottom-RV-signatures">
              <div class="RVbottom-left-signature">
                @if ($RVMaster[0]->BudgetOfficerSignature)
                  <h6><img src="/storage/signatures/{{$RVMaster[0]->BudgetOfficerSignature}}" alt="signature"></h6>
                @endif
                <h3>BUDGET ABAILABLE ON THIS REQUEST</h3>
                <h4></h4>
                <p>
                  {{$RVMaster[0]->BudgetOfficer}}
                  @if ($RVMaster[0]->BudgetOfficer==$RVMaster[0]->IfDeclined)
                    <i class="fa fa-times decliner"></i>
                  @endif
                </p>
                <label>Budget Officer</label>
              </div>
              <div class="RVbottom-right-signature">
                <h3>Approved:</h3>
                <div class="requestRV-content">
                  @if ($RVMaster[0]->GeneralManagerSignature)
                    <h6><img src="/storage/signatures/{{$RVMaster[0]->GeneralManagerSignature}}" alt="signature"></h6>
                  @endif
                  <p>
                    {{$RVMaster[0]->GeneralManager}}
                    @if ($RVMaster[0]->GeneralManager==$RVMaster[0]->IfDeclined)
                      <i class="fa fa-times decliner"></i>
                    @endif
                  </p>
                  <label>General Manager</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
