@extends('layouts.master')
@section('title')
  PO|Full Preview
@endsection
@section('body')
  <div class="full-PO-container">
    <div class="po-full-buttons">
      <div class="print-po-btn">
        @if (($OrderMaster[0]->GeneralManagerSignature!=null)||($OrderMaster[0]->ApprovalReplacerSignature!=null))
          <form action="{{route('downloadPO')}}" method="post">
            {{ csrf_field() }}
            <button type="submit" name="PONo" value="{{$OrderMaster[0]->PONo}}"><i class="fa fa-print"></i> Print</button>
          </form>
        @else
          <div class="empty-left">
          </div>
        @endif
        <div class="signature-btns-wrap-po">
          @if ((Auth::check()) && (Auth::user()->Role==2) && ($OrderMaster[0]->GeneralManager==Auth::user()->Fname.' '.Auth::user()->Lname)&&($OrderMaster[0]->GeneralManagerSignature==null)&&($OrderMaster[0]->IfDeclined==null)&&($OrderMaster[0]->ApprovalReplacerSignature==null))
          <form class="signaturePObtn" action="{{route('GMSignatureOrder')}}" method="post">
            {{ csrf_field() }}
            <input type="text" name="PONo" value="{{$OrderMaster[0]->PONo}}" style="display:none">
            <button  type="submit" name="RVNo" value="{{$OrderMaster[0]->RVNo}}"><i class="fa fa-pencil"></i> Signature</button>
          </form>
          <form class="declinePObtn" action="{{route('GMDeclining')}}" method="post">
            {{ csrf_field() }}
            <button type="submit" name="PONo" value="{{$OrderMaster[0]->PONo}}"><i class="fa fa-times"></i> Decline</button>
          </form>
          @endif
          @if ((Auth::user()->Role==0)&&($OrderMaster[0]->ApprovalReplacerFname==null)&&($OrderMaster[0]->ApprovalReplacerLname==null)&&($OrderMaster[0]->GeneralManagerSignature==null))
            <div class="managerNote"><h1><i class="fa fa-info-circle color-blue"></i> If the <span class="underline">General Manager</span> is N/A, you can click this </h1></div>
            <form class="submit-authorize" action="{{route('AuthorizedInBehalf',[$OrderMaster[0]->PONo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT')}}
              <button type="submit"><i class="fa fa-pencil color-blue"></i> Authorize</button>
            </form>
          @elseif ((Auth::user()->Role==0)&&($OrderMaster[0]->ApprovalReplacerFname==Auth::user()->Fname)&&($OrderMaster[0]->ApprovalReplacerLname==Auth::user()->Lname)&&($OrderMaster[0]->ApprovalReplacerSignature==null))
          <div class="wait-for-confim-msg">
            <h2><i class="fa fa-info-circle color-blue"></i> Please wait for the confimation from the <span class="color-blue"><i class="fa fa-user"></i> Admin</span></h2>
            <form class="cancel-replace-btn" action="{{route('AuthorizeInBehalfCancel',[$OrderMaster[0]->PONo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              <button type="submit"><i class="fa fa-times"></i></button>
            </form>
          </div>
          @endif
        </div>
      </div>
    </div>
    <div class="PO-bondpaper">
      <div class="PO-top-titles">
        <h3>BOHOL 1 ELECTRIC COOPERATIVE INC.</h3>
        <h3>CABULIJAN, TUBIGON, BOHOL</h3>
        <p>Tel# 508-9741 / 508-9731</p>
        <h1>PURCHASE ORDER</h1>
      </div>
      <div class="po-master-data">
        <div class="left-data-po">
          <ul>
            <li><label>TO: </label> <h1>{{$OrderMaster[0]->Supplier}}</h1></li>
            <li><label></label><h1>{{$OrderMaster[0]->Address}}</h1></li>
            <li><label></label><h1>Tel# {{$OrderMaster[0]->Telephone}}</h1></li>
          </ul>
        </div>
        <div class="right-data-po">
          <ul>
            <li><label>P.O. No.</label><h1>{{$OrderMaster[0]->PONo}}</h1></li>
            <li><label>DATE:</label><h1>{{$OrderMaster[0]->PODate->format('M d, Y')}}</h1></li>
            <li><label>TERMS:</label><h1></h1></li>
            <li>({{$OrderMaster[0]->Purpose}})</li>
          </ul>
        </div>
      </div>
      <div class="po-statement">
        <p>Please furnish the following articles subject to all terms and conditions stated here and in accordance with the quotation.</p>
      </div>
      <div class="PO-Details-table">
        <table>
          <tr>
            <th>ITEM</th>
            <th>QTY</th>
            <th>UNIT</th>
            <th>DESCRIPTION</th>
            <th>UNIT PRICE</th>
            <th>AMOUNT</th>
          </tr>
          @foreach ($OrderMaster[0]->PODetails as $key=> $POdetail)
            <tr>
              <td>{{$key+1}}</td>
              <td>{{$POdetail->Qty}}</td>
              <td>{{$POdetail->Unit}}</td>
              <td>{{$POdetail->Description}}</td>
              <td>{{number_format($POdetail->Price,'2','.',',')}}</td>
              <td>{{number_format($POdetail->Amount,'2','.',',')}}</td>
            </tr>
          @endforeach
          <tr class="total-amt-po">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>TOTAL AMOUNT:</td>
            <td>{{number_format($totalAmt,'2','.',',')}}</td>
          </tr>
        </table>
      </div>
      <div class="RV-data-in-PO">
        <h1>APPROVED RV No.{{$OrderMaster[0]->RVNo}}</h1><p>Dated: {{$OrderMaster[0]->RVDate->format('M d, Y')}}</p>
      </div>
      <div class="signatures-po">
        <div class="label-signatures-po">
         <h4>ACCEPTED ORDER AND RECEIVED<br> ORIGINAL COPY OF THIS PURCHASE<br> ORDER:</h4>
         <h1></h1>
         <p>(Seller)</p>
         <h1></h1>
        </div>
        <div class="label-signatures-po">
          <h4>ORDER ISSUED AND AUTHORIZED<br> BY:</h4>
          <li>
            @if ($OrderMaster[0]->GeneralManagerSignature)
              <h6><img src="/storage/signatures/{{$OrderMaster[0]->GeneralManagerSignature}}" alt="signature"></h6>
            @endif
            <h3>
              {{$OrderMaster[0]->GeneralManager}}
              @if ($OrderMaster[0]->IfDeclined)
                <i class="fa fa-times decliner"></i>
              @endif
            </h3>
            <label>General Manager</label>
          </li>
        </div>
      </div>
      @if ($OrderMaster[0]->ApprovalReplacerSignature)
        <div class="Authorized-in-behalf">
          <h4>ORDER ISSUED AND AUTHORIZED <br> IN BEHALF OF THE GENERAL MANAGER DINO NICOLAS ROXAS BY :</h4>
          <div class="signature-inbehalf-authorizer">
            <h1><img src="/storage/signatures/{{$OrderMaster[0]->ApprovalReplacerSignature}}" alt="signature"></h1>
            <p>{{$OrderMaster[0]->ApprovalReplacerFname.' '.$OrderMaster[0]->ApprovalReplacerLname}}</p>
            <label>{{$OrderMaster[0]->ApprovalReplacerPosition}}</label>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection
