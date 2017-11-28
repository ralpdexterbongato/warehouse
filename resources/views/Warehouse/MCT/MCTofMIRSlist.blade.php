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
          <th>Status</th>
          <th>View</th>
        </tr>
        @foreach ($MCTMaster as $mctmaster)
          <tr>
            <td>{{$mctmaster->MCTNo}}</td>
            <td>{{$mctmaster->MCTDate->format('M d, Y')}}</td>
            <td>{{$mctmaster->Particulars}}</td>
            <td>{{$mctmaster->AddressTo}}</td>
            </td>
            <td>
              @if ($mctmaster->Status=='1')
                <i class="material-icons decliner">close</i>
              @elseif($mctmaster->Status=='0')
                <i class="material-icons">thumb_up</i>
              @else
                <i class="material-icons color-blue">access_time</i>
              @endif
            </td>
            <td><a href="{{route('MCTpageOnly',[$mctmaster->MCTNo])}}"><i class="material-icons">remove_red_eye</i></a></td>
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
