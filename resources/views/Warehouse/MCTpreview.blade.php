@extends('layouts.master')
@section('title')
  MIRS|Preview
@endsection
@section('body')
  <div class="Preview-MCT-container">
      <div class="print-MCT-wrap">
        <div class="MCT-title">
          <form action="{{route('print-mct')}}" method="post">
            {{ csrf_field() }}
            <input type="text" name="MIRSNo" value="{{$MCTMast[0]->MIRSNo}}" style="display:none">
            <button type="submit" name="button"><i class="fa fa-file-pdf-o"></i>.pdf</button>
          </form>
          <div class="Create-MRT-btn">
            <form action="{{route('create.mrt')}}" method="get">
              <input type="text" name="MCTNo" value="{{$MCTMast[0]->MCTNo}}" style="display:none">
              <button type="submit"><i class="fa fa-plus"></i> Make MRT</button>
            </form>
          </div>
          <h1>MCT Print Preview</h1>
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
              <li><label>Date : </label><h2>{{$MCTMast[0]->MIRSDate}}</h2></li>
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
              <div class="signature-issued">
                <img src="/DesignIMG/signature1.png" alt="signature">
              </div>
              <h1>{{$MCTMast[0]->Issuedby}}</h1>
              <h5>HEAD-Warehouse Section</h5>
            </div>
          </div>
          <div class="mct-signature-right">
            <div class="recievedby-label">
              Recieved by:
            </div>
            <div class="recievedby-data">
              <div class="signature-recieved">
                <img src="/DesignIMG/signature5.png" alt="signature">
              </div>
              <h1>{{$MCTMast[0]->Recievedby}}</h1>
              <h5>Leadman</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
