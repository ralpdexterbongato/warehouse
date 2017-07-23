@extends('layouts.master')
@section('title')
  My RR Signature request
@endsection
@section('body')
  <div class="RRrequest-container">
    <div class="requestRR-table">
      @if (!empty($requestRR[0]))
      <table>
        <tr>
          <th>RR No.</th>
          <th>Suplier</th>
          <th>Address</th>
          <th>RV No.</th>
          <th>Carrier</th>
          <th>Received by</th>
          <th>Received Original by</th>
          <th>Verified by</th>
          <th>Posted to BIN by</th>
          <th>Action</th>
        </tr>
        @foreach ($requestRR as $requestSignature)
          <tr>
            <td>{{$requestSignature->RRNo}}</td>
            <td>{{$requestSignature->Suplier}}</td>
            <td>{{$requestSignature->Address}}</td>
            <td>{{$requestSignature->RVNo}}</td>
            <td>{{$requestSignature->Carrier}}</td>
            <td>
              {{$requestSignature->Receivedby}}
              @if ($requestSignature->ReceivedbySignature)
                <br><i class="fa fa-check"></i>
              @endif
            </td>
            <td>
              {{$requestSignature->ReceivedOriginalby}}
              @if ($requestSignature->ReceivedOriginalbySignature)
                <br><i class="fa fa-check"></i>
              @endif
            </td>
            <td>
              {{$requestSignature->Verifiedby}}
              @if ($requestSignature->VerifiedbySignature)
                <br><i class="fa fa-check"></i>
              @endif
            </td>
            <td>
              {{$requestSignature->PostedtoBINby}}
              @if ($requestSignature->PostedtoBINbySignature)
              <br><i class="fa fa-check"></i>
              @endif
            </td>
            <td><a href="{{route('RRfullpreview',[$requestSignature->RRNo])}}"><i class="fa fa-eye"></i></a></td>
          </tr>
        @endforeach
      </table>
      @else
        <h1 class="no-RR">RR request is empty</h1>
      @endif
      @if (!empty($requestRR[0]))
        {{$requestRR->links()}}
      @endif
    </div>
  </div>
@endsection
