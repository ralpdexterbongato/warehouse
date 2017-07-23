@extends('layouts.master')
@section('title')
  MIRS|Preview
@endsection
@section('body')
  <div class="Preview-MCT-container">
      <div class="print-MCT-wrap">
        <div class="MCT-title">
          @if (($MCTMast[0]->IssuedbySignature!=null)&&($MCTMast[0]->ReceivedbySignature!=null))
            <form class="print-buttonMCT"action="{{route('print-mct')}}" method="post">
              {{ csrf_field() }}
              <input type="text" name="MIRSNo" value="{{$MCTMast[0]->MIRSNo}}" style="display:none">
              <button type="submit" name="button"><i class="fa fa-file-pdf-o"></i>.pdf</button>
            </form>
          @else
            <div class="empty-div-left">
            </div>
          @endif
          @if (($MCTMast[0]->IssuedbySignature!=null)&&($MCTMast[0]->ReceivedbySignature!=null))
          @if (($MRTcheck[0]==null)&&(Auth::user()->Role==4))
          <div class="Create-MRT-btn">
            <form action="{{route('create.mrt')}}" method="get">
              <input type="text" name="MCTNo" value="{{$MCTMast[0]->MCTNo}}" style="display:none">
              <button type="submit"><i class="fa fa-plus"></i> Make MRT</button>
            </form>
          </div>
          @elseif($MRTcheck[0]!=null)
            <div class="View-MRT-btn">
              <div class="mrt-done">
                 <form action="{{route('mrt-viewer')}}" method="get">
                   <input type="text" name="MCTNo" value="{{$MCTMast[0]->MCTNo}}" style="display:none">
                   <button type="submit"><i class="fa fa-eye eyesicon"></i> View MRT</button>
                 </form>
              </div>
            </div>
          @else
            No MRT generated yet
          @endif
        @elseif(($MCTMast[0]->Issuedby==Auth::user()->Fname.' '.Auth::user()->Lname)||(($MCTMast[0]->Receivedby==Auth::user()->Fname.' '.Auth::user()->Lname)))
          @if (($MCTMast[0]->IssuedbySignature!=Auth::user()->Signature)&&($MCTMast[0]->ReceivedbySignature!=Auth::user()->Signature))
            <div class="signature-mct-btn">
              <form action="{{route('MCTsignature')}}" method="post">
                {{ csrf_field() }}
                <button type="submit" name="MCTNo" value="{{$MCTMast[0]->MCTNo}}"><i class="fa fa-pencil"></i> Signature</button>
              </form>
            </div>
          @endif
        @endif
        </div>
      <div class="bondpaper-preview">
        <div class="bond-center-titles">
          <h1>BOHOL I ELECTRIC COOPERATIVE, INC.</h1>
          <h5>Cabulijan, Tubigon, Bohol</h5>
          <h2>MATERIALS CHARGE TICKET</h2>
          <img src="/DesignIMG/logo.png" alt="logo">
        </div>
        <div class="MCTMaster-details">
          <div class="MCTmaster-left">
            <ul>
              <li><label>Particulars :</label><h2>{{$MCTMast[0]->Particulars}}</h2></li>
              <li><label>Address :</label><h2>{{$MCTMast[0]->AddressTo}}</h2></li>
            </ul>
          </div>
          <div class="MCTmaster-right">
            <ul>
              <li><label>MIRS No :</label><h2>{{$MCTMast[0]->MIRSNo}}</h2></li>
              <li><label>MCT Date : </label><h2>{{$MCTMast[0]->MCTDate->format('m/d/Y')}}</h2></li>
              <li><label>MCT No.:</label><h2>{{$MCTMast[0]->MCTNo}}</h2></li>
            </ul>
          </div>
        </div>
        <div class="TicketDetail-table">
          <table>
            <tr>
              <th>Acct. Code</th>
              <th>Item Code</th>
              <th>Description</th>
              <th>Unit Cost</th>
              <th>Amount</th>
              <th>Unit</th>
              <th>Quantity</th>
            </tr>
            @foreach ($MTDetails as $MTDetail)
            <tr>
              <td>{{$MTDetail->AccountCode}}</td>
              <td>{{$MTDetail->ItemCode}}</td>
              <td class="align-left">{{$MTDetail->MasterItems->Description}}</td>
              <td>{{number_format($MTDetail->UnitCost,'2','.',',')}}</td>
              <td>{{number_format($MTDetail->Amount,'2','.',',')}}</td>
              <td>{{$MTDetail->Unit}}</td>
              <td>{{$MTDetail->Quantity}}</td>
            </tr>
            @endforeach
          </table>
        </div>
        <div class="totalcost-mct">
          <ul>
            @foreach ($AccountCodeGroup as $Account)
              <li><label>{{$Account->AccountCode}}</label><h1>{{number_format($Account->totals,'2','.',',')}}</h1></li>
            @endforeach
          </ul>
          <div class="total-result">
            <h1>TOTAL</h1><h2>{{number_format($totalsum,'2','.',',')}}</h2>
          </div>
        </div>
        <div class="signatures-mct-preview">
          <div class="mct-signature-left">
            <div class="issuedby-label">
              Issued by:
            </div>
            <div class="issuedby-data">
              <div class="signature-issuedmct">
                @if (!empty($MCTMast[0]->IssuedbySignature))
                  <img src="/storage/signatures/{{$MCTMast[0]->IssuedbySignature}}" alt="signature">
                @endif
              </div>
              <h1>{{$MCTMast[0]->Issuedby}}</h1>
              <h5>{{$MCTMast[0]->IssuedbyPosition}}</h5>
            </div>
          </div>
          <div class="mct-signature-right">
            <div class="recievedby-label">
              Recieved by:
            </div>
            <div class="recievedby-data">
              <div class="signature-recievedmct">
                @if (!empty($MCTMast[0]->ReceivedbySignature))
                 <img src="/storage/signatures/{{$MCTMast[0]->ReceivedbySignature}}" alt="signature">
                @endif
              </div>
              <h1>{{$MCTMast[0]->Receivedby}}</h1>
              <h5>{{$MCTMast[0]->ReceivedbyPosition}}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
