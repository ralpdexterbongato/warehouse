@extends('layouts.master')
@section('title')
  MCT Sign request list
@endsection
@section('body')
  <div class="mct-request-list-Container">
    <h1><i class="material-icons">mode_edit</i>MCT Sign request</h1>
    <div class="mct-request-table">
      @if (!empty($myrequestMCT[0]))
      <table>
        <tr>
          <th>MCT No.</th>
          <th>MCT Date</th>
          <th>MIRS No.</th>
          <th>Particulars</th>
          <th>Address to</th>
          <th>Action</th>
        </tr>
        @foreach ($myrequestMCT as $myMCT)
          <tr>
            <td>{{$myMCT->MCTNo}}</td>
            <td>{{$myMCT->MCTDate->format('m/d/Y')}}</td>
            <td>{{$myMCT->MIRSNo}}</td>
            <td>{{$myMCT->Particulars}}</td>
            <td>{{$myMCT->AddressTo}}</td>
            <td><a href="{{route('MCTpageOnly',[$myMCT->MCTNo])}}"><i class="material-icons">remove_red_eye</i></a></td>
          </tr>
        @endforeach
      </table>
      {{$myrequestMCT->links()}}
      @else
        <h3 class="mct-empty">No sign request received</h3>
      @endif
    </div>
  </div>
@endsection
