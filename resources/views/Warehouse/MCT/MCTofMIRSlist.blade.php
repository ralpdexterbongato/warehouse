@extends('layouts.master')
@section('title')
  MCT's of MIRS
@endsection
@section('body')
  <div class="mct-of-mirs-container">
    <div class="title-of-page-container">
      <h1><i class="fa fa-table"></i> M.C.T. list of MIRS No. {{$MCTMaster[0]->MIRSNo}}</h1>
    </div>
    <div class="table-mct-of-mirs">
      @if (isset($MCTMaster[0]))
      <table>
        <tr>
          <th>MCT No.</th>
          <th>MCT Date</th>
          <th>Particulars</th>
          <th>Address to</th>
          <th>Received by</th>
          <th>Status</th>
          <th>View</th>
        </tr>
        @foreach ($MCTMaster as $mctmaster)
          <tr>
            <td>{{$mctmaster->MCTNo}}</td>
            <td>{{$mctmaster->MCTDate->format('M d, Y')}}</td>
            <td>{{$mctmaster->Particulars}}</td>
            <td>{{$mctmaster->AddressTo}}</td>
            <td>{{$mctmaster->Receivedby}}
            @if ($mctmaster->IfDeclined==$mctmaster->Receivedby)
              <i class="fa fa-times decliner"></i>
            @endif
            </td>
            <td>
              @if ($mctmaster->IfDeclined!=null)
                <i class="fa fa-times decliner"></i>
              @elseif($mctmaster->ReceivedbySignature!=null)
                <i class="fa fa-thumbs-up"></i>
              @else
                <i class="fa fa-clock-o color-blue"></i>
              @endif
            </td>
            <td><a href="{{route('MCTpageOnly',[$mctmaster->MCTNo])}}"><i class="fa fa-eye"></i></a></td>
          </tr>
        @endforeach
      </table>
      <div class="pagination-container">
        {{$MCTMaster->links()}}
      </div>
      @endif
    </div>
  </div>
@endsection
