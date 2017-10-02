@extends('layouts.master')
@section('title')
  RR Preview
@endsection
@section('body')
  <div class="previewRR-Container">
    @if ((($RRMaster[0]->Verifiedby==Auth::user()->Fname.' '.Auth::user()->Lname)&&($RRMaster[0]->VerifiedbySignature==null))||(($RRMaster[0]->ReceivedOriginalby==Auth::user()->Fname.' '.Auth::user()->Lname)&&($RRMaster[0]->ReceivedOriginalbySignature==null))||(($RRMaster[0]->PostedtoBINby==Auth::user()->Fname.' '.Auth::user()->Lname)&&($RRMaster[0]->PostedtoBINbySignature==null)))
        <div class="signature-btn">
          <form class="RRsignatureForm" action="{{route('RRsigning')}}" method="post">
            {{ csrf_field() }}
            <button type="submit" name="RRNo" value="{{$RRMaster[0]->RRNo}}"><i class="fa fa-pencil"></i> Signature</button>
          </form>
          <form class="Declined" action="{{route('RRdecline')}}" method="post">
            {{ csrf_field() }}
            <button type="submit" name="RRNo" value="{{$RRMaster[0]->RRNo}}"><i class="fa fa-times"></i> Decline</button>
          </form>
        </div>
    @else
    <div class="print-RR-btn">
      <form action="{{route('RR-printing')}}" method="POST">
        {{ csrf_field() }}
        <button type="submit" class="bttn-unite bttn-xs bttn-primary" name="RRNo" value="{{$RRMaster[0]->RRNo}}"><i class="fa fa-file-pdf-o"></i> print</button>
      </form>
      <div>
        @if (!empty($checkMR[0]))
          <a href="{{route('ViewMR.ofRR',[$RRMaster[0]->RRNo])}}"><button type="button" id="full-mr-preview-btn" class="bttn-unite bttn-xs bttn-primary"><i class="fa fa-folder"></i> M.R. list</button></a>
        @endif
        @if ((Auth::user()->Role==4)||(Auth::user()->Role==3))
          <a href="{{route('create-mr',[$RRMaster[0]->RRNo])}}"><button type="button" class="make-mr bttn-unite bttn-xs bttn-primary"><i class="fa fa-plus"></i> Make M.R.</button></a>
        @endif
      </div>
    </div>
  @endif
      <div class="bondpaper-RR">
        <div class="top-title-rr">
          <h5>BOHOL I ELECTRIC COOPERATIVE,INC.</h5>
          <h6>Cabulijan, Tubigon, Bohol</h6>
        </div>
        <div class="rr-titlebox">
          RECEIVING REPORT
        </div>
        <div class="right-date-rr">
          <div class="empty-left-rr">
          </div>
          <div class="content-right-rr">
            <li><label>RR No.:</label><h5>{{$RRMaster[0]->RRNo}}</h5></li>
            <li><label>Date:</label><h5>{{$RRMaster[0]->RRDate->format('M d,Y')}}</h5></li>
          </div>
        </div>
        <div class="RRmasters-details">
          <div class="RRmaster-left">
            <ul>
              <li><label>Supplier:</label><h4>{{$RRMaster[0]->Supplier}}</h4></li>
              <li><label>Address:</label><h4>{{$RRMaster[0]->Address}}</h4></li>
              <li><label>Invoice No.:</label><h4>{{$RRMaster[0]->InvoiceNo}}</h4></li>
              <li><label>R.V. No.:</label><h4>{{$RRMaster[0]->RVNo}}</h4></li>
            </ul>
          </div>
          <div class="RRmaster-right">
            <ul>
              <li><label>Carrier:</label>@if($RRMaster[0]->Carrier!=null)<h4>{{$RRMaster[0]->Carrier}}</h4>@endif</li>
              <li><label>Delivery Receipt No:</label>@if($RRMaster[0]->DeliveryReceiptNo!=null)<h4>{{$RRMaster[0]->DeliveryReceiptNo}}</h4>@endif</li>
              <li><label>P.O. No:</label>@if($RRMaster[0]->PONo!=null)<h4>{{$RRMaster[0]->PONo}}</h4>@endif</li>
            </ul>
          </div>
        </div>
        <div class="RR-table-container">
          <table>
            <tr>
              <th class="left-side-th">Code No.</th>
              <th>Article</th>
              <th>Unit</th>
              <th>Quantity Delivered</th>
              <th>Quantity Accepted</th>
              <th>U-Cost</th>
              <th class="right-side-th">Amount</th>
            </tr>
            @foreach ($RRconfirmationDetails as $RRMTDetail)
              <tr>
                <td>{{$RRMTDetail->ItemCode}}</td>
                <td>{{$RRMTDetail->Description}}</td>
                <td>{{$RRMTDetail->Unit}}</td>
                <td>{{$RRMTDetail->RRQuantityDelivered}}</td>
                <td>{{$RRMTDetail->QuantityAccepted}}</td>
                <td>{{number_format($RRMTDetail->UnitCost,'2','.',',')}}</td>
                <td>{{number_format($RRMTDetail->Amount,'2','.',',')}}</td>
              </tr>
            @endforeach
          </table>
        </div>
        <div class="RRNetsales-Total">
          <div class="netsales-total-content">
            <li><label>Net Sales</label><h4>{{number_format($Netsales,'2','.',',')}}</h4></li>
            <li class="RRadded-VAT"><h5>Add:Vat</h5> <h5>12%</h5><h5>{{number_format($VAT,'2','.',',')}}</h5></li>
            <li><label>TOTAL AMOUNT</label><h4>{{number_format($TOTALamt,'2','.',',')}}</h4></li>
          </div>
        </div>
        <h1 class="noteRR"><label>Note:</label><p>{{$RRMaster[0]->Note}}</p></h1>
        <div class="RRSignatures-container">
          <div class="bottom-signatures-rr">
            <div class="signature-rr-left">
              <label>RECEIVED BY:</label>
              <div class="signatureRR-content">
                @if ($RRMaster[0]->ReceivedbySignature)
                  <h2><img src="/storage/signatures/{{$RRMaster[0]->ReceivedbySignature}}" alt="signature"></h2>
                @endif
                <h4>{{$RRMaster[0]->Receivedby}}</h4>
                <p>{{$RRMaster[0]->ReceivedbyPosition}}</p>
              </div>
            </div>
            <div class="signature-rr-right">
              @if ($RRMaster[0]->ReceivedOriginalbySignature)
                <h2><img src="/storage/signatures/{{$RRMaster[0]->ReceivedOriginalbySignature}}" alt="signature"></h2>
              @endif
              <label>RECEIVED ORIGINAL BY:</label>
              <div class="signatureRR-content">

                <h4>{{$RRMaster[0]->ReceivedOriginalby}}
                  @if (($RRMaster[0]->IfDeclined!=null)&&($RRMaster[0]->IfDeclined==$RRMaster[0]->ReceivedOriginalby))
                    <i class="fa fa-times"></i>
                  @endif
                </h4>
                <p>{{$RRMaster[0]->ReceivedOriginalbyPosition}}</p>
              </div>
            </div>
          </div>
          <div class="bottom-signatures-rr">
            <div class="signature-rr-left">
              <label>VERIFIED BY:</label>
              <div class="signatureRR-content">
                @if ($RRMaster[0]->VerifiedbySignature)
                  <h2><img src="/storage/signatures/{{$RRMaster[0]->VerifiedbySignature}}" alt="signature"></h2>
                @endif
                <h4>
                  {{$RRMaster[0]->Verifiedby}}
                  @if (($RRMaster[0]->IfDeclined!=null)&&($RRMaster[0]->IfDeclined==$RRMaster[0]->Verifiedby))
                    <i class="fa fa-times"></i>
                  @endif
                </h4>
                <p>{{$RRMaster[0]->VerifiedbyPosition}}</p>
              </div>
            </div>
            <div class="signature-rr-right">
              @if ($RRMaster[0]->PostedtoBINbySignature)
                <h2><img src="/storage/signatures/{{$RRMaster[0]->PostedtoBINbySignature}}" alt="signature"></h2>
              @endif
              <label>POSTED TO BIN CARD BY:</label>
              <div class="signatureRR-content">
                <h4>
                  {{$RRMaster[0]->PostedtoBINby}}
                  @if (($RRMaster[0]->IfDeclined!=null)&&($RRMaster[0]->IfDeclined==$RRMaster[0]->PostedtoBINby))
                    <i class="fa fa-times"></i>
                  @endif
                </h4>
                <p>{{$RRMaster[0]->PostedtoBINbyPosition}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
@endsection
