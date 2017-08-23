@extends('layouts.master')
@section('title')
  M.R.|full Preview
@endsection
@section('body')
<div class="MR-full-container">
  <div class="btns-mr-full">
    <div>
      @if ((($MRMaster[0]->RecommendedbySignature!=null)&&($MRMaster[0]->GeneralManagerSignature!=null))||(($MRMaster[0]->RecommendedbySignature!=null)&&($MRMaster[0]->ApprovalReplacerSignature!=null)))
        <form action="{{route('printMR')}}" method="post">
          {{ csrf_field() }}
          <button type="submit" name="MRNo" value="{{$MRMaster[0]->MRNo}}"><i class="fa fa-print"></i> Print</button>
        </form>
      @endif
    </div>
    <div class="signature-MR-btns">
      @if (($MRMaster[0]->RecommendbySignature==null)||($MRMaster[0]->GeneralManagerSignature==null))
        @if (($MRMaster[0]!=null)&&($MRMaster[0]->RecommendedbySignature!=Auth::user()->Signature)&&($MRMaster[0]->GeneralManagerSignature != Auth::user()->Signature)&&($MRMaster[0]->IfDeclined == null)&&($MRMaster[0]->ApprovalReplacerSignature==null))
          @if (($MRMaster[0]->Recommendedby == Auth::user()->Fname . ' '. Auth::user()->Lname)||($MRMaster[0]->GeneralManager == Auth::user()->Fname . ' '. Auth::user()->Lname))
        <form class="signature-mr" action="{{route('SignatureMR')}}" method="post">
          {{ csrf_field() }}
          <button type="submit" name="MRNo" value="{{$MRMaster[0]->MRNo}}"><i class="fa fa-pencil"></i> Signature</button>
        </form>
        <form class="decline-mr" action="{{route('DeclineMR')}}" method="post">
          {{ csrf_field() }}
          <button type="submit" name="MRNo" value="{{$MRMaster[0]->MRNo}}">Decline</button>
        </form>
        @endif
      @endif
    @endif
    @if ((Auth::user()->Role==0)&&($MRMaster[0]->RecommendedbySignature!=null)&&($MRMaster[0]->ApprovalReplacerFname==null)&&($MRMaster[0]->ApprovalReplacerLname==null))
      <div class="managerNote"><h1><i class="fa fa-info-circle color-blue"></i> If the <span class="underline">General Manager</span> is N/A, you can click this </h1></div>
      <form class="submit-authorize" action="{{route('ApproveMR.inbehalf',[$MRMaster[0]->MRNo])}}" method="post">
        {{ csrf_field() }}
        {{ method_field('PUT')}}
        <button type="submit"><i class="fa fa-pencil color-blue"></i> Approve</button>
      </form>
    @elseif ((Auth::user()->Role==0)&&($MRMaster[0]->RecommendedbySignature!=null)&&($MRMaster[0]->ApprovalReplacerFname!=null)&&($MRMaster[0]->ApprovalReplacerLname!=null)&&($MRMaster[0]->ApprovalReplacerSignature==null))
      <div class="wait-for-confim-msg">
        <h2><i class="fa fa-info-circle color-blue"></i> Please wait for the confimation from the <span class="color-blue"><i class="fa fa-user"></i> Admin</span></h2>
        <form class="cancel-replace-btn" action="{{route('ApproveinBehalfCancel',[$MRMaster[0]->MRNo])}}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit"><i class="fa fa-times"></i></button>
        </form>
      </div>
    @endif
    </div>
  </div>
  <div class="mr-full-bondpaper">
    <div class="mr-top-titles">
      <h1>BOHOL I ELECTRIC COOPERATIVE, INC.</h1>
      <h3>Cabulijan, Tubigon, Bohol</h3>
      <h2>MEMORANDUM RECEIPT FOR EQUIPMENT . SEMI-EXPENDABLE AND NON EXPENDABLE PROPERTY</h2>
    </div>
    <div class="list-number-dates">
      <div class="DateNumBox">
        <h1>RR No. {{$MRMaster[0]->RRNo}}</h1>
        <p>{{$MRMaster[0]->RRDate->format('M d, Y')}}</p>
      </div>
      <div class="DateNumBox">
        <h1>RV No. {{$MRMaster[0]->RVNo}}</h1>
        <p>{{$MRMaster[0]->RVDate->format('M d, Y')}}</p>
      </div>
      <div class="DateNumBox">
        <h1>MR No. {{$MRMaster[0]->MRNo}}</h1>
        <p>{{$MRMaster[0]->MRDate->format('M d, Y')}}</p>
      </div>
    </div>
    <div class="acknowledgeParagraph">
      <p class="align-center">I HEREBY ACKNOWLEGE to have received from
        <span class="bold">{{$MRMaster[0]->WarehouseMan}}</span> Warehouseman
        the following</p><p> property
        for which I am responsible, subject to the provision of the usual accounting and auditing rules and regulations
        and which will be used for General Services.
      </p>
    </div>
    <div class="table-mr-list-container">
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
        @foreach ($MRMaster[0]->MRDetail as $mrdata)
        <tr>
          <td>{{$mrdata->Quantity}}</td>
          <td>{{$mrdata->Unit}}</td>
          <td>{{$mrdata->NameDescription}}</td>
          <td></td>
          <td>{{number_format($mrdata->UnitValue,'2','.',',')}}</td>
          <td>{{number_format($mrdata->TotalValue,'2','.',',')}}</td>
          <td>{{$mrdata->Remarks}}</td>
        </tr>
        @endforeach
        <tr>
          <td>.</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>
    </div>
    <div class="note-mr-container">
      <p>Note:{{$MRMaster[0]->Note}}</p>
    </div>
    <div class="bottom-mr-bondpaper">
      <div class="left-reference-box">
        <div class="reference-box">
          <h3>REFERENCE:</h3>
          <p>Purchase from: {{$MRMaster[0]->Supplier}}</p>
          <p>Invoice number: {{$MRMaster[0]->InvoiceNo}}</p>
        </div>
        <div class="instruction-for-mr">
          <h3>INSTRUCTION</h3>
          <p>
            This form shall be prepared in FOUR (4)<br>
            LEGIBLE COPIES,DISTRIBUTION:(1) ORIGINAL<br>
            should be KEPT by the Accountable Officer<br>
            (2) DUPLICATE must be FILED in the Personal<br>
             file of the Employee Concerned. (3) TRIPLICATE <br>
             should be FILED in the OFFICE OF THE <br>
             Accounting Section.(4) QUADRUPLICATE <br>
             must be KEPT by the Responsible Employee.
           </p>
        </div>
        @if ($MRMaster[0]->ApprovalReplacerSignature)
          <div class="Approve-inbehalf-MR">
            <p>APPROVED IN BEHALF OF THE <br>GENERAL MANAGER <span class="underline uppercase">{{$MRMaster[0]->GeneralManager}}</span> BY :</p>
            <div class="signature-of-replacer">
              <h2><img src="/storage/signatures/{{$MRMaster[0]->ApprovalReplacerSignature}}" alt="signature"></h2>
              <h1 class="underline">{{$MRMaster[0]->ApprovalReplacerFname}} {{$MRMaster[0]->ApprovalReplacerLname}}</h1>
              <p>{{$MRMaster[0]->ApprovalReplacerPosition}}</p>
            </div>
          </div>
        @endif
      </div>
      <div class="MR-Signatures-container">
        <h4>P.O. Number: {{$MRMaster[0]->PONo}}</h4>
        <div class="signature-mr-box">
          <label>RECOMMENDING APPROVAL:</label>
          @if ($MRMaster[0]->RecommendedbySignature)
            <h5><img src="/storage/signatures/{{$MRMaster[0]->RecommendedbySignature}}" alt="signature"></h5>
          @endif
          <h3>
            {{$MRMaster[0]->Recommendedby}}
            @if ($MRMaster[0]->Recommendedby==$MRMaster[0]->IfDeclined)
              <i class="fa fa-times decliner"></i>
            @endif
          </h3>
          <p>{{$MRMaster[0]->RecommendedbyPosition}}</p>
        </div>
        <div class="signature-mr-box">
          <label>APPROVED:</label>
          @if ($MRMaster[0]->GeneralManagerSignature)
            <h5><img src="/storage/signatures/{{$MRMaster[0]->GeneralManagerSignature}}" alt="signature"></h5>
          @endif
          <h3>
            {{$MRMaster[0]->GeneralManager}}
            @if ($MRMaster[0]->GeneralManager==$MRMaster[0]->IfDeclined)
              <i class="fa fa-times decliner"></i>
            @endif
          </h3>
          <p>General Manager</p>
        </div>
        <div class="signature-mr-box">
          <label>RECEIVED:</label>
          <h3>{{$MRMaster[0]->Receivedby}}</h3>
          <p>{{$MRMaster[0]->ReceivedbyPosition}}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
