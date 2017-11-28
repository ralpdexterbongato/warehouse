@extends('layouts.master')
@section('title')
MIRS Ready for MCT
@endsection
@section('body')
  <div class="MIRS-Ready-Container">
    <div class="ready-mirs-container">
      <div class="ready-mirs-title">
        <h1><i class="material-icons">thumb_up</i> Newly approved M.I.R.S.</h1>
      </div>
      @if (!empty($readyformct[0]))
      <table>
        <tr>
          <th>MIRS No.</th>
          <th>MIRS Date</th>
          <th>Purpose</th>
          <th>View</th>
        </tr>
        @foreach ($readyformct as $mctReady)
          <tr>
            <td>{{$mctReady->MIRSNo}}</td>
            <td>{{$mctReady->MIRSDate->format('M, d Y')}}</td>
            <td>{{$mctReady->Purpose}}</td>
            <td><a href="{{route('full-mirs',[$mctReady->MIRSNo])}}"><i class="material-icons">remove_red_eye</i></a></td>
          </tr>
        @endforeach
      </table>
      <div class="paginate-container">
        {{$readyformct->links()}}
      </div>
      @else
        <h1 class="no-waitingMIRS">MIRS waiting for MCT is empty</h1>
      @endif
    </div>
  </div>
@endsection
