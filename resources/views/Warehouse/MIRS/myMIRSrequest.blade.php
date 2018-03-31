@extends('layouts.master')
@section('title')
MIRS Sign request list
@endsection
@section('body')
  <div class="MIRS-request-container">
    <h1><i class="material-icons">mode_edit</i>MIRS Sign request</h1>
      @if (!empty($myrequestMIRS[0]))
    <div class="mirs-request-table-list">
      <table>
        <tr>
          <th>MIRS No.</th>
          <th>MIRS Date</th>
          <th>Purpose</th>
          <th>View</th>
        </tr>
          @foreach ($myrequestMIRS as $mirsRequest)
          <tr>
            <td>{{$mirsRequest->MIRSNo}}</td>
            <td>{{$mirsRequest->MIRSDate->format('M d, Y')}}</td>
            <td>{{$mirsRequest->Purpose}}</td>
            <td><a href="{{route('full-mirs',[$mirsRequest->MIRSNo])}}"><i class="material-icons">remove_red_eye</i></a></td>
          </tr>
          @endforeach
      </table>
      <div class="paginate-container">
        {{$myrequestMIRS->links()}}
      </div>
    </div>
  @else
  <h3 class="MIRS-request-empty">No sign request received</h3>
  @endif
  </div>
@endsection
