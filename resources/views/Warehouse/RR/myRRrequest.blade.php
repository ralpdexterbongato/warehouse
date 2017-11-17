@extends('layouts.master')
@section('title')
  My RR Signature request
@endsection
@section('body')
  <div class="RRrequest-container">
    <div class="title-rr-request">
      <h1>My RR signature<i class="fa fa-pencil"></i> requests</h1>
    </div>
    <div class="requestRR-table">
      @if (!empty($requestRR[0]))
      <table>
        <tr>
          <th>RR No.</th>
          <th>Supplier</th>
          <th>Address</th>
          <th>RV No.</th>
          <th>Received by</th>
          <th>Received Original by</th>
          <th>Verified by</th>
          <th>Posted to BIN by</th>
          <th>Show</th>
        </tr>
        @foreach ($requestRR as $requestSignature)
          <tr>
            <td>{{$requestSignature->RRNo}}</td>
            <td>{{$requestSignature->Supplier}}</td>
            <td>{{$requestSignature->Address}}</td>
            <td>{{$requestSignature->RVNo}}</td>
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
      <div class="paginate-container">
        {{$requestRR->links()}}
      </div>
      @endif
    </div>
  </div>
@endsection
