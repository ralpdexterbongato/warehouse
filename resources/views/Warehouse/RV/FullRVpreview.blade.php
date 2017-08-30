@extends('layouts.master')
@section('title')
  RV|Full preview
@endsection
@section('body')
  <div class="fullRV-container">
    <div class="RV-signature-print-container">
      @if ((($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->GeneralManagerSignature!=null))||(($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->ApprovalReplacerSignature!=null)))
      <div class="print-and-unreceved">
        <form action="{{route('downloadRVprint')}}" method="post">
          {{ csrf_field() }}
          <button type="submit" class="bttn-unite bttn-sm bttn-primary" name="RVNo" value="{{$RVMaster[0]->RVNo}}"><i class="fa fa-print"></i> Print</button>
        </form>
        @if (($RVMaster[0]->IfPurchased==null)&&($checkPO==null)&&($checkRR!=null))
        <li class="pending-delivery-number"><h1>Unreceived items: <span class="color-blue">{{$undeliveredTotal}}</span></h1></li>
        @endif
      </div>
      @else
        <div class="empty-left">
        </div>
      @endif
      <div class="declineOrSignatureBtn">
        @if (($RVMaster[0]->RequisitionerSignature!=Auth::user()->Signature)&&($RVMaster[0]->BudgetOfficerSignature!=Auth::user()->Signature)&&($RVMaster[0]->RecommendedbySignature!=Auth::user()->Signature)&&($RVMaster[0]->GeneralManagerSignature!=Auth::user()->Signature)&&($RVMaster[0]->IfDeclined==null))
          @if(($RVMaster[0]->Requisitioner==Auth::user()->Fname.' '.Auth::user()->Lname)||($RVMaster[0]->BudgetOfficer==Auth::user()->Fname.' '.Auth::user()->Lname)||($RVMaster[0]->Recommendedby==Auth::user()->Fname.' '.Auth::user()->Lname)||($RVMaster[0]->GeneralManager==Auth::user()->Fname.' '.Auth::user()->Lname))
            @if ((Auth::user()->Fname.' '.Auth::user()->Lname==$RVMaster[0]->GeneralManager)&&($RVMaster[0]->ApprovalReplacerSignature!=null))
            @else
            <div class="RVapprove">
              <button type="button" onclick="$('.rv-signature-form').submit()"><i class="fa fa-pencil"></i> Signature</button>
            </div>
            <form class="RVdecline" action="{{route('DeclineRV',[$RVMaster[0]->RVNo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              <button type="submit"><i class="fa fa-times"></i> Decline</button>
            </form>
            @endif
          @endif
        @endif
        @if (($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->GeneralManagerSignature==null)&&($RVMaster[0]->ApprovalReplacerFname==null)&&($RVMaster[0]->ApprovalReplacerLname==null)&&(Auth::user()->Role==0))
          <div class="managerNote">
            <h1><i class="fa fa-info-circle color-blue"></i> If the GM is N/A, you can click this</h1>
          </div>
          <form class="signature-if-gm-NA" action="{{route('sending.approvebehalf.admin',[$RVMaster[0]->RVNo])}}" method="post">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <button type="submit"><i class="fa fa-pencil color-blue"></i> Approve Signature</button>
          </form>
        @endif
        @if ((Auth::user()->Role==0)&&($RVMaster[0]->ApprovalReplacerFname!=null)&&($RVMaster[0]->ApprovalReplacerLname!=null)&&($RVMaster[0]->ApprovalReplacerSignature==null))
          <div class="wait-for-confim-msg"><h2><i class="fa fa-info-circle color-blue"></i> Please wait for the confimation from the <span class="color-blue"><i class="fa fa-user"></i> Admin</span></h2></div>
          <form class="cancel-replace-btn" action="{{route('cancel-rv-approve-inbehalf',[$RVMaster[0]->RVNo])}}" method="post">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <button type="submit"><i class="fa fa-times"></i></button>
          </form>
        @endif
        @if($checkPO!=null)
          <div class="viewPObtn">
            @if (!empty($RVMaster[0]->IfPurchased))
              <div class="status-po-wrapper">
                <h1>Status : <span class="underline">Already Purchased</span> <i class="fa fa-check"></i></h1>
              </div>
            @else
              <div class="status-po-wrapper">
                <h1>Status: <span class="underline">Waiting for all to be received</span></h1>
              </div>
            @endif
            <a href="{{route('POListView',[$RVMaster[0]->RVNo])}}"><button type="button" class="bttn-unite bttn-sm bttn-primary">Show P.O.</button></a>
          </div>
        @elseif((($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->GeneralManagerSignature!=null)&&($checkPO==null))||(($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->ApprovalReplacerSignature)))
          @if (($RVMaster[0]->IfPurchased==null)&&($checkPO==null))
          <div class="status-po-wrapper">
            <h1 class="no-PO">Status : <span class="underline">Waiting for all to be received</span></h1>
          </div>
          @endif
          @if (($RVMaster[0]->IfPurchased)&&($checkPO==null))
          <div class="status-po-wrapper">
            <h1 class="done-but-no-po">Status : <span class="underline">Already purchased <i class="fa fa-check"></i> without P.O.</span></h1>
          </div>
          @elseif(($RVMaster[0]->IfPurchased==null)&&($checkPO==null)&&((Auth::user()->Role==4)||(Auth::user()->Role==3)))
            <div class="CreateRRwoPO">
              <a href="{{route('CreatingRR.without.po',[$RVMaster[0]->RVNo])}}"><button type="button" class="bttn-unite bttn-sm bttn-primary"> <i class="fa fa-plus"></i> RR</button></a>
            </div>
          @endif
        @endif
      @if ((($checkRR==null)&&($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->GeneralManagerSignature!=null)&&(Auth::user()->Role==4)&&($RVMaster[0]->IfPurchased==null))||(($checkRR==null)&&($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->ApprovalReplacerSignature!=null)&&(Auth::user()->Role==4)&&($RVMaster[0]->IfPurchased==null))||(($checkRR==null)&&($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->GeneralManagerSignature!=null)&&(Auth::user()->Role==3)&&($RVMaster[0]->IfPurchased==null))||(($checkRR==null)&&($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->ApprovalReplacerSignature!=null)&&(Auth::user()->Role==3)&&($RVMaster[0]->IfPurchased==null)))
        <div class="CanvasBtn">
          <form action="{{route('TocanvassPage',[$RVMaster[0]->RVNo])}}" method="GET">
            <button type="submit" class="bttn-unite bttn-sm bttn-primary"><i class="fa fa-building"></i> Canvass</button>
          </form>
        </div>
      @elseif ($checkRR!=null)
        <div class="show-rr-of-rv">
          <a href="{{route('showRR-ofRV',[$RVMaster[0]->RVNo])}}"><button class="bttn-unite bttn-sm bttn-primary" type="button">R.R. history</button></a>
        </div>
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
          @elseif(($RVMaster[0]->RequisitionerSignature!=null)&&($RVMaster[0]->RecommendedbySignature!=null)&&($RVMaster[0]->BudgetOfficerSignature!=null)&&($RVMaster[0]->ApprovalReplacerSignature!=null))
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
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>.</td>
            </tr>
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
                <h5>Recommended by:</h5>
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
                <h3>BUDGET AVAILABLE ON THIS REQUEST</h3>
                <h4>
                    <form class="rv-signature-form" action="{{route('SignatureThisRV')}}" method="post">
                      {{ csrf_field() }}
                      <input type="text" autocomplete="off" name="RVNo" value="{{$RVMaster[0]->RVNo}}" style="display:none">
                      @if ((Auth::user()->Role==7)&&($RVMaster[0]->BudgetAvailable==null)&&($RVMaster[0]->BudgetOfficer==Auth::user()->Fname.' '.Auth::user()->Lname))
                      <input class="forBudgetOfficerOnly" placeholder="Input Budget available" type="text" autocomplete="off" name="BudgetAvailable">
                      @endif
                    </form>
                        <span class="budget-number">₱ {{number_format($RVMaster[0]->BudgetAvailable,'2','.',',')}}</span>
                        @if ((Auth::user()->Role==7)&&($RVMaster[0]->BudgetAvailable!=null)&&($RVMaster[0]->BudgetOfficer==Auth::user()->Fname.' '.Auth::user()->Lname))
                          <form class="form-edit-budget" action="{{route('Update.budget',[$RVMaster[0]->RVNo])}}" method="post">
                            {{ csrf_field() }}
                            {{ method_field('put')}}
                            <input type="text" autocomplete="off" name="BudgetUpdate" autocomplete="off" class="editbudget">
                            <button class="editbudget" type="submit"><i class="fa fa-check"></i></button>
                            <button type="button" class="editbudget cancel-edit"><i class="fa fa-times"></i></i></button>
                            <button type="button" class="edit-budget-opener"><i class="fa fa-pencil-square-o"></i></button>
                          </form>
                      @endif
                </h4>
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
            @if ($RVMaster[0]->ApprovalReplacerSignature)
              <div class="ApprovedRVInbehalf">
                <h3>Approved in behalf of the General Manager {{$RVMaster[0]->GeneralManager}}:</h3>
                <div class="approve-replacer-rv-info">
                  <h1><img src="/storage/signatures/{{$RVMaster[0]->ApprovalReplacerSignature}}" alt="signature"></h1>
                  <h4>{{$RVMaster[0]->ApprovalReplacerFname.' '.$RVMaster[0]->ApprovalReplacerLname}}</h4>
                  <p>{{$RVMaster[0]->ApprovalReplacerPosition}}</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
