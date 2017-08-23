@extends('layouts.master')
@section('title')
  MCT signature request list
@endsection
@section('body')
  <div class="mct-request-list-Container">
    <h1>My MCT signature <i class="fa fa-pencil"></i>request</h1>
    <div class="mct-request-table">
      @if (!empty($myrequestMCT[0]))
      <table>
        <tr>
          <th>MCT No.</th>
          <th>MIRS No.</th>
          <th>Particulars</th>
          <th>Address to</th>
          <th>Issued by</th>
          <th>Received by</th>
          <th>MIRS Date</th>
          <th>Action</th>
        </tr>
        <tr>
          @foreach ($myrequestMCT as $myMCT)
            <tr>
              <td>{{$myMCT->MCTNo}}</td>
              <td>{{$myMCT->MIRSNo}}</td>
              <td>{{$myMCT->Particulars}}</td>
              <td>{{$myMCT->AddressTo}}</td>
              <td>{{$myMCT->Issuedby}}
                @if (!empty($myMCT->IssuedbySignature))
                  <i class="fa fa-check"></i>
                @endif
              </td>
              <td>{{$myMCT->Receivedby}}
                @if (!empty($myMCT->ReceivedbySignature))
                  <i class="fa fa-check"></i>
                @endif
              </td>
              <td>{{$myMCT->MCTDate->format('m/d/Y')}}</td>
              <td><a href="{{route('previewMCT',[$myMCT->MCTNo])}}"><i class="fa fa-eye"></i></a></td>
            </tr>
          @endforeach
        </tr>
      </table>
      {{$myrequestMCT->links()}}
      @else
        <h3 class="mct-empty">MCT Request is empty</h3>
      @endif
    </div>
  </div>
@endsection
